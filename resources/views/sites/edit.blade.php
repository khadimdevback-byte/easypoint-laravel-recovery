<x-app-layout>
    @php
        $company = Auth::user()->company;
        $currentSitesCount = App\Models\Site::where('company_id', $company->id)->count();

        $subscription = $company->activeSubscription;

        $maxSites = 2;
        $planName = 'Free';

        if ($subscription && $subscription->plan) {
            $plan = $subscription->plan;
            $planName = $plan->name ?? 'Free';
            $planNameLower = strtolower($planName);

            if ($plan->is_visible === false || $plan->is_visible === 0) {
                $maxSites = 999999;
            } else {
                $quotas = [
                    'Free' => 2,
                    'STARTER' => 2,
                    'basic' => 2,
                    'GROWTH' => 2,
                    'professional' => 2,
                    'BUSINESS' => 2,
                    'enterprise' => 2,
                ];
                $maxSites = $quotas[$planNameLower] ?? 2;
            }
        }

        $canCreateSite = ($maxSites === 999999) || ($currentSitesCount < $maxSites);
        $remainingSites = ($maxSites === 999999) ? 'Illimité' : ($maxSites - $currentSitesCount);
        $percentageUsed = ($maxSites !== 999999 && $maxSites > 0) ? min(($currentSitesCount / $maxSites) * 100, 100) : 0;

        // Récupérer les IDs des contrôleurs et employés déjà associés au site
        $selectedControllers = $site->controller->pluck('id')->toArray();
        $selectedUsers = $site->users->pluck('id')->toArray();
    @endphp

    <div class="add-site-container">
        {{-- Bandeau limite --}}
        <div class="info-banner">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-chart-line" style="color: #2e7d64;"></i>
                    <span style="font-size: 0.8rem; color: #1a5a48;">
                        Plan <strong>{{ $planName }}</strong> :
                        @if($maxSites === 999999)
                            Sites illimités. Vous avez actuellement <strong>{{ $currentSitesCount }}</strong> site(s).
                        @else
                            Vous avez créé <strong>{{ $currentSitesCount }}</strong> site(s) sur <strong>{{ $maxSites }}</strong> maximum.
                            @if($remainingSites > 0)
                                Il vous reste <strong>{{ $remainingSites }}</strong> emplacement(s).
                            @endif
                        @endif
                    </span>
                </div>
                @if($maxSites !== 999999)
                    <a href="{{ route('subscriptions.upgrade') }}" style="font-size: 0.7rem; color: #2e7d64; text-decoration: none; font-weight: 600;">
                        <i class="fas fa-crown"></i> Upgrade
                    </a>
                @endif
            </div>
            @if($maxSites !== 999999 && $percentageUsed > 0)
                <div style="height: 4px; background: #d4dfe2; border-radius: 2px; margin-top: 10px;">
                    <div style="width: {{ $percentageUsed }}%; height: 4px; background: #2e7d64; border-radius: 2px;"></div>
                </div>
            @endif
        </div>

        {{-- En-tête --}}
        <div class="add-site-header">
            <div class="header-content">
                <h1 class="header-title">{{ __('Modifier le lieu de travail') }}</h1>
                <p class="header-subtitle">{{ __('Modifiez les informations du lieu de travail') }}</p>
            </div>
            <div class="header-icon"><i class="fas fa-map-marker-alt"></i></div>
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
        <div class="add-site-card">
            <form method="POST" action="{{ route('sites.update', $site->id) }}" enctype="multipart/form-data" id="siteForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="company_id" value="{{ Auth::user()->company_id }}">

                {{-- Informations générales --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="fas fa-building"></i></div>
                        <div>
                            <h3 class="section-title">{{ __('Informations générales') }}</h3>
                            <p class="section-desc">{{ __('Nom du lieu de travail') }}</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-row">
                            <div class="form-group" style="grid-column: span 2;">
                                <label class="form-label">{{ __('Nom du site') }} <span class="required">*</span></label>
                                <input type="text" name="name" class="form-input" placeholder="{{ __('Nom du site') }}" value="{{ old('name', $site->name) }}" required>
                                <x-input-error :messages="$errors->get('name')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Personnel autorisé --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="fas fa-users"></i></div>
                        <div>
                            <h3 class="section-title">{{ __('Personnel autorisé') }}</h3>
                            <p class="section-desc">{{ __('Contrôleurs et employés ayant accès au site') }}</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-row">
                            {{-- CONTRÔLEURS --}}
                            <div class="form-group">
                                <label class="form-label">{{ __('Contrôleurs') }}</label>
                                <div class="multi-select-container">
                                    <div class="multi-select-search">
                                        <i class="fas fa-search"></i>
                                        <input type="text" placeholder="Rechercher un contrôleur..." class="ms-search" data-target="list-controllers" autocomplete="off">
                                    </div>
                                    <div class="multi-select-list" id="list-controllers">
                                        @forelse($controllers as $controller)
                                            <label class="multi-select-item {{ in_array($controller->id, old('controller_id', $selectedControllers)) ? 'selected' : '' }}">
                                                <input type="checkbox" name="controller_id[]" value="{{ $controller->id }}"
                                                    {{ in_array($controller->id, old('controller_id', $selectedControllers)) ? 'checked' : '' }}>
                                                <div class="ms-avatar">{{ strtoupper(substr($controller->first_name ?? $controller->name, 0, 1)) }}</div>
                                                <span>{{ $controller->first_name }} {{ $controller->name }}</span>
                                                <i class="fas fa-check ms-check"></i>
                                            </label>
                                        @empty
                                            <div class="ms-empty"><i class="fas fa-info-circle"></i> Aucun contrôleur disponible</div>
                                        @endforelse
                                    </div>
                                    <div class="multi-select-footer">
                                        <i class="fas fa-user-check"></i>
                                        <span class="ms-count" data-target="list-controllers">0</span> sélectionné(s)
                                    </div>
                                </div>
                                <x-input-error :messages="$errors->get('controller_id')" />
                            </div>

                            {{-- EMPLOYÉS --}}
                            <div class="form-group">
                                <label class="form-label">{{ __('Employés admis') }}</label>
                                <div class="multi-select-container">
                                    <div class="multi-select-search">
                                        <i class="fas fa-search"></i>
                                        <input type="text" placeholder="Rechercher un employé..." class="ms-search" data-target="list-users" autocomplete="off">
                                    </div>
                                    <div class="multi-select-list" id="list-users">
                                        @forelse($users as $user)
                                            <label class="multi-select-item {{ in_array($user->id, old('user_id', $selectedUsers)) ? 'selected' : '' }}">
                                                <input type="checkbox" name="user_id[]" value="{{ $user->id }}"
                                                    {{ in_array($user->id, old('user_id', $selectedUsers)) ? 'checked' : '' }}>
                                                <div class="ms-avatar">{{ strtoupper(substr($user->first_name ?? $user->name, 0, 1)) }}</div>
                                                <span>{{ $user->first_name }} {{ $user->name }}</span>
                                                <i class="fas fa-check ms-check"></i>
                                            </label>
                                        @empty
                                            <div class="ms-empty"><i class="fas fa-info-circle"></i> Aucun employé disponible</div>
                                        @endforelse
                                    </div>
                                    <div class="multi-select-footer">
                                        <i class="fas fa-users"></i>
                                        <span class="ms-count" data-target="list-users">0</span> sélectionné(s)
                                    </div>
                                </div>
                                <x-input-error :messages="$errors->get('user_id')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Configuration --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="fas fa-toggle-on"></i></div>
                        <div>
                            <h3 class="section-title">{{ __('Configuration') }}</h3>
                            <p class="section-desc">{{ __('Statut et paramètres') }}</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">{{ __('Statut') }} <span class="required">*</span></label>
                                <div class="select-wrapper">
                                    <select name="status" class="form-select">
                                        <option value="active" {{ old('status', $site->status) == 'active' ? 'selected' : '' }}>{{ __('Actif') }}</option>
                                        <option value="inactive" {{ old('status', $site->status) == 'inactive' ? 'selected' : '' }}>{{ __('Inactif') }}</option>
                                    </select>
                                    <i class="fas fa-chevron-down select-icon"></i>
                                </div>
                                <x-input-error :messages="$errors->get('status')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Description & Fuseau horaire --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="fas fa-clock"></i></div>
                        <div>
                            <h3 class="section-title">{{ __('Description & Fuseau horaire') }}</h3>
                            <p class="section-desc">{{ __('Informations complémentaires') }}</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">{{ __('Description') }}</label>
                                <textarea name="description" class="form-textarea" rows="4" placeholder="{{ __('Description du lieu...') }}">{{ old('description', $site->description) }}</textarea>
                                <x-input-error :messages="$errors->get('description')" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Fuseau horaire') }}</label>
                                <div class="select-wrapper">
                                    <select name="timezone" class="form-select">
                                        @foreach ($timezones as $key => $value)
                                            <option value="{{ $key }}" {{ old('timezone', $site->timezone) == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <i class="fas fa-chevron-down select-icon"></i>
                                </div>
                                <x-input-error :messages="$errors->get('timezone')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Horaires de travail --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="fas fa-calendar-alt"></i></div>
                        <div>
                            <h3 class="section-title">{{ __('Horaires de travail') }}</h3>
                            <p class="section-desc">{{ __('Définissez les horaires par jour') }}</p>
                        </div>
                    </div>
                    <div class="section-body">
                        @foreach (['monday' => 'Lundi', 'tuesday' => 'Mardi', 'wednesday' => 'Mercredi',
                                   'thursday' => 'Jeudi', 'friday' => 'Vendredi', 'saturday' => 'Samedi',
                                   'sunday' => 'Dimanche'] as $dayEn => $dayFr)
                            @php
                                $isChecked = old($dayEn . '_checked', $site->{$dayEn . '_checked'});
                                $startTime = old('start_time_' . $dayEn, $site->{'start_time_' . $dayEn});
                                $endTime = old('end_time_' . $dayEn, $site->{'end_time_' . $dayEn});
                            @endphp
                            <div class="schedule-row">
                                <label class="schedule-label">
                                    <input type="checkbox" id="checkbox-{{ $dayEn }}" name="{{ $dayEn }}_checked" value="1"
                                        {{ $isChecked ? 'checked' : '' }}>
                                    <span>{{ $dayFr }}</span>
                                </label>
                                <div class="schedule-times">
                                    <input type="time" id="start_time_{{ $dayEn }}" name="start_time_{{ $dayEn }}"
                                        class="schedule-input"
                                        value="{{ $startTime ? \Carbon\Carbon::parse($startTime)->format('H:i') : '' }}"
                                        {{ $isChecked ? '' : 'disabled' }}>
                                    <span class="schedule-sep">—</span>
                                    <input type="time" id="end_time_{{ $dayEn }}" name="end_time_{{ $dayEn }}"
                                        class="schedule-input"
                                        value="{{ $endTime ? \Carbon\Carbon::parse($endTime)->format('H:i') : '' }}"
                                        {{ $isChecked ? '' : 'disabled' }}>
                                </div>
                            </div>
                        @endforeach
                        <div class="form-group" style="margin-top: 20px;">
                            <label class="form-label">{{ __('Marquage tardif après (minutes)') }}</label>
                            <input type="number" name="late_mark_duration" class="form-input" placeholder="{{ __('Minutes') }}" value="{{ old('late_mark_duration', $site->late_mark_duration) }}">
                            <x-input-error :messages="$errors->get('late_mark_duration')" />
                        </div>
                    </div>
                </div>

                {{-- Localisation --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="fas fa-map-pin"></i></div>
                        <div>
                            <h3 class="section-title">{{ __('Localisation') }}</h3>
                            <p class="section-desc">{{ __('Positionnez le lieu sur la carte') }}</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div style="margin-bottom: 25px;">
                            <button type="button" id="getLocation" class="btn-location">
                                <i class="fas fa-location-dot"></i> Utiliser ma position actuelle
                            </button>
                        </div>
                        <div class="form-row" style="margin-bottom: 25px;">
                            <div class="form-group">
                                <label class="form-label">{{ __('Adresse') }} <span class="required">*</span></label>
                                <input type="text" id="address" name="address" class="form-input" placeholder="{{ __('Adresse') }}" value="{{ old('address', $site->address) }}" required>
                                <x-input-error :messages="$errors->get('address')" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Rayon (en mètres)') }} <span class="required">*</span></label>
                                <input type="number" id="radius" name="radius" class="form-input" placeholder="{{ __('Rayon') }}" value="{{ old('radius', $site->radius) }}" required>
                                <x-input-error :messages="$errors->get('radius')" />
                            </div>
                        </div>
                        <div class="form-row" style="margin-bottom: 25px;">
                            <div class="form-group">
                                <label class="form-label">{{ __('Latitude') }} <span class="required">*</span></label>
                                <input type="text" id="latitude" name="latitude" class="form-input" placeholder="{{ __('Latitude') }}" value="{{ old('latitude', $site->latitude) }}" required readonly>
                                <x-input-error :messages="$errors->get('latitude')" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('Longitude') }} <span class="required">*</span></label>
                                <input type="text" id="longitude" name="longitude" class="form-input" placeholder="{{ __('Longitude') }}" value="{{ old('longitude', $site->longitude) }}" required readonly>
                                <x-input-error :messages="$errors->get('longitude')" />
                            </div>
                        </div>
                        <div style="margin-bottom: 25px;">
                            <label class="form-label">{{ __('Rechercher un lieu') }}</label>
                            <input type="text" id="search" class="form-input" placeholder="{{ __('Chercher une adresse...') }}">
                            <div id="results" class="search-results"></div>
                        </div>
                        <div id="map" class="site-map"></div>
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

        .add-site-container { max-width: 1200px; margin: 0 auto; padding: 24px 28px; }

        .info-banner { background: #e8f5f1; border-radius: 12px; padding: 12px 16px; margin-bottom: 20px; border-left: 4px solid #2e7d64; }

        .add-site-header {
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

        .add-site-card { background: #fff; border-radius: 20px; border: 1px solid var(--gl); overflow: hidden; }

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
        .form-input:focus, .form-textarea:focus { outline: none; border-color: var(--vp); box-shadow: 0 0 0 3px rgba(46,125,100,0.08); }

        .select-wrapper { position: relative; }
        .form-select {
            width: 100%; padding: 11px 14px; border: 1.5px solid var(--gl);
            border-radius: 12px; font-size: 0.85rem; background: white;
            appearance: none; transition: var(--t);
        }
        .form-select:focus { outline: none; border-color: var(--vp); box-shadow: 0 0 0 3px rgba(46,125,100,0.08); }
        .select-icon { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: var(--gm); font-size: 0.75rem; pointer-events: none; }

        /* ── Multi-select ────────────────────────────────────── */
        .multi-select-container {
            border: 1.5px solid var(--gl);
            border-radius: 12px;
            overflow: hidden;
            background: white;
            transition: border-color 0.2s;
        }
        .multi-select-container:focus-within {
            border-color: var(--vp);
            box-shadow: 0 0 0 3px rgba(46,125,100,0.08);
        }
        .multi-select-search {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 14px; border-bottom: 1px solid var(--gl); background: var(--gtc);
        }
        .multi-select-search i { color: var(--gm); font-size: 0.75rem; }
        .multi-select-search input {
            border: none; background: transparent; outline: none;
            font-size: 0.82rem; width: 100%; color: var(--gf);
        }
        .multi-select-list { max-height: 220px; overflow-y: auto; }
        .multi-select-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 14px; cursor: pointer;
            transition: background 0.15s;
            border-bottom: 1px solid #f1f5f9;
            user-select: none;
        }
        .multi-select-item:last-child { border-bottom: none; }
        .multi-select-item:hover { background: #f0fdf4; }
        .multi-select-item input[type="checkbox"] { display: none; }
        .ms-avatar {
            width: 30px; height: 30px; border-radius: 8px;
            background: var(--gtc); color: var(--vp);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem; font-weight: 700; flex-shrink: 0;
            border: 1px solid #dcfce7;
            transition: var(--t);
        }
        .multi-select-item span { font-size: 0.82rem; color: var(--gf); flex-grow: 1; }
        .ms-check { color: var(--vp); font-size: 0.75rem; opacity: 0; transition: opacity 0.15s; }
        .multi-select-item.selected { background: #f0fdf4; }
        .multi-select-item.selected .ms-avatar { background: var(--vp); color: white; border-color: var(--vp); }
        .multi-select-item.selected .ms-check { opacity: 1; }
        .multi-select-item.ms-hidden { display: none; }
        .ms-empty { padding: 20px; text-align: center; color: var(--gm); font-size: 0.8rem; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .multi-select-footer {
            display: flex; align-items: center; gap: 6px;
            padding: 8px 14px; background: var(--gtc);
            border-top: 1px solid var(--gl);
            font-size: 0.72rem; color: var(--gm); font-weight: 600;
        }

        /* ── Horaires ────────────────────────────────────────── */
        .schedule-row {
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 16px; padding: 12px 0;
            border-bottom: 1px solid var(--gl);
        }
        .schedule-row:last-of-type { border-bottom: none; }
        .schedule-label { display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: 600; color: var(--gf); min-width: 120px; }
        .schedule-label input { width: 18px; height: 18px; accent-color: var(--vp); cursor: pointer; }
        .schedule-times { display: flex; align-items: center; gap: 12px; }
        .schedule-input { padding: 8px 12px; border: 1px solid var(--gl); border-radius: 10px; font-size: 0.8rem; background: white; width: 120px; transition: var(--t); }
        .schedule-input:focus { outline: none; border-color: var(--vp); }
        .schedule-input:disabled { background: var(--gtc); opacity: 0.6; cursor: not-allowed; }
        .schedule-sep { color: var(--gm); font-weight: 300; }

        .btn-location {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--gtc); color: var(--vp); border: 1px solid var(--gl);
            padding: 8px 20px; border-radius: 40px; font-size: 0.8rem; font-weight: 500; cursor: pointer;
            transition: var(--t);
        }
        .btn-location:hover { background: var(--vp); color: white; border-color: var(--vp); }

        .search-results {
            border: 1px solid var(--gl); border-radius: 12px; max-height: 150px;
            overflow-y: auto; display: none; background: white; margin-top: 8px;
        }
        .search-results div { padding: 10px 14px; cursor: pointer; font-size: 0.8rem; transition: var(--t); }
        .search-results div:hover { background: var(--gtc); color: var(--vp); }

        .site-map { height: 400px; border-radius: 12px; border: 1px solid var(--gl); overflow: hidden; }

        .form-actions {
            display: flex; justify-content: flex-end; gap: 16px;
            padding: 20px 24px; background: var(--gtc); border-top: 1px solid var(--gl);
        }
        .btn-submit, .btn-cancel {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 28px; border-radius: 40px; font-size: 0.8rem; font-weight: 600; cursor: pointer; border: none;
            transition: var(--t);
        }
        .btn-submit { background: var(--vp); color: white; }
        .btn-submit:hover { background: var(--vf); transform: translateY(-2px); box-shadow: 0 8px 20px rgba(46,125,100,0.3); }
        .btn-cancel { background: var(--gtc); color: var(--gf); border: 1px solid var(--gl); }
        .btn-cancel:hover { background: #e5e7eb; }

        @media (max-width: 768px) {
            .add-site-container { padding: 16px; }
            .add-site-header { padding: 20px; flex-direction: column; text-align: center; }
            .header-icon { display: none; }
            .form-row { grid-template-columns: 1fr; gap: 16px; }
            .site-map { height: 300px; }
            .schedule-row { flex-direction: column; align-items: stretch; }
            .schedule-times { justify-content: space-between; }
            .schedule-input { width: 100%; }
            .form-actions { flex-direction: column; }
            .btn-submit, .btn-cancel { justify-content: center; }
        }
    </style>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
    $(document).ready(function () {
        // ── Horaires ──────────────────────────────────────────────
        $('input[type="checkbox"][id^="checkbox-"]').change(function () {
            var day = $(this).attr('id').replace('checkbox-', '');
            if (this.checked) {
                $('#start_time_' + day).prop('disabled', false);
                $('#end_time_' + day).prop('disabled', false);
            } else {
                $('#start_time_' + day).prop('disabled', true).val('');
                $('#end_time_' + day).prop('disabled', true).val('');
            }
        });

        // ── Multi-select ──────────────────────────────────────────
        function initMultiSelect(listId) {
            const list = document.getElementById(listId);
            if (!list) return;
            const items = list.querySelectorAll('.multi-select-item');
            const countEl = document.querySelector('.ms-count[data-target="' + listId + '"]');

            function updateCount() {
                if (!countEl) return;
                countEl.textContent = list.querySelectorAll('input[type="checkbox"]:checked').length;
            }

            // Restaurer état initial
            items.forEach(function (item) {
                const cb = item.querySelector('input[type="checkbox"]');
                if (cb && cb.checked) {
                    item.classList.add('selected');
                }
            });

            // Clic sur un item
            items.forEach(function (item) {
                item.addEventListener('click', function () {
                    const cb = this.querySelector('input[type="checkbox"]');
                    cb.checked = !cb.checked;
                    this.classList.toggle('selected', cb.checked);
                    updateCount();
                });
            });

            updateCount();
        }

        // ── Recherche dans le multi-select ────────────────────────
        document.querySelectorAll('.ms-search').forEach(function (input) {
            input.addEventListener('input', function () {
                const query = this.value.toLowerCase().trim();
                const listId = this.dataset.target;
                const list = document.getElementById(listId);
                if (!list) return;
                list.querySelectorAll('.multi-select-item').forEach(function (item) {
                    const text = item.querySelector('span').textContent.toLowerCase();
                    item.classList.toggle('ms-hidden', query.length > 0 && !text.includes(query));
                });
            });
        });

        initMultiSelect('list-controllers');
        initMultiSelect('list-users');

        // ── Carte Leaflet ─────────────────────────────────────────
        var initialLat = parseFloat($('#latitude').val()) || 48.8566;
        var initialLng = parseFloat($('#longitude').val()) || 2.3522;

        var map = L.map('map').setView([initialLat, initialLng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);

        function updateHiddenFields(lat, lng, address) {
            $('#latitude').val(lat);
            $('#longitude').val(lng);
            if (address) $('#address').val(address);
        }

        function fetchAddress(lat, lng) {
            $.getJSON('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng, function (data) {
                if (data.display_name) $('#address').val(data.display_name);
            });
        }

        function updateMap(lat, lng) {
            marker.setLatLng([lat, lng]);
            map.setView([lat, lng], 13);
            fetchAddress(lat, lng);
        }

        $('#search').on('input', function () {
            var query = $(this).val();
            if (query.length <= 2) { $('#results').hide(); return; }
            $.getJSON('https://nominatim.openstreetmap.org/search?q=' + encodeURIComponent(query) + '&format=json&limit=5', function (data) {
                var rc = $('#results').empty().hide();
                if (data.length > 0) {
                    rc.show();
                    data.forEach(function (loc) {
                        $('<div>' + loc.display_name + '</div>').on('click', function () {
                            var lat = parseFloat(loc.lat);
                            var lng = parseFloat(loc.lon);
                            updateMap(lat, lng);
                            updateHiddenFields(lat, lng, loc.display_name);
                            rc.hide();
                            $('#search').val(loc.display_name);
                        }).appendTo(rc);
                    });
                }
            });
        });

        marker.on('dragend', function () {
            var ll = marker.getLatLng();
            updateHiddenFields(ll.lat, ll.lng);
            fetchAddress(ll.lat, ll.lng);
        });

        $('#getLocation').on('click', function () {
            if (!navigator.geolocation) { alert('Géolocalisation non supportée'); return; }
            $(this).html('<i class="fas fa-spinner fa-spin"></i> Recherche...').prop('disabled', true);
            
            navigator.geolocation.getCurrentPosition(function (pos) {
                updateMap(pos.coords.latitude, pos.coords.longitude);
                updateHiddenFields(pos.coords.latitude, pos.coords.longitude);
                $('#getLocation').html('<i class="fas fa-location-dot"></i> Utiliser ma position actuelle').prop('disabled', false);
            }, function (err) {
                alert('Erreur: ' + err.message);
                $('#getLocation').html('<i class="fas fa-location-dot"></i> Utiliser ma position actuelle').prop('disabled', false);
            });
        });

        // Validation du formulaire
        $('#siteForm').on('submit', function(e) {
            var errors = [];
            var days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            
            days.forEach(function(day) {
                var checked = $('#checkbox-' + day).is(':checked');
                if (checked) {
                    var start = $('#start_time_' + day).val();
                    var end = $('#end_time_' + day).val();
                    if (start && end && start >= end) {
                        errors.push('L\'heure de fin doit être après l\'heure de début pour ' + 
                            $('#checkbox-' + day).next('span').text());
                    }
                }
            });
            
            if (errors.length > 0) {
                alert(errors.join('\n'));
                e.preventDefault();
                return false;
            }
        });
    });
    </script>
</x-app-layout>