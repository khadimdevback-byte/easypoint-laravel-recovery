<x-app-layout>
    <div class="add-user-simple-container">
        {{-- En-tête avec dégradé --}}
        <div class="add-user-header">
            <div class="header-content">
                <h1 class="header-title">{{ __('Paramètres de profil') }}</h1>
                <p class="header-subtitle">{{ __('Gérez vos informations et votre sécurité') }}</p>
            </div>
            <div class="header-icon">
                <i class="fas fa-user-cog"></i>
            </div>
        </div>

        {{-- Alertes --}}
        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
                <button type="button" onclick="this.parentElement.remove()" class="alert-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
                <button type="button" onclick="this.parentElement.remove()" class="alert-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        {{-- Carte principale --}}
        <div class="add-user-card">
            {{-- Section Informations personnelles --}}
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h3 class="section-title">{{ __('Informations personnelles') }}</h3>
                        <p class="section-desc">{{ __('Mettez à jour vos coordonnées') }}</p>
                    </div>
                </div>
                <div class="section-body">
                    @if (is_super_admin())
                        <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="profile-form">
                            @csrf
                            @method('patch')
                            <input type="hidden" name="user_type" value="{{ $user->user_type }}" required>
                            <input type="hidden" name="company_id" value="{{ $user->company_id }}" required>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Nom') }} <span class="required">*</span></label>
                                    <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required autofocus>
                                    @error('name')
                                        <p class="text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ __('Prénoms') }} <span class="required">*</span></label>
                                    <input type="text" name="first_name" class="form-input" value="{{ old('first_name', $user->first_name) }}" required>
                                    @error('first_name')
                                        <p class="text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row mt-4">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Téléphone') }} <span class="required">*</span></label>
                                    <input type="text" name="phone" class="form-input" value="{{ old('phone', $user->phone) }}" required>
                                    @error('phone')
                                        <p class="text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ __('Email') }} <span class="required">*</span></label>
                                    <input type="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <p class="text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row mt-4">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Adresse') }} <span class="required">*</span></label>
                                    <input type="text" name="address" class="form-input" value="{{ old('address', $user->address) }}" required>
                                    @error('address')
                                        <p class="text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ __('Photo de profil') }}</label>
                                    <input type="file" name="avatar" class="form-input" accept="image/*">
                                    @error('avatar')
                                        <p class="text-red-500">{{ $message }}</p>
                                    @enderror
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" id="user-avatar"
                                            class="mt-3 w-20 h-20 object-cover border-2 border-gray-300 rounded-lg">
                                    @else
                                        <div class="mt-3 w-20 h-20 flex items-center justify-center bg-gray-200 border-2 border-gray-300 rounded-lg">
                                            <span class="text-2xl font-bold text-gray-600">
                                                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-actions-submit">
                                <button type="submit" class="btn-submit">
                                    <i class="fas fa-save"></i> {{ __('Enregistrer') }}
                                </button>
                            </div>
                        </form>
                    @else
                        <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="profile-form">
                            @csrf
                            @method('patch')
                            <input type="hidden" name="user_type" value="{{ $user->user_type }}" required>
                            <input type="hidden" name="company_id" value="{{ $user->company_id }}" required>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Nom') }} <span class="required">*</span></label>
                                    <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required autofocus>
                                    @error('name')
                                        <p class="text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ __('Prénoms') }} <span class="required">*</span></label>
                                    <input type="text" name="first_name" class="form-input" value="{{ old('first_name', $user->first_name) }}" required>
                                    @error('first_name')
                                        <p class="text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row mt-4">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Téléphone') }} <span class="required">*</span></label>
                                    <input type="text" name="phone" class="form-input" value="{{ old('phone', $user->phone) }}" required>
                                    @error('phone')
                                        <p class="text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ __('Email') }} <span class="required">*</span></label>
                                    <input type="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <p class="text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row mt-4">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Adresse') }} <span class="required">*</span></label>
                                    <input type="text" name="address" class="form-input" value="{{ old('address', $user->address) }}" required>
                                    @error('address')
                                        <p class="text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ __('Photo de profil') }}</label>
                                    <input type="file" name="avatar" class="form-input" accept="image/*">
                                    @error('avatar')
                                        <p class="text-red-500">{{ $message }}</p>
                                    @enderror
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" id="user-avatar"
                                            class="mt-3 w-20 h-20 object-cover border-2 border-gray-300 rounded-lg">
                                    @else
                                        <div class="mt-3 w-20 h-20 flex items-center justify-center bg-gray-200 border-2 border-gray-300 rounded-lg">
                                            <span class="text-2xl font-bold text-gray-600">
                                                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-actions-submit">
                                <button type="submit" class="btn-submit">
                                    <i class="fas fa-save"></i> {{ __('Enregistrer') }}
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Section Sécurité --}}
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div>
                        <h3 class="section-title">{{ __('Sécurité') }}</h3>
                        <p class="section-desc">{{ __('Modifiez votre mot de passe') }}</p>
                    </div>
                </div>
                <div class="section-body">
                    <form method="post" action="{{ route('password.update') }}" class="profile-form">
                        @csrf
                        @method('put')
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">{{ __('Mot de passe actuel') }} <span class="required">*</span></label>
                                <input type="password" name="current_password" class="form-input" placeholder="{{ __('Mot de passe actuel') }}" required>
                                @error('current_password')
                                    <p class="text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row mt-4">
                            <div class="form-group">
                                <label class="form-label">{{ __('Nouveau mot de passe') }} <span class="required">*</span></label>
                                <input type="password" name="password" class="form-input" placeholder="{{ __('Nouveau mot de passe') }}" required>
                                @error('password')
                                    <p class="text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Confirmer le mot de passe') }} <span class="required">*</span></label>
                                <input type="password" name="password_confirmation" class="form-input" placeholder="{{ __('Confirmer le mot de passe') }}" required>
                            </div>
                        </div>
                        <div class="form-actions-submit">
                            <button type="submit" class="btn-submit">
                                <i class="fas fa-key"></i> {{ __('Changer le mot de passe') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* ============================================ */
        /* DESIGN MODERNE - PAGE PARAMÈTRES PROFIL      */
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

        .add-user-simple-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 24px 28px;
        }

        /* En-tête */
        .add-user-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(135deg, #2e7d64, #1a5a48);
            border-radius: 20px;
            padding: 28px 32px;
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
        }
        .header-content { position: relative; z-index: 2; }
        .header-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: white;
            margin: 0 0 6px 0;
            font-family: 'Syne', sans-serif;
        }
        .header-subtitle {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.7);
            margin: 0;
        }
        .header-icon {
            position: relative;
            z-index: 1;
            font-size: 4rem;
            opacity: 0.15;
            color: white;
        }
        .header-icon i { font-size: 4rem; }

        /* Alertes */
        .alert {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-close {
            margin-left: auto;
            background: none;
            border: none;
            cursor: pointer;
            opacity: 0.6;
            transition: var(--t);
        }
        .alert-close:hover { opacity: 1; }

        /* Carte principale */
        .add-user-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--gl);
            overflow: hidden;
        }

        /* Sections */
        .form-section {
            border-bottom: 1px solid var(--gl);
        }
        .form-section:last-child {
            border-bottom: none;
        }
        .section-header {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 24px;
            background: var(--gtc);
        }
        .section-icon {
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
        .section-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--gf);
            margin: 0;
        }
        .section-desc {
            font-size: 0.7rem;
            color: var(--gm);
            margin: 2px 0 0 0;
        }
        .section-body {
            padding: 24px;
        }

        /* Formulaires */
        .profile-form {
            max-width: 100%;
        }

        /* Grille */
        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .form-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--gm);
            letter-spacing: 0.5px;
        }
        .required { color: #dc2626; }

        .form-input {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid var(--gl);
            border-radius: 12px;
            font-size: 0.85rem;
            transition: var(--t);
            background: white;
            font-family: inherit;
        }
        .form-input:focus {
            outline: none;
            border-color: var(--vp);
            box-shadow: 0 0 0 3px rgba(46,125,100,0.1);
        }
        select.form-input {
            appearance: auto;
            cursor: pointer;
        }
        input[type="file"].form-input {
            padding: 8px 14px;
        }

        /* Messages d'erreur */
        .text-red-500 {
            color: #dc2626;
            font-size: 0.75rem;
            margin-top: 4px;
        }

        /* Boutons */
        .form-actions-submit {
            display: flex;
            justify-content: flex-end;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid var(--gl);
        }

        .btn-submit {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 28px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--t);
            border: none;
            background: var(--vp);
            color: white;
            text-decoration: none;
        }
        .btn-submit:hover {
            background: var(--vf);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(46,125,100,0.3);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .add-user-simple-container { padding: 16px; }
            .add-user-header { padding: 20px; }
            .header-icon { display: none; }
            .form-row { grid-template-columns: 1fr; gap: 16px; }
            .section-header { padding: 14px 20px; }
            .section-body { padding: 20px; }
            .form-actions-submit { justify-content: center; }
            .btn-submit { width: 100%; justify-content: center; }
        }
    </style>

    <script>
        const fileInput = document.querySelector('input[name="avatar"]');
        const userAvatar = document.getElementById('user-avatar');

        if (fileInput) {
            fileInput.addEventListener('change', (event) => {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        if (userAvatar) {
                            userAvatar.src = e.target.result;
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    </script>
</x-app-layout>
