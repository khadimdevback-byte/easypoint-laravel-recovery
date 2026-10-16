@php
    use Carbon\Carbon;
@endphp

<x-app-layout>
    {{-- ===================================================== --}}
    {{-- BARRE D'ACTIONS — collée sous la navbar               --}}
    {{-- ===================================================== --}}
    <div class="action-bar">
        <div class="action-bar-inner">
            <div class="action-info">
                <i class="fas fa-chart-line"></i>
                <span>{{ __('Bilan des Sociétés') }}</span>
            </div>
            <div class="action-spacer"></div>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- CONTENU PRINCIPAL                                     --}}
    {{-- ===================================================== --}}
    <div class="page-content">
        <div class="content-card">
            <div class="content-header">
                <h1 class="page-title">{{ __('Bilan des Sociétés') }}</h1>
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

            {{-- Tableau --}}
            <div class="table-wrapper">
                <table class="data-table" id="export-table">
                    <thead>
                        <tr>
                            <th>{{ __('Nom') }}</th>
                            <th>{{ __('Adresse') }}</th>
                            {{-- <th>{{ __('N° Siret') }}</th>
                            <th>{{ __('N° Siren') }}</th> --}}
                            <th>{{ __('Statut') }}</th>
                            <th>{{ __('Lieux') }}</th>
                            {{-- <th>{{ __('Gestionnaire') }}</th> --}}
                            <th>{{ __('Contrôleur') }}</th>
                            <th>{{ __('Employés') }}</th>
                            {{-- <th>{{ __('Clients') }}</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @if ($companies->count() > 0)
                            @foreach ($companies as $company)
                                <tr>
                                    <td>
                                        <div class="company-cell">
                                            <div class="company-avatar">
                                                @if($company->logo && Storage::disk('public')->exists($company->logo))
                                                    <img src="{{ asset('storage/' . $company->logo) }}" class="company-logo" alt="Logo">
                                                @else
                                                    <div class="company-logo-placeholder">
                                                        {{ strtoupper(substr($company->compagny_name ?? 'C', 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="company-info">
                                                <div class="company-name">{{ $company->compagny_name ?? '-' }}</div>
                                                <div class="company-email">{{ $company->compagny_email ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="address-text">{{ $company->address ?? '-' }}</span>
                                    </td>
                                    {{-- <td class="text-center">{{ $company->n_siret ?? '-' }}</td>
                                    <td class="text-center">{{ $company->n_siren ?? '-' }}</td> --}}
                                    <td>
                                        @if ($company->status == 'active')
                                            <span class="status-badge status-badge--active">Actif</span>
                                        @else
                                            <span class="status-badge status-badge--inactive">Inactif</span>
                                        @endif
                                    </td>
                                    <td class="text-center"><span class="stat-number">{{ $company->sites->count() ?? 0 }}</span></td>
                                    {{-- <td class="text-center"><span class="stat-number">{{ $company->managers_count }}</span></td> --}}
                                    <td class="text-center"><span class="stat-number">{{ $company->controllers_count }}</span></td>
                                    <td class="text-center"><span class="stat-number">{{ $company->users_count }}</span></td>
                                    {{-- <td class="text-center"><span class="stat-number">{{ $company->customers_count }}</span></td> --}}
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="10" class="empty-row">Aucune donnée disponible</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                {{ $companies->links() }}
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

        .action-info {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: rgba(255,255,255,0.95);
            font-size: 0.85rem;
            font-weight: 500;
        }

        .action-info i {
            font-size: 1rem;
        }

        .page-content { padding-top: 46px; }
        .content-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--gl);
            padding: 24px;
            overflow: visible;
            position: relative;
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

        /* Tableau */
        .table-wrapper {
            overflow-x: auto;
            position: relative;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.83rem;
            color: #374151;
        }
        .data-table thead tr {
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }
        .data-table th {
            padding: 12px 16px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #6b7280;
            text-align: left;
            white-space: nowrap;
        }
        .data-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
            white-space: nowrap;
        }
        .data-table tbody tr:last-child td { border-bottom: none; }
        .data-table tbody tr:hover { background: #f9fafb; }

        /* Company cell */
        .company-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .company-avatar {
            flex-shrink: 0;
        }
        .company-logo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #e5e7eb;
        }
        .company-logo-placeholder {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #2e7d64;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: bold;
        }
        .company-info {
            display: flex;
            flex-direction: column;
        }
        .company-name {
            font-weight: 600;
            color: #1f2937;
            font-size: 0.85rem;
        }
        .company-email {
            font-size: 0.7rem;
            color: #9ca3af;
        }

        .address-text {
            font-size: 0.8rem;
            color: #4b5563;
            max-width: 200px;
            display: block;
            white-space: normal;
            word-break: break-word;
        }

        .stat-number {
            font-weight: 700;
            font-size: 1rem;
            color: #2e7d64;
            background: #e8f5f1;
            display: inline-block;
            min-width: 40px;
            padding: 4px 8px;
            border-radius: 20px;
            text-align: center;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .status-badge--active { background: #dcfce7; color: #166534; }
        .status-badge--inactive { background: #fee2e2; color: #991b1b; }

        .text-center {
            text-align: center;
        }

        .empty-row { text-align: center; padding: 40px 16px; color: #9ca3af; font-size: 0.85rem; }
        .pagination-wrapper { display: flex; justify-content: flex-end; margin-top: 20px; }

        @media (max-width: 1280px) { .action-bar { left: 0; } }
        @media (max-width: 768px) {
            .page-content { padding: 46px 0 0 0; }
            .content-card { margin: 0 16px; padding: 16px; }
            .action-bar-inner { padding: 0 14px; }
            .content-header { flex-direction: column; align-items: flex-start; }
            .company-cell { min-width: 180px; }
            .address-text { min-width: 150px; }
            .stat-number { min-width: 50px; display: inline-block; }
        }
    </style>

    <script>
        // Export Excel
        document.getElementById('export-excel')?.addEventListener('click', function() {
            let table = document.getElementById('export-table');
            let html = table.outerHTML;
            let url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
            let link = document.createElement('a');
            link.download = 'bilan_societes_' + new Date().toISOString().slice(0,19) + '.xls';
            link.href = url;
            link.click();
        });

        // Export CSV
        document.getElementById('export-csv')?.addEventListener('click', function() {
            let rows = document.querySelectorAll('#export-table tr');
            let csv = [];
            rows.forEach(row => {
                let cols = row.querySelectorAll('td, th');
                let rowData = [];
                cols.forEach(col => {
                    let text = col.innerText.replace(/"/g, '""');
                    if (col.querySelector('.company-cell')) {
                        text = col.querySelector('.company-name')?.innerText || '';
                    }
                    rowData.push('"' + text + '"');
                });
                csv.push(rowData.join(','));
            });
            let blob = new Blob([csv.join('\n')], { type: 'text/csv' });
            let link = document.createElement('a');
            link.download = 'bilan_societes_' + new Date().toISOString().slice(0,19) + '.csv';
            link.href = URL.createObjectURL(blob);
            link.click();
        });

        // Export PDF
        document.getElementById('export-pdf')?.addEventListener('click', function() {
            let printWindow = window.open('', '_blank', 'width=1200,height=800');
            let tableHtml = document.getElementById('export-table').outerHTML;
            let title = document.querySelector('.page-title')?.innerText || 'Bilan des Sociétés';

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
                        .status-badge--active { background: #dcfce7; color: #166534; display: inline-block; padding: 2px 8px; border-radius: 20px; }
                        .status-badge--inactive { background: #fee2e2; color: #991b1b; display: inline-block; padding: 2px 8px; border-radius: 20px; }
                        .stat-number { font-weight: bold; color: #2e7d64; }
                        .text-center { text-align: center; }
                        .company-name { font-weight: bold; }
                        .company-email { font-size: 10px; color: gray; }
                    </style>
                </head>
                <body>
                    <h1>${title}</h1>
                    ${tableHtml.replace(/<button/g, '<span').replace(/<\/button>/g, '</span>')}
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
            printWindow.close();
        });
    </script>
</x-app-layout>
