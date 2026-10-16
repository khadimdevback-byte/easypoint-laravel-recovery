<x-app-layout>
    {{-- ===================================================== --}}
    {{-- BARRE D'ACTIONS — collée sous la navbar               --}}
    {{-- ===================================================== --}}
    <div class="action-bar">
        <div class="action-bar-inner">
            <a href="{{ route('companies.create') }}" id="add_button" class="action-btn">
                <i class="fas fa-plus"></i>
                <span>{{ __('Add') }}</span>
            </a>
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
                <span>{{ __('Disable') }}</span>
            </a>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- CONTENU PRINCIPAL                                     --}}
    {{-- ===================================================== --}}
    <div class="page-content">
        <div class="content-card">
            <div class="content-header">
                <h1 class="page-title">{{ __('All companies') }}</h1>
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
                            <th>{{ __('Company name') }}</th>
                            <th>{{ __('Company email') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>{{ __('Address') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th style="width: 80px;">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody id="companies-table-body">
                        @forelse($companies as $company)
                            <tr id="company-row-{{ $company->id }}" data-company-id="{{ $company->id }}">
                                <td>
                                    <input type="checkbox" class="row-checkbox"
                                           name="company_id" value="{{ $company->id }}"
                                           data-company-id="{{ $company->id }}">
                                </td>
                                <td>
                                    <a href="{{ route('companies.show', $company->id) }}" class="company-cell">
                                        @if($company->logo && Storage::disk('public')->exists($company->logo))
                                            <img src="{{ asset('storage/' . $company->logo) }}" class="company-logo" alt="">
                                        @else
                                            <div class="company-logo-placeholder">
                                                {{ strtoupper(substr($company->compagny_name ?? 'C', 0, 1)) }}
                                            </div>
                                        @endif
                                        <span>{{ $company->compagny_name ?? '' }}</span>
                                    </a>
                                </td>
                                <td>{{ $company->compagny_email ?? '-' }}</td>
                                <td>{{ $company->phone ?? '-' }}</td>
                                <td>{{ $company->address ?? '-' }}</td>
                                <td>
                                    <span class="status-badge
                                        @if($company->status == 'active') status-badge--active
                                        @elseif($company->status == 'pending') status-badge--pending
                                        @else status-badge--inactive @endif">
                                        {{ __($company->status) ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="dropdown-container">
                                        <button class="dots-btn" onclick="toggleDropdown(this, event)">
                                            <i class="fa-solid fa-ellipsis"></i>
                                        </button>
                                        <div class="dropdown-menu-custom">
                                            <a href="{{ route('companies.edit', $company->id) }}" class="dropdown-item-custom">
                                                <i class="fas fa-edit"></i> {{ __('Edit') }}
                                            </a>
                                            @if($company->status === 'inactive')
                                                <button onclick="activateCompany({{ $company->id }})" class="dropdown-item-custom">
                                                    <i class="fas fa-check-circle"></i> {{ __('Activer') }}
                                                </button>
                                                <button class="dropdown-item-custom disabled" disabled>
                                                    <i class="fas fa-ban"></i> {{ __('Désactiver') }}
                                                </button>
                                            @else
                                                <button onclick="confirmDisable({{ $company->id }})" class="dropdown-item-custom">
                                                    <i class="fas fa-ban"></i> {{ __('Désactiver') }}
                                                </button>
                                                <button class="dropdown-item-custom disabled" disabled>
                                                    <i class="fas fa-check-circle"></i> {{ __('Activer') }}
                                                </button>
                                            @endif
                                            <button onclick="confirmDelete({{ $company->id }})" class="dropdown-item-custom" style="color: #dc2626;">
                                                <i class="fas fa-trash-alt"></i> {{ __('Delete') }}
                                            </button>
                                            {{-- <a href="{{ route('superadmin.menu-management.edit', $company->id) }}" class="dropdown-item-custom">
                                                <i class="fas fa-bars"></i> {{ __('Gerer le Menu') }}
                                            </a> --}}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="empty-row">
                                <td colspan="7" class="empty-row">{{ __('No Content') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper" id="pagination-wrapper">
                {{ $companies->links() }}
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
                    <textarea id="disableReason" class="modal-textarea" placeholder="Entrez le motif..."></textarea>
                </div>
                <div class="modal-footer">
                    <button onclick="closeDisableModal()" class="modal-btn-cancel">{{ __('Cancel') }}</button>
                    <button id="confirmDisable" class="modal-btn-confirm">{{ __('Record') }}</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* ============================================ */
        /* STYLES MODERNES POUR LA PAGE COMPANIES       */
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
            border: none;
            background: none;
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

        .row-checkbox {
            width: 15px;
            height: 15px;
            accent-color: #2e7d64;
            cursor: pointer;
        }

        .company-cell {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #1f2937;
        }
        .company-cell:hover { color: #2e7d64; }
        .company-logo {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            border: 1px solid #e5e7eb;
        }
        .company-logo-placeholder {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #2e7d64;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
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
        .status-badge--pending { background: #fef3c7; color: #92400e; border-color: #fde68a; }

        /* Dropdown custom */
        .dropdown-container { position: relative; display: inline-block; }
        .dropdown-menu-custom {
            position: fixed;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            min-width: 180px;
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
        .dropdown-item-custom.disabled { opacity: 0.5; cursor: not-allowed; }

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
        .modal-textarea {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.85rem;
            color: #374151;
            resize: vertical;
            min-height: 80px;
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

        /* Animation de suppression */
        .row-deleting {
            animation: rowFadeOut 0.4s ease forwards;
        }

        @keyframes rowFadeOut {
            0% { opacity: 1; transform: scale(1); max-height: 100px; padding: 10px 0; }
            100% { opacity: 0; transform: scale(0.95); max-height: 0; padding: 0; margin: 0; overflow: hidden; }
        }

        @media (max-width: 1280px) { .action-bar { left: 0; } }
        @media (max-width: 768px) {
            .page-content { padding: 46px 0 0 0; }
            .content-card { margin: 0 16px; padding: 16px; }
            .action-bar-inner { padding: 0 14px; gap: 0; overflow-x: auto; }
            .content-header { flex-direction: column; align-items: flex-start; }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // =====================================================
        // DROPDOWN
        // =====================================================
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
                menu.style.left = (rect.right - 180) + 'px';
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

        // =====================================================
        // SELECT ALL & UPDATE BUTTONS
        // =====================================================
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

        // =====================================================
        // BOUTON EDIT
        // =====================================================
        document.getElementById('edit_button')?.addEventListener('click', function(e) {
            e.preventDefault();
            var selectedCheckbox = document.querySelector('.row-checkbox:checked:not(#selectAllCheckbox)');
            if (selectedCheckbox) {
                var companyId = selectedCheckbox.value;
                window.location.href = "{{ url('companies') }}/" + companyId + "/edit";
            } else {
                Swal.fire('Information', 'Veuillez sélectionner une entreprise.', 'info');
            }
        });

        // =====================================================
        // SUPPRESSION D'UNE LIGNE DU DOM
        // =====================================================
        function removeCompanyRow(companyId) {
            var row = document.getElementById('company-row-' + companyId);
            if (row) {
                row.classList.add('row-deleting');
                setTimeout(function() {
                    row.remove();
                    // Vérifier s'il reste des lignes
                    var remainingRows = document.querySelectorAll('#companies-table-body tr:not(#empty-row)');
                    if (remainingRows.length === 0) {
                        var tbody = document.getElementById('companies-table-body');
                        var emptyRow = document.getElementById('empty-row');
                        if (!emptyRow) {
                            var newEmptyRow = document.createElement('tr');
                            newEmptyRow.id = 'empty-row';
                            newEmptyRow.innerHTML = '<td colspan="7" class="empty-row">{{ __('No Content') }}</td>';
                            tbody.appendChild(newEmptyRow);
                        } else {
                            emptyRow.style.display = '';
                        }
                    }
                    updateButtons();
                }, 400);
            }
        }

        // =====================================================
        // SUPPRESSION INDIVIDUELLE (Dropdown)
        // =====================================================
        function confirmDelete(companyId) {
            Swal.fire({
                title: 'Confirmation',
                text: 'Êtes-vous sûr de vouloir supprimer cette entreprise ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    var token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                    // Utiliser la route nommée pour la suppression
                    var deleteUrl = "{{ route('companies.destroy', ':id') }}".replace(':id', companyId);

                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        data: {
                            _token: token
                        },
                        success: function(response) {
                            removeCompanyRow(companyId);
                            Swal.fire('Succès', response.message || 'L\'entreprise a été supprimée.', 'success');
                        },
                        error: function(xhr) {
                            console.error('Erreur:', xhr);
                            if (xhr.status === 404) {
                                removeCompanyRow(companyId);
                                Swal.fire('Information', 'Cette entreprise a déjà été supprimée.', 'info');
                            } else {
                                try {
                                    var response = JSON.parse(xhr.responseText);
                                    Swal.fire('Erreur', response.message || 'Une erreur est survenue.', 'error');
                                } catch (e) {
                                    Swal.fire('Erreur', 'Une erreur est survenue lors de la suppression.', 'error');
                                }
                            }
                        }
                    });
                }
            });
        }

        // =====================================================
        // SUPPRESSION MULTIPLE (Barre d'actions)
        // =====================================================
        document.getElementById('delete_button')?.addEventListener('click', function(e) {
            e.preventDefault();
            var selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked:not(#selectAllCheckbox)');
            if (selectedCheckboxes.length === 0) {
                Swal.fire('Information', 'Veuillez sélectionner au moins une entreprise.', 'info');
                return;
            }

            var companyIds = [];
            selectedCheckboxes.forEach(cb => {
                companyIds.push(cb.value);
            });

            Swal.fire({
                title: 'Confirmation',
                text: 'Êtes-vous sûr de vouloir supprimer ' + selectedCheckboxes.length + ' entreprise(s) ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    var completed = 0;
                    var total = companyIds.length;
                    var token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                    companyIds.forEach(function(companyId) {
                        var deleteUrl = "{{ route('companies.destroy', ':id') }}".replace(':id', companyId);

                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': token
                            },
                            data: {
                                _token: token
                            },
                            success: function() {
                                removeCompanyRow(companyId);
                                completed++;
                                if (completed === total) {
                                    Swal.fire('Succès', 'Toutes les entreprises ont été supprimées.', 'success');
                                }
                            },
                            error: function(xhr) {
                                completed++;
                                if (xhr.status === 404) {
                                    removeCompanyRow(companyId);
                                }
                                if (completed === total) {
                                    Swal.fire('Information', 'Suppression terminée.', 'info');
                                }
                            }
                        });
                    });
                }
            });
        });

        // =====================================================
        // DESACTIVATION
        // =====================================================
        let currentDisableId = null;

        document.getElementById('disabled_button')?.addEventListener('click', function(e) {
            e.preventDefault();
            var selectedCheckbox = document.querySelector('.row-checkbox:checked:not(#selectAllCheckbox)');
            if (!selectedCheckbox) {
                Swal.fire('Information', 'Veuillez sélectionner une entreprise.', 'info');
                return;
            }
            currentDisableId = selectedCheckbox.value;
            document.getElementById('disableModal').classList.remove('hidden');
        });

        function closeDisableModal() {
            document.getElementById('disableModal').classList.add('hidden');
            document.getElementById('disableReason').value = '';
            currentDisableId = null;
        }

        document.getElementById('confirmDisable')?.addEventListener('click', function() {
            var reason = document.getElementById('disableReason').value.trim();
            if (!reason) {
                Swal.fire('Erreur', 'Veuillez entrer un motif.', 'error');
                return;
            }
            var token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            $.ajax({
                url: "{{ url('companies/disabled') }}/" + currentDisableId,
                type: 'PATCH',
                data: {
                    _token: token,
                    reason: reason
                },
                success: function(response) {
                    // Mettre à jour le statut dans la ligne
                    var row = document.getElementById('company-row-' + currentDisableId);
                    if (row) {
                        var statusBadge = row.querySelector('.status-badge');
                        if (statusBadge) {
                            statusBadge.className = 'status-badge status-badge--inactive';
                            statusBadge.textContent = 'inactive';
                        }
                        // Recharger le dropdown pour mettre à jour les boutons
                        location.reload();
                    }
                    Swal.fire('Succès', response.message || 'L\'entreprise a été désactivée.', 'success');
                    closeDisableModal();
                },
                error: function(xhr) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        Swal.fire('Erreur', response.message || 'Une erreur est survenue.', 'error');
                    } catch (e) {
                        Swal.fire('Erreur', 'Une erreur est survenue.', 'error');
                    }
                }
            });
        });

        function confirmDisable(companyId) {
            currentDisableId = companyId;
            document.getElementById('disableModal').classList.remove('hidden');
        }

        // =====================================================
        // ACTIVATION
        // =====================================================
        function activateCompany(companyId) {
            Swal.fire({
                title: 'Confirmation',
                text: 'Voulez-vous activer cette entreprise ?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2e7d64',
                confirmButtonText: 'Oui, activer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    var token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                    $.ajax({
                        url: "{{ url('companies') }}/" + companyId + "/activate",
                        type: 'PATCH', // ← Utiliser PATCH ou POST selon votre route
                        data: { 
                            _token: token,
                            _method: 'PATCH' // Si votre route utilise PATCH
                        },
                        success: function(response) {
                            var row = document.getElementById('company-row-' + companyId);
                            if (row) {
                                var statusBadge = row.querySelector('.status-badge');
                                if (statusBadge) {
                                    statusBadge.className = 'status-badge status-badge--active';
                                    statusBadge.textContent = 'active';
                                }
                                // Recharger la page pour mettre à jour les boutons du dropdown
                                location.reload();
                            }
                            Swal.fire('Succès', response.message || 'L\'entreprise a été activée.', 'success');
                        },
                        error: function(xhr) {
                            try {
                                var response = JSON.parse(xhr.responseText);
                                Swal.fire('Erreur', response.message || 'Une erreur est survenue.', 'error');
                            } catch (e) {
                                Swal.fire('Erreur', 'Une erreur est survenue lors de l\'activation.', 'error');
                            }
                        }
                    });
                }
            });
        }
    </script>
</x-app-layout>
