@php
    use Carbon\Carbon;
@endphp

<x-app-layout>
    {{-- Barre d'actions --}}
    <div class="action-bar">
        <div class="action-bar-inner">
            <div class="action-info">
                <i class="fas fa-chart-line"></i>
                <span>{{ __('Bilan des utilisateurs') }}</span>
            </div>
            <div class="action-stats">
                <span class="stat-badge">
                    <i class="fas fa-users"></i>
                    {{ $users->total() }} utilisateurs
                </span>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="content-card">
            {{-- En-tête --}}
            <div class="content-header">
                <div>
                    <h1 class="page-title">{{ __('Bilan') }}</h1>
                    <p class="page-subtitle">{{ __('Rapports des utilisateurs') }}</p>
                </div>
                <div class="export-group">
                    <button id="export-excel" class="export-btn export-btn--excel">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                    <button id="export-csv" class="export-btn export-btn--csv">
                        <i class="fas fa-file-alt"></i> Export CSV
                    </button>
                    <button id="export-pdf" class="export-btn export-btn--pdf">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                </div>
            </div>

            {{-- Formulaire de filtrage --}}
            <div class="filters-section">
                <form action="{{ route('balance.sheet.index') }}" method="get" class="filters-form">
                    <div class="filters-grid">
                        <div class="filter-group">
                            <label class="filter-label">Site / Type</label>
                            <select name="type" id="filter-type" class="filter-select">
                                <option value="">Choisissez</option>
                                @foreach ($sites as $site)
                                    <option value="{{ $site->name }}" {{ request('type') == $site->name ? 'selected' : '' }}>
                                        {{ $site->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">Période</label>
                            <select name="period" id="filter-period" class="filter-select">
                                <option value="day" {{ request('period') == 'day' ? 'selected' : '' }}>Jour</option>
                                <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Semaine</option>
                                <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Mois</option>
                                <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>Année</option>
                            </select>
                        </div>

                        <div class="filter-group" id="day-select-container">
                            <label class="filter-label">Date</label>
                            <input type="date" name="day" value="{{ request('day') ?? now()->format('Y-m-d') }}" class="filter-input">
                        </div>

                        <div class="filter-group" id="week-select-container" style="display: none;">
                            <label class="filter-label">Semaine</label>
                            <input type="week" name="week" value="{{ request('week') ?? now()->format('Y-\WW') }}" class="filter-input">
                        </div>

                        <div class="filter-group" id="month-select-container" style="display: none;">
                            <label class="filter-label">Mois</label>
                            <input type="month" name="month" value="{{ request('month') ?? now()->format('Y-m') }}" class="filter-input">
                        </div>

                        <div class="filter-group" id="year-select-container" style="display: none;">
                            <label class="filter-label">Année</label>
                            <select name="year" class="filter-select">
                                @foreach (range(now()->year - 10, now()->year) as $yearOption)
                                    <option value="{{ $yearOption }}" {{ (request('year') ?? now()->year) == $yearOption ? 'selected' : '' }}>
                                        {{ $yearOption }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-group filter-actions">
                            <button type="submit" class="filter-btn-primary">
                                <i class="fas fa-filter"></i> Filtrer
                            </button>
                            <a href="{{ route('balance.sheet.index') }}" class="filter-btn-secondary">
                                <i class="fas fa-sync-alt"></i> Réinitialiser
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Tableau --}}
            <div class="table-container">
                @if ($users->count() > 0)
                    <div class="table-wrapper">
                        <table class="data-table" id="export-table">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Fonction</th>
                                    {{-- <th>Taux salarial</th> --}}
                                    <th>Statut</th>
                                    <th>Présent</th>
                                    <th>Retard</th>
                                    <th>Absent</th>
                                    <th>Temps</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>
                                            <div class="user-cell">
                                                <div class="user-avatar">
                                                    @if($user->avatar && Storage::disk('public')->exists($user->avatar))
                                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar">
                                                    @else
                                                        <div class="avatar-placeholder">
                                                            {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="user-info">
                                                    <a href="{{ route('users.show', $user->id) }}" class="user-name">
                                                        {{ $user->first_name ?? '' }} {{ $user->name ?? '' }}
                                                    </a>
                                                    <span class="user-email">{{ $user->email ?? '' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $user->employee_function ?? '-' }}</td>
                                        {{-- <td>{{ $user->salary_rate ?? '-' }}</td> --}}
                                        <td>
                                            @if ($user->status == 'active')
                                                <span class="status-badge status-active">
                                                    <i class="fas fa-check-circle"></i> Actif
                                                </span>
                                            @else
                                                <span class="status-badge status-inactive">
                                                    <i class="fas fa-times-circle"></i> Inactif
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="stat-number stat-present">
                                                {{ isset($metrics[$user->id]['total_present']) ? $metrics[$user->id]['total_present'] : '0' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="stat-number stat-late">
                                                {{ isset($metrics[$user->id]['total_late']) ? $metrics[$user->id]['total_late'] : '0' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="stat-number stat-absent">
                                                {{ isset($metrics[$user->id]['total_absent']) ? $metrics[$user->id]['total_absent'] : '0' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="stat-number stat-hours">
                                                {{ isset($metrics[$user->id]['total_hours']) ? $metrics[$user->id]['total_hours'] : '0' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination-wrapper">
                        {{ $users->links() }}
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-chart-line fa-3x mb-3 opacity-50"></i>
                        <p>Aucun utilisateur trouvé</p>
                        <span class="empty-sub">Veuillez modifier vos critères de recherche</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        /* ============================================ */
        /* STYLES MODERNES POUR LA PAGE BILAN           */
        /* ============================================ */

        :root {
            --vp: #2e7d64;
            --vc: #e8f5f1;
            --vf: #1a5a48;
            --va: #40a088;
            --gf: #2d3a3e;
            --gm: #5a6b6f;
            --gl: #d4dfe2;
            --gtc: #eef3f5;
            --t: all 0.32s cubic-bezier(.4,0,.2,1);
        }

        /* Barre d'actions */
        .action-bar {
            position: fixed;
            top: var(--navbar-height, 60px);
            left: var(--sidebar-width, 260px);
            right: 0;
            z-index: 900;
            background: #2e7d64;
            border-bottom: 2px solid rgba(0,0,0,0.1);
            transition: left 0.32s cubic-bezier(.4,0,.2,1);
        }

        .action-bar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            height: 46px;
        }

        .action-info { display: inline-flex; align-items: center; gap: 10px; color: white; font-size: 0.85rem; font-weight: 500; }
        .action-stats { background: rgba(255,255,255,0.15); padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; color: white; }

        /* Page content */
        .page-content { padding-top: 46px; }
        .content-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--gl);
            padding: 24px;
            margin: 0 24px;
        }

        .content-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
        }
        .page-title { font-size: 1.35rem; font-weight: 700; color: #1f2937; margin: 0; }
        .page-subtitle { font-size: 0.8rem; color: #6b7280; margin-top: 4px; }

        /* Export buttons */
        .export-group { display: flex; gap: 8px; flex-wrap: wrap; }
        .export-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 7px;
            border: none;
            cursor: pointer;
            transition: filter 0.18s, transform 0.1s;
            color: #fff;
        }
        .export-btn:hover { filter: brightness(1.1); transform: translateY(-1px); }
        .export-btn--excel { background: #16a34a; }
        .export-btn--csv   { background: #2563eb; }
        .export-btn--pdf   { background: #dc2626; }

        /* Filtres */
        .filters-section { margin-bottom: 24px; }
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
            align-items: end;
        }
        .filter-group { display: flex; flex-direction: column; gap: 6px; }
        .filter-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: var(--gm); letter-spacing: 0.5px; }
        .filter-select, .filter-input {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--gl);
            border-radius: 12px;
            font-size: 0.85rem;
            background: white;
            transition: var(--t);
        }
        .filter-select:focus, .filter-input:focus {
            outline: none;
            border-color: var(--vp);
            box-shadow: 0 0 0 3px rgba(46,125,100,0.1);
        }
        .filter-actions { display: flex; gap: 10px; flex-wrap: wrap; }
        .filter-btn-primary, .filter-btn-secondary {
            padding: 10px 20px;
            border-radius: 40px;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--t);
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .filter-btn-primary { background: var(--vp); color: white; }
        .filter-btn-primary:hover { background: var(--vf); transform: translateY(-2px); }
        .filter-btn-secondary { background: var(--gtc); color: var(--gf); border: 1px solid var(--gl); }
        .filter-btn-secondary:hover { background: var(--gl); transform: translateY(-2px); }

        /* Tableau */
        .table-container {
            overflow-x: auto;
            border-radius: 16px;
            border: 1px solid var(--gl);
            background: white;
        }
        .table-wrapper { overflow-x: auto; }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
            min-width: 900px;
        }
        .data-table th {
            padding: 12px 16px;
            background: var(--gtc);
            font-weight: 600;
            color: var(--gf);
            border-bottom: 1px solid var(--gl);
            text-align: left;
        }
        .data-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }
        .data-table tr:hover td { background: #f9fafb; }

        /* User cell */
        .user-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .user-avatar { flex-shrink: 0; }
        .user-avatar img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
        .avatar-placeholder {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--vp);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
        }
        .user-info { display: flex; flex-direction: column; }
        .user-name {
            font-weight: 600;
            color: var(--gf);
            text-decoration: none;
            transition: var(--t);
        }
        .user-name:hover { color: var(--vp); }
        .user-email { font-size: 0.7rem; color: var(--gm); }

        /* Status badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .status-active { background: #dcfce7; color: #166534; }
        .status-inactive { background: #fee2e2; color: #991b1b; }

        /* Stat numbers */
        .stat-number { font-weight: 700; font-size: 0.9rem; }
        .stat-present { color: #10b981; }
        .stat-late { color: #f59e0b; }
        .stat-absent { color: #ef4444; }
        .stat-hours { color: var(--vp); }

        .pagination-wrapper { display: flex; justify-content: flex-end; padding: 16px; border-top: 1px solid var(--gl); }

        /* Empty state */
        .empty-state { text-align: center; padding: 60px 20px; color: #9ca3af; }
        .empty-sub { font-size: 0.75rem; display: block; margin-top: 8px; }

        @media (max-width: 1280px) { .action-bar { left: 0; } }
        @media (max-width: 768px) {
            .page-content { padding: 46px 0 0 0; }
            .content-card { margin: 0 16px; padding: 16px; }
            .filters-grid { grid-template-columns: 1fr; }
            .filter-actions { flex-direction: column; }
            .filter-btn-primary, .filter-btn-secondary { width: 100%; justify-content: center; }
            .user-cell { min-width: 180px; }
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const $filterPeriod = $('#filter-period');
            const $daySelect = $('#day-select-container');
            const $weekSelect = $('#week-select-container');
            const $monthSelect = $('#month-select-container');
            const $yearSelect = $('#year-select-container');

            function toggleFields() {
                const period = $filterPeriod.val();
                $daySelect.hide();
                $weekSelect.hide();
                $monthSelect.hide();
                $yearSelect.hide();

                if (period === 'day') $daySelect.show();
                else if (period === 'week') $weekSelect.show();
                else if (period === 'month') $monthSelect.show();
                else if (period === 'year') $yearSelect.show();
            }

            toggleFields();
            $filterPeriod.on('change', toggleFields);

            // Export Excel
            $('#export-excel').on('click', function() {
                let table = document.getElementById('export-table');
                let html = table.outerHTML;
                let url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
                let link = document.createElement('a');
                link.download = 'bilan_utilisateurs_' + new Date().toISOString().slice(0,19) + '.xls';
                link.href = url;
                link.click();
            });

            // Export CSV
            $('#export-csv').on('click', function() {
                let rows = document.querySelectorAll('#export-table tr');
                let csv = [];
                rows.forEach(row => {
                    let cols = row.querySelectorAll('td, th');
                    let rowData = [];
                    cols.forEach(col => {
                        let text = col.innerText.replace(/"/g, '""').replace(/\n/g, ' ');
                        rowData.push('"' + text + '"');
                    });
                    csv.push(rowData.join(','));
                });
                let blob = new Blob([csv.join('\n')], { type: 'text/csv' });
                let link = document.createElement('a');
                link.download = 'bilan_utilisateurs_' + new Date().toISOString().slice(0,19) + '.csv';
                link.href = URL.createObjectURL(blob);
                link.click();
            });

            // Export PDF
            $('#export-pdf').on('click', function() {
                let printWindow = window.open('', '_blank', 'width=1200,height=800');
                let tableHtml = document.getElementById('export-table').outerHTML;
                let title = 'Bilan des utilisateurs';

                printWindow.document.write(`
                    <html>
                    <head>
                        <title>${title}</title>
                        <style>
                            body { font-family: Arial, sans-serif; margin: 20px; }
                            h1 { color: #2e7d64; font-size: 18px; margin-bottom: 20px; }
                            table { width: 100%; border-collapse: collapse; font-size: 11px; }
                            th { background: #f3f4f6; padding: 8px; border: 1px solid #ddd; text-align: left; }
                            td { padding: 8px; border: 1px solid #ddd; }
                            .status-active { color: #166534; }
                            .status-inactive { color: #991b1b; }
                        </style>
                    </head>
                    <body>
                        <h1>${title}</h1>
                        ${tableHtml}
                    </body>
                    </html>
                `);
                printWindow.document.close();
                printWindow.print();
                printWindow.close();
            });
        });
    </script>
</x-app-layout>
