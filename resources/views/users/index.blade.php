<x-app-layout>
    @php
        $userTypesTitle = [
            'super_admin' => __('All Super Admins'),
            'admin'       => __('All Admins'),
            'manager'     => __('All Managers'),
            'controller'  => __('All Controllers'),
            'user'        => __('All Employees'),
            'customer'    => __('All Customers'),
        ];

        $addRoutes = [
            'user'        => ['simple' => route('users.create.simple', 'user'), 'with_schedule' => route('users.create', 'user')],
            'super_admin' => route('super-admin.create'),
            'admin'       => '#',
            'manager'     => route('users.create', 'manager'),
            'controller'  => route('users.create', 'controller'),
            'customer'    => route('users.create', 'customer'),
        ];

        $currentFilter = request()->query('filter', 'all');
    @endphp

    {{-- ===================================================== --}}
    {{-- BARRE D'ACTIONS — collée sous la navbar               --}}
    {{-- ===================================================== --}}
    @if (!is_controller())
        <div class="action-bar">
            <div class="action-bar-inner">
                @if (isset($addRoutes[$user_type]))
                    <a href="{{ is_array($addRoutes[$user_type]) ? $addRoutes[$user_type]['with_schedule'] : $addRoutes[$user_type] }}"
                       id="add_button" class="action-btn">
                        <i class="fas fa-plus"></i>
                        <span>{{ __('Add') }}</span>
                    </a>
                @endif
                <a href="#" id="delete_button" class="action-btn">
                    <i class="fas fa-trash"></i>
                    <span>{{ __('Delete') }}</span>
                </a>
                <a href="#" id="edit_button" class="action-btn">
                    <i class="fas fa-pen"></i>
                    <span>{{ __('Edit') }}</span>
                </a>
                <a href="#" id="disabled_button" class="action-btn">
                    <i class="fas fa-lock"></i>
                    <span>{{ __('Activer / Désactiver') }}</span>
                </a>
            </div>
        </div>
    @endif

    {{-- ===================================================== --}}
    {{-- CONTENU PRINCIPAL                                     --}}
    {{-- ===================================================== --}}
    <div class="page-content">
        <div class="content-card">
            <div class="content-header">
                <h1 class="page-title">{{ $userTypesTitle[$user_type] ?? '' }}</h1>
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
                            <th style="width: 40px;"><input type="checkbox" id="selectAllCheckbox" class="row-checkbox"></th>
                            @if($user_type == 'user')
                                <th>{{ __('Matricule') }}</th>
                            @endif
                            <th>{{ __('Name') }}</th>
                            {{-- <th>{{ __('N. Social') }}</th> --}}
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Role') }}</th>
                            <th>{{ __('Status') }}</th>
                            @if (!is_controller())
                                <th style="width: 80px;">{{ __('Action') }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="tbody">
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <input type="checkbox" class="row-checkbox"
                                           name="user_id" value="{{ $user->id }}"
                                           data-user-type="{{ $user->user_type }}"
                                           data-user-id="{{ $user->id }}">
                                </td>
                                @if($user_type == 'user')
                                    <td>{{ $user->matricule ?? '-' }}</td>
                                @endif
                                <td>
                                    <a href="{{ route('users.show', $user->id) }}" class="user-cell">
                                        @if($user->avatar && Storage::disk('public')->exists($user->avatar))
                                            <img src="{{ asset('storage/' . $user->avatar) }}" class="user-avatar" alt="">
                                        @else
                                            <div class="user-avatar-placeholder">
                                                {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                            </div>
                                        @endif
                                        <span>{{ $user->first_name ?? '' }} <strong>{{ strtoupper($user->name ?? '') }}</strong></span>
                                    </a>
                                </td>
                                {{-- <td>{{ $user->n_social ?? '-' }}</td> --}}
                                <td>{{ $user->email ?? '-' }}</td>
                                <td class="capitalize">{{ __($user->user_type) ?? '-' }}</td>
                                <td>
                                    <span class="status-badge {{ $user->status == 'active' ? 'status-badge--active' : 'status-badge--inactive' }}">
                                        {{ __($user->status) ?? '-' }}
                                    </span>
                                </td>
                                @if (!is_controller())
                                    <td class="action-cell">
                                        <div class="dropdown-container">
                                            <button class="dots-btn" onclick="toggleDropdown(this, event)">
                                                <i class="fa-solid fa-ellipsis"></i>
                                            </button>
                                            <div class="dropdown-menu-custom">
                                                <a href="{{ route('users.edit', ['user_type' => $user->user_type, 'id' => $user->id]) }}" class="dropdown-item-custom">
                                                    <i class="fas fa-edit"></i> {{ __('Edit') }}
                                                </a>
                                                @if($user->status == 'active')
                                                    <button onclick="confirmDisable({{ $user->id }}, '{{ $user->user_type }}')" class="dropdown-item-custom">
                                                        <i class="fas fa-ban"></i> {{ __('Désactiver') }}
                                                    </button>
                                                @else
                                                    <form action="{{ route('users.reactivate', ['user_type' => $user->user_type, 'id' => $user->id]) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item-custom">
                                                            <i class="fas fa-check-circle"></i> {{ __('Activer') }}
                                                        </button>
                                                    </form>
                                                @endif
                                                <button onclick="confirmDelete({{ $user->id }}, '{{ $user->user_type }}')" class="dropdown-item-custom" style="color: #dc2626;">
                                                    <i class="fas fa-trash-alt"></i> {{ __('Delete') }}
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="empty-row">{{ __('No Content') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                {{ $users->appends(['filter' => $currentFilter])->links() }}
            </div>
        </div>
    </div>

    {{-- Modal désactivation --}}
    <div id="disableModal" class="modal-overlay hidden">
        <div class="modal-container">
            <div class="modal-card">
                <div class="modal-header">
                    <h3>Confirmer la modification du statut</h3>
                    <button type="button" class="modal-close" onclick="closeDisableModal()">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <label class="modal-label">Motif *</label>
                    <select id="disableReason" class="modal-select">
                        <option value="">Selectionnez Un Motif</option>
                        <option value="Fin de Contrat">Fin de Contrat</option>
                        <option value="Permission">Permission</option>
                        <option value="Congé">Congé</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button onclick="closeDisableModal()" class="modal-btn-cancel">{{ __('Cancel') }}</button>
                    <button id="confirmDisable" class="modal-btn-confirm">{{ __('Record') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- STYLES                                                --}}
    {{-- ===================================================== --}}
    <style>
        /* === BARRE D'ACTIONS === */
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
        .action-btn + .action-btn {
            border-left: 1px solid rgba(255,255,255,0.18);
            border-radius: 0;
            padding-left: 18px;
        }
        .disabled { opacity: 0.5; pointer-events: none; }
        .page-content { padding-top: 46px; }
        .content-card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            padding: 24px;
            overflow: visible;
            position: relative;
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
        .row-checkbox {
            width: 15px;
            height: 15px;
            accent-color: #2e7d64;
            cursor: pointer;
        }
        .user-cell {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #1f2937;
        }
        .user-cell:hover { color: #2e7d64; }
        .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            object-fit: cover;
            flex-shrink: 0;
            border: 1px solid #e5e7eb;
        }
        .user-avatar-placeholder {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: #2e7d64;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 600;
            border: 1px solid transparent;
        }
        .status-badge--active { background: #dcfce7; color: #166534; border-color: #bbf7d0; }
        .status-badge--inactive { background: #fee2e2; color: #991b1b; border-color: #fecaca; }

        /* Dropdown custom */
        .action-cell { position: relative; }
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
        .dots-btn {
            background: none;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 4px 10px;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.15s;
        }
        .dots-btn:hover { background: #f3f4f6; color: #111; }
        .dropdown-item-custom {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            padding: 8px 16px;
            text-align: left;
            background: none;
            border: none;
            font-size: 0.8rem;
            color: #374151;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.15s;
        }
        .dropdown-item-custom:hover { background: #f3f4f6; color: #2e7d64; }
        .empty-row { text-align: center; padding: 40px 16px; color: #9ca3af; font-size: 0.85rem; }
        .pagination-wrapper { display: flex; justify-content: flex-end; margin-top: 20px; }

        /* Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.hidden { display: none; }
        .modal-container { width: 100%; max-width: 500px; margin: 20px; }
        .modal-card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 20px 60px rgba(0,0,0,0.12);
            overflow: hidden;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid #f3f4f6;
        }
        .modal-header h3 { font-size: 0.95rem; font-weight: 700; color: #1f2937; margin: 0; }
        .modal-close {
            background: #f3f4f6;
            border: none;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #6b7280;
        }
        .modal-body { padding: 20px; }
        .modal-label { display: block; font-size: 0.82rem; font-weight: 600; color: #374151; margin-bottom: 8px; }
        .modal-select {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.85rem;
            color: #374151;
        }
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 14px 20px;
            border-top: 1px solid #f3f4f6;
        }
        .modal-btn-cancel {
            padding: 8px 16px;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            font-size: 0.8rem;
            cursor: pointer;
        }
        .modal-btn-confirm {
            padding: 8px 16px;
            background: #2e7d64;
            border: none;
            border-radius: 6px;
            font-size: 0.8rem;
            color: white;
            cursor: pointer;
        }
        .modal-btn-confirm:hover { background: #1a5a48; }

        @media (max-width: 1280px) { .action-bar { left: 0; } }
        @media (max-width: 768px) {
            .action-bar-inner { padding: 0 14px; gap: 0; overflow-x: auto; }
            .content-card { padding: 16px; }
            .content-header { flex-direction: column; align-items: flex-start; }
        }
    </style>

    {{-- ===================================================== --}}
    {{-- SCRIPTS CORRIGÉS                                      --}}
    {{-- ===================================================== --}}
    <script>
        // Gestion du dropdown avec positionnement dynamique
        function toggleDropdown(btn, event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            document.querySelectorAll('.dropdown-menu-custom').forEach(menu => {
                if (menu !== btn.nextElementSibling) {
                    menu.classList.remove('show');
                }
            });

            var menu = btn.nextElementSibling;
            if (!menu) return;

            if (menu.classList.contains('show')) {
                menu.classList.remove('show');
            } else {
                var rect = btn.getBoundingClientRect();
                menu.style.position = 'fixed';
                menu.style.top = (rect.bottom + 5) + 'px';
                menu.style.left = (rect.right - 160) + 'px';
                menu.classList.add('show');
            }
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown-container')) {
                document.querySelectorAll('.dropdown-menu-custom').forEach(menu => {
                    menu.classList.remove('show');
                });
            }
        });

        // Select All
        document.getElementById('selectAllCheckbox')?.addEventListener('change', function() {
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                if(cb !== this) cb.checked = this.checked;
            });
            updateButtons();
        });

        function updateButtons() {
            var selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked:not(#selectAllCheckbox)');
            var selectedCount = selectedCheckboxes.length;

            var addBtn = document.getElementById('add_button');
            var deleteBtn = document.getElementById('delete_button');
            var editBtn = document.getElementById('edit_button');
            var disabledBtn = document.getElementById('disabled_button');

            if (addBtn) {
                if (selectedCount > 0) addBtn.classList.add('disabled');
                else addBtn.classList.remove('disabled');
            }
            if (selectedCount === 1) {
                if (deleteBtn) deleteBtn.classList.remove('disabled');
                if (editBtn) editBtn.classList.remove('disabled');
                if (disabledBtn) disabledBtn.classList.remove('disabled');
            } else if (selectedCount > 1) {
                if (editBtn) editBtn.classList.add('disabled');
                if (disabledBtn) disabledBtn.classList.add('disabled');
                if (deleteBtn) deleteBtn.classList.remove('disabled');
            } else {
                if (deleteBtn) deleteBtn.classList.add('disabled');
                if (editBtn) editBtn.classList.add('disabled');
                if (disabledBtn) disabledBtn.classList.add('disabled');
            }
        }

        document.querySelectorAll('.row-checkbox').forEach(cb => {
            cb.addEventListener('change', updateButtons);
        });
        updateButtons();

        // 1. Bouton EDIT (Barre d'actions supérieure)
        document.getElementById('edit_button')?.addEventListener('click', function(e) {
            e.preventDefault();
            var selectedCheckbox = document.querySelector('.row-checkbox:checked:not(#selectAllCheckbox)');
            if (selectedCheckbox) {
                var userId = selectedCheckbox.dataset.userId;
                var userType = selectedCheckbox.dataset.userType;

                if (!userId || !userType) return;

                var editUrl = "{{ route('users.edit', ['user_type' => 'UT_PLACEHOLDER', 'id' => 'ID_PLACEHOLDER']) }}";
                editUrl = editUrl.replace('UT_PLACEHOLDER', userType).replace('ID_PLACEHOLDER', userId);

                window.location.href = editUrl;
            } else {
                Swal.fire('Information', 'Veuillez sélectionner un utilisateur.', 'info');
            }
        });

        // 2. Bouton DELETE (Barre d'actions supérieure)
        document.getElementById('delete_button')?.addEventListener('click', function(e) {
            e.preventDefault();
            var selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked:not(#selectAllCheckbox)');
            if (selectedCheckboxes.length === 0) {
                Swal.fire('Information', 'Veuillez sélectionner au moins un utilisateur.', 'info');
                return;
            }
            Swal.fire({
                title: 'Confirmation',
                text: 'Êtes-vous sûr de vouloir supprimer ' + selectedCheckboxes.length + ' utilisateur(s) ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    var completedRequests = 0;
                    var totalRequests = selectedCheckboxes.length;

                    selectedCheckboxes.forEach(cb => {
                        var userId = cb.dataset.userId;
                        var userType = cb.dataset.userType;

                        var deleteUrl = "{{ route('users.destroy', ['user_type' => 'UT_PLACEHOLDER', 'id' => 'ID_PLACEHOLDER']) }}";
                        deleteUrl = deleteUrl.replace('UT_PLACEHOLDER', userType).replace('ID_PLACEHOLDER', userId);

                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function(response) {
                                completedRequests++;
                                if (completedRequests === totalRequests) {
                                    location.reload();
                                }
                            },
                            error: function() {
                                completedRequests++;
                                if (completedRequests === totalRequests) {
                                    location.reload();
                                }
                            }
                        });
                    });
                }
            });
        });

        // 3. Bouton DISABLE (Barre d'actions supérieure)
        document.getElementById('disabled_button')?.addEventListener('click', function(e) {
            e.preventDefault();
            var selectedCheckbox = document.querySelector('.row-checkbox:checked:not(#selectAllCheckbox)');
            if (!selectedCheckbox) {
                Swal.fire('Information', 'Veuillez sélectionner un utilisateur.', 'info');
                return;
            }
            if (document.querySelectorAll('.row-checkbox:checked:not(#selectAllCheckbox)').length > 1) {
                Swal.fire('Information', 'Veuillez sélectionner un seul utilisateur pour la désactivation.', 'info');
                return;
            }
            document.getElementById('disableModal').classList.remove('hidden');
        });

        function closeDisableModal() {
            document.getElementById('disableModal').classList.add('hidden');
            document.getElementById('disableReason').value = "";
        }

        document.getElementById('confirmDisable')?.addEventListener('click', function() {
            var selectedCheckbox = document.querySelector('.row-checkbox:checked:not(#selectAllCheckbox)');
            if (selectedCheckbox) {
                var userId = selectedCheckbox.dataset.userId;
                var userType = selectedCheckbox.dataset.userType;
                var reason = document.getElementById('disableReason').value.trim();

                if (!reason) {
                    Swal.fire('Erreur', 'Veuillez sélectionner ou entrer un motif.', 'error');
                    return;
                }

                var disableUrl = "{{ route('users.disabled', ['user_type' => 'UT_PLACEHOLDER', 'id' => 'ID_PLACEHOLDER']) }}";
                disableUrl = disableUrl.replace('UT_PLACEHOLDER', userType).replace('ID_PLACEHOLDER', userId);

                $.ajax({
                    url: disableUrl,
                    type: 'PATCH',
                    data: {
                        _token: '{{ csrf_token() }}',
                        reason: reason
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function() {
                        Swal.fire('Erreur', 'Une erreur est survenue lors de la désactivation.', 'error');
                    }
                });
            }
            closeDisableModal();
        });

        // 4. Fonctions d'actions individuelles (Dropdown des 3 points)
        function confirmDelete(userId, userType) {
            Swal.fire({
                title: 'Confirmation',
                text: 'Êtes-vous sûr de vouloir supprimer cet utilisateur ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    var deleteUrl = "{{ route('users.destroy', ['user_type' => 'UT_PLACEHOLDER', 'id' => 'ID_PLACEHOLDER']) }}";
                    deleteUrl = deleteUrl.replace('UT_PLACEHOLDER', userType).replace('ID_PLACEHOLDER', userId);

                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            Swal.fire('Supprimé!', 'L\'utilisateur a été supprimé.', 'success').then(() => {
                                location.reload();
                            });
                        },
                        error: function() {
                            Swal.fire('Erreur!', 'Une erreur est survenue.', 'error');
                        }
                    });
                }
            });
        }

        function confirmDisable(userId, userType) {
            // Coche uniquement cet utilisateur pour synchroniser l'état globale avec le modal
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.checked = (cb.value == userId && !cb.id.includes('selectAllCheckbox'));
            });
            updateButtons();
            document.getElementById('disableModal').classList.remove('hidden');
        }
    </script>
</x-app-layout>
