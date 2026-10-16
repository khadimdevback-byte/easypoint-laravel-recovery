<x-app-layout>
    {{-- ===================================================== --}}
    {{-- BARRE D'ACTIONS — collée sous la navbar               --}}
    {{-- ===================================================== --}}
    <div class="action-bar">
        <div class="action-bar-inner">
            <div class="action-info">
                <i class="fas fa-headset"></i>
                <span>{{ __('Support client') }}</span>
            </div>
            <div class="action-stats">
                <div class="stat-badge">
                    <i class="fas fa-clock"></i>
                    <span>En attente: {{ $messages->where('status', 'en_attente')->count() }}</span>
                </div>
                <div class="stat-badge">
                    <i class="fas fa-spinner"></i>
                    <span>En cours: {{ $messages->where('status', 'en_cours')->count() }}</span>
                </div>
                <div class="stat-badge">
                    <i class="fas fa-check-circle"></i>
                    <span>Résolus: {{ $messages->where('status', 'resolu')->count() }}</span>
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
                    <h1 class="page-title">{{ __('Support client') }}</h1>
                    <p class="page-subtitle">{{ __('Gérer les demandes d\'assistance des entreprises') }}</p>
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

            {{-- Tableau --}}
            <div class="table-wrapper">
                <table class="data-table" id="export-table">
                    <thead>
                        <tr>
                            <th>{{ __('Entreprise') }}</th>
                            <th>{{ __('Sujet') }}</th>
                            <th>{{ __('Catégorie') }}</th>
                            <th>{{ __('Statut') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th style="width: 100px;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($messages as $message)
                            <tr>
                                <td>
                                    <div class="company-cell">
                                        <div class="company-avatar">
                                            <div class="avatar-placeholder">
                                                {{ strtoupper(substr($message->company->compagny_name ?? 'C', 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="company-info">
                                            <div class="company-name">{{ $message->company->compagny_name ?? 'N/A' }}</div>
                                            <div class="company-email">{{ $message->company->compagny_email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="subject-cell">
                                        <div class="subject-title">{{ $message->subject }}</div>
                                        <div class="subject-preview">{{ Str::limit($message->message, 50) }}</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="category-badge
                                        @if($message->category == 'question') category-question
                                        @elseif($message->category == 'bug') category-bug
                                        @elseif($message->category == 'facturation') category-facturation
                                        @elseif($message->category == 'renouvellement') category-renouvellement
                                        @else category-other @endif">
                                        @if($message->category == 'question') <i class="fas fa-question-circle"></i> Question
                                        @elseif($message->category == 'bug') <i class="fas fa-bug"></i> Problème technique
                                        @elseif($message->category == 'facturation') <i class="fas fa-file-invoice"></i> Facturation
                                        @elseif($message->category == 'renouvellement') <i class="fas fa-sync-alt"></i> Renouvellement
                                        @else <i class="fas fa-comment"></i> Autre
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    @if($message->status == 'en_attente')
                                        <span class="status-badge status-waiting"><i class="fas fa-clock"></i> En attente</span>
                                    @elseif($message->status == 'en_cours')
                                        <span class="status-badge status-progress"><i class="fas fa-spinner"></i> En cours</span>
                                    @elseif($message->status == 'resolu')
                                        <span class="status-badge status-resolved"><i class="fas fa-check-circle"></i> Résolu</span>
                                    @else
                                        <span class="status-badge status-closed"><i class="fas fa-archive"></i> Fermé</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="date-text">{{ $message->created_at->format('d/m/Y H:i') }}</span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('superadmin.support.show', $message) }}" class="action-btn-reply" title="Voir et répondre">
                                            <i class="fas fa-reply"></i>
                                        </a>
                                        @if($message->replies()->count() == 0)
                                            <form action="{{ route('superadmin.support.destroy', $message) }}" method="POST" class="inline-form" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce message ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn-delete" title="Supprimer">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="action-btn-disabled" title="Ce message a des réponses, vous ne pouvez pas le supprimer">
                                                <i class="fas fa-trash-alt"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-row">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                    <p>Aucun message de support</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($messages->hasPages())
                <div class="pagination-wrapper">
                    {{ $messages->links() }}
                </div>
            @endif
        </div>
    </div>

    <style>
        /* ============================================ */
        /* STYLES MODERNES POUR LA PAGE SUPPORT         */
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
        .avatar-placeholder {
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

        /* Subject cell */
        .subject-cell {
            max-width: 300px;
        }
        .subject-title {
            font-weight: 600;
            color: #1f2937;
            font-size: 0.85rem;
            margin-bottom: 4px;
        }
        .subject-preview {
            font-size: 0.7rem;
            color: #6b7280;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Category badges */
        .category-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 500;
        }
        .category-question { background: #e0f2fe; color: #0369a1; }
        .category-bug { background: #fee2e2; color: #b91c1c; }
        .category-facturation { background: #fef3c7; color: #92400e; }
        .category-renouvellement { background: #dcfce7; color: #166534; }
        .category-other { background: #f3e8ff; color: #6b21a5; }

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
        .status-waiting { background: #fef3c7; color: #92400e; }
        .status-progress { background: #e0f2fe; color: #0369a1; }
        .status-resolved { background: #dcfce7; color: #166534; }
        .status-closed { background: #f3f4f6; color: #4b5563; }

        .date-text {
            font-size: 0.75rem;
            color: #6b7280;
            white-space: nowrap;
        }

        /* Action buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        .action-btn-reply, .action-btn-delete {
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
        .action-btn-reply {
            background: var(--gtc);
            color: var(--vp);
        }
        .action-btn-reply:hover {
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
        .action-btn-disabled {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            background: #f3f4f6;
            color: #9ca3af;
            cursor: not-allowed;
        }
        .inline-form {
            display: inline-block;
        }

        .empty-row {
            text-align: center;
            padding: 60px 16px;
            color: #9ca3af;
        }
        .pagination-wrapper {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #f3f4f6;
        }

        @media (max-width: 1280px) { .action-bar { left: 0; } }
        @media (max-width: 768px) {
            .page-content { padding: 46px 0 0 0; }
            .content-card { margin: 0 16px; padding: 16px; }
            .action-bar-inner { padding: 0 14px; flex-wrap: wrap; gap: 8px; height: auto; padding: 8px 14px; }
            .action-stats { flex-wrap: wrap; }
            .content-header { flex-direction: column; align-items: flex-start; }
            .company-cell { min-width: 180px; }
            .subject-cell { min-width: 200px; }
        }
    </style>

    <script>
        // Export Excel
        document.getElementById('export-excel')?.addEventListener('click', function() {
            let table = document.getElementById('export-table');
            let html = table.outerHTML;
            let url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
            let link = document.createElement('a');
            link.download = 'support_messages_' + new Date().toISOString().slice(0,19) + '.xls';
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
            link.download = 'support_messages_' + new Date().toISOString().slice(0,19) + '.csv';
            link.href = URL.createObjectURL(blob);
            link.click();
        });

        // Export PDF
        document.getElementById('export-pdf')?.addEventListener('click', function() {
            let printWindow = window.open('', '_blank', 'width=1200,height=800');
            let tableHtml = document.getElementById('export-table').outerHTML;
            let title = document.querySelector('.page-title')?.innerText || 'Messages de support';

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
                        .status-waiting, .status-progress, .status-resolved, .status-closed { display: inline-block; padding: 2px 8px; border-radius: 20px; }
                        .status-waiting { background: #fef3c7; color: #92400e; }
                        .status-progress { background: #e0f2fe; color: #0369a1; }
                        .status-resolved { background: #dcfce7; color: #166534; }
                        .status-closed { background: #f3f4f6; color: #4b5563; }
                        .category-question, .category-bug, .category-facturation, .category-renouvellement, .category-other { display: inline-block; padding: 2px 8px; border-radius: 20px; }
                        .category-question { background: #e0f2fe; }
                        .category-bug { background: #fee2e2; }
                        .category-facturation { background: #fef3c7; }
                        .category-renouvellement { background: #dcfce7; }
                        .category-other { background: #f3e8ff; }
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
