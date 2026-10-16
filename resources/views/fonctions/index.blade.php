<x-app-layout>
    {{-- ===================================================== --}}
    {{-- BARRE D'ACTIONS — collée sous la navbar               --}}
    {{-- ===================================================== --}}
    <div class="action-bar">
        <div class="action-bar-inner">
            <a href="{{ route('fonctions.ajouter') }}" id="add_button" class="action-btn">
                <i class="fas fa-plus"></i>
                <span>{{ __('Ajouter') }}</span>
            </a>
            <a href="#" id="delete_button" class="action-btn">
                <i class="fas fa-trash"></i>
                <span>{{ __('Supprimer') }}</span>
            </a>
            <a href="#" id="edit_button" class="action-btn">
                <i class="fas fa-pen"></i>
                <span>{{ __('Modifier') }}</span>
            </a>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- CONTENU PRINCIPAL                                     --}}
    {{-- ===================================================== --}}
    <div class="page-content">
        <div class="content-card">
            <div class="content-header">
                <h1 class="page-title">{{ __('Listes Des Fonctions') }}</h1>
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
                            <th>{{ __('Nom Fonctions') }}</th>
                            <th>{{ __('Description Fonctions') }}</th>
                            <th style="width: 80px;">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody id="fonctions-table-body">
                        @forelse($fonction as $fonctions)
                            <tr id="fonction-row-{{ $fonctions->id }}" data-fonction-id="{{ $fonctions->id }}">
                                <td>
                                    <input type="checkbox" class="row-checkbox"
                                           name="fonction_id" value="{{ $fonctions->id }}"
                                           data-fonction-id="{{ $fonctions->id }}">
                                </td>
                                <td>
                                    <span class="font-medium text-gray-900">{{ $fonctions->nom_fonction ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="text-gray-600">{{ $fonctions->description_fonction ?? '-' }}</span>
                                </td>
                                <td>
                                    <div class="dropdown-container">
                                        <button class="dots-btn" onclick="toggleDropdown(this, event)">
                                            <i class="fa-solid fa-ellipsis"></i>
                                        </button>
                                        <div class="dropdown-menu-custom">
                                            <a href="{{ route('fonctions.edit', $fonctions->id) }}" class="dropdown-item-custom">
                                                <i class="fas fa-edit"></i> {{ __('Modifier') }}
                                            </a>
                                            <button onclick="confirmDelete({{ $fonctions->id }})" class="dropdown-item-custom" style="color: #dc2626;">
                                                <i class="fas fa-trash-alt"></i> {{ __('Supprimer') }}
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="empty-row">
                                <td colspan="4" class="empty-row">{{ __('Aucune fonction trouvée') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper" id="pagination-wrapper">
                {{ $fonction->links() }}
            </div>
        </div>
    </div>

    <style>
        /* ============================================ */
        /* STYLES MODERNES POUR LA PAGE FONCTIONS       */
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

            if (addBtn) {
                if (selectedCount > 0) addBtn.classList.add('disabled');
                else addBtn.classList.remove('disabled');
            }
            if (selectedCount === 1) {
                if (deleteBtn) deleteBtn.classList.remove('disabled');
                if (editBtn) editBtn.classList.remove('disabled');
            } else if (selectedCount > 1) {
                if (editBtn) editBtn.classList.add('disabled');
                if (deleteBtn) deleteBtn.classList.remove('disabled');
            } else {
                if (deleteBtn) deleteBtn.classList.add('disabled');
                if (editBtn) editBtn.classList.add('disabled');
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
                var fonctionId = selectedCheckbox.value;
                var editUrl = "{{ route('fonctions.edit', ':id') }}".replace(':id', fonctionId);
                window.location.href = editUrl;
            } else {
                Swal.fire('Information', 'Veuillez sélectionner une fonction.', 'info');
            }
        });

        // =====================================================
        // SUPPRESSION D'UNE LIGNE DU DOM
        // =====================================================
        function removeFonctionRow(fonctionId) {
            var row = document.getElementById('fonction-row-' + fonctionId);
            if (row) {
                row.classList.add('row-deleting');
                setTimeout(function() {
                    row.remove();
                    var remainingRows = document.querySelectorAll('#fonctions-table-body tr:not(#empty-row)');
                    if (remainingRows.length === 0) {
                        var tbody = document.getElementById('fonctions-table-body');
                        var emptyRow = document.getElementById('empty-row');
                        if (!emptyRow) {
                            var newEmptyRow = document.createElement('tr');
                            newEmptyRow.id = 'empty-row';
                            newEmptyRow.innerHTML = '<td colspan="4" class="empty-row">{{ __('Aucune fonction trouvée') }}</td>';
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
        // SUPPRESSION INDIVIDUELLE (Dropdown) - CORRIGÉE
        // =====================================================
        function confirmDelete(fonctionId) {
            Swal.fire({
                title: 'Confirmation',
                text: 'Êtes-vous sûr de vouloir supprimer cette fonction ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    var token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                    // ✅ Utiliser la bonne URL avec la méthode DELETE
                    var deleteUrl = "{{ url('fonctions/supprimerFonction') }}/" + fonctionId;

                    $.ajax({
                        url: deleteUrl,
                        type: 'POST',
                        data: {
                            _token: token,
                            _method: 'DELETE'
                        },
                        success: function(response) {
                            // ✅ Supprimer la ligne immédiatement
                            removeFonctionRow(fonctionId);
                            Swal.fire('Succès', 'La fonction a été supprimée.', 'success');
                        },
                        error: function(xhr) {
                            console.error('Erreur:', xhr);
                            if (xhr.status === 404) {
                                removeFonctionRow(fonctionId);
                                Swal.fire('Information', 'Cette fonction a déjà été supprimée.', 'info');
                            } else {
                                Swal.fire('Erreur', 'Une erreur est survenue lors de la suppression.', 'error');
                            }
                        }
                    });
                }
            });
        }

        // =====================================================
        // SUPPRESSION MULTIPLE (Barre d'actions) - CORRIGÉE
        // =====================================================
        document.getElementById('delete_button')?.addEventListener('click', function(e) {
            e.preventDefault();
            var selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked:not(#selectAllCheckbox)');
            if (selectedCheckboxes.length === 0) {
                Swal.fire('Information', 'Veuillez sélectionner au moins une fonction.', 'info');
                return;
            }

            var fonctionIds = [];
            selectedCheckboxes.forEach(cb => {
                fonctionIds.push(cb.value);
            });

            Swal.fire({
                title: 'Confirmation',
                text: 'Êtes-vous sûr de vouloir supprimer ' + selectedCheckboxes.length + ' fonction(s) ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    var completed = 0;
                    var total = fonctionIds.length;
                    var token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                    fonctionIds.forEach(function(fonctionId) {
                        // ✅ Utiliser la bonne URL avec la méthode DELETE
                        var deleteUrl = "{{ url('fonctions/supprimerFonction') }}/" + fonctionId;

                        $.ajax({
                            url: deleteUrl,
                            type: 'POST',
                            data: {
                                _token: token,
                                _method: 'DELETE'
                            },
                            success: function() {
                                removeFonctionRow(fonctionId);
                                completed++;
                                if (completed === total) {
                                    Swal.fire('Succès', 'Toutes les fonctions ont été supprimées.', 'success');
                                }
                            },
                            error: function(xhr) {
                                completed++;
                                if (xhr.status === 404) {
                                    removeFonctionRow(fonctionId);
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
    </script>
</x-app-layout>
