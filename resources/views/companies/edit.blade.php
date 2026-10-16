<x-app-layout>
    <div class="edit-company-container">
        {{-- En-tête --}}
        <div class="edit-company-header">
            <div class="header-content">
                <h1 class="header-title">{{ __('Modifier une entreprise') }}</h1>
                <p class="header-subtitle">{{ __('Modifiez les informations de l\'entreprise') }}</p>
            </div>
            <div class="header-icon"><i class="fas fa-building"></i></div>
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
        <div class="edit-company-card">
            <form method="POST" action="{{ route('companies.update', $company->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                {{-- Informations générales --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="fas fa-info-circle"></i></div>
                        <div>
                            <h3 class="section-title">{{ __('Informations générales') }}</h3>
                            <p class="section-desc">{{ __('Identité et coordonnées de l\'entreprise') }}</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">{{ __('Nom de l\'entreprise') }} <span class="required">*</span></label>
                                <input type="text" name="compagny_name" class="form-input" placeholder="{{ __('Nom de l\'entreprise') }}" value="{{ old('compagny_name', $company->compagny_name) }}" required autofocus>
                                <x-input-error :messages="$errors->get('compagny_name')" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Email de l\'entreprise') }} <span class="required">*</span></label>
                                <input type="email" name="compagny_email" class="form-input" placeholder="{{ __('contact@entreprise.com') }}" value="{{ old('compagny_email', $company->compagny_email) }}" required>
                                <x-input-error :messages="$errors->get('compagny_email')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Adresse & Contact --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <h3 class="section-title">{{ __('Adresse & Contact') }}</h3>
                            <p class="section-desc">{{ __('Localisation et numéro de téléphone') }}</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">{{ __('Adresse') }}</label>
                                <input type="text" name="address" class="form-input" placeholder="{{ __('Adresse de l\'entreprise') }}" value="{{ old('address', $company->address) }}">
                                <x-input-error :messages="$errors->get('address')" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Téléphone') }}</label>
                                <input type="tel" name="phone" class="form-input" placeholder="{{ __('+228 XX XXX XXX') }}" value="{{ old('phone', $company->phone) }}">
                                <x-input-error :messages="$errors->get('phone')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Localisation & Devise --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="fas fa-globe"></i></div>
                        <div>
                            <h3 class="section-title">{{ __('Localisation & Devise') }}</h3>
                            <p class="section-desc">{{ __('Pays, devise et fuseau horaire') }}</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">{{ __('Pays') }} <span class="required">*</span></label>
                                <div class="select-wrapper">
                                    <select id="country" name="country" class="form-select" required>
                                        <option value="">{{ __('Sélectionnez un pays') }}</option>
                                        <option value="togo" {{ old('country', $company->country) == 'togo' ? 'selected' : '' }}>TOGO</option>
                                        <option value="benin" {{ old('country', $company->country) == 'benin' ? 'selected' : '' }}>BENIN</option>
                                        <option value="ghana" {{ old('country', $company->country) == 'ghana' ? 'selected' : '' }}>GHANA</option>
                                        <option value="sénégal" {{ old('country', $company->country) == 'sénégal' ? 'selected' : '' }}>SENEGAL</option>
                                        <option value="france" {{ old('country', $company->country) == 'france' ? 'selected' : '' }}>FRANCE</option>
                                        <option value="états-unis" {{ old('country', $company->country) == 'états-unis' ? 'selected' : '' }}>USA</option>
                                        <option value="royaume-uni" {{ old('country', $company->country) == 'royaume-uni' ? 'selected' : '' }}>ROYAUME-UNI</option>
                                        <option value="suisse" {{ old('country', $company->country) == 'suisse' ? 'selected' : '' }}>SUISSE</option>
                                        <option value="canada" {{ old('country', $company->country) == 'canada' ? 'selected' : '' }}>CANADA</option>
                                        <option value="côte d'Ivoire" {{ old('country', $company->country) == 'côte d\'Ivoire' ? 'selected' : '' }}>CÔTE D'IVOIRE</option>
                                        <option value="nigeria" {{ old('country', $company->country) == 'nigeria' ? 'selected' : '' }}>NIGERIA</option>
                                        <option value="afrique du sud" {{ old('country', $company->country) == 'afrique du sud' ? 'selected' : '' }}>AFRIQUE DU SUD</option>
                                        <option value="maroc" {{ old('country', $company->country) == 'maroc' ? 'selected' : '' }}>MAROC</option>
                                        <option value="allemagne" {{ old('country', $company->country) == 'allemagne' ? 'selected' : '' }}>ALLEMAGNE</option>
                                        <option value="espagne" {{ old('country', $company->country) == 'espagne' ? 'selected' : '' }}>ESPAGNE</option>
                                        <option value="italie" {{ old('country', $company->country) == 'italie' ? 'selected' : '' }}>ITALIE</option>
                                        <option value="portugal" {{ old('country', $company->country) == 'portugal' ? 'selected' : '' }}>PORTUGAL</option>
                                        <option value="chine" {{ old('country', $company->country) == 'chine' ? 'selected' : '' }}>CHINE</option>
                                        <option value="japon" {{ old('country', $company->country) == 'japon' ? 'selected' : '' }}>JAPON</option>
                                        <option value="russie" {{ old('country', $company->country) == 'russie' ? 'selected' : '' }}>RUSSIE</option>
                                        <option value="brésil" {{ old('country', $company->country) == 'brésil' ? 'selected' : '' }}>BRÉSIL</option>
                                        <option value="inde" {{ old('country', $company->country) == 'inde' ? 'selected' : '' }}>INDE</option>
                                        <option value="mexique" {{ old('country', $company->country) == 'mexique' ? 'selected' : '' }}>MEXIQUE</option>
                                        <option value="argentine" {{ old('country', $company->country) == 'argentine' ? 'selected' : '' }}>ARGENTINE</option>
                                        <option value="australie" {{ old('country', $company->country) == 'australie' ? 'selected' : '' }}>AUSTRALIE</option>
                                        <option value="corée du sud" {{ old('country', $company->country) == 'corée du sud' ? 'selected' : '' }}>CORÉE DU SUD</option>
                                        <option value="turquie" {{ old('country', $company->country) == 'turquie' ? 'selected' : '' }}>TURQUIE</option>
                                        <option value="égypte" {{ old('country', $company->country) == 'égypte' ? 'selected' : '' }}>ÉGYPTE</option>
                                        <option value="tunisie" {{ old('country', $company->country) == 'tunisie' ? 'selected' : '' }}>TUNISIE</option>
                                        <option value="algérie" {{ old('country', $company->country) == 'algérie' ? 'selected' : '' }}>ALGÉRIE</option>
                                        <option value="émirats arabes unis" {{ old('country', $company->country) == 'émirats arabes unis' ? 'selected' : '' }}>ÉMIRATS ARABES UNIS</option>
                                        <option value="qatar" {{ old('country', $company->country) == 'qatar' ? 'selected' : '' }}>QATAR</option>
                                        <option value="arabie saoudite" {{ old('country', $company->country) == 'arabie saoudite' ? 'selected' : '' }}>ARABIE SAOUDITE</option>
                                        <option value="pakistan" {{ old('country', $company->country) == 'pakistan' ? 'selected' : '' }}>PAKISTAN</option>
                                        <option value="vietnam" {{ old('country', $company->country) == 'vietnam' ? 'selected' : '' }}>VIETNAM</option>
                                        <option value="indonésie" {{ old('country', $company->country) == 'indonésie' ? 'selected' : '' }}>INDONÉSIE</option>
                                        <option value="malaisie" {{ old('country', $company->country) == 'malaisie' ? 'selected' : '' }}>MALAISIE</option>
                                        <option value="singapour" {{ old('country', $company->country) == 'singapour' ? 'selected' : '' }}>SINGAPOUR</option>
                                        <option value="thailande" {{ old('country', $company->country) == 'thailande' ? 'selected' : '' }}>THAÏLANDE</option>
                                    </select>
                                    <i class="fas fa-chevron-down select-icon"></i>
                                </div>
                                <x-input-error :messages="$errors->get('country')" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Devise') }} <span class="required">*</span></label>
                                <div class="select-wrapper">
                                    <select id="currency" name="currency" class="form-select" required>
                                        <option value="">{{ __('Sélectionnez une devise') }}</option>
                                    </select>
                                    <i class="fas fa-chevron-down select-icon"></i>
                                </div>
                                <x-input-error :messages="$errors->get('currency')" />
                            </div>
                        </div>
                        <div class="form-row" style="margin-top: 20px;">
                            <div class="form-group">
                                <label class="form-label">{{ __('Fuseau horaire') }}</label>
                                <div class="select-wrapper">
                                    <select name="timezone" class="form-select">
                                        <option value="">{{ __('Sélectionnez un fuseau horaire') }}</option>
                                        @foreach($timezones as $key => $value)
                                            <option value="{{ $key }}" {{ old('timezone', $company->timezone) == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <i class="fas fa-chevron-down select-icon"></i>
                                </div>
                                <x-input-error :messages="$errors->get('timezone')" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Site web') }}</label>
                                <input type="url" name="website" class="form-input" placeholder="{{ __('https://www.entreprise.com') }}" value="{{ old('website', $company->website) }}">
                                <x-input-error :messages="$errors->get('website')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Configuration --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="fas fa-cog"></i></div>
                        <div>
                            <h3 class="section-title">{{ __('Configuration') }}</h3>
                            <p class="section-desc">{{ __('Statut et sécurité') }}</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">{{ __('Statut') }} <span class="required">*</span></label>
                                <div class="select-wrapper">
                                    <select name="status" class="form-select" required>
                                        <option value="active" {{ old('status', $company->status) == 'active' ? 'selected' : '' }}>{{ __('Actif') }}</option>
                                        <option value="inactive" {{ old('status', $company->status) == 'inactive' ? 'selected' : '' }}>{{ __('Inactif') }}</option>
                                    </select>
                                    <i class="fas fa-chevron-down select-icon"></i>
                                </div>
                                <x-input-error :messages="$errors->get('status')" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Mot de passe') }}</label>
                                <input type="password" name="password" class="form-input" placeholder="{{ __('Nouveau mot de passe (laisser vide pour conserver l\'actuel)') }}">
                                <p class="field-hint">{{ __('Laissez vide pour conserver le mot de passe actuel') }}</p>
                                <x-input-error :messages="$errors->get('password')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Logo --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="fas fa-image"></i></div>
                        <div>
                            <h3 class="section-title">{{ __('Logo') }}</h3>
                            <p class="section-desc">{{ __('Logo de l\'entreprise') }}</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">{{ __('Logo de l\'entreprise') }}</label>
                                <div class="file-upload-container">
                                    <input type="file" name="logo" id="logo" class="file-input" accept="image/*" onchange="previewLogo(this)">
                                    <label for="logo" class="file-label">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <span>{{ __('Changer le logo') }}</span>
                                    </label>
                                    <div id="logo-preview" class="logo-preview">
                                        @if($company->logo)
                                            <img id="logo-preview-img" src="{{ asset('storage/' . $company->logo) }}" alt="Logo actuel">
                                        @else
                                            <img id="logo-preview-img" src="#" alt="Aperçu du logo" style="display: none;">
                                        @endif
                                        <button type="button" onclick="clearLogo()" class="remove-logo" style="{{ !$company->logo ? 'display: none;' : '' }}">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                    </div>
                                </div>
                                <p class="file-hint">Formats acceptés : JPG, PNG, GIF. Max 2MB</p>
                                <x-input-error :messages="$errors->get('logo')" />
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
                        <i class="fas fa-save"></i> {{ __('Mettre à jour') }}
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

        .edit-company-container { max-width: 1200px; margin: 0 auto; padding: 24px 28px; }

        .edit-company-header {
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

        .edit-company-card { background: #fff; border-radius: 20px; border: 1px solid var(--gl); overflow: hidden; }

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
        }
        .form-input:focus, .form-textarea:focus { outline: none; border-color: var(--vp); }

        .select-wrapper { position: relative; }
        .form-select {
            width: 100%; padding: 11px 14px; border: 1.5px solid var(--gl);
            border-radius: 12px; font-size: 0.85rem; background: white;
            appearance: none;
        }
        .select-icon { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: var(--gm); font-size: 0.75rem; pointer-events: none; }

        .field-hint {
            font-size: 0.7rem;
            color: var(--gm);
            margin-top: 4px;
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
        .logo-preview {
            position: relative;
            width: 120px;
            height: 120px;
            border: 2px solid var(--gl);
            border-radius: 12px;
            overflow: hidden;
            background: var(--gtc);
        }
        .logo-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .remove-logo {
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
        .remove-logo:hover {
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
        }
        .btn-submit { background: var(--vp); color: white; }
        .btn-submit:hover { background: var(--vf); }
        .btn-cancel { background: var(--gtc); color: var(--gf); border: 1px solid var(--gl); }

        @media (max-width: 768px) {
            .edit-company-container { padding: 16px; }
            .edit-company-header { padding: 20px; }
            .header-icon { display: none; }
            .form-row { grid-template-columns: 1fr; gap: 16px; }
        }
    </style>

    <script>
        // Devises par pays
        const currencies = {
            'togo': ['Franc CFA (XOF)'],
            'benin': ['Franc CFA (XOF)'],
            'ghana': ['Cedi ghanéen (GHS)'],
            'sénégal': ['Franc CFA (XOF)'],
            'france': ['Euro (EUR)'],
            'états-unis': ['Dollar américain (USD)'],
            'royaume-uni': ['Livre sterling (GBP)'],
            'suisse': ['Franc suisse (CHF)'],
            'canada': ['Dollar canadien (CAD)'],
            'côte d\'Ivoire': ['Franc CFA (XOF)'],
            'nigeria': ['Naira nigérian (NGN)'],
            'afrique du sud': ['Rand sud-africain (ZAR)'],
            'maroc': ['Dirham marocain (MAD)'],
            'allemagne': ['Euro (EUR)'],
            'espagne': ['Euro (EUR)'],
            'italie': ['Euro (EUR)'],
            'portugal': ['Euro (EUR)'],
            'chine': ['Yuan chinois (CNY)'],
            'japon': ['Yen japonais (JPY)'],
            'russie': ['Rouble russe (RUB)'],
            'brésil': ['Real brésilien (BRL)'],
            'inde': ['Roupie indienne (INR)'],
            'mexique': ['Peso mexicain (MXN)'],
            'argentine': ['Peso argentin (ARS)'],
            'australie': ['Dollar australien (AUD)'],
            'corée du sud': ['Won sud-coréen (KRW)'],
            'turquie': ['Livre turque (TRY)'],
            'égypte': ['Livre égyptienne (EGP)'],
            'tunisie': ['Dinar tunisien (TND)'],
            'algérie': ['Dinar algérien (DZD)'],
            'émirats arabes unis': ['Dirham des Émirats (AED)'],
            'qatar': ['Riyal qatari (QAR)'],
            'arabie saoudite': ['Riyal saoudien (SAR)'],
            'pakistan': ['Roupie pakistanaise (PKR)'],
            'vietnam': ['Dong vietnamien (VND)'],
            'indonésie': ['Roupie indonésienne (IDR)'],
            'malaisie': ['Ringgit malaisien (MYR)'],
            'singapour': ['Dollar de Singapour (SGD)'],
            'thailande': ['Baht thaïlandais (THB)'],
        };

        // Mise à jour des devises selon le pays
        document.addEventListener('DOMContentLoaded', function() {
            const countrySelect = document.getElementById('country');
            const currencySelect = document.getElementById('currency');
            const oldCurrency = "{{ old('currency', $company->currency) }}";

            function updateCurrencies() {
                const country = countrySelect.value;
                currencySelect.innerHTML = '<option value="">{{ __("Sélectionnez une devise") }}</option>';

                if (currencies[country]) {
                    currencies[country].forEach(currency => {
                        const option = document.createElement('option');
                        option.value = currency;
                        option.textContent = currency;
                        if (currency === oldCurrency) {
                            option.selected = true;
                        }
                        currencySelect.appendChild(option);
                    });
                }
            }

            countrySelect.addEventListener('change', updateCurrencies);

            // Déclencher au chargement
            if (countrySelect.value) {
                updateCurrencies();
            }
        });

        // Aperçu du logo
        function previewLogo(input) {
            const preview = document.getElementById('logo-preview');
            const previewImg = document.getElementById('logo-preview-img');
            const removeBtn = document.querySelector('.remove-logo');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';
                    if (removeBtn) removeBtn.style.display = 'flex';
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        function clearLogo() {
            const fileInput = document.getElementById('logo');
            const previewImg = document.getElementById('logo-preview-img');
            const removeBtn = document.querySelector('.remove-logo');

            fileInput.value = '';
            previewImg.src = '#';
            previewImg.style.display = 'none';
            if (removeBtn) removeBtn.style.display = 'none';
        }
    </script>
</x-app-layout>
