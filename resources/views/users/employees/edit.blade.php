<x-app-layout>
    @php
        $userTypesTitle = [
            'manager' => __('Modifier un gestionnaire'),
            'controller' => __('Modifier un contrôleur'),
            'user' => __('Modifier un employé'),
            'customer' => __('Modifier le client'),
        ];
    @endphp

    <div class="edit-user-container">
        {{-- En-tête avec dégradé --}}
        <div class="edit-user-header">
            <div class="header-content">
                <h1 class="header-title">{{ $userTypesTitle[$user->user_type] ?? __('Modifier un utilisateur') }}</h1>
                <p class="header-subtitle">{{ __('Modifiez les informations de l\'utilisateur') }}</p>
            </div>
            <div class="header-icon">
                <i class="fas fa-user-edit"></i>
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
        <div class="edit-user-card">
            <form action="{{ route('users.update', ['user_type' => $user_type, 'id' => $user->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <input type="hidden" name="user_type" value="{{ $user->user_type }}" required>
                <input type="hidden" name="company_id" value="{{ $user->company_id }}" required>
                <input type="hidden" name="type_horaire" value="fixe">
                <input type="hidden" name="id_number" id="id_number_hidden" value="{{ old('id_number', $user->id_number) }}">

                {{-- SECTION 1 : INFORMATIONS PERSONNELLES --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <h3 class="section-title">{{ __('Informations personnelles') }}</h3>
                            <p class="section-desc">{{ __('Identité et coordonnées') }}</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">{{ __('Nom') }} <span class="required">*</span></label>
                                <input type="text" name="name" class="form-input" placeholder="{{ __('Nom') }}" value="{{ old('name', $user->name) }}" required>
                                <x-input-error :messages="$errors->get('name')" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Prénoms') }} <span class="required">*</span></label>
                                <input type="text" name="first_name" class="form-input" placeholder="{{ __('Prénoms') }}" value="{{ old('first_name', $user->first_name) }}" required>
                                <x-input-error :messages="$errors->get('first_name')" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Téléphone') }} <span class="required">*</span></label>
                                <input type="tel" name="phone" class="form-input" placeholder="{{ __('+228 XX XXX XXX') }}" value="{{ old('phone', $user->phone) }}" required>
                                <x-input-error :messages="$errors->get('phone')" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Email') }} <span class="required">*</span></label>
                                <input type="email" name="email" class="form-input" placeholder="{{ __('contact@entreprise.com') }}" value="{{ old('email', $user->email) }}" required>
                                <x-input-error :messages="$errors->get('email')" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Adresse') }} <span class="required">*</span></label>
                                <input type="text" name="address" class="form-input" placeholder="{{ __('Adresse complète') }}" value="{{ old('address', $user->address) }}" required>
                                <x-input-error :messages="$errors->get('address')" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Photo') }}</label>
                                <div class="file-upload">
                                    <input type="file" name="avatar" id="avatar" class="file-input" accept="image/*">
                                    <label for="avatar" class="file-label">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <span id="file-name">{{ __('Changer la photo') }}</span>
                                    </label>
                                    <div id="image-preview" class="image-preview">
                                        @if($user->avatar && Storage::disk('public')->exists($user->avatar))
                                            <img src="{{ asset('storage/' . $user->avatar) }}" class="current-avatar" alt="Avatar actuel">
                                        @endif
                                    </div>
                                </div>
                                <p class="form-hint">{{ __('Formats acceptés : JPG, PNG. Max 2MB') }}</p>
                                <x-input-error :messages="$errors->get('avatar')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 2 : AUTHENTIFICATION --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div>
                            <h3 class="section-title">{{ __('Authentification') }}</h3>
                            <p class="section-desc">{{ __('Changer le mot de passe') }}</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">{{ __('Nouveau mot de passe') }}</label>
                                <input type="password" name="password" class="form-input" placeholder="{{ __('Laisser vide pour ne pas modifier') }}">
                                <x-input-error :messages="$errors->get('password')" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Confirmation') }}</label>
                                <input type="password" name="password_confirmation" class="form-input" placeholder="{{ __('Confirmer') }}">
                                <x-input-error :messages="$errors->get('password_confirmation')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 3 : INFORMATIONS PROFESSIONNELLES --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div>
                            <h3 class="section-title">{{ __('Informations professionnelles') }}</h3>
                            <p class="section-desc">{{ __('Poste et rémunération') }}</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">{{ __('Statut') }} <span class="required">*</span></label>
                                <div class="select-wrapper">
                                    <select name="status" class="form-select" required>
                                        <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>{{ __('Actif') }}</option>
                                        <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>{{ __('Inactif') }}</option>
                                    </select>
                                    <i class="fas fa-chevron-down select-icon"></i>
                                </div>
                                <x-input-error :messages="$errors->get('status')" />
                            </div>

                            <div class="form-group">
                                <label class="form-label">{{ __('Fonction') }} <span class="required">*</span></label>
                                <div class="select-wrapper">
                                    <select name="employee_function" class="form-select" required>
                                        <option value="">{{ __('Sélectionnez une fonction') }}</option>
                                        @foreach($fonction as $func)
                                            <option value="{{ $func->id }}" {{ old('employee_function', $user->employee_function) == $func->id ? 'selected' : '' }}>
                                                {{ $func->nom_fonction }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <i class="fas fa-chevron-down select-icon"></i>
                                </div>
                                <x-input-error :messages="$errors->get('employee_function')" />
                            </div>

                            <div class="form-group">
                                <label class="form-label">{{ __('Salaire fixe mensuel') }}</label>
                                <input type="number" name="salaire_fixe" class="form-input" step="0.01" placeholder="{{ __('0') }}" value="{{ old('salaire_fixe', $user->salaire_fixe) }}">
                                <x-input-error :messages="$errors->get('salaire_fixe')" />
                            </div>

                            <div class="form-group">
                                <label class="form-label">{{ __('Date début de contrat') }}</label>
                                <input type="date" name="contract_start_date" class="form-input" value="{{ old('contract_start_date', $user->contract_start_date) }}">
                                <x-input-error :messages="$errors->get('contract_start_date')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 4 : PIÈCE D'IDENTITÉ AVEC TOGGLE --}}
                <div class="form-section">
                    <div class="toggle-header">
                        <div class="toggle-left">
                            <div class="section-icon" style="background: #8b5cf6;">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div>
                                <h3 class="section-title">{{ __('Pièce d\'identité') }}</h3>
                                <p class="section-desc">{{ __('Ajoutez une pièce d\'identité') }}</p>
                            </div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" id="identity-toggle" {{ old('has_identity_document', $user->has_identity_document ?? false) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div id="identity-document-section" class="toggle-content" style="{{ old('has_identity_document', $user->has_identity_document ?? false) ? 'display: block;' : 'display: none;' }}">
                        <input type="hidden" name="has_identity_document" id="has_identity_document" value="{{ old('has_identity_document', $user->has_identity_document ?? '0') }}">
                        <div class="section-body">
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Type de pièce') }} <span class="required">*</span></label>
                                    <div class="select-wrapper">
                                        <select id="identity_document_type" name="identity_document_type" class="form-select">
                                            <option value="cni" {{ old('identity_document_type', $user->identity_document_type) == 'cni' ? 'selected' : '' }}>{{ __('Carte nationale d\'identité (CNI)') }}</option>
                                            <option value="passport" {{ old('identity_document_type', $user->identity_document_type) == 'passport' ? 'selected' : '' }}>{{ __('Passeport') }}</option>
                                            <option value="driving_licenses" {{ old('identity_document_type', $user->identity_document_type) == 'driving_licenses' ? 'selected' : '' }}>{{ __('Permis de conduire') }}</option>
                                        </select>
                                        <i class="fas fa-chevron-down select-icon"></i>
                                    </div>
                                    <x-input-error :messages="$errors->get('identity_document_type')" />
                                </div>

                                <div id="cni_field" class="form-group" style="display: none;">
                                    <label class="form-label">{{ __('Numéro CNI') }} <span class="required">*</span></label>
                                    <input type="text" id="cni_number" name="cni_number" class="form-input" placeholder="1234567890" value="{{ old('cni_number', ($user->identity_document_type == 'cni' ? $user->id_number : '')) }}">
                                    <p class="form-hint">{{ __('Format: 10 chiffres (ex: 1234567890)') }}</p>
                                    <x-input-error :messages="$errors->get('cni_number')" />
                                </div>

                                <div id="passport_field" class="form-group" style="display: none;">
                                    <label class="form-label">{{ __('Numéro Passeport') }} <span class="required">*</span></label>
                                    <input type="text" id="passport_number" name="passport_number" class="form-input" placeholder="PA1234567" value="{{ old('passport_number', ($user->identity_document_type == 'passport' ? $user->id_number : '')) }}">
                                    <p class="form-hint">{{ __('Format: 1-2 lettres suivies de 7-9 chiffres') }}</p>
                                    <x-input-error :messages="$errors->get('passport_number')" />
                                </div>

                                <div id="permis_field" class="form-group" style="display: none;">
                                    <label class="form-label">{{ __('Numéro Permis') }} <span class="required">*</span></label>
                                    <input type="text" id="permis_number" name="permis_number" class="form-input" placeholder="123456789012" value="{{ old('permis_number', ($user->identity_document_type == 'driving_licenses' ? $user->id_number : '')) }}">
                                    <p class="form-hint">{{ __('Format: 12 chiffres (ex: 123456789012)') }}</p>
                                    <x-input-error :messages="$errors->get('permis_number')" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 5 : CONTACT D'URGENCE AVEC TOGGLE --}}
                <div class="form-section">
                    <div class="toggle-header">
                        <div class="toggle-left">
                            <div class="section-icon" style="background: #f59e0b;">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <h3 class="section-title">{{ __('Contact d\'urgence') }}</h3>
                                <p class="section-desc">{{ __('Ajoutez un contact à prévenir en cas d\'urgence') }}</p>
                            </div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" id="emergency-toggle" {{ old('has_emergency_contact', $user->emergency_contact_name ? true : false) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div id="emergency-contact-section" class="toggle-content" style="{{ old('has_emergency_contact', $user->emergency_contact_name ? true : false) ? 'display: block;' : 'display: none;' }}">
                        <input type="hidden" name="has_emergency_contact" id="has_emergency_contact" value="{{ old('has_emergency_contact', $user->emergency_contact_name ? '1' : '0') }}">
                        <div class="section-body">
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Nom et prénoms') }} <span class="required">*</span></label>
                                    <input type="text" id="emergency_contact_name" name="emergency_contact_name" class="form-input" placeholder="{{ __('Nom et prénoms') }}" value="{{ old('emergency_contact_name', $user->emergency_contact_name) }}">
                                    <x-input-error :messages="$errors->get('emergency_contact_name')" />
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ __('Téléphone') }} <span class="required">*</span></label>
                                    <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone" class="form-input" placeholder="{{ __('+228 XX XXX XXX') }}" value="{{ old('emergency_contact_phone', $user->emergency_contact_phone) }}">
                                    <x-input-error :messages="$errors->get('emergency_contact_phone')" />
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ __('Email') }}</label>
                                    <input type="email" id="emergency_contact_email" name="emergency_contact_email" class="form-input" placeholder="{{ __('email@exemple.com') }}" value="{{ old('emergency_contact_email', $user->emergency_contact_email) }}">
                                    <x-input-error :messages="$errors->get('emergency_contact_email')" />
                                </div>
                                <div class="form-group">
                                    <label class="form-label">{{ __('Relation') }} <span class="required">*</span></label>
                                    <div class="select-wrapper">
                                        <select id="emergency_contact_relationship" name="emergency_contact_relationship" class="form-select">
                                            <option value="">{{ __('Sélectionnez une relation') }}</option>
                                            <option value="parent" {{ old('emergency_contact_relationship', $user->emergency_contact_relationship) == 'parent' ? 'selected' : '' }}>{{ __('Parent') }}</option>
                                            <option value="conjoint" {{ old('emergency_contact_relationship', $user->emergency_contact_relationship) == 'conjoint' ? 'selected' : '' }}>{{ __('Conjoint/Conjointe') }}</option>
                                            <option value="enfant" {{ old('emergency_contact_relationship', $user->emergency_contact_relationship) == 'enfant' ? 'selected' : '' }}>{{ __('Enfant') }}</option>
                                            <option value="frere_soeur" {{ old('emergency_contact_relationship', $user->emergency_contact_relationship) == 'frere_soeur' ? 'selected' : '' }}>{{ __('Frère/Sœur') }}</option>
                                            <option value="ami" {{ old('emergency_contact_relationship', $user->emergency_contact_relationship) == 'ami' ? 'selected' : '' }}>{{ __('Ami(e)') }}</option>
                                            <option value="autre" {{ old('emergency_contact_relationship', $user->emergency_contact_relationship) == 'autre' ? 'selected' : '' }}>{{ __('Autre') }}</option>
                                        </select>
                                        <i class="fas fa-chevron-down select-icon"></i>
                                    </div>
                                    <x-input-error :messages="$errors->get('emergency_contact_relationship')" />
                                </div>
                                <div class="form-group full-width">
                                    <label class="form-label">{{ __('Adresse') }}</label>
                                    <input type="text" id="emergency_contact_address" name="emergency_contact_address" class="form-input" placeholder="{{ __('Adresse complète') }}" value="{{ old('emergency_contact_address', $user->emergency_contact_address) }}">
                                    <x-input-error :messages="$errors->get('emergency_contact_address')" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 6 : HORAIRES DE TRAVAIL AVEC TOGGLE --}}
                <div class="form-section">
                    <div class="toggle-header">
                        <div class="toggle-left">
                            <div class="section-icon" style="background: #3b82f6;">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h3 class="section-title">{{ __('Horaires de travail') }}</h3>
                                <p class="section-desc">{{ __('Personnalisez les horaires par jour') }}</p>
                            </div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" id="schedule-toggle" {{ old('has_schedule', $hasSchedule ?? false) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div id="schedule-section" class="toggle-content" style="{{ old('has_schedule', $hasSchedule ?? false) ? 'display: block;' : 'display: none;' }}">
                        <input type="hidden" name="has_schedule" id="has_schedule" value="{{ old('has_schedule', $hasSchedule ?? '0') }}">
                        <div class="section-body">
                            @foreach (['Monday' => 'Lundi', 'Tuesday' => 'Mardi', 'Wednesday' => 'Mercredi',
                                       'Thursday' => 'Jeudi', 'Friday' => 'Vendredi', 'Saturday' => 'Samedi',
                                       'Sunday' => 'Dimanche'] as $dayEn => $dayFr)
                                <div class="schedule-row">
                                    <label class="schedule-label">
                                        <input type="checkbox" id="checkbox-{{ strtolower($dayEn) }}" 
                                               name="{{ strtolower($dayEn) }}_checked" value="1" 
                                               {{ old(strtolower($dayEn) . '_checked', $user->{strtolower($dayEn) . '_checked'}) ? 'checked' : '' }}>
                                        <span>{{ $dayFr }}</span>
                                    </label>
                                    <div class="schedule-times">
                                        <input type="time" id="start_time_{{ strtolower($dayEn) }}" 
                                               name="start_time_{{ strtolower($dayEn) }}" 
                                               class="schedule-input" 
                                               value="{{ old('start_time_' . strtolower($dayEn), $user->{'start_time_' . strtolower($dayEn)} ? \Carbon\Carbon::parse($user->{'start_time_' . strtolower($dayEn)})->format('H:i') : '') }}" 
                                               {{ old(strtolower($dayEn) . '_checked', $user->{strtolower($dayEn) . '_checked'}) ? '' : 'disabled' }}>
                                        <span class="schedule-sep">—</span>
                                        <input type="time" id="end_time_{{ strtolower($dayEn) }}" 
                                               name="end_time_{{ strtolower($dayEn) }}" 
                                               class="schedule-input" 
                                               value="{{ old('end_time_' . strtolower($dayEn), $user->{'end_time_' . strtolower($dayEn)} ? \Carbon\Carbon::parse($user->{'end_time_' . strtolower($dayEn)})->format('H:i') : '') }}" 
                                               {{ old(strtolower($dayEn) . '_checked', $user->{strtolower($dayEn) . '_checked'}) ? '' : 'disabled' }}>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Boutons d'action --}}
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
        /* ============================================ */
        /* DESIGN MODERNE - PAGE MODIFICATION USER     */
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

        .edit-user-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 24px 28px;
        }

        /* En-tête */
        .edit-user-header {
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
        .edit-user-card {
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
        .form-group.full-width {
            grid-column: span 2;
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
            cursor: pointer;
            transition: var(--t);
        }
        .form-select:focus {
            outline: none;
            border-color: var(--vp);
            box-shadow: 0 0 0 3px rgba(46,125,100,0.1);
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
        .current-avatar {
            width: 80px;
            height: 80px;
            border-radius: 12px;
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

        /* Horaires */
        .schedule-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            padding: 12px 0;
            border-bottom: 1px solid var(--gl);
        }
        .schedule-row:last-child {
            border-bottom: none;
        }
        .schedule-label {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            font-weight: 600;
            color: var(--gf);
            min-width: 120px;
        }
        .schedule-label input {
            width: 18px;
            height: 18px;
            accent-color: var(--vp);
            cursor: pointer;
        }
        .schedule-times {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .schedule-input {
            padding: 8px 12px;
            border: 1px solid var(--gl);
            border-radius: 10px;
            font-size: 0.8rem;
            background: white;
            width: 120px;
        }
        .schedule-input:disabled {
            background: var(--gtc);
            opacity: 0.6;
            cursor: not-allowed;
        }
        .schedule-sep {
            color: var(--gm);
            font-size: 0.8rem;
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
            .edit-user-container { padding: 16px; }
            .edit-user-header { padding: 20px; }
            .header-icon { display: none; }
            .form-row { grid-template-columns: 1fr; gap: 16px; }
            .form-group.full-width { grid-column: span 1; }
            .section-header { padding: 14px 20px; }
            .section-body { padding: 20px; }
            .form-actions { flex-direction: column; }
            .btn-submit, .btn-cancel { justify-content: center; }
            .schedule-row { flex-direction: column; align-items: flex-start; }
            .schedule-times { margin-left: 28px; }
            .schedule-input { width: 100px; }
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
                            preview.innerHTML = `<img src="${e.target.result}" class="current-avatar" alt="Nouvelle photo">`;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        fileName.innerHTML = 'Changer la photo';
                        preview.innerHTML = `@if($user->avatar && Storage::disk('public')->exists($user->avatar))
                            <img src="{{ asset('storage/' . $user->avatar) }}" class="current-avatar" alt="Avatar actuel">
                        @endif`;
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
            const emergencyPhone = document.getElementById('emergency_contact_phone');
            const emergencyEmail = document.getElementById('emergency_contact_email');
            const emergencyRelation = document.getElementById('emergency_contact_relationship');
            const emergencyAddress = document.getElementById('emergency_contact_address');

            function toggleEmergencySection() {
                if (toggle.checked) {
                    section.style.display = 'block';
                    hiddenInput.value = '1';
                    if (emergencyName) emergencyName.required = true;
                    if (emergencyPhone) emergencyPhone.required = true;
                    if (emergencyRelation) emergencyRelation.required = true;
                } else {
                    section.style.display = 'none';
                    hiddenInput.value = '0';
                    if (emergencyName) emergencyName.required = false;
                    if (emergencyPhone) emergencyPhone.required = false;
                    if (emergencyRelation) emergencyRelation.required = false;
                    if (emergencyName) emergencyName.value = '';
                    if (emergencyPhone) emergencyPhone.value = '';
                    if (emergencyEmail) emergencyEmail.value = '';
                    if (emergencyRelation) emergencyRelation.value = '';
                    if (emergencyAddress) emergencyAddress.value = '';
                }
            }

            if (toggle) {
                toggle.addEventListener('change', toggleEmergencySection);
                if (toggle.checked) toggleEmergencySection();
            }
        });

        // Gestion du toggle pour la pièce d'identité
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('identity-toggle');
            const section = document.getElementById('identity-document-section');
            const hiddenInput = document.getElementById('has_identity_document');
            
            const documentTypeSelect = document.getElementById('identity_document_type');
            const cniField = document.getElementById('cni_field');
            const passportField = document.getElementById('passport_field');
            const permisField = document.getElementById('permis_field');
            const cniInput = document.getElementById('cni_number');
            const passportInput = document.getElementById('passport_number');
            const permisInput = document.getElementById('permis_number');

            function toggleIdentitySection() {
                if (toggle.checked) {
                    section.style.display = 'block';
                    hiddenInput.value = '1';
                    if (documentTypeSelect) documentTypeSelect.disabled = false;
                    if (documentTypeSelect) {
                        showDocumentField(documentTypeSelect.value);
                    }
                } else {
                    section.style.display = 'none';
                    hiddenInput.value = '0';
                    if (documentTypeSelect) documentTypeSelect.disabled = true;
                    if (cniField) cniField.style.display = 'none';
                    if (passportField) passportField.style.display = 'none';
                    if (permisField) permisField.style.display = 'none';
                    if (cniInput) { cniInput.disabled = true; cniInput.required = false; cniInput.value = ''; }
                    if (passportInput) { passportInput.disabled = true; passportInput.required = false; passportInput.value = ''; }
                    if (permisInput) { permisInput.disabled = true; permisInput.required = false; permisInput.value = ''; }
                }
            }

            function showDocumentField(documentType) {
                if (!toggle.checked) return;
                
                if (cniField) cniField.style.display = 'none';
                if (passportField) passportField.style.display = 'none';
                if (permisField) permisField.style.display = 'none';
                
                if (cniInput) { cniInput.disabled = true; cniInput.required = false; }
                if (passportInput) { passportInput.disabled = true; passportInput.required = false; }
                if (permisInput) { permisInput.disabled = true; permisInput.required = false; }
                
                if (documentType === 'cni' && cniField && cniInput) {
                    cniField.style.display = 'block';
                    cniInput.disabled = false;
                    cniInput.required = true;
                } else if (documentType === 'passport' && passportField && passportInput) {
                    passportField.style.display = 'block';
                    passportInput.disabled = false;
                    passportInput.required = true;
                } else if (documentType === 'driving_licenses' && permisField && permisInput) {
                    permisField.style.display = 'block';
                    permisInput.disabled = false;
                    permisInput.required = true;
                }
            }

            if (toggle) {
                toggle.addEventListener('change', toggleIdentitySection);
                if (toggle.checked) {
                    toggleIdentitySection();
                    if (documentTypeSelect && documentTypeSelect.value) {
                        showDocumentField(documentTypeSelect.value);
                    }
                }
            }

            if (documentTypeSelect) {
                documentTypeSelect.addEventListener('change', function() {
                    if (toggle && toggle.checked) {
                        showDocumentField(this.value);
                    }
                });
            }
        });

        // Gestion du toggle pour les horaires de travail
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('schedule-toggle');
            const section = document.getElementById('schedule-section');
            const hiddenInput = document.getElementById('has_schedule');

            function toggleScheduleSection() {
                if (toggle.checked) {
                    section.style.display = 'block';
                    hiddenInput.value = '1';
                } else {
                    section.style.display = 'none';
                    hiddenInput.value = '0';
                    document.querySelectorAll('.schedule-input').forEach(input => {
                        input.disabled = true;
                        input.value = '';
                    });
                    document.querySelectorAll('input[type="checkbox"][id^="checkbox-"]').forEach(checkbox => {
                        checkbox.checked = false;
                    });
                }
            }

            if (toggle) {
                toggle.addEventListener('change', toggleScheduleSection);
                if (toggle.checked) toggleScheduleSection();
            }
        });

        // Gestion des horaires par jour (activation/désactivation des champs)
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"][id^="checkbox-"]');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const day = this.id.replace('checkbox-', '');
                    const startTime = document.getElementById(`start_time_${day}`);
                    const endTime = document.getElementById(`end_time_${day}`);
                    if (this.checked) {
                        if (startTime) { startTime.disabled = false; }
                        if (endTime) { endTime.disabled = false; }
                    } else {
                        if (startTime) { startTime.disabled = true; startTime.value = ''; }
                        if (endTime) { endTime.disabled = true; endTime.value = ''; }
                    }
                });
            });
        });

        // Gestion des pièces d'identité - Version intégrée avec le toggle
        document.addEventListener('DOMContentLoaded', function() {
            const documentTypeSelect = document.getElementById('identity_document_type');
            const cniField = document.getElementById('cni_field');
            const passportField = document.getElementById('passport_field');
            const permisField = document.getElementById('permis_field');
            const cniInput = document.getElementById('cni_number');
            const passportInput = document.getElementById('passport_number');
            const permisInput = document.getElementById('permis_number');
            const hiddenInput = document.getElementById('id_number_hidden');
            const identityToggle = document.getElementById('identity-toggle');

            function showDocumentField(documentType) {
                if (!identityToggle || !identityToggle.checked) {
                    if (cniField) cniField.style.display = 'none';
                    if (passportField) passportField.style.display = 'none';
                    if (permisField) permisField.style.display = 'none';
                    if (cniInput) { cniInput.disabled = true; cniInput.required = false; }
                    if (passportInput) { passportInput.disabled = true; passportInput.required = false; }
                    if (permisInput) { permisInput.disabled = true; permisInput.required = false; }
                    return;
                }

                if (cniField) cniField.style.display = 'none';
                if (passportField) passportField.style.display = 'none';
                if (permisField) permisField.style.display = 'none';

                if (cniInput) { cniInput.disabled = true; cniInput.required = false; }
                if (passportInput) { passportInput.disabled = true; passportInput.required = false; }
                if (permisInput) { permisInput.disabled = true; permisInput.required = false; }

                if (documentType === 'cni' && cniField && cniInput) {
                    cniField.style.display = 'block';
                    cniInput.disabled = false;
                    cniInput.required = true;
                } else if (documentType === 'passport' && passportField && passportInput) {
                    passportField.style.display = 'block';
                    passportInput.disabled = false;
                    passportInput.required = true;
                } else if (documentType === 'driving_licenses' && permisField && permisInput) {
                    permisField.style.display = 'block';
                    permisInput.disabled = false;
                    permisInput.required = true;
                }
            }

            function updateHiddenInput() {
                if (!documentTypeSelect) return;
                const documentType = documentTypeSelect.value;
                let value = '';
                if (documentType === 'cni' && cniInput && cniInput.value) value = cniInput.value;
                else if (documentType === 'passport' && passportInput && passportInput.value) value = passportInput.value;
                else if (documentType === 'driving_licenses' && permisInput && permisInput.value) value = permisInput.value;
                if (hiddenInput) hiddenInput.value = value;
            }

            if (documentTypeSelect) {
                documentTypeSelect.addEventListener('change', function() {
                    showDocumentField(this.value);
                    updateHiddenInput();
                });
            }

            if (cniInput) cniInput.addEventListener('input', updateHiddenInput);
            if (passportInput) passportInput.addEventListener('input', updateHiddenInput);
            if (permisInput) permisInput.addEventListener('input', updateHiddenInput);

            const form = document.querySelector('form');
            if (form) form.addEventListener('submit', updateHiddenInput);

            if (documentTypeSelect && identityToggle && identityToggle.checked) {
                if (documentTypeSelect.value) showDocumentField(documentTypeSelect.value);
                else { documentTypeSelect.value = 'cni'; showDocumentField('cni'); }
            }
        });
    </script>
</x-app-layout>