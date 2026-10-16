<x-app-layout>
    @php
        $userTypesTitle = [
            'manager' => __('Add Manager'),
            'controller' => __('Ajouter Un Controlleur'),
            'user' => __('Add Employee'),
            'customer' => __('Add Customer'),
        ];
    @endphp

    <div class="add-user-simple-container">
        {{-- En-tête avec dégradé --}}
        <div class="add-user-header">
            <div class="header-content">
                <h1 class="header-title">{{ $userTypesTitle[$user_type] ?? __('Ajouter un utilisateur') }}</h1>
                <p class="header-subtitle">{{ __('Ajoutez un nouvel utilisateur à votre plateforme') }}</p>
            </div>
            <div class="header-icon">
                <i class="fas fa-user-plus"></i>
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
        
        {{--Errors--}}
        @if (session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
                <button type="button" onclick="this.parentElement.remove()" class="alert-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        {{-- Formulaire --}}
        <div class="add-user-card">
            <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="user_type" value="{{ $user_type }}" required>
                <input type="hidden" name="company_id" value="{{ Auth::user()->company_id }}" required>

                {{-- Section Identité --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <h3 class="section-title">{{ __('Identité') }}</h3>
                            <p class="section-desc">{{ __('Informations personnelles') }}</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">{{ __('Nom') }} <span class="required">*</span></label>
                                <input type="text" name="name" class="form-input" placeholder="{{ __('Nom') }}" value="{{ old('name') }}" required>
                                <x-input-error :messages="$errors->get('name')" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Prénoms') }} <span class="required">*</span></label>
                                <input type="text" name="first_name" class="form-input" placeholder="{{ __('Prénoms') }}" value="{{ old('first_name') }}" required>
                                <x-input-error :messages="$errors->get('first_name')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section Contact --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-address-card"></i>
                        </div>
                        <div>
                            <h3 class="section-title">{{ __('Contact') }}</h3>
                            <p class="section-desc">{{ __('Coordonnées') }}</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">{{ __('Téléphone') }} <span class="required">*</span></label>
                                <input type="tel" name="phone" class="form-input" placeholder="{{ __('+228 XX XXX XXX') }}" value="{{ old('phone') }}" required>
                                <x-input-error :messages="$errors->get('phone')" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Email') }} <span class="required">*</span></label>
                                <input type="email" name="email" class="form-input" placeholder="{{ __('exemple@email.com') }}" value="{{ old('email') }}" required>
                                <x-input-error :messages="$errors->get('email')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section Coordonnées --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h3 class="section-title">{{ __('Coordonnées') }}</h3>
                            <p class="section-desc">{{ __('Adresse et statut') }}</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">{{ __('Adresse') }} <span class="required">*</span></label>
                                <input type="text" name="address" class="form-input" placeholder="{{ __('Adresse') }}" value="{{ old('address') }}" required>
                                <x-input-error :messages="$errors->get('address')" />
                            </div>
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

                {{-- Section Authentification --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div>
                            <h3 class="section-title">{{ __('Authentification') }}</h3>
                            <p class="section-desc">{{ __('Mot de passe') }}</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">{{ __('Mot de passe') }} <span class="required">*</span></label>
                                <input type="password" name="password" class="form-input" placeholder="{{ __('Mot de passe') }}" required>
                                <x-input-error :messages="$errors->get('password')" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Confirmation') }} <span class="required">*</span></label>
                                <input type="password" name="password_confirmation" class="form-input" placeholder="{{ __('Confirmer') }}" required>
                                <x-input-error :messages="$errors->get('password_confirmation')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section Photo --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-camera"></i>
                        </div>
                        <div>
                            <h3 class="section-title">{{ __('Photo') }}</h3>
                            <p class="section-desc">{{ __('Photo de profil') }}</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">{{ __('Photo') }}</label>
                                <div class="file-upload">
                                    <input type="file" name="avatar" id="avatar" class="file-input" accept="image/*">
                                    <label for="avatar" class="file-label">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <span id="file-name">{{ __('Choisir une photo') }}</span>
                                    </label>
                                    <div id="image-preview" class="image-preview"></div>
                                </div>
                                <p class="form-hint">{{ __('Formats acceptés : JPG, PNG. Max 2MB') }}</p>
                                <x-input-error :messages="$errors->get('avatar')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section Contact d'urgence AVEC TOGGLE --}}
                <div class="form-section">
                    <div class="toggle-header">
                        <div class="toggle-left">
                            <div class="section-icon" style="background: #f59e0b;">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <h3 class="section-title">{{ __('Contact d\'urgence') }}</h3>
                                <p class="section-desc">{{ __('Personne à contacter en cas d\'urgence') }}</p>
                            </div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" id="emergency-toggle" {{ old('has_emergency_contact') ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div id="emergency-contact-section" class="toggle-content" style="{{ old('has_emergency_contact') ? 'display: block;' : 'display: none;' }}">
                        <input type="hidden" name="has_emergency_contact" id="has_emergency_contact" value="{{ old('has_emergency_contact', '0') }}">
                        <div class="section-body">
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Nom et prénoms') }} <span class="required">*</span></label>
                                    <input type="text" id="emergency_contact_name" name="emergency_contact_name" class="form-input" placeholder="{{ __('Nom et prénoms') }}" value="{{ old('emergency_contact_name') }}">
                                    <x-input-error :messages="$errors->get('emergency_contact_name')" />
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ __('Email') }} <span class="required">*</span></label>
                                    <input type="email" id="emergency_contact_email" name="emergency_contact_email" class="form-input" placeholder="{{ __('email@exemple.com') }}" value="{{ old('emergency_contact_email') }}">
                                    <x-input-error :messages="$errors->get('emergency_contact_email')" />
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ __('Adresse') }} <span class="required">*</span></label>
                                    <input type="text" id="emergency_contact_address" name="emergency_contact_address" class="form-input" placeholder="{{ __('Adresse') }}" value="{{ old('emergency_contact_address') }}">
                                    <x-input-error :messages="$errors->get('emergency_contact_address')" />
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ __('Téléphone') }} <span class="required">*</span></label>
                                    <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone" class="form-input" placeholder="{{ __('+228 XX XXX XXX') }}" value="{{ old('emergency_contact_phone') }}">
                                    <x-input-error :messages="$errors->get('emergency_contact_phone')" />
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ __('Relation') }} <span class="required">*</span></label>
                                    <input type="text" id="emergency_contact_relationship" name="emergency_contact_relationship" class="form-input" placeholder="{{ __('Relation') }}" value="{{ old('emergency_contact_relationship') }}">
                                    <x-input-error :messages="$errors->get('emergency_contact_relationship')" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section Informations complémentaires --}}
                {{-- <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div>
                            <h3 class="section-title">{{ __('Informations complémentaires') }}</h3>
                            <p class="section-desc">{{ __('Numéro de sécurité sociale') }}</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">{{ __('Numéro SS') }}</label>
                                <input type="text" name="n_social" class="form-input" placeholder="{{ __('Numéro de sécurité sociale') }}" value="{{ old('n_social') }}">
                                <x-input-error :messages="$errors->get('n_social')" />
                            </div>
                        </div>
                    </div>
                </div> --}}

                {{-- Boutons d'action --}}
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
        }
        .form-input:focus {
            outline: none;
            border-color: var(--vp);
            box-shadow: 0 0 0 3px rgba(46,125,100,0.1);
        }

        /* Select */
        .select-wrapper {
            position: relative;
        }
        .form-select {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid var(--gl);
            border-radius: 12px;
            font-size: 0.85rem;
            appearance: none;
            background: white;
        }
        .select-icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gm);
            font-size: 0.75rem;
            pointer-events: none;
        }

        /* Upload fichier */
        .file-upload {
            position: relative;
        }
        .file-input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }
        .file-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--gtc);
            border: 1.5px dashed var(--gl);
            border-radius: 12px;
            padding: 11px 16px;
            cursor: pointer;
            transition: var(--t);
            font-size: 0.85rem;
            color: var(--gf);
        }
        .file-label:hover {
            border-color: var(--vp);
            background: var(--vc);
        }
        .image-preview {
            margin-top: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .image-preview img {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            object-fit: cover;
            border: 1px solid var(--gl);
        }
        .form-hint {
            font-size: 0.65rem;
            color: var(--gm);
            margin-top: 6px;
        }

        /* Toggle */
        .toggle-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 24px;
            background: var(--gtc);
            border-bottom: 1px solid var(--gl);
        }
        .toggle-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .3s;
            border-radius: 24px;
        }
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
        }
        .toggle-switch input:checked + .toggle-slider {
            background-color: var(--vp);
        }
        .toggle-switch input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }
        .toggle-content {
            transition: all 0.3s ease;
        }

        /* Boutons */
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
            transition: var(--t);
            border: none;
        }
        .btn-submit {
            background: var(--vp);
            color: white;
        }
        .btn-submit:hover {
            background: var(--vf);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(46,125,100,0.3);
        }
        .btn-cancel {
            background: var(--gtc);
            color: var(--gf);
            border: 1px solid var(--gl);
        }
        .btn-cancel:hover {
            background: var(--gl);
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .add-user-simple-container { padding: 16px; }
            .add-user-header { padding: 20px; }
            .header-icon { display: none; }
            .form-row { grid-template-columns: 1fr; gap: 16px; }
            .section-header { padding: 14px 20px; }
            .section-body { padding: 20px; }
            .form-actions { flex-direction: column; }
            .btn-submit, .btn-cancel { justify-content: center; }
        }
    </style>

    <script>
        // Aperçu de l'image
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('avatar');
            const fileName = document.getElementById('file-name');
            const preview = document.getElementById('image-preview');

            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        fileName.innerHTML = `<i class="fas fa-check-circle" style="color:#2e7d64"></i> ${file.name}`;
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            preview.innerHTML = `<img src="${e.target.result}" alt="Aperçu">`;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        fileName.innerHTML = 'Choisir une photo';
                        preview.innerHTML = '';
                    }
                });
            }
        });

        // Gestion du toggle pour le contact d'urgence
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('emergency-toggle');
            const section = document.getElementById('emergency-contact-section');
            const hiddenInput = document.getElementById('has_emergency_contact');

            const emergencyName = document.getElementById('emergency_contact_name');
            const emergencyEmail = document.getElementById('emergency_contact_email');
            const emergencyAddress = document.getElementById('emergency_contact_address');
            const emergencyPhone = document.getElementById('emergency_contact_phone');
            const emergencyRelation = document.getElementById('emergency_contact_relationship');

            function toggleEmergencySection() {
                if (toggle.checked) {
                    section.style.display = 'block';
                    hiddenInput.value = '1';
                    if (emergencyName) emergencyName.required = true;
                    if (emergencyEmail) emergencyEmail.required = true;
                    if (emergencyAddress) emergencyAddress.required = true;
                    if (emergencyPhone) emergencyPhone.required = true;
                    if (emergencyRelation) emergencyRelation.required = true;
                } else {
                    section.style.display = 'none';
                    hiddenInput.value = '0';
                    if (emergencyName) emergencyName.required = false;
                    if (emergencyEmail) emergencyEmail.required = false;
                    if (emergencyAddress) emergencyAddress.required = false;
                    if (emergencyPhone) emergencyPhone.required = false;
                    if (emergencyRelation) emergencyRelation.required = false;
                    if (emergencyName) emergencyName.value = '';
                    if (emergencyEmail) emergencyEmail.value = '';
                    if (emergencyAddress) emergencyAddress.value = '';
                    if (emergencyPhone) emergencyPhone.value = '';
                    if (emergencyRelation) emergencyRelation.value = '';
                }
            }

            if (toggle) {
                toggle.addEventListener('change', toggleEmergencySection);
                if (toggle.checked) toggleEmergencySection();
            }
        });
    </script>
</x-app-layout>