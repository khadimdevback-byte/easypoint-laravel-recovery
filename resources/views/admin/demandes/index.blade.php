<x-app-layout>
    {{-- Barre d'actions --}}
    <div class="action-bar">
        <div class="action-bar-inner">
            <div class="action-info">
                <i class="fas fa-calendar-alt"></i>
                <span>{{ __('Gestion des Demandes') }}</span>
            </div>
            <div class="action-stats">
                <span class="stat-badge">
                    <i class="fas fa-clock"></i>
                    En attente: {{ $demandes->where('statut', 'en_attente')->count() }}
                </span>
                <span class="stat-badge">
                    <i class="fas fa-check-circle"></i>
                    Approuvées: {{ $demandes->where('statut', 'approuve')->count() }}
                </span>
                <span class="stat-badge">
                    <i class="fas fa-times-circle"></i>
                    Rejetées: {{ $demandes->where('statut', 'rejete')->count() }}
                </span>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="content-card">
            <div class="content-header">
                <div>
                    <h1 class="page-title">{{ __('Gestion des Demandes (Congés / Permissions)') }}</h1>
                    <p class="page-subtitle">{{ __('Gérez les demandes de congés et permissions des employés') }}</p>
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

            @if (session('success'))
                <div id="session-success-alert-div" class="alert alert--success">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                    <button onclick="document.getElementById('session-success-alert-div').style.display='none'" class="alert-close">&#10005;</button>
                </div>
            @endif

            @if (session('error'))
                <div id="session-error-alert-div" class="alert alert--error">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                    <button onclick="document.getElementById('session-error-alert-div').style.display='none'" class="alert-close">&#10005;</button>
                </div>
            @endif

            <div class="selection-bar">
                <div class="selection-actions">
                    <label class="select-all-label">
                        <input type="checkbox" id="checkbox-all" class="select-all-checkbox">
                        <span class="select-all-text">Sélectionner tout</span>
                    </label>
                    @if (is_admin())
                        <a href="{{ route('demandes.ajouter') }}" id="add_button" class="action-btn-primary">
                            <i class="fas fa-plus"></i>
                            <span>{{ __('Nouvelle Demande') }}</span>
                        </a>
                        <button id="delete_button" class="action-btn-danger">
                            <i class="fas fa-trash-alt"></i>
                            <span>{{ __('Supprimer') }}</span>
                        </button>
                        <button id="edit_button" class="action-btn-warning">
                            <i class="fas fa-edit"></i>
                            <span>{{ __('Traiter / Modifier') }}</span>
                        </button>
                    @endif
                </div>
            </div>

            <div class="table-container">
                <div class="table-wrapper">
                    <table class="data-table" id="export-table">
                        <thead>
                            <tr>
                                <th style="width: 40px;"><input type="checkbox" id="selectAllCheckbox" class="row-checkbox"></th>
                                <th>{{ __('Employé') }}</th>
                                <th>{{ __('Catégorie') }}</th>
                                <th>{{ __('Date Début') }}</th>
                                <th>{{ __('Date Fin') }}</th>
                                <th>{{ __('Motif') }}</th>
                                <th>{{ __('Statut') }}</th>
                                <th style="width: 80px;">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($demandes as $demande)
                                <tr class="demande-row">
                                    <td>
                                        <input type="checkbox" name="demande_id" value="{{ $demande->id }}"
                                               class="row-checkbox checkbox-item"
                                               data-demande-id="{{ $demande->id }}">
                                    </td>
                                    <td>
                                        <div class="employee-cell">
                                            <div class="employee-avatar">
                                                @if($demande->employe && $demande->employe->avatar && Storage::disk('public')->exists($demande->employe->avatar))
                                                    <img src="{{ asset('storage/' . $demande->employe->avatar) }}" alt="Avatar">
                                                @else
                                                    <div class="avatar-placeholder">
                                                        {{ strtoupper(substr($demande->employe->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($demande->employe->name ?? 'U', 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="employee-info">
                                                <span class="employee-name">{{ $demande->employe->first_name ?? '' }} {{ $demande->employe->name ?? 'Employé inconnu' }}</span>
                                                <span class="employee-matricule">Matricule: {{ $demande->employe->matricule ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="category-badge category-{{ $demande->categorie }}">
                                            <i class="fas {{ $demande->categorie == 'conge' ? 'fa-umbrella-beach' : 'fa-clock' }}"></i>
                                            {{ $demande->categorie == 'conge' ? 'Congé' : 'Permission' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="date-cell">
                                            <span class="date-day">{{ \Carbon\Carbon::parse($demande->date_debut)->format('d/m/Y') }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="date-cell">
                                            <span class="date-day">{{ \Carbon\Carbon::parse($demande->date_fin)->format('d/m/Y') }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="motif-cell" title="{{ $demande->motif }}">
                                            {{ Str::limit($demande->motif ?? '-', 40) }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($demande->statut === 'en_attente')
                                            <span class="status-badge status-pending">
                                                <i class="fas fa-clock"></i> En attente
                                            </span>
                                        @elseif($demande->statut === 'approuve')
                                            <span class="status-badge status-approved">
                                                <i class="fas fa-check-circle"></i> Approuvée
                                            </span>
                                        @else
                                            <span class="status-badge status-rejected">
                                                <i class="fas fa-times-circle"></i> Rejetée
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown-container">
                                            <button class="dots-btn" onclick="toggleDropdown(this, event)">
                                                <i class="fa-solid fa-ellipsis"></i>
                                            </button>
                                            <div class="dropdown-menu-custom">
                                                <a href="{{ route('demandes.edit', $demande->id) }}" class="dropdown-item-custom">
                                                    <i class="fas fa-edit"></i> {{ __('Modifier') }}
                                                </a>
                                                <button onclick="confirmDelete({{ $demande->id }})" class="dropdown-item-custom" style="color: #dc2626;">
                                                    <i class="fas fa-trash-alt"></i> {{ __('Supprimer') }}
                                                </button>
                                            </div>
                                        </div>
                                        <form id="delete-form-{{ $demande->id }}" action="{{ route('demandes.delete', $demande->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="empty-row">
                                        <i class="fas fa-calendar-times fa-3x mb-3 opacity-50"></i>
                                        <p>{{ __('Aucune demande trouvée pour le moment.') }}</p>
                                        <span class="empty-sub">{{ __('Les demandes des employés apparaîtront ici') }}</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($demandes->hasPages())
                    <div class="pagination-wrapper">
                        {{ $demandes->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        /* ============================================ */
        /* STYLES MODERNES POUR LA PAGE DEMANDES        */
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
        .action-stats { display: flex; gap: 12px; }
        .stat-badge { background: rgba(255,255,255,0.15); padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; color: white; display: inline-flex; align-items: center; gap: 6px; }

        /* Page content */
        .page-content { padding-top: 46px; }
        .content-card { background: #fff; border-radius: 20px; border: 1px solid var(--gl); padding: 24px; margin: 0 24px; }
        .content-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 20px; }
        .page-title { font-size: 1.35rem; font-weight: 700; color: #1f2937; margin: 0; }
        .page-subtitle { font-size: 0.8rem; color: #6b7280; margin-top: 4px; }

        /* Alertes */
        .alert { display: flex; align-items: center; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; position: relative; }
        .alert--success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert--error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-close { margin-left: auto; background: none; border: none; font-size: 1rem; cursor: pointer; opacity: 0.6; }

        /* Export buttons */
        .export-group { display: flex; gap: 8px; flex-wrap: wrap; }
        .export-btn { display: inline-flex; align-items: center; gap: 6px; font-size: 0.75rem; font-weight: 600; padding: 6px 14px; border-radius: 7px; border: none; cursor: pointer; transition: var(--t); color: #fff; }
        .export-btn:hover { filter: brightness(1.1); transform: translateY(-1px); }
        .export-btn--excel { background: #16a34a; }
        .export-btn--csv   { background: #2563eb; }
        .export-btn--pdf   { background: #dc2626; }

        /* Selection bar */
        .selection-bar { margin-bottom: 20px; padding: 12px 16px; background: var(--gtc); border-radius: 16px; }
        .selection-actions { display: flex; align-items: center; flex-wrap: wrap; gap: 16px; }
        .select-all-label { display: flex; align-items: center; gap: 8px; cursor: pointer; }
        .select-all-checkbox { width: 16px; height: 16px; accent-color: var(--vp); }
        .select-all-text { font-size: 0.8rem; font-weight: 500; color: var(--gf); }
        .action-btn-primary, .action-btn-danger, .action-btn-warning { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 40px; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: var(--t); border: none; text-decoration: none; }
        .action-btn-primary { background: var(--vp); color: white; }
        .action-btn-primary:hover { background: var(--vf); transform: translateY(-1px); }
        .action-btn-danger { background: #fee2e2; color: #dc2626; }
        .action-btn-danger:hover { background: #dc2626; color: white; transform: translateY(-1px); }
        .action-btn-warning { background: #fef3c7; color: #d97706; }
        .action-btn-warning:hover { background: #d97706; color: white; transform: translateY(-1px); }

        /* Table container */
        .table-container { background: #fff; border-radius: 16px; border: 1px solid var(--gl); overflow: hidden; }
        .table-wrapper { overflow-x: auto; }
        .data-table { width: 100%; border-collapse: collapse; font-size: 0.8rem; min-width: 900px; }
        .data-table th { padding: 12px 16px; background: var(--gtc); font-weight: 600; color: var(--gf); border-bottom: 1px solid var(--gl); text-align: left; }
        .data-table td { padding: 12px 16px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
        .data-table tr:hover td { background: #f9fafb; }

        .row-checkbox { width: 16px; height: 16px; accent-color: var(--vp); }

        /* Employee cell */
        .employee-cell { display: flex; align-items: center; gap: 12px; }
        .employee-avatar { flex-shrink: 0; }
        .employee-avatar img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
        .avatar-placeholder { width: 40px; height: 40px; border-radius: 50%; background: var(--vp); color: white; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: bold; }
        .employee-info { display: flex; flex-direction: column; }
        .employee-name { font-weight: 600; color: var(--gf); }
        .employee-matricule { font-size: 0.65rem; color: var(--gm); }

        /* Category badge */
        .category-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; }
        .category-conge { background: #e0f2fe; color: #0369a1; }
        .category-permission { background: #fef3c7; color: #92400e; }

        /* Date cell */
        .date-cell { display: flex; flex-direction: column; }
        .date-day { font-weight: 600; font-size: 0.8rem; }

        /* Motif cell */
        .motif-cell { max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 0.75rem; color: var(--gm); }

        /* Status badges */
        .status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-approved { background: #dcfce7; color: #166534; }
        .status-rejected { background: #fee2e2; color: #991b1b; }

        /* Dropdown custom */
        .dropdown-container { position: relative; display: inline-block; }
        .dropdown-menu-custom {
            position: fixed;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            min-width: 160px;
            z-index: 9999;
            overflow: hidden;
            display: none;
        }
        .dropdown-menu-custom.show { display: block; }
        .dots-btn { background: none; border: 1px solid #e5e7eb; border-radius: 6px; padding: 4px 10px; color: #6b7280; cursor: pointer; transition: all 0.15s; }
        .dots-btn:hover { background: #f3f4f6; color: #111; }
        .dropdown-item-custom { display: flex; align-items: center; gap: 8px; width: 100%; padding: 8px 16px; text-align: left; background: none; border: none; font-size: 0.8rem; color: #374151; cursor: pointer; text-decoration: none; transition: background 0.15s; }
        .dropdown-item-custom:hover { background: #f3f4f6; color: #2e7d64; }

        .empty-row { text-align: center; padding: 60px 16px; color: #9ca3af; }
        .empty-sub { font-size: 0.7rem; display: block; margin-top: 8px; }
        .pagination-wrapper { display: flex; justify-content: flex-end; padding: 16px; border-top: 1px solid var(--gl); }

        .disabled { opacity: 0.5; pointer-events: none; }

        @media (max-width: 1280px) { .action-bar { left: 0; } }
        @media (max-width: 768px) {
            .page-content { padding: 46px 0 0 0; }
            .content-card { margin: 0 16px; padding: 16px; }
            .action-bar-inner { padding: 0 14px; flex-wrap: wrap; gap: 8px; height: auto; padding: 8px 14px; }
            .action-stats { flex-wrap: wrap; }
            .content-header { flex-direction: column; align-items: flex-start; }
            .selection-actions { flex-direction: column; align-items: stretch; }
            .action-btn-primary, .action-btn-danger, .action-btn-warning { justify-content: center; }
            .employee-cell { min-width: 180px; }
        }
    </style>

    <script>
        function toggleDropdown(btn, event) {
            if (event) { event.preventDefault(); event.stopPropagation(); }
            document.querySelectorAll('.dropdown-menu-custom').forEach(menu => {
                if (menu !== btn.nextElementSibling) menu.classList.remove('show');
            });
            var menu = btn.nextElementSibling;
            if (!menu) return;
            if (menu.classList.contains('show')) menu.classList.remove('show');
            else {
                var rect = btn.getBoundingClientRect();
                menu.style.position = 'fixed';
                menu.style.top = (rect.bottom + 5) + 'px';
                menu.style.left = (rect.right - 160) + 'px';
                menu.classList.add('show');
            }
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown-container')) {
                document.querySelectorAll('.dropdown-menu-custom').forEach(menu => menu.classList.remove('show'));
            }
        });

        $(document).ready(function() {
            function updateButtons() {
                var selectedCheckboxes = $(".checkbox-item:checked");
                var selectedCount = selectedCheckboxes.length;

                if (selectedCount > 0) {
                    $("#add_button").addClass("disabled").prop("disabled", true);
                } else {
                    $("#add_button").removeClass("disabled").prop("disabled", false);
                }

                if (selectedCount === 1) {
                    $("#delete_button").removeClass("disabled").prop("disabled", false);
                    $("#edit_button").removeClass("disabled").prop("disabled", false);
                } else {
                    $("#delete_button").addClass("disabled").prop("disabled", true);
                    $("#edit_button").addClass("disabled").prop("disabled", true);
                }
            }

            // Select All
            $('#checkbox-all, #selectAllCheckbox').on('click', function() {
                $('.checkbox-item').prop('checked', this.checked);
                updateButtons();
            });

            $('.checkbox-item').on('change', function() {
                if (!this.checked) {
                    $('#checkbox-all, #selectAllCheckbox').prop('checked', false);
                }
                if ($('.checkbox-item:checked').length === $('.checkbox-item').length) {
                    $('#checkbox-all, #selectAllCheckbox').prop('checked', true);
                }
                updateButtons();
            });

            updateButtons();

            $('#edit_button').on('click', function(e) {
                e.preventDefault();
                var selectedCheckbox = $('input[name="demande_id"]:checked').first();
                if (selectedCheckbox.length) {
                    var demandeId = selectedCheckbox.val();
                    window.location.href = "{{ url('demandes') }}/" + demandeId + "/modifier";
                }
            });

            $('#delete_button').on('click', function(e) {
                e.preventDefault();
                var selectedCheckbox = $('input[name="demande_id"]:checked').first();
                if (selectedCheckbox.length) {
                    var demandeId = selectedCheckbox.val();
                    Swal.fire({
                        title: 'Confirmation',
                        text: 'Êtes-vous sûr de vouloir supprimer cette demande ?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        confirmButtonText: 'Oui, supprimer',
                        cancelButtonText: 'Annuler'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('delete-form-' + demandeId).submit();
                        }
                    });
                }
            });
        });

        function confirmDelete(demandeId) {
            Swal.fire({
                title: 'Confirmation',
                text: 'Êtes-vous sûr de vouloir supprimer cette demande ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + demandeId).submit();
                }
            });
        }

        // Export Excel
        document.getElementById('export-excel')?.addEventListener('click', function() {
            let table = document.getElementById('export-table');
            let html = table.outerHTML;
            let url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
            let link = document.createElement('a');
            link.download = 'demandes_' + new Date().toISOString().slice(0,19) + '.xls';
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
                    rowData.push('"' + col.innerText.replace(/"/g, '""') + '"');
                });
                csv.push(rowData.join(','));
            });
            let blob = new Blob([csv.join('\n')], { type: 'text/csv' });
            let link = document.createElement('a');
            link.download = 'demandes_' + new Date().toISOString().slice(0,19) + '.csv';
            link.href = URL.createObjectURL(blob);
            link.click();
        });
    </script>
</x-app-layout>
