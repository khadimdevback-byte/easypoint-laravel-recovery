<x-app-layout>
    @php
        $userTypesTitle = [
            'manager' => __('Add Manager'),
            'controller' => __('Add Controller'),
            'user' => __('Add Employee'),
            'customer' => __('Add Customer'),
        ];
    @endphp

    <div class="add-client-container">
        {{-- En-tête avec dégradé --}}
        <div class="add-client-header">
            <div class="header-content">
                <h1 class="header-title">{{ $userTypesTitle[$user_type] ?? __('Ajouter un client') }}</h1>
                <p class="header-subtitle">Ajoutez un nouveau client à votre plateforme</p>
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
        <div class="add-client-card">
            <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="user_type" value="{{ $user_type }}" required>
                <input type="hidden" name="company_id" value="{{ Auth::user()->company_id }}" required>
                <input type="hidden" name="status" value="active" required>
                <input type="hidden" name="password" value="activecustomer" required>
                <input type="hidden" name="password_confirmation" value="activecustomer" required>

                {{-- Section 1 : Type de client --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-user-tag"></i>
                        </div>
                        <div>
                            <h3 class="section-title">Type de client</h3>
                            <p class="section-desc">Spécifiez le type de client</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Type de client <span class="required">*</span></label>
                                <div class="select-wrapper">
                                    <select id="customer_type" name="customer_type" class="form-select" required>
                                        <option value="physical_person" {{ old('customer_type') == 'physical_person' ? 'selected' : '' }}>👤 Personne physique</option>
                                        <option value="legal_person" {{ old('customer_type') == 'legal_person' ? 'selected' : '' }}>🏢 Personne morale</option>
                                    </select>
                                    <i class="fas fa-chevron-down select-icon"></i>
                                </div>
                                <x-input-error :messages="$errors->get('customer_type')" />
                            </div>

                            <div id="raison_sociale_container" class="form-group" style="display: none;">
                                <label class="form-label">Raison Sociale <span class="required">*</span></label>
                                <input type="text" id="raison_sociale" name="raison_sociale" class="form-input" placeholder="Nom de l'entreprise" value="{{ old('raison_sociale') }}">
                                <x-input-error :messages="$errors->get('raison_sociale')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 2 : Identité --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div>
                            <h3 class="section-title">Identité</h3>
                            <p class="section-desc">Informations personnelles du client</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Nom <span class="required">*</span></label>
                                <input type="text" name="name" class="form-input" placeholder="Nom" value="{{ old('name') }}" required>
                                <x-input-error :messages="$errors->get('name')" />
                            </div>

                            <div class="form-group">
                                <label class="form-label">Prénoms <span class="required">*</span></label>
                                <input type="text" name="first_name" class="form-input" placeholder="Prénoms" value="{{ old('first_name') }}" required>
                                <x-input-error :messages="$errors->get('first_name')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 3 : Coordonnées --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-address-card"></i>
                        </div>
                        <div>
                            <h3 class="section-title">Coordonnées</h3>
                            <p class="section-desc">Adresse et moyens de contact</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Adresse</label>
                                <input type="text" name="address" class="form-input" placeholder="Adresse complète" value="{{ old('address') }}">
                                <x-input-error :messages="$errors->get('address')" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">Téléphone</label>
                                <input type="tel" name="phone" class="form-input" placeholder="+228 XX XXX XXX" value="{{ old('phone') }}">
                                <x-input-error :messages="$errors->get('phone')" />
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Email <span class="required">*</span></label>
                                <input type="email" name="email" class="form-input" placeholder="client@exemple.com" value="{{ old('email') }}" required>
                                <x-input-error :messages="$errors->get('email')" />
                            </div>

                            <div class="form-group">
                                <label class="form-label">Photo</label>
                                <div class="file-upload">
                                    <input type="file" name="avatar" id="avatar" class="file-input" accept="image/*">
                                    <label for="avatar" class="file-label">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <span id="file-name">Choisir une photo</span>
                                    </label>
                                    <div id="image-preview" class="image-preview"></div>
                                </div>
                                <p class="form-hint">Formats acceptés : JPG, PNG. Max 2MB</p>
                                <x-input-error :messages="$errors->get('avatar')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Boutons d'action --}}
                <div class="form-actions">
                    <button type="button" onclick="history.back()" class="btn-cancel">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* ============================================ */
        /* DESIGN MODERNE - PAGE AJOUT CLIENT          */
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

        .add-client-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 24px 28px;
        }

        /* En-tête avec dégradé */
        .add-client-header {
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
        .add-client-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--gl);
            overflow: hidden;
        }

        /* Sections du formulaire */
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
            padding: 18px 24px;
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

        /* Select personnalisé */
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
            .add-client-container { padding: 16px; }
            .add-client-header { padding: 20px; }
            .header-icon { display: none; }
            .form-row { grid-template-columns: 1fr; gap: 16px; }
            .section-header { padding: 14px 20px; }
            .section-body { padding: 20px; }
            .form-actions { flex-direction: column; }
            .btn-submit, .btn-cancel { justify-content: center; }
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let customerTypeSelect = document.getElementById("customer_type");
            let raisonSocialeContainer = document.getElementById("raison_sociale_container");

            function toggleRaisonSociale() {
                if (customerTypeSelect.value === "legal_person") {
                    raisonSocialeContainer.style.display = "block";
                    document.getElementById("raison_sociale").setAttribute("required", "true");
                } else {
                    raisonSocialeContainer.style.display = "none";
                    document.getElementById("raison_sociale").removeAttribute("required");
                }
            }

            toggleRaisonSociale();
            customerTypeSelect.addEventListener("change", toggleRaisonSociale);

            // Aperçu de l'image
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
    </script>
</x-app-layout>
