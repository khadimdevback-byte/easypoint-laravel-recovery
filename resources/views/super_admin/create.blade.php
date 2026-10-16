<x-app-layout>
    <div class="add-superadmin-container">
        {{-- En-tête --}}
        <div class="add-superadmin-header">
            <div class="header-content">
                <h1 class="header-title">{{ __('Ajouter un Super Administrateur') }}</h1>
                <p class="header-subtitle">{{ __('Créez un nouveau compte administrateur') }}</p>
            </div>
            <div class="header-icon"><i class="fas fa-user-shield"></i></div>
        </div>

        {{-- Alertes --}}
        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
                <button type="button" onclick="this.parentElement.remove()" class="alert-close"><i class="fas fa-times"></i></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
                <button type="button" onclick="this.parentElement.remove()" class="alert-close"><i class="fas fa-times"></i></button>
            </div>
        @endif

        {{-- Formulaire --}}
        <div class="add-superadmin-card">
            <form method="POST" action="{{ route('super-admin.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="user_type" value="super_admin" required>

                {{-- Informations personnelles --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="fas fa-user"></i></div>
                        <div>
                            <h3 class="section-title">{{ __('Informations personnelles') }}</h3>
                            <p class="section-desc">{{ __('Identité du super administrateur') }}</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">{{ __('Nom') }} <span class="required">*</span></label>
                                <input type="text" name="name" class="form-input" placeholder="{{ __('Nom de famille') }}" value="{{ old('name') }}" required autofocus>
                                <x-input-error :messages="$errors->get('name')" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Prénom(s)') }} <span class="required">*</span></label>
                                <input type="text" name="first_name" class="form-input" placeholder="{{ __('Prénom(s)') }}" value="{{ old('first_name') }}" required>
                                <x-input-error :messages="$errors->get('first_name')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Contact & Sécurité --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="fas fa-envelope"></i></div>
                        <div>
                            <h3 class="section-title">{{ __('Contact & Sécurité') }}</h3>
                            <p class="section-desc">{{ __('Email, mot de passe et informations sociales') }}</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">{{ __('Email') }} <span class="required">*</span></label>
                                <input type="email" name="email" class="form-input" placeholder="{{ __('exemple@email.com') }}" value="{{ old('email') }}" required>
                                <x-input-error :messages="$errors->get('email')" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Mot de passe') }} <span class="required">*</span></label>
                                <div class="password-wrapper">
                                    <input type="password" id="password" name="password" class="form-input" placeholder="{{ __('Mot de passe') }}" required>
                                    <button type="button" onclick="togglePassword()" class="password-toggle">
                                        <i class="fas fa-eye" id="password-eye"></i>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password')" />
                            </div>
                        </div>
                        <div class="form-row" style="margin-top: 20px;">
                            {{-- <div class="form-group">
                                <label class="form-label">{{ __('Numéro Social (SS)') }}</label>
                                <input type="text" name="n_social" class="form-input" placeholder="{{ __('Numéro Social') }}" value="{{ old('n_social') }}">
                                <x-input-error :messages="$errors->get('n_social')" />
                            </div> --}}
                            <div class="form-group">
                                <label class="form-label">{{ __('Statut') }} <span class="required">*</span></label>
                                <div class="select-wrapper">
                                    <select name="status" class="form-select" required>
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>{{ __('Actif') }}</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>{{ __('Inactif') }}</option>
                                    </select>
                                    <i class="fas fa-chevron-down select-icon"></i>
                                </div>
                                <x-input-error :messages="$errors->get('status')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Photo de profil --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="fas fa-image"></i></div>
                        <div>
                            <h3 class="section-title">{{ __('Photo de profil') }}</h3>
                            <p class="section-desc">{{ __('Photo du super administrateur') }}</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">{{ __('Photo') }}</label>
                                <div class="file-upload-container">
                                    <input type="file" name="avatar" id="avatar" class="file-input" accept="image/*" onchange="previewAvatar(this)">
                                    <label for="avatar" class="file-label">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <span>{{ __('Choisir une photo') }}</span>
                                    </label>
                                    <div id="avatar-preview" class="avatar-preview" style="display: none;">
                                        <img id="avatar-preview-img" src="#" alt="Aperçu de la photo">
                                        <button type="button" onclick="clearAvatar()" class="remove-avatar">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                    </div>
                                </div>
                                <p class="file-hint">Formats acceptés : JPG, PNG, GIF. Max 2MB</p>
                                <x-input-error :messages="$errors->get('avatar')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Boutons --}}
                <div class="form-actions">
                    <button type="button" onclick="history.back()" class="btn-cancel">
                        <i class="fas fa-times"></i> {{ __('Annuler') }}
                    </button>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> {{ __('Enregistrer') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
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

        .add-superadmin-container { max-width: 1200px; margin: 0 auto; padding: 24px 28px; }

        .add-superadmin-header {
            display: flex; align-items: center; justify-content: space-between;
            background: linear-gradient(135deg, #2e7d64, #1a5a48);
            border-radius: 20px; padding: 28px 32px; margin-bottom: 28px;
            position: relative; overflow: hidden;
        }
        .header-content { position: relative; z-index: 2; }
        .header-title { font-size: 1.5rem; font-weight: 800; color: white; margin: 0 0 6px 0; }
        .header-subtitle { font-size: 0.85rem; color: rgba(255,255,255,0.7); margin: 0; }
        .header-icon { position: relative; z-index: 1; font-size: 4rem; opacity: 0.15; color: white; }
        .header-icon i { font-size: 4rem; }

        .alert { display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-close { margin-left: auto; background: none; border: none; cursor: pointer; opacity: 0.6; }

        .add-superadmin-card { background: #fff; border-radius: 20px; border: 1px solid var(--gl); overflow: hidden; }

        .form-section { border-bottom: 1px solid var(--gl); }
        .form-section:last-child { border-bottom: none; }

        .section-header { display: flex; align-items: center; gap: 14px; padding: 16px 24px; background: var(--gtc); }
        .section-icon { width: 44px; height: 44px; background: var(--vp); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.1rem; }
        .section-title { font-size: 1rem; font-weight: 700; color: var(--gf); margin: 0; }
        .section-desc { font-size: 0.7rem; color: var(--gm); margin: 2px 0 0 0; }
        .section-body { padding: 24px; }

        .form-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }

        .form-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: var(--gm); letter-spacing: 0.5px; }
        .required { color: #dc2626; }

        .form-input, .form-textarea {
            width: 100%; padding: 11px 14px; border: 1.5px solid var(--gl);
            border-radius: 12px; font-size: 0.85rem; background: white;
            transition: var(--t);
        }
        .form-input:focus, .form-textarea:focus { outline: none; border-color: var(--vp); box-shadow: 0 0 0 3px rgba(46,125,100,0.1); }

        .select-wrapper { position: relative; }
        .form-select {
            width: 100%; padding: 11px 14px; border: 1.5px solid var(--gl);
            border-radius: 12px; font-size: 0.85rem; background: white;
            appearance: none;
            transition: var(--t);
        }
        .form-select:focus { outline: none; border-color: var(--vp); box-shadow: 0 0 0 3px rgba(46,125,100,0.1); }
        .select-icon { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: var(--gm); font-size: 0.75rem; pointer-events: none; }

        /* Password wrapper */
        .password-wrapper {
            position: relative;
            width: 100%;
        }
        .password-wrapper .form-input {
            padding-right: 44px;
        }
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gm);
            cursor: pointer;
            font-size: 1rem;
            padding: 4px;
            transition: var(--t);
        }
        .password-toggle:hover {
            color: var(--vp);
        }

        /* File upload */
        .file-upload-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .file-input {
            display: none;
        }
        .file-label {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 11px 20px;
            background: var(--gtc);
            border: 1.5px solid var(--gl);
            border-radius: 12px;
            cursor: pointer;
            font-size: 0.85rem;
            color: var(--gf);
            transition: var(--t);
            width: fit-content;
        }
        .file-label:hover {
            background: var(--vp);
            color: white;
            border-color: var(--vp);
        }
        .file-label i {
            font-size: 1rem;
        }
        .avatar-preview {
            position: relative;
            width: 100px;
            height: 100px;
            border: 2px solid var(--gl);
            border-radius: 12px;
            overflow: hidden;
            background: var(--gtc);
        }
        .avatar-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .remove-avatar {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc2626;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            transition: var(--t);
        }
        .remove-avatar:hover {
            background: #991b1b;
        }
        .file-hint {
            font-size: 0.7rem;
            color: var(--gm);
            margin-top: 5px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 16px;
            padding: 20px 24px;
            background: var(--gtc);
            border-top: 1px solid var(--gl);
        }
        .btn-submit, .btn-cancel {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 28px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: var(--t);
        }
        .btn-submit { background: var(--vp); color: white; }
        .btn-submit:hover { background: var(--vf); transform: translateY(-1px); }
        .btn-cancel { background: var(--gtc); color: var(--gf); border: 1px solid var(--gl); }
        .btn-cancel:hover { background: #e5e7eb; }

        @media (max-width: 768px) {
            .add-superadmin-container { padding: 16px; }
            .add-superadmin-header { padding: 20px; }
            .header-icon { display: none; }
            .form-row { grid-template-columns: 1fr; gap: 16px; }
        }
    </style>

    <script>
        // Toggle mot de passe visible/caché
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('password-eye');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }

        // Aperçu de la photo avant upload
        function previewAvatar(input) {
            const preview = document.getElementById('avatar-preview');
            const previewImg = document.getElementById('avatar-preview-img');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        function clearAvatar() {
            const fileInput = document.getElementById('avatar');
            const preview = document.getElementById('avatar-preview');
            const previewImg = document.getElementById('avatar-preview-img');

            fileInput.value = '';
            previewImg.src = '#';
            preview.style.display = 'none';
        }
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</x-app-layout>
