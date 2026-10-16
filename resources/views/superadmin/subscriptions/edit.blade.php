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
                <h1 class="page-title">
                    {{ __('Modifier la Souscription') }}
                    <span class="page-title-sub">{{ $subscription->company->compagny_name ?? $subscription->company->name ?? '' }}</span>
                </h1>
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

            <!-- Informations actuelles -->
            <div class="info-grid">
                <div class="info-item">
                    <p class="info-label">{{ __('Plan actuel') }}</p>
                    <p class="info-value">{{ $subscription->plan->name ?? 'N/A' }}</p>
                </div>
                <div class="info-item">
                    <p class="info-label">{{ __('Prix / Mois') }}</p>
                    <p class="info-value">{{ number_format($subscription->plan->price ?? 0, 0, ',', ' ') }} {{ $subscription->plan->currency ?? 'XOF' }}</p>
                </div>
                <div class="info-item">
                    <p class="info-label">{{ __('Début') }}</p>
                    <p class="info-value">
                        @if($subscription->starts_at)
                            {{ $subscription->starts_at->format('d/m/Y H:i') }}
                        @else
                            <span class="info-value--pending">{{ __('En attente') }}</span>
                        @endif
                    </p>
                </div>
                <div class="info-item">
                    <p class="info-label">{{ __('Statut') }}</p>
                    <p class="info-value">
                        @if($subscription->status === 'active')
                            <span class="status-badge status-badge--active">✅ {{ __('Actif') }}</span>
                        @elseif($subscription->status === 'pending_payment')
                            <span class="status-badge status-badge--pending">⏳ {{ __('En attente') }}</span>
                        @elseif($subscription->status === 'expired')
                            <span class="status-badge status-badge--expired">❌ {{ __('Expiré') }}</span>
                        @else
                            <span class="status-badge status-badge--cancelled">{{ $subscription->status }}</span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- Formulaire -->
            <form method="POST" action="{{ route('superadmin.subscriptions.update', $subscription->id) }}" class="form-container">
                @csrf
                @method('PUT')

                <!-- Ligne: Statut + Durée + Montant -->
                <div class="form-grid form-grid-3">
                    <div class="form-group">
                        <x-input-label for="status" :value="__('Statut *')" class="form-label" />
                        <select id="status" name="status" required class="form-select">
                            <option value="active" {{ old('status', $subscription->status) == 'active' ? 'selected' : '' }}>{{ __('Actif / Payé') }}</option>
                            <option value="pending_payment" {{ old('status', $subscription->status) == 'pending_payment' ? 'selected' : '' }}>{{ __('En attente de paiement') }}</option>
                            <option value="expired" {{ old('status', $subscription->status) == 'expired' ? 'selected' : '' }}>{{ __('Expiré') }}</option>
                            <option value="canceled" {{ old('status', $subscription->status) == 'canceled' ? 'selected' : '' }}>{{ __('Annulé / Suspendu') }}</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="form-error" />
                    </div>

                    <div class="form-group">
                        <x-input-label for="duration_months" :value="__('Durée (Mois) *')" class="form-label" />
                        <input id="duration_months" name="duration_months" type="number" min="1" max="60"
                               value="{{ old('duration_months', $subscription->duration_months ?? 1) }}"
                               oninput="calculateAll()" required
                               class="form-input" />
                        <x-input-error :messages="$errors->get('duration_months')" class="form-error" />
                    </div>

                    <div class="form-group">
                        <x-input-label for="total_amount" :value="__('Montant Total (XOF) *')" class="form-label" />
                        <input id="total_amount" name="total_amount" type="number" min="0" step="1"
                               value="{{ old('total_amount', $subscription->total_amount ?? 0) }}"
                               readonly
                               class="form-input form-input--readonly" />
                        <small class="form-hint">{{ __('Calculé automatiquement') }}</small>
                        <x-input-error :messages="$errors->get('total_amount')" class="form-error" />
                    </div>
                </div>

                <!-- Ligne: Date de fin (automatique) -->
                <div class="form-grid">
                    <div class="form-group">
                        <x-input-label for="ends_at_display" :value="__('Date de fin calculée')" class="form-label" />
                        <input id="ends_at_display" type="text"
                               value="{{ $subscription->ends_at ? $subscription->ends_at->format('d/m/Y H:i') : 'Non définie' }}"
                               readonly
                               class="form-input form-input--readonly" />
                        <small class="form-hint">{{ __('La date de fin est calculée automatiquement en fonction de la durée.') }}</small>
                    </div>

                    <!-- Champ caché pour envoyer la date de fin -->
                    <input type="hidden" id="ends_at" name="ends_at" value="{{ $subscription->ends_at ? $subscription->ends_at->format('Y-m-d\TH:i') : '' }}" />
                </div>

                <!-- Boutons -->
                <div class="form-actions">
                    <button type="button" onclick="window.location.href='{{ route('superadmin.subscriptions.index') }}'" class="btn btn--cancel">
                        {{ __('Annuler') }}
                    </button>

                    <button type="submit" class="btn btn--submit">
                        {{ __('Mettre à jour la souscription') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* ============================================ */
        /* STYLES MODERNES COMME L'INDEX ET LE CREATE    */
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

        .page-title-sub {
            color: #2e7d64;
            display: inline-block;
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

        /* Informations */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
            padding: 20px 24px;
            background: #f9fafb;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .info-label {
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #9ca3af;
        }

        .info-value {
            font-size: 0.9rem;
            font-weight: 600;
            color: #1f2937;
        }

        .info-value--pending {
            color: #d97706;
        }

        /* Status badges */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .status-badge--active { background: #dcfce7; color: #166534; }
        .status-badge--pending { background: #ffedd5; color: #c2410c; }
        .status-badge--expired { background: #fef3c7; color: #92400e; }
        .status-badge--cancelled { background: #fee2e2; color: #991b1b; }

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
            .info-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .form-grid {
                grid-template-columns: 1fr 1fr;
            }
            .form-grid-3 {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 768px) {
            .page-content { padding: 46px 0 0 0; }
            .content-card { margin: 0 16px; padding: 16px; }
            .action-bar-inner { padding: 0 14px; overflow-x: auto; }
            .info-grid { grid-template-columns: 1fr 1fr; gap: 12px; padding: 16px; }
            .form-grid { grid-template-columns: 1fr; gap: 16px; margin-bottom: 16px; }
            .form-grid-3 { grid-template-columns: 1fr; }
            .btn--submit { margin-left: 0; width: 100%; }
            .btn--cancel { width: 100%; }
            .form-actions { flex-direction: column; }
            .page-title { font-size: 1.1rem; }
            .page-title-sub { display: block; margin-top: 4px; }
        }
    </style>

    <script>
        function calculateAll() {
            const durationInput = document.getElementById('duration_months');
            const totalInput = document.getElementById('total_amount');
            const endsAtDisplay = document.getElementById('ends_at_display');
            const endsAtHidden = document.getElementById('ends_at');

            const months = parseInt(durationInput.value) || 1;
            const pricePerMonth = {{ $subscription->plan->price ?? 0 }};
            const startDate = '{{ $subscription->starts_at ? $subscription->starts_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s') }}';

            // Calculer le montant total
            const total = pricePerMonth * months;
            totalInput.value = total;

            // Calculer la nouvelle date de fin
            const start = new Date(startDate);
            const end = new Date(start);
            end.setMonth(end.getMonth() + months);

            // Formater la date d'affichage
            const formattedDate = end.toLocaleDateString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            endsAtDisplay.value = formattedDate;

            // Mettre à jour le champ caché pour l'envoi
            const year = end.getFullYear();
            const month = String(end.getMonth() + 1).padStart(2, '0');
            const day = String(end.getDate()).padStart(2, '0');
            const hours = String(end.getHours()).padStart(2, '0');
            const minutes = String(end.getMinutes()).padStart(2, '0');
            endsAtHidden.value = `${year}-${month}-${day}T${hours}:${minutes}`;
        }

        // Déclencher le calcul au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            calculateAll();
        });
    </script>
</x-app-layout>
