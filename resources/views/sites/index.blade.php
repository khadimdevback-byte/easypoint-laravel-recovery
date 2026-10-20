<x-app-layout>
    <div class="page-content">
        <div class="content-card">
            {{-- Barre d'actions --}}
            @if (is_admin() || is_manager())
                <div class="action-bar">
                    <div class="action-bar-inner">
                        <a href="{{ route('sites.create') }}" id="add_button" class="action-btn">
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
                            <span>{{ __('Activer / Désactiver') }}</span>
                        </a>
                    </div>
                </div>
            @endif

            {{-- En-tête --}}
            <div class="content-header">
                <h1 class="page-title">{{ __('Lieux de travail') }}</h1>
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

            {{-- Alertes session --}}
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
                            <th>{{ __('Nom du site') }}</th>
                            <th>{{ __('Propriétaire') }}</th>
                            <th>{{ __('Adresse') }}</th>
                            <th>{{ __('Marquage tardif après') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th style="width: 80px;">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody id="tbody">
                        @forelse($sites as $site)
                            @php
                                $qrCodeHtml = QrCode::size(200)->generate($site->qr_code);
                                $addressParts = array_values(array_filter(array_map('trim', explode(',', $site->address ?? ''))));
                                preg_match('/\b\d{5}\b/u', $site->address ?? '', $postcodeMatch);
                                $addressCity = null;
                                foreach ($addressParts as $index => $part) {
                                    if (preg_match('/\barrondissement\b/iu', $part) && isset($addressParts[$index + 1])) {
                                        $addressCity = $addressParts[$index + 1];
                                        break;
                                    }
                                }
                                $shortAddress = $addressParts[0] ?? '-';
                                if (! empty($postcodeMatch[0])) {
                                    $shortAddress .= ', '.$postcodeMatch[0].($addressCity ? ' '.$addressCity : '');
                                } elseif (isset($addressParts[1])) {
                                    $shortAddress .= ', '.$addressParts[1];
                                }
                            @endphp
                            <tr id="site-row-{{ $site->id }}">
                                <td>
                                    <input type="checkbox" class="row-checkbox"
                                           name="site_id" value="{{ $site->id }}"
                                           data-site-id="{{ $site->id }}">
                                </td>
                                <td>
                                    <a href="" class="site-link">
                                        {{ $site->name ?? '-' }}
                                    </a>
                                </td>
                                <td>{{ $site->customer->name ?? '-' }}</td>
                                <td>{{ $shortAddress }}</td>
                                <td>{{ $site->late_mark_duration ?? '-' }} min</td>
                                <td>
                                    <span class="status-badge {{ $site->status == 'active' ? 'status-badge--active' : 'status-badge--inactive' }}">
                                        {{ __($site->status) ?? '-' }}
                                    </span>
                                </td>
                                @if (is_admin() || is_manager())
                                    <td class="action-cell">
                                        <div class="dropdown-container">
                                            <button class="dots-btn" onclick="toggleDropdown(this, event)">
                                                <i class="fa-solid fa-ellipsis"></i>
                                            </button>
                                            <div class="dropdown-menu-custom">
                                                <button onclick="printQRCode({{ $site->id }}, '{{ addslashes($site->name) }}', `{{ addslashes($qrCodeHtml) }}`)" class="dropdown-item-custom">
                                                    <i class="fas fa-print"></i> {{ __('Imprimer QR Code') }}
                                                </button>
                                                <a href="{{ route('sites.edit', $site->id) }}" class="dropdown-item-custom">
                                                    <i class="fas fa-edit"></i> {{ __('Modifier') }}
                                                </a>
                                                <button onclick="confirmDisable({{ $site->id }})" class="dropdown-item-custom">
                                                    <i class="fas fa-ban"></i> {{ __('Désactiver') }}
                                                </button>
                                                <button onclick="confirmDelete({{ $site->id }})" class="dropdown-item-custom" style="color: #dc2626;">
                                                    <i class="fas fa-trash-alt"></i> {{ __('Supprimer') }}
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="empty-row">{{ __('No Content') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                {{ $sites->links() }}
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
                    <button onclick="closeDisableModal()" class="modal-btn-cancel">{{ __('Annuler') }}</button>
                    <button id="confirmDisable" class="modal-btn-confirm">{{ __('Enregistrer') }}</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* ============================================ */
        /* STYLES                                      */
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

        .page-content {
            padding: 46px 24px 24px 24px;
        }

        .content-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--gl);
            padding: 24px;
            overflow: visible;
            position: relative;
        }

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

        .content-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
        }

        .page-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }

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

        .site-link {
            color: #1f2937;
            text-decoration: none;
            font-weight: 500;
        }
        .site-link:hover { color: #2e7d64; }

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

        .action-cell { position: relative; }
        .dropdown-container { position: relative; display: inline-block; }
        .dropdown-menu-custom {
            position: fixed;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            min-width: 160px;
            z-index: 99999;
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

        @media (max-width: 1280px) { .action-bar { left: 0; } }
        @media (max-width: 768px) {
            .page-content { padding: 46px 16px 16px 16px; }
            .content-card { padding: 16px; }
            .action-bar-inner { padding: 0 14px; gap: 0; overflow-x: auto; }
            .content-header { flex-direction: column; align-items: flex-start; }
        }
    </style>

    <!-- AJOUT DE JQUERY ET SWEETALERT2 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                menu.style.left = (rect.right - 170) + 'px';
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

        // Bouton EDIT
        document.getElementById('edit_button')?.addEventListener('click', function(e) {
            e.preventDefault();
            var selectedCheckbox = document.querySelector('.row-checkbox:checked:not(#selectAllCheckbox)');
            if (selectedCheckbox) {
                var siteId = selectedCheckbox.value;
                window.location.href = "{{ url('sites') }}/" + siteId + "/edit";
            } else {
                Swal.fire('Information', 'Veuillez sélectionner un site.', 'info');
            }
        });

        // Bouton DELETE (suppression multiple avec Ajax)
        document.getElementById('delete_button')?.addEventListener('click', function(e) {
            e.preventDefault();
            var selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked:not(#selectAllCheckbox)');
            if (selectedCheckboxes.length === 0) {
                Swal.fire('Information', 'Veuillez sélectionner au moins un site.', 'info');
                return;
            }

            Swal.fire({
                title: 'Confirmation',
                text: 'Êtes-vous sûr de vouloir supprimer ' + selectedCheckboxes.length + ' site(s) ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    var completedRequests = 0;
                    var totalRequests = selectedCheckboxes.length;

                    selectedCheckboxes.forEach(cb => {
                        var siteId = cb.value;

                        $.ajax({
                            url: "{{ url('sites') }}/" + siteId,
                            type: 'DELETE',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function(response) {
                                // Supprimer la ligne du tableau
                                $('#site-row-' + siteId).remove();
                                completedRequests++;
                                if (completedRequests === totalRequests) {
                                    location.reload();
                                }
                            },
                            error: function(xhr) {
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

        // Bouton DISABLE
        let currentDisableId = null;
        document.getElementById('disabled_button')?.addEventListener('click', function(e) {
            e.preventDefault();
            var selectedCheckbox = document.querySelector('.row-checkbox:checked:not(#selectAllCheckbox)');
            if (!selectedCheckbox) {
                Swal.fire('Information', 'Veuillez sélectionner un site.', 'info');
                return;
            }
            if (document.querySelectorAll('.row-checkbox:checked:not(#selectAllCheckbox)').length > 1) {
                Swal.fire('Information', 'Veuillez sélectionner un seul site pour la désactivation.', 'info');
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

            $.ajax({
                url: "{{ url('sites/disabled') }}/" + currentDisableId,
                type: 'PATCH',
                data: {
                    _token: '{{ csrf_token() }}',
                    reason: reason
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    Swal.fire('Erreur', 'Une erreur est survenue lors de la désactivation.', 'error');
                }
            });
            closeDisableModal();
        });

        // Fonction confirmDelete (suppression individuelle)
        function confirmDelete(siteId) {
            Swal.fire({
                title: 'Confirmation',
                text: 'Êtes-vous sûr de vouloir supprimer ce site ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('sites') }}/" + siteId,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        dataType: 'json', // force le parsing JSON pour détecter la vraie cause
                        success: function(response) {
                            $('#site-row-' + siteId).fadeOut(200, function () { $(this).remove(); });
                            Swal.fire('Succès', 'Site supprimé avec succès.', 'success');
                        },
                        error: function(xhr) {
                            // DEBUG temporaire : regarde la console pour voir la vraie réponse serveur
                            console.error('Statut HTTP:', xhr.status);
                            console.error('Réponse brute:', xhr.responseText);

                            if (xhr.status === 419) {
                                Swal.fire('Session expirée', 'Merci de recharger la page et réessayer.', 'warning');
                            } else if (xhr.status === 200) {
                                // Le serveur a répondu 200 mais pas en JSON valide (ex: vue Blade ou redirection)
                                // → la suppression a probablement réussi côté serveur
                                $('#site-row-' + siteId).fadeOut(200, function () { $(this).remove(); });
                                Swal.fire('Succès', 'Site supprimé avec succès.', 'success');
                            } else {
                                Swal.fire('Erreur', 'Une erreur est survenue lors de la suppression (code ' + xhr.status + ').', 'error');
                            }
                        }
                    });
                }
            });
        }

        function confirmDisable(siteId) {
            // Coche uniquement ce site pour synchroniser l'état globale avec le modal
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.checked = (cb.value == siteId && !cb.id.includes('selectAllCheckbox'));
            });
            updateButtons();
            currentDisableId = siteId;
            document.getElementById('disableModal').classList.remove('hidden');
        }

        // Impression QR Code
        function printQRCode(siteId, siteName, qrCodeHtml) {
            var printWindow = window.open('', '_blank', 'width=550,height=650');
            printWindow.document.write(`
                <html>
                <head>
                    <title>QR Code - ${escapeHtml(siteName)}</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            text-align: center;
                            margin: 0;
                            padding: 30px;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            min-height: 100vh;
                            background: white;
                        }
                        .qr-container {
                            margin: 0 auto;
                            text-align: center;
                            padding: 20px;
                            border: 2px solid #e5e7eb;
                            border-radius: 10px;
                            background: white;
                        }
                        .qr-title {
                            font-size: 20px;
                            margin-bottom: 25px;
                            color: #1f2937;
                            font-weight: 600;
                        }
                        .qr-code-wrapper {
                            margin: 20px 0;
                            display: flex;
                            justify-content: center;
                        }
                        .qr-code-wrapper svg,
                        .qr-code-wrapper img {
                            max-width: 250px;
                            height: auto;
                        }
                        .site-info {
                            margin-top: 20px;
                            padding-top: 10px;
                            border-top: 1px solid #e5e7eb;
                            font-size: 12px;
                            color: #6b7280;
                        }
                        @media print {
                            body {
                                margin: 0;
                                padding: 0;
                            }
                            .qr-container {
                                border: none;
                                padding: 10px;
                            }
                        }
                    </style>
                </head>
                <body>
                    <div class="qr-container">
                        <h2 class="qr-title">QR Code : ${escapeHtml(siteName)}</h2>
                        <div class="qr-code-wrapper">
                            ${qrCodeHtml}
                        </div>
                        <div class="site-info">
                            Site ID: ${siteId} | Généré le ${new Date().toLocaleDateString()}
                        </div>
                    </div>
                    <script>
                        window.onload = function() {
                            setTimeout(function() {
                                window.print();
                                setTimeout(function() {
                                    window.close();
                                }, 500);
                            }, 500);
                        };
                    <\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        }

        function escapeHtml(text) {
            if (!text) return '';
            var div = document.createElement('div');
            div.appendChild(document.createTextNode(text));
            return div.innerHTML;
        }
    </script>
</x-app-layout>
