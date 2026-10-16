<x-app-layout>
    {{-- ===================================================== --}}
    {{-- BARRE D'ACTIONS — collée sous la navbar               --}}
    {{-- ===================================================== --}}
    <div class="action-bar">
        <div class="action-bar-inner">
            <a href="{{ route('superadmin.subscriptions.index') }}" class="action-btn">
                <i class="fas fa-arrow-left"></i>
                <span>{{ __('Retour à la liste') }}</span>
            </a>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- CONTENU PRINCIPAL                                     --}}
    {{-- ===================================================== --}}
    <div class="page-content">
        <div class="content-card">
            <div class="content-header">
                <h1 class="page-title">{{ __('Attribuer une Souscription à une Compagnie') }}</h1>
            </div>

            <!-- Erreurs -->
            @if ($errors->any())
                <div id="session-error-alert-div" class="alert alert--error">
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button onclick="document.getElementById('session-error-alert-div').style.display='none'" class="alert-close">&#10005;</button>
                </div>
            @endif

            <!-- Formulaire -->
            <form method="POST" action="{{ route('superadmin.subscriptions.store') }}" class="form-container">
                @csrf

                <!-- Ligne 1: Compagnie + Type de tarification -->
                <div class="form-grid">
                    <div class="form-group">
                        <x-input-label for="company_id" :value="__('Sélectionner la Compagnie *')" class="form-label" />
                        <select id="company_id" name="company_id" required class="form-select">
                            <option value="">-- {{ __('Choisir une entreprise') }} --</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                    {{ $company->compagny_name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('company_id')" class="form-error" />
                    </div>

                    <div class="form-group">
                        <x-input-label for="plan_type" :value="__('Type de Tarification *')" class="form-label" />
                        <select id="plan_type" name="plan_type" onchange="togglePlanType()" class="form-select">
                            <option value="standard" {{ old('plan_type', 'standard') == 'standard' ? 'selected' : '' }}>{{ __('Plan Standard Public') }}</option>
                            <option value="custom" {{ old('plan_type') == 'custom' ? 'selected' : '' }}>{{ __('Plan Sur Mesure (Entreprise)') }}</option>
                        </select>
                        <x-input-error :messages="$errors->get('plan_type')" class="form-error" />
                    </div>
                </div>

                <!-- Ligne 2: Plan Standard -->
                <div id="section_standard" class="form-grid">
                    <div class="form-group">
                        <x-input-label for="plan_id" :value="__('Choisir le Plan Tarifaire *')" class="form-label" />
                        <select id="plan_id" name="plan_id" onchange="calculateTotal(); checkFreePlan();" class="form-select">
                            <option value="" data-price="0" data-currency="">-- {{ __('Choisir un forfait') }} --</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}" data-price="{{ $plan->price }}" data-currency="{{ $plan->currency }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                    {{ $plan->name }} ({{ $plan->price > 0 ? number_format($plan->price, 0, ',', ' ') . ' ' . $plan->currency . '/mois' : 'Gratuit' }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('plan_id')" class="form-error" />
                    </div>
                </div>

                <!-- Ligne 3: Durée + Mode d'encaissement + Total -->
                <div class="form-grid form-grid-3">
                    <div class="form-group">
                        <x-input-label for="duration_months" :value="__('Durée de l\'engagement * (Mois)')" class="form-label" />
                        <input id="duration_months" name="duration_months" type="number" min="1" max="60" placeholder="Ex: 3, 6, 12" :value="old('duration_months', '1')" oninput="calculateTotal()" required class="form-input" />
                        <small class="form-hint">{{ __('Le système calculera automatiquement la date d\'expiration.') }}</small>
                        <x-input-error :messages="$errors->get('duration_months')" class="form-error" />
                    </div>

                    <div class="form-group">
                        <x-input-label for="payment_mode" :value="__('Mode d\'encaissement *')" class="form-label" />
                        <select id="payment_mode" name="payment_mode" class="form-select">
                            <option value="manual" {{ old('payment_mode') == 'manual' ? 'selected' : '' }}>{{ __('Paiement Manuel (Espèces, Chèque, Virement)') }}</option>
                            <option value="fedapay" {{ old('payment_mode') == 'fedapay' ? 'selected' : '' }}>{{ __('Paiement en ligne (FedaPay Mobile Money)') }}</option>
                            <option value="free" id="free_payment_option" style="display: none; background-color: #f0fdf4; color: #166534;">
                                🎁 {{ __('Gratuit - Aucun paiement requis') }}
                            </option>
                        </select>
                        <x-input-error :messages="$errors->get('payment_mode')" class="form-error" />
                    </div>

                    <div class="form-group">
                        <x-input-label for="total_display" :value="__('Montant Total Estimé')" class="form-label" />
                        <input id="total_display" type="text" value="0 XOF" readonly class="form-input form-input--readonly" />
                    </div>
                </div>

                <!-- ⭐ Message pour les plans gratuits -->
                <div id="free_plan_message" class="free-message" style="display: none;">
                    <div class="free-message-content">
                        <svg class="free-message-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <span class="free-message-title">{{ __('🎉 Plan gratuit sélectionné !') }}</span>
                            <p class="free-message-text">{{ __('Aucun paiement n\'est requis. La souscription sera activée immédiatement.') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Section Plan Sur Mesure -->
                <div id="section_custom" class="custom-section" style="display: none;">
                    <h3 class="custom-section-title">
                        <svg class="custom-section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        {{ __('Configuration de l\'offre sur mesure') }}
                    </h3>

                    <div class="form-grid form-grid-3">
                        <div class="form-group">
                            <x-input-label for="custom_name" :value="__('Nom personnalisé du Pack *')" class="form-label" />
                            <input id="custom_name" name="custom_name" type="text" placeholder="Ex: Offre Spéciale - École Alpha" :value="old('custom_name')" class="form-input" />
                            <x-input-error :messages="$errors->get('custom_name')" class="form-error" />
                        </div>

                        <div class="form-group">
                            <x-input-label for="custom_price" :value="__('Prix Global Négocié (XOF) *')" class="form-label" />
                            <input id="custom_price" name="custom_price" type="number" min="0" placeholder="Ex: 450000" :value="old('custom_price')" oninput="calculateTotal()" class="form-input" />
                            <x-input-error :messages="$errors->get('custom_price')" class="form-error" />
                        </div>

                        <div class="form-group">
                            <x-input-label for="custom_employees" :value="__('Nombre max d\'employés (-1 pour illimité)')" class="form-label" />
                            <input id="custom_employees" name="custom_employees" type="number" min="-1" :value="old('custom_employees', '-1')" class="form-input" />
                            <x-input-error :messages="$errors->get('custom_employees')" class="form-error" />
                        </div>
                    </div>

                    <div class="features-section">
                        <x-input-label class="features-label" :value="__('Sélectionner les Fonctionnalités Incluses *')" />
                        <div class="features-grid">
                            @foreach($features as $feature)
                                <div class="feature-item">
                                    <input type="checkbox" name="features[]" value="{{ $feature->id }}" id="feat_{{ $feature->id }}"
                                        {{ is_array(old('features')) && in_array($feature->id, old('features')) ? 'checked' : '' }}
                                        class="feature-checkbox">
                                    <label for="feat_{{ $feature->id }}" class="feature-label">
                                        {{ $feature->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('features')" class="form-error" />
                    </div>
                </div>

                <!-- Boutons -->
                <div class="form-actions">
                    <button type="button" onclick="window.location.href='{{ route('superadmin.subscriptions.index') }}'" class="btn btn--cancel">
                        {{ __('Annuler') }}
                    </button>

                    <button type="submit" id="submit_button" class="btn btn--submit">
                        {{ __('Valider la souscription') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* ============================================ */
        /* STYLES MODERNES COMME L'INDEX                 */
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
            padding: 0 24px;
            height: 46px;
            gap: 4px;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: rgba(255,255,255,0.92);
            font-size: 0.82rem;
            font-weight: 500;
            padding: 6px 16px;
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.18s;
            white-space: nowrap;
            cursor: pointer;
        }

        .action-btn:hover { background: rgba(255,255,255,0.15); color: #fff; }
        .action-btn i { font-size: 0.78rem; opacity: 0.85; }

        .page-content { padding-top: 46px; }
        .content-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--gl);
            padding: 24px;
            overflow: visible;
            position: relative;
            margin: 0 24px;
        }

        .content-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
        }

        .page-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }

        /* Alertes */
        .alert {
            position: relative;
            padding: 12px 44px 12px 16px;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 16px;
        }
        .alert--success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert--error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-close {
            position: absolute;
            top: 10px;
            right: 12px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 0.8rem;
            opacity: 0.6;
        }

        /* Formulaire */
        .form-container {
            margin-top: 8px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }

        .form-grid-3 {
            grid-template-columns: 1fr 1fr 1fr;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #374151;
        }

        .form-input, .form-select {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            font-size: 0.85rem;
            color: #1f2937;
            transition: all 0.2s;
            background: #fff;
        }

        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: #2e7d64;
            box-shadow: 0 0 0 3px rgba(46,125,100,0.15);
        }

        .form-input--readonly {
            background: #f9fafb;
            color: #1f2937;
            font-weight: 700;
            cursor: not-allowed;
        }

        .form-hint {
            font-size: 0.7rem;
            color: #9ca3af;
            margin-top: 2px;
        }

        .form-error {
            font-size: 0.75rem;
            color: #dc2626;
            margin-top: 4px;
        }

        /* Message gratuit */
        .free-message {
            margin-bottom: 24px;
            padding: 16px 20px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 12px;
        }

        .free-message-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .free-message-icon {
            width: 24px;
            height: 24px;
            color: #22c55e;
            flex-shrink: 0;
        }

        .free-message-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #166534;
        }

        .free-message-text {
            font-size: 0.8rem;
            color: #15803d;
            margin-top: 2px;
        }

        /* Section plan sur mesure */
        .custom-section {
            margin-top: 32px;
            padding-top: 32px;
            border-top: 2px dashed #e5e7eb;
        }

        .custom-section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.05rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
        }

        .custom-section-icon {
            width: 22px;
            height: 22px;
            color: #2563eb;
        }

        .features-section {
            margin-top: 20px;
        }

        .features-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #374151;
            display: block;
            margin-bottom: 12px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            background: #f9fafb;
            padding: 16px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .feature-checkbox {
            width: 16px;
            height: 16px;
            accent-color: #2e7d64;
            cursor: pointer;
        }

        .feature-label {
            font-size: 0.8rem;
            color: #374151;
            cursor: pointer;
        }

        /* Boutons */
        .form-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
        }

        .btn {
            padding: 10px 24px;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .btn--cancel {
            background: #f3f4f6;
            color: #6b7280;
            border: 1px solid #e5e7eb;
        }

        .btn--cancel:hover {
            background: #e5e7eb;
        }

        .btn--submit {
            background: #2e7d64;
            color: #fff;
            margin-left: auto;
            box-shadow: 0 4px 12px rgba(46,125,100,0.3);
        }

        .btn--submit:hover {
            background: #1a5a48;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(46,125,100,0.4);
        }

        /* Responsive */
        @media (max-width: 1280px) { .action-bar { left: 0; } }

        @media (max-width: 1024px) {
            .form-grid {
                grid-template-columns: 1fr 1fr;
            }
            .form-grid-3 {
                grid-template-columns: 1fr 1fr;
            }
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .page-content { padding: 46px 0 0 0; }
            .content-card { margin: 0 16px; padding: 16px; }
            .action-bar-inner { padding: 0 14px; overflow-x: auto; }
            .form-grid { grid-template-columns: 1fr; gap: 16px; margin-bottom: 16px; }
            .form-grid-3 { grid-template-columns: 1fr; }
            .features-grid { grid-template-columns: 1fr; }
            .btn--submit { margin-left: 0; width: 100%; }
            .btn--cancel { width: 100%; }
            .form-actions { flex-direction: column; }
        }
    </style>

    <script>
        function togglePlanType() {
            const planType = document.getElementById('plan_type').value;
            const sectionStandard = document.getElementById('section_standard');
            const sectionCustom = document.getElementById('section_custom');
            const planSelect = document.getElementById('plan_id');
            const freeOption = document.getElementById('free_payment_option');
            const paymentMode = document.getElementById('payment_mode');

            if (planType === 'custom') {
                sectionStandard.style.display = 'none';
                sectionCustom.style.display = 'block';
                planSelect.removeAttribute('required');

                freeOption.style.display = 'none';
                paymentMode.style.borderColor = '';
                paymentMode.style.backgroundColor = '';

                const paymentModeOptions = paymentMode.options;
                for (let i = 0; i < paymentModeOptions.length; i++) {
                    if (paymentModeOptions[i].value !== 'free') {
                        paymentModeOptions[i].disabled = false;
                        paymentModeOptions[i].style.opacity = '1';
                    }
                }

                if (paymentMode.value === 'free') {
                    paymentMode.value = 'manual';
                }

                document.getElementById('free_plan_message').style.display = 'none';

            } else {
                sectionStandard.style.display = 'grid';
                sectionCustom.style.display = 'none';
                planSelect.setAttribute('required', 'required');
            }
            calculateTotal();
        }

        function calculateTotal() {
            const planType = document.getElementById('plan_type').value;
            const totalDisplay = document.getElementById('total_display');
            const freePlanMessage = document.getElementById('free_plan_message');

            let total = 0;

            if (planType === 'standard') {
                const planSelect = document.getElementById('plan_id');
                const durationInput = document.getElementById('duration_months');
                const selectedOption = planSelect.options[planSelect.selectedIndex];

                const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
                const months = parseInt(durationInput.value) || 1;

                total = price * months;
            } else {
                const customPriceInput = document.getElementById('custom_price');
                const durationInput = document.getElementById('duration_months');

                const price = parseFloat(customPriceInput.value) || 0;
                const months = parseInt(durationInput.value) || 1;

                total = price * months;
            }

            if (total === 0 && planType === 'standard') {
                freePlanMessage.style.display = 'block';
            } else {
                freePlanMessage.style.display = 'none';
            }

            const formattedTotal = new Intl.NumberFormat('fr-FR').format(total);
            totalDisplay.value = `${formattedTotal} XOF`;
        }

        function checkFreePlan() {
            const planType = document.getElementById('plan_type').value;

            if (planType === 'custom') {
                return;
            }

            const planSelect = document.getElementById('plan_id');
            const selectedOption = planSelect.options[planSelect.selectedIndex];
            const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
            const paymentMode = document.getElementById('payment_mode');
            const freeOption = document.getElementById('free_payment_option');
            const paymentModeOptions = paymentMode.options;

            if (price === 0) {
                freeOption.style.display = 'block';
                paymentMode.value = 'free';
                paymentMode.style.borderColor = '#22c55e';
                paymentMode.style.backgroundColor = '#f0fdf4';

                for (let i = 0; i < paymentModeOptions.length; i++) {
                    if (paymentModeOptions[i].value !== 'free') {
                        paymentModeOptions[i].disabled = true;
                        paymentModeOptions[i].style.opacity = '0.3';
                    } else {
                        paymentModeOptions[i].disabled = false;
                        paymentModeOptions[i].style.opacity = '1';
                    }
                }

                document.getElementById('free_plan_message').style.display = 'block';
            } else {
                freeOption.style.display = 'none';
                paymentMode.style.borderColor = '';
                paymentMode.style.backgroundColor = '';

                for (let i = 0; i < paymentModeOptions.length; i++) {
                    if (paymentModeOptions[i].value !== 'free') {
                        paymentModeOptions[i].disabled = false;
                        paymentModeOptions[i].style.opacity = '1';
                    }
                }

                if (paymentMode.value === 'free') {
                    paymentMode.value = 'manual';
                }

                document.getElementById('free_plan_message').style.display = 'none';
            }
        }

        document.querySelector('form').addEventListener('submit', function(e) {
            const paymentMode = document.getElementById('payment_mode');
            const planSelect = document.getElementById('plan_id');
            const selectedOption = planSelect.options[planSelect.selectedIndex];
            const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
            const planType = document.getElementById('plan_type').value;

            if (planType === 'standard' && (price === 0 || paymentMode.value === 'free')) {
                paymentMode.value = 'manual';
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            togglePlanType();
            checkFreePlan();
        });
    </script>
</x-app-layout>
