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
                <i class="fas fa-envelope"></i>
                <span>{{ __('Messages de contact') }}</span>
            </div>
            <div class="action-stats">
                <div class="stat-badge">
                    <i class="fas fa-clock"></i>
                    <span>En attente: {{ $statusCounts['pending'] ?? 0 }}</span>
                </div>
                <div class="stat-badge">
                    <i class="fas fa-spinner"></i>
                    <span>En traitement: {{ $statusCounts['processing'] ?? 0 }}</span>
                </div>
                <div class="stat-badge">
                    <i class="fas fa-check-circle"></i>
                    <span>Répondu: {{ $statusCounts['responded'] ?? 0 }}</span>
                </div>
                <div class="stat-badge">
                    <i class="fas fa-archive"></i>
                    <span>Clôturé: {{ $statusCounts['closed'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- CONTENU PRINCIPAL                                     --}}
    {{-- ===================================================== --}}
    <div class="page-content">
        <div class="content-card">
            <div class="content-header">
                <div>
                    <h1 class="page-title">{{ __('Messages de contact') }}</h1>
                    <p class="page-subtitle">{{ __('Gérez les messages reçus via le formulaire de contact') }}</p>
                </div>
                <div class="export-group">
                    <button id="export-excel" class="export-btn export-btn--excel">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                    <button id="export-csv" class="export-btn export-btn--csv">
                        <i class="fas fa-file-alt"></i> Export CSV
                    </button>
                </div>
            </div>

            {{-- Filtres --}}
            <div class="filters-bar">
                <form method="GET" action="{{ route('admin.contacts.index') }}" class="filters-form">
                    <div class="filters-group">
                        <select name="status" class="filter-select">
                            <option value="">Tous les statuts</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>En traitement</option>
                            <option value="responded" {{ request('status') == 'responded' ? 'selected' : '' }}>Répondu</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Clôturé</option>
                        </select>

                        <select name="sort" class="filter-select">
                            <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Plus récent d'abord</option>
                            <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Plus ancien d'abord</option>
                        </select>

                        <button type="submit" class="filter-btn filter-btn-primary">
                            <i class="fas fa-filter"></i> Filtrer
                        </button>

                        <a href="{{ route('admin.contacts.index') }}" class="filter-btn filter-btn-secondary">
                            <i class="fas fa-sync-alt"></i> Réinitialiser
                        </a>
                    </div>
                </form>
            </div>

            {{-- Tableau --}}
            <div class="table-wrapper">
                <table class="data-table" id="export-table">
                    <thead>
                        <tr>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Nom complet') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Entreprise') }}</th>
                            <th>{{ __('Sujet') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Statut') }}</th>
                            <th style="width: 80px;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contacts as $contact)
                            <tr class="contact-row {{ !$contact->is_read ? 'unread-row' : '' }}">
                                <td data-label="{{ __('Date') }}">
                                    <div class="date-cell">
                                        <span class="date-day">{{ $contact->created_at->format('d/m/Y') }}</span>
                                        <span class="date-time">{{ $contact->created_at->format('H:i') }}</span>
                                    </div>
                                </td>
                                <td data-label="{{ __('Nom complet') }}">
                                    <div class="contact-cell">
                                        <div class="contact-avatar">
                                            <span class="avatar-initials">
                                                {{ strtoupper(substr($contact->first_name, 0, 1)) }}{{ strtoupper(substr($contact->last_name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div class="contact-info">
                                            <span class="contact-name {{ !$contact->is_read ? 'font-bold' : '' }}">{{ $contact->first_name }} {{ $contact->last_name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="{{ __('Email') }}">
                                    <a href="mailto:{{ $contact->email }}" class="email-link">{{ $contact->email }}</a>
                                </td>
                                <td data-label="{{ __('Entreprise') }}">
                                    <div class="company-cell">
                                        <span class="company-name">{{ $contact->company }}</span>
                                        @if($contact->employees)
                                            <span class="company-employees">{{ $contact->employees }} employés</span>
                                        @endif
                                    </div>
                                </td>
                                <td data-label="{{ __('Sujet') }}">
                                    <div class="subject-cell" title="{{ $contact->subject }}">
                                        {{ Str::limit($contact->subject, 40) }}
                                    </div>
                                </td>
                                <td data-label="{{ __('Type') }}">
                                    <span class="intent-badge
                                        @if($contact->intent == 'general') intent-general
                                        @elseif($contact->intent == 'devis') intent-devis
                                        @else intent-support @endif">
                                        @if($contact->intent == 'general')
                                            <i class="fas fa-comment"></i> Renseignement
                                        @elseif($contact->intent == 'devis')
                                            <i class="fas fa-file-invoice"></i> Devis
                                        @else
                                            <i class="fas fa-headset"></i> Support
                                        @endif
                                    </span>
                                </td>
                                <td data-label="{{ __('Statut') }}">
                                    <span class="status-badge
                                        @if($contact->status == 'pending') status-pending
                                        @elseif($contact->status == 'processing') status-processing
                                        @elseif($contact->status == 'responded') status-responded
                                        @else status-closed @endif">
                                        @if($contact->status == 'pending')
                                            <i class="fas fa-clock"></i> En attente
                                        @elseif($contact->status == 'processing')
                                            <i class="fas fa-spinner"></i> En traitement
                                        @elseif($contact->status == 'responded')
                                            <i class="fas fa-check-circle"></i> Répondu
                                        @else
                                            <i class="fas fa-archive"></i> Clôturé
                                        @endif
                                    </span>
                                </td>
                                <td data-label="{{ __('Actions') }}">
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.contacts.show', $contact->id) }}" class="action-btn-view" title="Voir le message">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button onclick="deleteContact({{ $contact->id }})" class="action-btn-delete" title="Supprimer">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="empty-row">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                    <p>{{ __('Aucun message de contact') }}</p>
                                    <span class="empty-sub">{{ __('Les messages des visiteurs apparaîtront ici') }}</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($contacts->hasPages())
                <div class="pagination-wrapper">
                    {{ $contacts->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    <style>
        /* ============================================ */
        /* STYLES MODERNES POUR LA PAGE CONTACTS        */
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

        .action-stats {
            display: flex;
            gap: 16px;
        }

        .stat-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.15);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            color: white;
        }

        /* Page content */
        .page-content { padding-top: 46px; }
        .content-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--gl);
            padding: 24px;
            overflow: visible;
            position: relative;
            margin: 0 24px;
            box-shadow: 0 12px 34px rgba(45, 58, 62, 0.06);
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

        /* Filtres */
        .filters-bar {
            background: #f6f9fa;
            border: 1px solid #e5ecee;
            border-radius: 14px;
            padding: 14px;
            margin-bottom: 20px;
        }
        .filters-form {
            width: 100%;
        }
        .filters-group {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }
        .filter-select {
            min-height: 42px;
            padding: 8px 14px;
            border: 1px solid #d8e2e5;
            border-radius: 10px;
            font-size: 0.8rem;
            background: white;
            color: var(--gf);
            min-width: 190px;
            transition: border-color 0.18s, box-shadow 0.18s;
        }
        .filter-select:focus {
            outline: none;
            border-color: var(--vp);
            box-shadow: 0 0 0 3px rgba(46, 125, 100, 0.12);
        }
        .filter-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 42px;
            padding: 8px 18px;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--t);
            border: none;
            text-decoration: none;
        }
        .filter-btn-primary {
            background: var(--vp);
            color: white;
        }
        .filter-btn-primary:hover {
            background: var(--vf);
            transform: translateY(-1px);
        }
        .filter-btn-secondary {
            background: #f3f4f6;
            color: var(--gf);
            border: 1px solid var(--gl);
        }
        .filter-btn-secondary:hover {
            background: var(--gl);
        }

        /* Tableau */
        .table-wrapper {
            overflow-x: auto;
            position: relative;
            border: 1px solid #e8eef0;
            border-radius: 14px;
            scrollbar-width: thin;
            scrollbar-color: #b8c9cc transparent;
        }
        .data-table {
            width: 100%;
            min-width: 1120px;
            border-collapse: collapse;
            font-size: 0.83rem;
            color: #374151;
        }
        .data-table thead tr {
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }
        .data-table th {
            padding: 14px 16px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #6b7280;
            text-align: left;
            white-space: nowrap;
            background: #f7f9fa;
        }
        .data-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }
        .data-table tbody tr:last-child td { border-bottom: none; }
        .data-table tbody tr:hover { background: #f9fafb; }
        .unread-row { background: #f1f7ff; }
        .unread-row td:first-child { box-shadow: inset 3px 0 #4c8bd9; }

        /* Date cell */
        .date-cell {
            display: flex;
            flex-direction: column;
        }
        .date-day {
            font-weight: 600;
            font-size: 0.8rem;
        }
        .date-time {
            font-size: 0.65rem;
            color: #9ca3af;
        }

        /* Contact cell */
        .contact-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .contact-avatar {
            flex-shrink: 0;
        }
        .avatar-initials {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--vp);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
        }
        .contact-name {
            font-size: 0.8rem;
            color: #1f2937;
            white-space: nowrap;
        }
        .font-bold {
            font-weight: 700;
        }

        /* Email link */
        .email-link {
            font-size: 0.75rem;
            color: var(--vp);
            text-decoration: none;
            white-space: nowrap;
        }
        .email-link:hover {
            text-decoration: underline;
        }

        /* Company cell */
        .company-cell {
            display: flex;
            flex-direction: column;
            min-width: 130px;
        }
        .company-name {
            font-size: 0.75rem;
            font-weight: 500;
            color: #1f2937;
        }
        .company-employees {
            font-size: 0.65rem;
            color: #9ca3af;
        }

        /* Subject cell */
        .subject-cell {
            font-size: 0.75rem;
            color: #4b5563;
            max-width: 230px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Intent badges */
        .intent-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 500;
        }
        .intent-general { background: #f3f4f6; color: #4b5563; }
        .intent-devis { background: #f3e8ff; color: #6b21a5; }
        .intent-support { background: #fed7aa; color: #9a3412; }

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
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-processing { background: #e0f2fe; color: #0369a1; }
        .status-responded { background: #dcfce7; color: #166534; }
        .status-closed { background: #f3f4f6; color: #4b5563; }

        /* Action buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }
        .action-btn-view, .action-btn-delete {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            transition: var(--t);
            cursor: pointer;
            border: none;
            background: none;
            text-decoration: none;
        }
        .action-btn-view {
            background: var(--gtc);
            color: var(--vp);
        }
        .action-btn-view:hover {
            background: var(--vp);
            color: white;
            transform: translateY(-2px);
        }
        .action-btn-delete {
            background: #fee2e2;
            color: #dc2626;
        }
        .action-btn-delete:hover {
            background: #dc2626;
            color: white;
            transform: translateY(-2px);
        }

        .empty-row {
            text-align: center;
            padding: 60px 16px;
            color: #9ca3af;
        }
        .empty-row i { color: #9aaeb2; }
        .empty-row p { margin: 10px 0 0; color: #52656a; font-size: 0.95rem; font-weight: 600; }
        .empty-sub {
            font-size: 0.7rem;
            display: block;
            margin-top: 8px;
        }
        .pagination-wrapper {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #f3f4f6;
        }

        @media (max-width: 1500px) {
            .table-wrapper { overflow: visible; border: 0; }
            .data-table,
            .data-table tbody { display: block; min-width: 0; }
            .data-table thead {
                position: absolute;
                width: 1px;
                height: 1px;
                padding: 0;
                margin: -1px;
                overflow: hidden;
                clip: rect(0, 0, 0, 0);
                white-space: nowrap;
                border: 0;
            }
            .data-table tbody { display: grid; gap: 12px; }
            .data-table tbody tr:not(.contact-row) { display: block; }
            .data-table tr.contact-row {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 0 16px;
                padding: 8px 14px;
                border: 1px solid #e5ecee;
                border-radius: 14px;
                background: white;
                box-shadow: 0 4px 14px rgba(45, 58, 62, 0.04);
            }
            .data-table tr.contact-row.unread-row { background: #f1f7ff; }
            .data-table tr.contact-row td {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                min-width: 0;
                padding: 10px 2px;
                border-bottom: 1px solid #edf1f2;
                overflow-wrap: anywhere;
            }
            .data-table tr.contact-row td::before {
                content: attr(data-label);
                flex: 0 0 82px;
                color: #78898d;
                font-size: 0.62rem;
                font-weight: 700;
                letter-spacing: 0.035em;
                text-transform: uppercase;
            }
            .data-table tr.contact-row td:nth-last-child(-n + 2) { border-bottom: 0; }
            .data-table tr.contact-row td:nth-child(3) .email-link {
                white-space: normal;
                overflow-wrap: anywhere;
                text-align: right;
            }
            .data-table tr.contact-row .subject-cell { max-width: none; text-align: right; white-space: normal; }
            .data-table tr.contact-row .contact-cell { min-width: 0; justify-content: flex-end; }
            .data-table tr.contact-row .company-cell { min-width: 0; text-align: right; }
            .data-table tr.contact-row .action-buttons { margin-left: auto; }
        }
        @media (max-width: 1280px) {
            .action-bar { left: 0; }
        }
        @media (max-width: 768px) {
            .page-content { padding: 76px 0 0 0; }
            .content-card { margin: 0 10px; padding: 16px 12px; border-radius: 14px; }
            .action-bar-inner { padding: 0 14px; flex-wrap: wrap; gap: 8px; height: auto; padding: 8px 14px; }
            .action-stats { flex-wrap: wrap; gap: 6px; }
            .stat-badge { padding: 4px 8px; font-size: 0.65rem; }
            .content-header { flex-direction: column; align-items: stretch; }
            .export-group { width: 100%; }
            .export-btn { flex: 1; justify-content: center; }
            .filters-bar { padding: 10px; }
            .filters-group { display: grid; grid-template-columns: 1fr 1fr; width: 100%; gap: 8px; }
            .filter-select { width: 100%; min-width: 0; }
            .filter-btn { justify-content: center; padding: 8px 10px; }
            .pagination-wrapper { overflow-x: auto; }
            .data-table tr.contact-row {
                grid-template-columns: minmax(0, 1fr);
                padding: 6px 12px;
            }
            .data-table tr.contact-row td:nth-last-child(2) { border-bottom: 1px solid #edf1f2; }
            .data-table tr.contact-row td::before { flex-basis: 92px; }
        }
    </style>

    @push('scripts')
    <script>
    function deleteContact(id) {
        Swal.fire({
            title: 'Confirmation',
            text: 'Voulez-vous vraiment supprimer ce message ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/contacts/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                }).then(response => response.json())
                  .then(data => {
                      if (data.success) {
                          Swal.fire('Supprimé!', 'Le message a été supprimé.', 'success');
                          location.reload();
                      }
                  }).catch(error => {
                      Swal.fire('Erreur!', 'Une erreur est survenue.', 'error');
                  });
            }
        });
    }

    // Export Excel
    document.getElementById('export-excel')?.addEventListener('click', function() {
        let table = document.getElementById('export-table');
        let html = table.outerHTML;
        let url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
        let link = document.createElement('a');
        link.download = 'contacts_' + new Date().toISOString().slice(0,19) + '.xls';
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
                let text = col.innerText.replace(/"/g, '""').replace(/\n/g, ' ');
                rowData.push('"' + text + '"');
            });
            csv.push(rowData.join(','));
        });
        let blob = new Blob([csv.join('\n')], { type: 'text/csv' });
        let link = document.createElement('a');
        link.download = 'contacts_' + new Date().toISOString().slice(0,19) + '.csv';
        link.href = URL.createObjectURL(blob);
        link.click();
    });
    </script>
    @endpush
</x-app-layout>
