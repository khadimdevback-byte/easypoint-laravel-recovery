<x-app-layout>
    {{-- ===================================================== --}}
    {{-- BARRE D'ACTIONS — collée sous la navbar               --}}
    {{-- ===================================================== --}}
    <div class="action-bar">
        <div class="action-bar-inner">
            <a href="{{ route('subscriptions.create') }}" id="add_button" class="action-btn">
                <i class="fas fa-plus"></i>
                <span>{{ __('Attribuer un Plan') }}</span>
            </a>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- CONTENU PRINCIPAL                                     --}}
    {{-- ===================================================== --}}
    <div class="page-content">
        <div class="content-card">
            <div class="content-header">
                <h1 class="page-title">{{ __('Gestion des Souscriptions Compagnies') }}</h1>
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

            {{-- 🌟 SYSTÈME D'ONGLETS / FILTRES DE STATUT --}}
            <div class="status-tabs">
                <a href="{{ route('subscriptions.index', ['status' => 'active']) }}" class="tab-item {{ $status === 'active' ? 'tab-item--active' : '' }}">
                    <i class="fas fa-check-circle"></i>
                    {{ __('Actives') }}
                    <span class="tab-count count--active">{{ $counts['active'] }}</span>
                </a>
                <a href="{{ route('subscriptions.index', ['status' => 'pending_payment']) }}" class="tab-item {{ $status === 'pending_payment' ? 'tab-item--active' : '' }}">
                    <i class="fas fa-clock"></i>
                    {{ __('En Attente') }}
                    <span class="tab-count count--pending">{{ $counts['pending_payment'] }}</span>
                </a>
                <a href="{{ route('subscriptions.index', ['status' => 'canceled']) }}" class="tab-item {{ $status === 'canceled' ? 'tab-item--active' : '' }}">
                    <i class="fas fa-ban"></i>
                    {{ __('Annulées') }}
                    <span class="tab-count count--canceled">{{ $counts['canceled'] }}</span>
                </a>
            </div>

            @if (session('success'))
                <div id="session-success-alert-div" class="alert alert--success">
                    {{ session('success') }}
                    <button onclick="document.getElementById('session-success-alert-div').style.display='none'" class="alert-close">&#10005;</button>
                </div>
            @endif

            @if (session('error'))
                <div id="session-error-alert-div" class="alert alert--error">
                    {{ session('error') }}
                    <button onclick="document.getElementById('session-error-alert-div').style.display='none'" class="alert-close">&#10005;</button>
                </div>
            @endif

            {{-- Tableau --}}
            <div class="table-wrapper">
                <table class="data-table" id="export-table">
                    <thead>
                        <tr>
                            <th>{{ __('Compagnie') }}</th>
                            <th>{{ __('Plan choisi') }}</th>
                            <th>{{ __('Durée (Mois)') }}</th>
                            <th>{{ __('Montant Total') }}</th>
                            <th>{{ __('Période') }}</th>
                            <th>{{ __('Statut') }}</th>
                            <th style="width: 130px; text-align: center;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscriptions as $subscription)
                            <tr>
                                <td>
                                    <span class="company-name">{{ $subscription->company->compagny_name }}</span>
                                </td>
                                <td>
                                    <span class="plan-badge">{{ $subscription->plan->name ?? __('Plan Supprimé') }}</span>
                                </td>
                                <td class="text-center">{{ $subscription->duration_months }}</td>
                                <td>
                                    <span class="amount">{{ number_format($subscription->total_amount, 0, ',', ' ') }} {{ $subscription->plan->currency ?? 'XOF' }}</span>
                                </td>
                                <td>
                                    <div class="date-range">
                                        @if($subscription->starts_at && $subscription->ends_at)
                                            <div><span class="date-label">Du :</span> {{ $subscription->starts_at->format('d/m/Y') }}</div>
                                            <div><span class="date-label">Au :</span> {{ $subscription->ends_at->format('d/m/Y') }}</div>
                                        @else
                                            <div class="text-orange-500 font-medium"><i class="fas fa-clock mr-1"></i> {{ __('En attente de paiement') }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($subscription->status === 'active')
                                        <span class="status-badge status-badge--active">{{ __('Actif') }}</span>
                                    @elseif($subscription->status === 'pending_payment')
                                        <span class="status-badge status-badge--pending">{{ __('En Attente') }}</span>
                                    @elseif($subscription->status === 'expired')
                                        <span class="status-badge status-badge--expired">{{ __('Expiré') }}</span>
                                    @else
                                        <span class="status-badge status-badge--cancelled">{{ __('Annulé') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        {{-- Bouton pour encaisser manuellement le paiement en espèces/chèque --}}
                                        @if($subscription->status === 'pending_payment')
                                            <form method="POST" action="{{ route('subscriptions.accept_manual', $subscription->id) }}" class="inline-form" onsubmit="return confirm('{{ __('Confirmez-vous avoir reçu le paiement en espèces ou chèque pour cette souscription ? Cela annulera ses autres souscriptions.') }}')">
                                                @csrf
                                                <button type="submit" class="action-btn-cash" title="{{ __('Valider le paiement reçu') }}">
                                                    <i class="fas fa-money-bill-wave"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- ⭐ BOUTON MODIFIER : Uniquement pour les souscriptions en attente de paiement --}}
                                        @if($subscription->status === 'pending_payment')
                                            <a href="{{ route('subscriptions.edit', $subscription->id) }}" class="action-btn-edit" title="{{ __('Modifier') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @else
                                            <span class="action-btn-edit disabled" title="{{ __('Modification non autorisée pour ce statut') }}">
                                                <i class="fas fa-edit"></i>
                                            </span>
                                        @endif

                                        {{-- Bouton Archiver --}}
                                        <form method="POST" action="{{ route('subscriptions.destroy', $subscription->id) }}" class="inline-form" onsubmit="return confirm('{{ __('Voulez-vous vraiment archiver cette souscription ?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn-delete" title="{{ __('Archiver') }}">
                                                <i class="fas fa-archive"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="empty-row">
                                    {{ __('Aucune souscription trouvée avec le statut :') }} <strong>{{ __($status) }}</strong>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                {{ $subscriptions->links() }}
            </div>
        </div>
    </div>

    <style>
        /* ============================================ */
        /* STYLES MODERNES POUR LA PAGE SUBSCRIPTIONS   */
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
            padding: 0 24px;
            height: 46px;
            gap: 4px;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: rgba(255,255,255,0.92);
            font-size: 0.82rem;
            font-weight: 500;
            padding: 6px 16px;
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.18s;
            white-space: nowrap;
            cursor: pointer;
        }

        .action-btn:hover { background: rgba(255,255,255,0.15); color: #fff; }
        .action-btn i { font-size: 0.78rem; opacity: 0.85; }

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

        /* 🌟 STYLES NOUVEAUX ONGLETS DE NAVIGATION */
        .status-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
            border-bottom: 1px solid var(--gl);
            padding-bottom: 8px;
        }
        .tab-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--gm);
            text-decoration: none;
            border-radius: 8px;
            transition: var(--t);
        }
        .tab-item:hover {
            background: #f3f4f6;
            color: var(--gf);
        }
        .tab-item--active {
            background: var(--vc);
            color: var(--vp);
        }
        .tab-count {
            font-size: 0.72rem;
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: 700;
        }
        .count--active { background: #dcfce7; color: #166534; }
        .count--pending { background: #ffedd5; color: #c2410c; }
        .count--canceled { background: #fee2e2; color: #991b1b; }

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

        /* Alertes */
        .alert {
            position: relative;
            padding: 12px 44px 12px 16px;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 16px;
        }
        .alert--success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert--error   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-close {
            position: absolute;
            top: 10px;
            right: 12px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 0.8rem;
            opacity: 0.6;
        }

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
            padding: 11px 16px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #6b7280;
            text-align: left;
            white-space: nowrap;
        }
        .data-table td {
            padding: 10px 16px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
            white-space: nowrap;
        }
        .data-table tbody tr:last-child td { border-bottom: none; }
        .data-table tbody tr:hover { background: #f9fafb; }

        .company-name {
            font-weight: 600;
            color: #1f2937;
        }

        .plan-badge {
            display: inline-block;
            background: #e8f5f1;
            color: #2e7d64;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .amount {
            font-weight: 700;
            color: #1f2937;
        }

        .date-range {
            font-size: 0.75rem;
            line-height: 1.5;
        }
        .date-label {
            color: #9ca3af;
            font-size: 0.7rem;
        }
        .text-orange-500 { color: #f97316; }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .status-badge--active { background: #dcfce7; color: #166534; }
        .status-badge--pending { background: #ffedd5; color: #c2410c; }
        .status-badge--expired { background: #fef3c7; color: #92400e; }
        .status-badge--cancelled { background: #fee2e2; color: #991b1b; }

        .action-buttons {
            display: flex;
            gap: 6px;
            justify-content: flex-start;
            align-items: center;
        }

        .action-btn-edit, .action-btn-delete, .action-btn-cash {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            transition: var(--t);
            cursor: pointer;
            border: none;
            background: none;
            font-size: 0.85rem;
        }

        /* ⭐ Style bouton Modifier désactivé */
        .action-btn-edit.disabled {
            opacity: 0.4;
            cursor: not-allowed;
            background: #f3f4f6;
            color: #9ca3af;
            pointer-events: none;
        }
        .action-btn-edit.disabled:hover {
            transform: none;
            background: #f3f4f6;
            color: #9ca3af;
        }

        /* Style bouton cash */
        .action-btn-cash {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }
        .action-btn-cash:hover {
            background: #059669;
            color: white;
            transform: translateY(-2px);
        }

        .action-btn-edit {
            background: var(--gtc);
            color: var(--vp);
        }
        .action-btn-edit:hover {
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

        .inline-form {
            display: inline-block;
            margin: 0;
        }

        .empty-row { text-align: center; padding: 40px 16px; color: #9ca3af; font-size: 0.85rem; }
        .pagination-wrapper { display: flex; justify-content: flex-end; margin-top: 20px; }

        @media (max-width: 1280px) { .action-bar { left: 0; } }
        @media (max-width: 768px) {
            .page-content { padding: 46px 0 0 0; }
            .content-card { margin: 0 16px; padding: 16px; }
            .action-bar-inner { padding: 0 14px; overflow-x: auto; }
            .content-header { flex-direction: column; align-items: flex-start; }
            .action-buttons { flex-direction: row; gap: 4px; }
            .data-table td, .data-table th { padding: 8px 12px; }
            .status-tabs { overflow-x: auto; padding-bottom: 4px; }
        }
    </style>

    <script>
        // Les scripts d'exports (Excel, CSV, PDF) restent identiques et fonctionnels...
        document.getElementById('export-excel')?.addEventListener('click', function() {
            let table = document.getElementById('export-table');
            let html = table.outerHTML;
            let url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
            let link = document.createElement('a');
            link.download = 'subscriptions_' + new Date().toISOString().slice(0,19) + '.xls';
            link.href = url;
            link.click();
        });

        document.getElementById('export-csv')?.addEventListener('click', function() {
            let rows = document.querySelectorAll('#export-table tr');
            let csv = [];
            rows.forEach(row => {
                let cols = row.querySelectorAll('td, th');
                let rowData = [];
                cols.forEach(col => {
                    rowData.push('"' + col.innerText.replace(/"/g, '""') + '"');
                });
                csv.push(rowData.join(','));
            });
            let blob = new Blob([csv.join('\n')], { type: 'text/csv' });
            let link = document.createElement('a');
            link.download = 'subscriptions_' + new Date().toISOString().slice(0,19) + '.csv';
            link.href = URL.createObjectURL(blob);
            link.click();
        });

        document.getElementById('export-pdf')?.addEventListener('click', function() {
            let printWindow = window.open('', '_blank', 'width=800,height=600');
            let tableHtml = document.getElementById('export-table').outerHTML;
            let title = document.querySelector('.page-title')?.innerText || 'Liste des souscriptions';

            printWindow.document.write(`
                <html>
                <head>
                    <title>${title}</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        h1 { color: #2e7d64; font-size: 18px; margin-bottom: 20px; }
                        table { width: 100%; border-collapse: collapse; }
                        th { background: #f3f4f6; padding: 8px; border: 1px solid #ddd; text-align: left; }
                        td { padding: 8px; border: 1px solid #ddd; }
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
    </script>
</x-app-layout>
