<x-app-layout>
    <div class="add-demand-simple-container">
        {{-- En-tête avec dégradé --}}
        <div class="add-demand-header">
            <div class="header-content">
                <h1 class="header-title">{{ __('Nouvelle Demande de Congé / Permission') }}</h1>
                <p class="header-subtitle">{{ __('Créez une nouvelle demande pour un employé') }}</p>
            </div>
            <div class="header-icon">
                <i class="fas fa-calendar-plus"></i>
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

        @if ($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <ul class="list-disc pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" onclick="this.parentElement.remove()" class="alert-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        {{-- Formulaire --}}
        <div class="add-demand-card">
            <form method="POST" action="{{ route('demandes.store') }}">
                @csrf

                {{-- Section Informations de la demande --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div>
                            <h3 class="section-title">{{ __('Informations de la demande') }}</h3>
                            <p class="section-desc">{{ __('Employé, catégorie et dates') }}</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">{{ __('Sélectionner l\'Employé') }} <span class="required">*</span></label>
                                <select id="user_id" name="user_id" required class="form-input">
                                    <option value="" disabled selected>{{ __('Choisir un employé...') }}</option>
                                    @foreach($employes as $employe)
                                        <option value="{{ $employe->id }}" {{ old('user_id') == $employe->id ? 'selected' : '' }}>
                                            {{ $employe->first_name }} {{ $employe->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">{{ __('Catégorie') }} <span class="required">*</span></label>
                                <select id="categorie" name="categorie" required class="form-input">
                                    <option value="conge" {{ old('categorie') == 'conge' ? 'selected' : '' }}>{{ __('Congé') }}</option>
                                    <option value="permission" {{ old('categorie') == 'permission' ? 'selected' : '' }}>{{ __('Permission') }}</option>
                                </select>
                                @error('categorie')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section Période --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <h3 class="section-title">{{ __('Période de la demande') }}</h3>
                            <p class="section-desc">{{ __('Dates de début et de fin') }}</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">{{ __('Date de Début') }} <span class="required">*</span></label>
                                <input type="date" id="date_debut" name="date_debut" required
                                    value="{{ old('date_debut') }}"
                                    class="form-input">
                                @error('date_debut')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">{{ __('Date de Fin') }} <span class="required">*</span></label>
                                <input type="date" id="date_fin" name="date_fin" required
                                    value="{{ old('date_fin') }}"
                                    class="form-input">
                                @error('date_fin')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section Motif --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-pencil-alt"></i>
                        </div>
                        <div>
                            <h3 class="section-title">{{ __('Motif de la demande') }}</h3>
                            <p class="section-desc">{{ __('Explication détaillée') }}</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="form-row-single">
                            <div class="form-group">
                                <label class="form-label">{{ __('Motif / Explication') }} <span class="required">*</span></label>
                                <textarea id="motif" name="motif" class="form-input"
                                    rows="4" placeholder="{{ __('Précisez le motif de votre absence...') }}">{{ old('motif') }}</textarea>
                                @error('motif')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Boutons d'action --}}
                <div class="form-actions">
                    <a href="{{ route('admin.demandes.index') }}" class="btn-cancel">
                        <i class="fas fa-times"></i> {{ __('Annuler') }}
                    </a>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> {{ __('Soumettre la demande') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* ============================================ */
        /* DESIGN MODERNE - PAGE AJOUT DEMANDE         */
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

        .add-demand-simple-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 24px 28px;
        }

        /* En-tête */
        .add-demand-header {
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
        .add-demand-card {
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

        /* Grilles */
        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .form-row-single {
            display: grid;
            grid-template-columns: 1fr;
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
            appearance: auto;
        }
        .form-input:focus {
            outline: none;
            border-color: var(--vp);
            box-shadow: 0 0 0 3px rgba(46,125,100,0.1);
        }
        select.form-input {
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%235a6b6f' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 40px;
        }
        textarea.form-input {
            resize: vertical;
            min-height: 100px;
        }
        input[type="date"].form-input {
            cursor: pointer;
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
            text-decoration: none;
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
            .add-demand-simple-container { padding: 16px; }
            .add-demand-header { padding: 20px; }
            .header-icon { display: none; }
            .form-row { grid-template-columns: 1fr; gap: 16px; }
            .section-header { padding: 14px 20px; }
            .section-body { padding: 20px; }
            .form-actions { flex-direction: column; }
            .btn-submit, .btn-cancel { justify-content: center; }
        }
    </style>
</x-app-layout>
