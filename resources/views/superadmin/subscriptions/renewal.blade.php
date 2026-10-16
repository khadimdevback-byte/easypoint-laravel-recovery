<x-app-layout>
    {{-- Barre d'actions --}}
    <div class="action-bar">
        <div class="action-bar-inner">
            <div class="action-info">
                <i class="fas fa-sync-alt"></i>
                <span>{{ __('Renouveler mon abonnement') }}</span>
            </div>
            <div class="action-stats">
                <span class="stat-badge">
                    <i class="fas fa-clock"></i>
                    Expire le {{ \Carbon\Carbon::parse($activeSubscription->ends_at)->format('d/m/Y') }}
                </span>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="content-card">
            {{-- En-tête --}}
            <div class="renewal-header">
                <div class="header-content">
                    <h1 class="header-title">Renouveler mon abonnement</h1>
                    <p class="header-subtitle">Choisissez la durée et le plan pour renouveler</p>
                </div>
                <div class="header-icon">
                    <i class="fas fa-sync-alt"></i>
                </div>
            </div>

            <div class="renewal-grid">
                {{-- Formulaire principal --}}
                <div class="form-column">
                    <div class="form-card">
                        <div class="form-header">
                            <i class="fas fa-sync-alt"></i>
                            <h5>Nouvel abonnement</h5>
                        </div>

                        <form action="{{ route('subscription.process-renewal', $activeSubscription) }}" method="POST" class="form-body">
                            @csrf

                            {{-- Abonnement actuel --}}
                            <div class="form-group">
                                <label class="form-label">Abonnement actuel</label>
                                <div class="current-subscription">
                                    <div class="subscription-info">
                                        <i class="fas fa-gem"></i>
                                        <div>
                                            <p class="plan-name">{{ $activeSubscription->plan->name }}</p>
                                            <p class="expiry-date">Expire le {{ \Carbon\Carbon::parse($activeSubscription->ends_at)->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                    <div class="progress-indicator">
                                        @php
                                            $totalDays = \Carbon\Carbon::parse($activeSubscription->starts_at)->diffInDays(\Carbon\Carbon::parse($activeSubscription->ends_at));
                                            $daysPassed = \Carbon\Carbon::parse($activeSubscription->starts_at)->diffInDays(now());
                                            $progress = $totalDays > 0 ? min(100, ($daysPassed / $totalDays) * 100) : 0;
                                        @endphp
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: {{ $progress }}%"></div>
                                        </div>
                                        <span class="progress-text">{{ round($progress) }}% utilisé</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Nouveau plan avec attribut data-price sécurisé --}}
                            <div class="form-group">
                                <label class="form-label">Nouveau plan</label>
                                <select name="plan_id" class="form-select" required>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}"
                                                data-price="{{ $plan->price }}"
                                                {{ $activeSubscription->plan_id == $plan->id ? 'selected' : '' }}>
                                            {{ $plan->name }} - {{ number_format($plan->price, 0, ',', ' ') }} F CFA/mois
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Durée --}}
                            <div class="form-group">
                                <label class="form-label">Durée (mois)</label>
                                <div class="duration-buttons">
                                    @foreach([1, 3, 6, 12, 24, 36] as $month)
                                        <button type="button"
                                                data-months="{{ $month }}"
                                                class="duration-btn {{ $month == 12 ? 'active' : '' }}">
                                            {{ $month }} mois
                                            @if($month == 12)<span class="badge">Populaire</span>@endif
                                            @if($month == 24)<span class="badge">Économie</span>@endif
                                        </button>
                                    @endforeach
                                </div>
                                <input type="number" name="duration_months" id="duration_months" value="12" min="1" max="36" class="form-input duration-input" placeholder="Ou saisir personnalisé">
                            </div>

                            {{-- Total --}}
                            <div class="total-card">
                                <div class="total-info">
                                    <span class="total-label">Total à payer :</span>
                                    <div class="total-amount">
                                        <span class="total-value" id="total_amount">0</span>
                                        <span class="total-currency">F CFA</span>
                                    </div>
                                </div>
                                <div class="total-hint">
                                    <i class="fas fa-info-circle"></i>
                                    <span>TVA incluse</span>
                                </div>
                            </div>

                            {{-- Actions avec Sécurité au clic pour FedaPay --}}
                            <div class="form-actions">
                                <button type="submit"
                                        onclick="this.disabled=true; this.innerHTML='<i class=&quot;fas fa-spinner fa-spin&quot;></i> Redirection...'; this.form.submit();"
                                        class="btn-submit">
                                    <i class="fas fa-credit-card"></i> Continuer vers le paiement
                                </button>
                                <a href="{{ route('company.subscription') }}" class="btn-cancel">
                                    <i class="fas fa-times"></i> Annuler
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Colonne info --}}
                <div class="info-column">
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <h5>Pourquoi renouveler ?</h5>
                        <ul class="info-list">
                            <li><i class="fas fa-check-circle"></i> Continuer à utiliser tous les services</li>
                            <li><i class="fas fa-check-circle"></i> Conserver vos données et historiques</li>
                            <li><i class="fas fa-check-circle"></i> Bénéficier des dernières mises à jour</li>
                            <li><i class="fas fa-check-circle"></i> Support prioritaire 24/7</li>
                        </ul>
                    </div>

                    <div class="help-card">
                        <div class="help-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h5>Besoin d'aide ?</h5>
                        <p>Une question sur le renouvellement ?</p>
                        <a href="{{ route('support.create') }}" class="help-btn">
                            <i class="fas fa-headset"></i> Contacter le support
                        </a>
                    </div>
                </div>
            </div>
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
            justify-content: space-between;
            padding: 0 24px;
            height: 46px;
        }

        .action-info { display: inline-flex; align-items: center; gap: 10px; color: white; font-size: 0.85rem; font-weight: 500; }
        .action-stats { background: rgba(255,255,255,0.15); padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; color: white; }

        .page-content { padding-top: 46px; }
        .content-card { background: #fff; border-radius: 20px; border: 1px solid var(--gl); padding: 28px; margin: 0 24px; }

        /* En-tête */
        .renewal-header {
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
        .header-title { font-size: 1.6rem; font-weight: 800; color: white; margin: 0; font-family: 'Syne', sans-serif; }
        .header-subtitle { font-size: 0.85rem; color: rgba(255,255,255,0.7); margin-top: 6px; }
        .header-icon { position: absolute; right: 20px; top: 50%; transform: translateY(-50%); font-size: 6rem; opacity: 0.1; color: white; }
        .header-icon i { font-size: 6rem; }

        /* Grille */
        .renewal-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 28px; }

        /* Formulaire */
        .form-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--gl);
            overflow: hidden;
        }
        .form-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 16px 20px;
            background: var(--gtc);
            border-bottom: 1px solid var(--gl);
        }
        .form-header i { color: var(--vp); font-size: 1.1rem; }
        .form-header h5 { margin: 0; font-weight: 700; color: var(--gf); }
        .form-body { padding: 24px; }

        .form-group { margin-bottom: 24px; }
        .form-label { display: block; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--gm); margin-bottom: 8px; letter-spacing: 0.5px; }
        .form-select, .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid var(--gl);
            border-radius: 12px;
            font-size: 0.85rem;
            transition: var(--t);
        }
        .form-select:focus, .form-input:focus {
            outline: none;
            border-color: var(--vp);
            box-shadow: 0 0 0 3px rgba(46,125,100,0.1);
        }

        /* Abonnement actuel */
        .current-subscription {
            background: var(--gtc);
            border-radius: 16px;
            padding: 16px;
            border: 1px solid var(--gl);
        }
        .subscription-info { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
        .subscription-info i { font-size: 1.5rem; color: var(--vp); }
        .plan-name { font-weight: 700; color: var(--gf); margin: 0; }
        .expiry-date { font-size: 0.7rem; color: var(--gm); margin: 0; }
        .progress-indicator { margin-top: 8px; }
        .progress-bar { background: var(--gl); border-radius: 10px; height: 6px; overflow: hidden; }
        .progress-fill { background: var(--vp); border-radius: 10px; height: 100%; }
        .progress-text { font-size: 0.65rem; color: var(--gm); display: block; margin-top: 6px; }

        /* Duration buttons */
        .duration-buttons { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 12px; }
        .duration-btn {
            position: relative;
            padding: 10px 18px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--t);
            border: 1.5px solid var(--gl);
            background: white;
            color: var(--gf);
        }
        .duration-btn.active { background: var(--vp); border-color: var(--vp); color: white; }
        .duration-btn:hover:not(.active) { background: var(--gtc); border-color: var(--vp); }
        .duration-btn .badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #f59e0b;
            color: white;
            font-size: 0.6rem;
            padding: 2px 6px;
            border-radius: 20px;
            font-weight: 600;
        }
        .duration-input { margin-top: 8px; }

        /* Total card */
        .total-card {
            background: linear-gradient(135deg, var(--vc), var(--gtc));
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 24px;
            border: 1px solid var(--gl);
        }
        .total-info { display: flex; justify-content: space-between; align-items: baseline; flex-wrap: wrap; gap: 10px; }
        .total-label { font-size: 0.8rem; font-weight: 600; color: var(--gm); }
        .total-amount { display: flex; align-items: baseline; gap: 6px; }
        .total-value { font-size: 1.8rem; font-weight: 800; color: var(--vp); font-family: 'Syne', sans-serif; }
        .total-currency { font-size: 0.8rem; font-weight: 600; color: var(--gm); }
        .total-hint { display: flex; align-items: center; gap: 6px; margin-top: 8px; padding-top: 8px; border-top: 1px dashed var(--gl); }
        .total-hint i { font-size: 0.7rem; color: var(--gm); }
        .total-hint span { font-size: 0.65rem; color: var(--gm); }

        /* Actions */
        .form-actions { display: flex; gap: 16px; flex-wrap: wrap; }
        .btn-submit, .btn-cancel {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--t);
            border: none;
            text-decoration: none;
        }
        .btn-submit { background: var(--vp); color: white; }
        .btn-submit:hover { background: var(--vf); transform: translateY(-2px); box-shadow: 0 8px 20px rgba(46,125,100,0.3); }
        .btn-cancel { background: var(--gtc); color: var(--gf); border: 1px solid var(--gl); }
        .btn-cancel:hover { background: var(--gl); transform: translateY(-2px); }

        /* Info column */
        .info-card, .help-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--gl);
            overflow: hidden;
            margin-bottom: 24px;
        }
        .info-card { padding: 24px; text-align: center; }
        .info-icon { width: 64px; height: 64px; background: var(--vc); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
        .info-icon i { font-size: 1.5rem; color: var(--vp); }
        .info-card h5 { font-size: 1rem; font-weight: 700; color: var(--gf); margin-bottom: 16px; }
        .info-list { list-style: none; padding: 0; margin: 0; text-align: left; }
        .info-list li { display: flex; align-items: center; gap: 10px; padding: 8px 0; font-size: 0.8rem; color: var(--gm); border-bottom: 1px solid var(--gl); }
        .info-list li:last-child { border-bottom: none; }
        .info-list li i { color: var(--vp); font-size: 0.8rem; }

        .help-card { text-align: center; padding: 24px; background: linear-gradient(135deg, #2e7d64, #1a5a48); border: none; }
        .help-icon { width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
        .help-icon i { font-size: 1.5rem; color: white; }
        .help-card h5 { color: white; margin-bottom: 8px; }
        .help-card p { color: rgba(255,255,255,0.7); font-size: 0.75rem; margin-bottom: 20px; }
        .help-btn { display: inline-flex; align-items: center; gap: 8px; background: white; color: var(--vp); padding: 10px 20px; border-radius: 40px; text-decoration: none; font-weight: 600; font-size: 0.75rem; transition: var(--t); }
        .help-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }

        @media (max-width: 1280px) { .action-bar { left: 0; } }
        @media (max-width: 1024px) { .renewal-grid { grid-template-columns: 1fr; } }
        @media (max-width: 768px) {
            .page-content { padding: 46px 0 0 0; }
            .content-card { margin: 0 16px; padding: 16px; }
            .renewal-header { padding: 20px; }
            .duration-buttons { gap: 8px; }
            .duration-btn { padding: 8px 14px; font-size: 0.7rem; }
            .form-actions { flex-direction: column; }
            .btn-submit, .btn-cancel { width: 100%; }
        }
    </style>

    <script>
        document.querySelectorAll('.duration-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const months = parseInt(this.dataset.months);
                document.querySelectorAll('.duration-btn').forEach(b => {
                    b.classList.remove('active');
                });
                this.classList.add('active');
                document.getElementById('duration_months').value = months;
                updateTotal();
            });
        });

        document.getElementById('duration_months').addEventListener('input', function() {
            const months = parseInt(this.value) || 1;
            document.querySelectorAll('.duration-btn').forEach(btn => {
                btn.classList.remove('active');
                if (parseInt(btn.dataset.months) === months) {
                    btn.classList.add('active');
                }
            });
            updateTotal();
        });

        function updateTotal() {
            const months = parseInt(document.getElementById('duration_months').value) || 1;
            const select = document.querySelector('select[name="plan_id"]');
            const selectedOption = select.options[select.selectedIndex];

            // Correction propre : Récupération du prix direct depuis data-price
            const planPrice = parseInt(selectedOption.dataset.price) || 0;

            const total = months * planPrice;
            document.getElementById('total_amount').textContent = total.toLocaleString('fr-FR');
        }

        document.querySelector('select[name="plan_id"]').addEventListener('change', updateTotal);
        updateTotal();
    </script>
</x-app-layout>
