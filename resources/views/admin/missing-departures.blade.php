{{-- resources/views/admin/missing-departures.blade.php --}}
<x-app-layout>
    {{-- Barre d'actions --}}
    <div class="action-bar">
        <div class="action-bar-inner">
            <div class="action-info">
                <i class="fas fa-clock"></i>
                <span>{{ __('Demandes de validation - Départs manquants') }}</span>
            </div>
            <div class="action-stats">
                <span class="stat-badge">
                    <i class="fas fa-hourglass-half"></i>
                    {{ $pendingRequests->total() }} en attente
                </span>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="content-card">
            {{-- En-tête --}}
            <div class="content-header">
                <div>
                    <h1 class="page-title">Demandes de validation - Départs manquants</h1>
                    <p class="page-subtitle">Gérez les demandes des employés pour les heures de départ non enregistrées</p>
                </div>
            </div>

            {{-- Messages --}}
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>{{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" class="alert-close">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span>{{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()" class="alert-close">&times;</button>
                </div>
            @endif

            {{-- Cartes statistiques --}}
            <div class="stats-grid">
                <div class="stat-card stat-card-warning">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <p class="stat-label">En attente</p>
                        <p class="stat-value">{{ $pendingRequests->total() }}</p>
                    </div>
                </div>

                <div class="stat-card stat-card-success">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <p class="stat-label">Validés récemment</p>
                        <p class="stat-value">{{ $approvedRequests->count() }}</p>
                    </div>
                </div>

                <div class="stat-card stat-card-info">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-info">
                        <p class="stat-label">Total traitées</p>
                        <p class="stat-value">{{ $pendingRequests->total() + $approvedRequests->count() }}</p>
                    </div>
                </div>
            </div>

            {{-- Tableau des demandes en attente --}}
            <div class="table-container">
                <div class="table-header">
                    <h3 class="table-title">Demandes en attente</h3>
                    <p class="table-subtitle">Liste des demandes de validation de départ manquant</p>
                </div>

                @if($pendingRequests->count() > 0)
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Employé</th>
                                    <th>Date</th>
                                    <th>Heure prévue</th>
                                    <th>Statut</th>
                                    <th style="width: 180px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingRequests as $request)
                                <tr>
                                    <td>
                                        <div class="employee-cell">
                                            <div class="employee-avatar">
                                                @if($request->user->avatar && Storage::disk('public')->exists($request->user->avatar))
                                                    <img src="{{ Storage::url($request->user->avatar) }}" alt="Avatar">
                                                @else
                                                    <div class="avatar-placeholder">
                                                        {{ strtoupper(substr($request->user->first_name, 0, 1)) }}{{ strtoupper(substr($request->user->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="employee-details">
                                                <span class="employee-name">{{ $request->user->first_name }} {{ $request->user->name }}</span>
                                                <span class="employee-matricule">Matricule: {{ $request->user->matricule }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <tr>
                                        <div class="date-cell">
                                            <span class="date-day">{{ $request->date->format('d/m/Y') }}</span>
                                            <span class="date-weekday">{{ $request->date->translatedFormat('l') }}</span>
                                        </div>
                                    </div>
                                    <td>
                                        <span class="time-cell">{{ $request->expected_depart_time ? \Carbon\Carbon::parse($request->expected_depart_time)->format('H:i') : '17:00' }}</span>
                                    </div>
                                    <td>
                                        <span class="status-badge status-pending">
                                            <i class="fas fa-hourglass-half"></i> En attente
                                        </span>
                                    </div>
                                    <td>
                                        <div class="action-buttons">
                                            <button onclick="openApproveModal({{ $request->id }})" class="btn-approve">
                                                <i class="fas fa-check"></i> Valider
                                            </button>
                                            <button onclick="openRejectModal({{ $request->id }})" class="btn-reject">
                                                <i class="fas fa-times"></i> Rejeter
                                            </button>
                                        </div>
                                        <form id="approve-form-{{ $request->id }}" action="{{ route('admin.missing-departures.approve', $request->id) }}" method="POST" class="hidden">
                                            @csrf
                                            <input type="hidden" name="comment" id="approve-comment-{{ $request->id }}">
                                        </form>
                                        <form id="reject-form-{{ $request->id }}" action="{{ route('admin.missing-departures.reject', $request->id) }}" method="POST" class="hidden">
                                            @csrf
                                            <input type="hidden" name="comment" id="reject-comment-{{ $request->id }}">
                                        </form>
                                    </div>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination-wrapper">
                        {{ $pendingRequests->links() }}
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h3 class="empty-title">Aucune demande en attente</h3>
                        <p class="empty-subtitle">Toutes les demandes ont été traitées</p>
                    </div>
                @endif
            </div>

            {{-- Historique des validations --}}
            @if($approvedRequests->count() > 0)
            <div class="table-container mt-6">
                <div class="table-header">
                    <h3 class="table-title">Historique des validations</h3>
                    <p class="table-subtitle">Dernières demandes traitées</p>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Employé</th>
                                <th>Date</th>
                                <th>Validé par</th>
                                <th>Date validation</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($approvedRequests as $request)
                            <tr>
                                <td>
                                    <div class="employee-cell employee-cell-sm">
                                        <div class="employee-avatar">
                                            @if($request->user->avatar && Storage::disk('public')->exists($request->user->avatar))
                                                <img src="{{ Storage::url($request->user->avatar) }}" alt="Avatar">
                                            @else
                                                <div class="avatar-placeholder avatar-placeholder-sm">
                                                    {{ strtoupper(substr($request->user->first_name, 0, 1)) }}{{ strtoupper(substr($request->user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <span class="employee-name">{{ $request->user->first_name }} {{ $request->user->name }}</span>
                                    </div>
                                </div>
                                <td>
                                    <span class="date-cell">{{ $request->date->format('d/m/Y') }}</span>
                                </div>
                                <td>
                                    <span class="approver-name">{{ $request->approver->first_name ?? 'Admin' }} {{ $request->approver->name ?? '' }}</span>
                                </div>
                                <td>
                                    <span class="approved-date">{{ $request->approved_at ? $request->approved_at->format('d/m/Y H:i') : '-' }}</span>
                                </div>
                            </tr>
                            @endforeach
                        </tbody>
                    <table>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Modal Validation --}}
    <div id="approve-modal" class="modal-overlay hidden">
        <div class="modal-container">
            <div class="modal-card">
                <div class="modal-header">
                    <h3>Valider la demande</h3>
                    <button onclick="closeApproveModal()" class="modal-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="modal-text">Ajoutez un commentaire (optionnel) :</p>
                    <textarea id="approve-modal-comment" class="modal-textarea" rows="3" placeholder="Commentaire..."></textarea>
                </div>
                <div class="modal-footer">
                    <button onclick="closeApproveModal()" class="modal-btn-cancel">Annuler</button>
                    <button onclick="submitApprove()" class="modal-btn-approve">
                        <i class="fas fa-check mr-1"></i> Valider
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Rejet --}}
    <div id="reject-modal" class="modal-overlay hidden">
        <div class="modal-container">
            <div class="modal-card">
                <div class="modal-header">
                    <h3>Rejeter la demande</h3>
                    <button onclick="closeRejectModal()" class="modal-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="modal-text">Motif du rejet :</p>
                    <textarea id="reject-modal-comment" class="modal-textarea" rows="3" placeholder="Motif du rejet..." required></textarea>
                </div>
                <div class="modal-footer">
                    <button onclick="closeRejectModal()" class="modal-btn-cancel">Annuler</button>
                    <button onclick="submitReject()" class="modal-btn-reject">
                        <i class="fas fa-times mr-1"></i> Rejeter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* ============================================ */
        /* STYLES MODERNES POUR LA PAGE MISSING DEPARTURES */
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
        .content-card { background: #fff; border-radius: 20px; border: 1px solid var(--gl); padding: 24px; margin: 0 24px; }
        .content-header { margin-bottom: 24px; }
        .page-title { font-size: 1.35rem; font-weight: 700; color: #1f2937; margin: 0; }
        .page-subtitle { font-size: 0.8rem; color: #6b7280; margin-top: 4px; }

        /* Alertes */
        .alert-success, .alert-error { display: flex; align-items: center; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-close { margin-left: auto; background: none; border: none; font-size: 1.2rem; cursor: pointer; opacity: 0.6; }

        /* Stats grid */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 28px; }
        .stat-card { display: flex; align-items: center; gap: 16px; padding: 20px; border-radius: 16px; background: #fff; border: 1px solid var(--gl); transition: var(--t); }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
        .stat-card-warning { border-left: 4px solid #f59e0b; }
        .stat-card-success { border-left: 4px solid #10b981; }
        .stat-card-info { border-left: 4px solid #3b82f6; }
        .stat-icon { width: 48px; height: 48px; border-radius: 24px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        .stat-card-warning .stat-icon { background: #fef3c7; color: #f59e0b; }
        .stat-card-success .stat-icon { background: #dcfce7; color: #10b981; }
        .stat-card-info .stat-icon { background: #e0f2fe; color: #3b82f6; }
        .stat-info { flex: 1; }
        .stat-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; color: var(--gm); margin-bottom: 4px; }
        .stat-value { font-size: 1.8rem; font-weight: 800; color: var(--gf); font-family: 'Syne', sans-serif; }

        /* Table container */
        .table-container { background: #fff; border-radius: 16px; border: 1px solid var(--gl); overflow: hidden; }
        .table-header { padding: 16px 20px; border-bottom: 1px solid var(--gl); }
        .table-title { font-size: 1rem; font-weight: 700; color: var(--gf); margin: 0; }
        .table-subtitle { font-size: 0.7rem; color: var(--gm); margin-top: 2px; }

        /* Tableau */
        .table-wrapper { overflow-x: auto; }
        .data-table { width: 100%; border-collapse: collapse; font-size: 0.8rem; min-width: 800px; }
        .data-table th { padding: 12px 16px; background: var(--gtc); font-weight: 600; color: var(--gf); border-bottom: 1px solid var(--gl); text-align: left; }
        .data-table td { padding: 12px 16px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
        .data-table tr:hover td { background: #f9fafb; }

        /* Employee cell */
        .employee-cell { display: flex; align-items: center; gap: 12px; }
        .employee-cell-sm { gap: 8px; }
        .employee-avatar { flex-shrink: 0; }
        .employee-avatar img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
        .avatar-placeholder { width: 40px; height: 40px; border-radius: 50%; background: var(--vp); color: white; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: bold; }
        .avatar-placeholder-sm { width: 32px; height: 32px; font-size: 12px; }
        .employee-details { display: flex; flex-direction: column; }
        .employee-name { font-weight: 600; color: var(--gf); }
        .employee-matricule { font-size: 0.65rem; color: var(--gm); }

        /* Date cell */
        .date-cell { display: flex; flex-direction: column; }
        .date-day { font-weight: 600; font-size: 0.85rem; }
        .date-weekday { font-size: 0.65rem; color: var(--gm); }

        .time-cell { font-weight: 600; }

        /* Status badge */
        .status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; }
        .status-pending { background: #fef3c7; color: #92400e; }

        /* Action buttons */
        .action-buttons { display: flex; gap: 8px; }
        .btn-approve, .btn-reject { display: inline-flex; align-items: center; gap: 4px; padding: 6px 14px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; cursor: pointer; transition: var(--t); border: none; }
        .btn-approve { background: #10b981; color: white; }
        .btn-approve:hover { background: #059669; transform: translateY(-1px); }
        .btn-reject { background: #ef4444; color: white; }
        .btn-reject:hover { background: #dc2626; transform: translateY(-1px); }

        /* Pagination */
        .pagination-wrapper { padding: 16px; border-top: 1px solid var(--gl); display: flex; justify-content: flex-end; }

        /* Empty state */
        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-icon { width: 80px; height: 80px; background: var(--gtc); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
        .empty-icon i { font-size: 2rem; color: var(--gm); }
        .empty-title { font-size: 1rem; font-weight: 600; color: var(--gf); margin-bottom: 4px; }
        .empty-subtitle { font-size: 0.75rem; color: var(--gm); }

        /* Modal */
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 10000; display: flex; align-items: center; justify-content: center; }
        .modal-overlay.hidden { display: none; }
        .modal-container { width: 100%; max-width: 500px; margin: 20px; }
        .modal-card { background: #fff; border-radius: 20px; border: 1px solid var(--gl); overflow: hidden; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-bottom: 1px solid var(--gl); }
        .modal-header h3 { font-size: 1rem; font-weight: 700; color: var(--gf); margin: 0; }
        .modal-close { background: none; border: none; font-size: 1.2rem; cursor: pointer; color: var(--gm); transition: var(--t); }
        .modal-close:hover { color: var(--gf); }
        .modal-body { padding: 20px; }
        .modal-text { font-size: 0.8rem; color: var(--gf); margin-bottom: 12px; }
        .modal-textarea { width: 100%; padding: 10px 14px; border: 1px solid var(--gl); border-radius: 12px; font-size: 0.8rem; resize: vertical; transition: var(--t); }
        .modal-textarea:focus { outline: none; border-color: var(--vp); box-shadow: 0 0 0 3px rgba(46,125,100,0.1); }
        .modal-footer { display: flex; justify-content: flex-end; gap: 12px; padding: 16px 20px; border-top: 1px solid var(--gl); }
        .modal-btn-cancel { padding: 8px 20px; background: var(--gtc); border: 1px solid var(--gl); border-radius: 40px; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: var(--t); }
        .modal-btn-cancel:hover { background: var(--gl); }
        .modal-btn-approve { padding: 8px 20px; background: #10b981; border: none; border-radius: 40px; font-size: 0.75rem; font-weight: 600; color: white; cursor: pointer; transition: var(--t); }
        .modal-btn-approve:hover { background: #059669; transform: translateY(-1px); }
        .modal-btn-reject { padding: 8px 20px; background: #ef4444; border: none; border-radius: 40px; font-size: 0.75rem; font-weight: 600; color: white; cursor: pointer; transition: var(--t); }
        .modal-btn-reject:hover { background: #dc2626; transform: translateY(-1px); }

        /* Utilities */
        .mt-6 { margin-top: 24px; }
        .hidden { display: none; }

        @media (max-width: 1280px) { .action-bar { left: 0; } }
        @media (max-width: 768px) {
            .page-content { padding: 46px 0 0 0; }
            .content-card { margin: 0 16px; padding: 16px; }
            .action-bar-inner { padding: 0 14px; }
            .stats-grid { grid-template-columns: 1fr; gap: 12px; }
            .action-buttons { flex-direction: column; }
        }
    </style>

    <script>
        let currentRequestId = null;

        function openApproveModal(id) {
            currentRequestId = id;
            document.getElementById('approve-modal').classList.remove('hidden');
        }

        function closeApproveModal() {
            document.getElementById('approve-modal').classList.add('hidden');
            document.getElementById('approve-modal-comment').value = '';
        }

        function submitApprove() {
            const comment = document.getElementById('approve-modal-comment').value;
            document.getElementById('approve-comment-' + currentRequestId).value = comment;
            document.getElementById('approve-form-' + currentRequestId).submit();
        }

        function openRejectModal(id) {
            currentRequestId = id;
            document.getElementById('reject-modal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('reject-modal').classList.add('hidden');
            document.getElementById('reject-modal-comment').value = '';
        }

        function submitReject() {
            const comment = document.getElementById('reject-modal-comment').value;
            if (!comment.trim()) {
                Swal.fire('Erreur', 'Veuillez indiquer un motif de rejet', 'error');
                return;
            }
            document.getElementById('reject-comment-' + currentRequestId).value = comment;
            document.getElementById('reject-form-' + currentRequestId).submit();
        }
    </script>
</x-app-layout>
