<x-app-layout>
    <div class="settings-container">
        {{-- En-tête --}}
        
        <div class="settings-header">
            <h1 class="settings-title">{{ __('Paramètres de la société') }}</h1>
            <p class="settings-subtitle">Gérez les informations de votre entreprise et votre sécurité</p>
        </div>

        {{-- Alertes --}}
        @if (session('success'))
            <div id="session-success-alert-div" class="alert-success">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
                <button type="button" onclick="document.getElementById('session-success-alert-div').style.display='none'" class="alert-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div id="session-error-alert-div" class="alert-error">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
                <button type="button" onclick="document.getElementById('session-error-alert-div').style.display='none'" class="alert-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        {{-- Sections --}}
        <div class="settings-sections">
            {{-- Section Informations société --}}
            <div class="settings-card">
                <div class="card-header">
                    <div class="card-header-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="card-header-text">
                        <h3 class="card-title">Informations de la société</h3>
                        <p class="card-desc">Modifiez les informations de votre entreprise</p>
                    </div>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-company-profile-information-form')
                </div>
            </div>

            {{-- Section Mot de passe --}}
            <div class="settings-card">
                <div class="card-header">
                    <div class="card-header-icon card-header-icon-warning">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="card-header-text">
                        <h3 class="card-title">Sécurité</h3>
                        <p class="card-desc">Modifiez votre mot de passe</p>
                    </div>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>
    </div>

    <style>
        /* ============================================ */
        /* STYLES LÉGERS POUR LA PAGE PARAMÈTRES       */
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

        .settings-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 24px 28px;
        }

        /* En-tête */
        .settings-header {
            margin-bottom: 28px;
        }
        .settings-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--gf);
            margin: 0 0 6px 0;
            font-family: 'Syne', sans-serif;
        }
        .settings-subtitle {
            font-size: 0.85rem;
            color: var(--gm);
            margin: 0;
        }

        /* Alertes */
        .alert-success, .alert-error {
            position: relative;
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .alert-close {
            margin-left: auto;
            background: none;
            border: none;
            cursor: pointer;
            color: inherit;
            opacity: 0.6;
            transition: var(--t);
        }
        .alert-close:hover { opacity: 1; }

        /* Sections */
        .settings-sections {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* Cartes */
        .settings-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--gl);
            overflow: hidden;
            transition: var(--t);
        }
        .settings-card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.05);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 18px 24px;
            background: var(--gtc);
            border-bottom: 1px solid var(--gl);
        }
        .card-header-icon {
            width: 44px;
            height: 44px;
            background: var(--vp);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem;
        }
        .card-header-icon-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }
        .card-header-text {
            flex: 1;
        }
        .card-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--gf);
            margin: 0;
        }
        .card-desc {
            font-size: 0.7rem;
            color: var(--gm);
            margin: 4px 0 0 0;
        }
        .card-body {
            padding: 24px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .settings-container {
                padding: 16px;
            }
            .card-header {
                flex-direction: column;
                text-align: center;
            }
            .card-header-icon {
                margin-bottom: 8px;
            }
        }
    </style>
</x-app-layout>
