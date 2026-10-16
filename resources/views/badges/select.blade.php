<x-app-layout>
    {{-- Barre d'actions --}}
    <div class="action-bar">
        <div class="action-bar-inner">
            <div class="action-info">
                <i class="fas fa-id-badge"></i>
                <span>{{ __('Génération de badges') }}</span>
            </div>
            <div class="action-stats">
                <span class="stat-badge">
                    <i class="fas fa-users"></i>
                    {{ $users->count() }} employés
                </span>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="content-card">
            <div class="content-header">
                <div>
                    <h1 class="page-title">Sélectionnez les employés à imprimer</h1>
                    <p class="page-subtitle">Choisissez les employés pour lesquels vous souhaitez générer des badges</p>
                </div>
            </div>

            @if ($errors->has('matricule'))
                <div class="alert alert--error">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ $errors->first('matricule') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert--error">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('badges.generate') }}">
                @csrf

                <div class="selection-bar">
                    <label class="select-all-label">
                        <input type="checkbox" id="select-all" class="select-all-checkbox">
                        <span class="select-all-text">Sélectionner tous les employés</span>
                    </label>

                    <button type="submit" class="btn-generate">
                        <i class="fas fa-print"></i>
                        Générer les badges sélectionnés
                    </button>
                </div>

                <div class="employees-grid">
                    @foreach($users as $user)
                        <label class="employee-card">
                            <div class="employee-checkbox">
                                <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="employee-checkbox-input">
                                <span class="custom-checkbox"></span>
                            </div>
                            <div class="employee-info">
                                <div class="employee-avatar">
                                    @if($user->avatar && Storage::disk('public')->exists($user->avatar))
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar">
                                    @else
                                        <div class="avatar-placeholder">
                                            {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="employee-details">
                                    <div class="employee-name">
                                        {{ $user->first_name }} {{ $user->name }}
                                    </div>
                                    <div class="employee-function">
                                        <i class="fas fa-briefcase"></i>
                                        {{ $user->fonction->nom_fonction ?? 'Fonction inconnue' }}
                                    </div>
                                    <div class="employee-matricule">
                                        <i class="fas fa-id-card"></i>
                                        Matricule: <strong>{{ $user->matricule ?? 'Non défini' }}</strong>
                                    </div>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>

                @if($users->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-users-slash fa-3x mb-3 opacity-50"></i>
                        <p>Aucun employé trouvé</p>
                        <span class="empty-sub">Veuillez ajouter des employés pour générer des badges</span>
                    </div>
                @endif
            </form>
        </div>
    </div>

    {{-- Zone d'impression (cachée) --}}
    <div id="print-area" style="display: none;">
        @foreach($users as $user)
            <div class="badge-card">
                <div class="badge-header">
                    <div class="badge-logo">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 30px;">
                    </div>
                    <div class="badge-company">
                        {{ Auth::user()->company->name ?? 'Entreprise' }}
                    </div>
                </div>
                <div class="badge-body">
                    <div class="badge-photo">
                        @if($user->avatar && Storage::disk('public')->exists($user->avatar))
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Photo">
                        @else
                            <div class="badge-photo-placeholder">
                                {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="badge-info">
                        <div class="badge-name">{{ $user->first_name }} {{ $user->name }}</div>
                        <div class="badge-function">{{ $user->fonction->nom_fonction ?? 'Fonction' }}</div>
                        <div class="badge-matricule">
                            <span class="badge-label">Matricule :</span>
                            <span class="badge-value">{{ $user->matricule ?? 'N/A' }}</span>
                        </div>
                        <div class="badge-footer">
                            <div class="badge-qr">
                                <img src="{{ $user->qr_code ?? asset('images/qr-placeholder.png') }}" alt="QR Code" style="width: 40px; height: 40px;">
                            </div>
                            <div class="badge-validity">
                                <span class="badge-valid-label">Valable du</span>
                                <span class="badge-valid-date">{{ $user->contract_start_date ?? '---' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <style>
        /* ============================================ */
        /* STYLES MODERNES POUR LA PAGE BADGES          */
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

        .action-stats {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.15);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            color: white;
        }

        /* Page content */
        .page-content {
            padding-top: 46px;
        }

        .content-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--gl);
            padding: 24px;
            margin: 0 24px;
        }

        .content-header {
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }

        .page-subtitle {
            font-size: 0.8rem;
            color: #6b7280;
            margin-top: 4px;
        }

        /* Alertes */
        .alert--error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        /* Selection bar */
        .selection-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
            padding: 16px;
            background: var(--gtc);
            border-radius: 16px;
        }

        .select-all-label {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .select-all-checkbox {
            width: 18px;
            height: 18px;
            accent-color: var(--vp);
            cursor: pointer;
        }

        .select-all-text {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--gf);
        }

        .btn-generate {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--vp);
            color: white;
            border: none;
            border-radius: 40px;
            padding: 10px 24px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--t);
        }

        .btn-generate:hover {
            background: var(--vf);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(46,125,100,0.3);
        }

        /* Employees grid */
        .employees-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
            max-height: 65vh;
            overflow-y: auto;
            padding: 4px;
        }

        .employees-grid::-webkit-scrollbar {
            width: 6px;
        }

        .employees-grid::-webkit-scrollbar-track {
            background: var(--gtc);
            border-radius: 4px;
        }

        .employees-grid::-webkit-scrollbar-thumb {
            background: var(--vp);
            border-radius: 4px;
        }

        /* Employee card */
        .employee-card {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 16px;
            border: 1px solid var(--gl);
            border-radius: 16px;
            cursor: pointer;
            transition: var(--t);
            background: #fff;
        }

        .employee-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            border-color: var(--vp);
        }

        .employee-card:has(.employee-checkbox-input:checked) {
            border-color: var(--vp);
            background: var(--vc);
        }

        .employee-checkbox {
            position: relative;
            flex-shrink: 0;
        }

        .employee-checkbox-input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .custom-checkbox {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid var(--gl);
            border-radius: 6px;
            transition: var(--t);
            cursor: pointer;
        }

        .employee-checkbox-input:checked + .custom-checkbox {
            background: var(--vp);
            border-color: var(--vp);
            position: relative;
        }

        .employee-checkbox-input:checked + .custom-checkbox::after {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 11px;
            color: white;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .employee-info {
            display: flex;
            align-items: center;
            gap: 16px;
            flex: 1;
        }

        .employee-avatar {
            flex-shrink: 0;
        }

        .employee-avatar img {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            object-fit: cover;
            border: 2px solid var(--gl);
        }

        .avatar-placeholder {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--vp), var(--vf));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: bold;
        }

        .employee-details {
            flex: 1;
        }

        .employee-name {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--gf);
            margin-bottom: 4px;
        }

        .employee-function {
            font-size: 0.7rem;
            color: var(--gm);
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .employee-function i {
            font-size: 0.65rem;
            width: 14px;
        }

        .employee-matricule {
            font-size: 0.7rem;
            color: var(--gm);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .employee-matricule i {
            font-size: 0.65rem;
            width: 14px;
        }

        .employee-matricule strong {
            color: var(--vp);
            font-weight: 700;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #9ca3af;
        }

        .empty-sub {
            font-size: 0.75rem;
            display: block;
            margin-top: 8px;
        }

        /* ============================================ */
        /* STYLES D'IMPRESSION - UNIQUEMENT LA CARTE     */
        /* ============================================ */

        @media print {
            /* Masquer tout sauf la zone d'impression */
            body * {
                visibility: hidden;
            }

            #print-area,
            #print-area * {
                visibility: visible !important;
            }

            #print-area {
                display: block !important;
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 20px;
                background: white;
            }

            /* Style des badges pour l'impression */
            .badge-card {
                display: inline-block;
                width: 85mm;
                height: 54mm;
                background: white;
                border: 2px solid #2e7d64;
                border-radius: 8px;
                padding: 8px;
                margin: 5mm;
                page-break-inside: avoid;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            }

            .badge-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                border-bottom: 2px solid #2e7d64;
                padding-bottom: 4px;
                margin-bottom: 6px;
            }

            .badge-logo img {
                height: 25px !important;
            }

            .badge-company {
                font-size: 10px;
                font-weight: 700;
                color: #2e7d64;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .badge-body {
                display: flex;
                gap: 10px;
                align-items: center;
            }

            .badge-photo {
                flex-shrink: 0;
            }

            .badge-photo img,
            .badge-photo-placeholder {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                object-fit: cover;
                border: 2px solid #2e7d64;
            }

            .badge-photo-placeholder {
                background: linear-gradient(135deg, #2e7d64, #1a5a48);
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 18px;
                font-weight: bold;
            }

            .badge-info {
                flex: 1;
            }

            .badge-name {
                font-size: 13px;
                font-weight: 700;
                color: #1f2937;
                margin-bottom: 1px;
            }

            .badge-function {
                font-size: 9px;
                color: #6b7280;
                margin-bottom: 2px;
            }

            .badge-matricule {
                font-size: 8px;
                color: #4b5563;
                margin-bottom: 4px;
            }

            .badge-label {
                font-weight: 600;
                color: #6b7280;
            }

            .badge-value {
                font-weight: 700;
                color: #2e7d64;
            }

            .badge-footer {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-top: 2px;
                border-top: 1px solid #e5e7eb;
                padding-top: 4px;
            }

            .badge-qr img {
                width: 35px !important;
                height: 35px !important;
            }

            .badge-validity {
                text-align: right;
            }

            .badge-valid-label {
                display: block;
                font-size: 6px;
                color: #9ca3af;
                text-transform: uppercase;
                letter-spacing: 0.3px;
            }

            .badge-valid-date {
                font-size: 8px;
                font-weight: 600;
                color: #1f2937;
            }
        }

        /* Responsive */
        @media (max-width: 1280px) {
            .action-bar { left: 0; }
        }

        @media (max-width: 768px) {
            .page-content { padding: 46px 0 0 0; }
            .content-card { margin: 0 16px; padding: 16px; }
            .action-bar-inner { padding: 0 14px; }
            .selection-bar { flex-direction: column; align-items: flex-start; }
            .btn-generate { width: 100%; justify-content: center; }
            .employees-grid { grid-template-columns: 1fr; }
            .employee-info { flex-direction: column; text-align: center; }
            .employee-function, .employee-matricule { justify-content: center; }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('select-all');
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function () {
                    const checkboxes = document.querySelectorAll('.employee-checkbox-input');
                    checkboxes.forEach(cb => cb.checked = this.checked);
                });
            }
        });
    </script>
</x-app-layout>