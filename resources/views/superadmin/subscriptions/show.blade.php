<x-app-layout>
    {{-- Barre d'actions --}}
    <div class="action-bar">
        <div class="action-bar-inner">
            <div class="action-info">
                <i class="fas fa-tags"></i>
                <span>{{ __('Mon abonnement') }}</span>
            </div>
            <div class="action-stats">
                @if($activeSubscription)
                    <span class="stat-badge">
                        <i class="fas fa-check-circle"></i>
                        {{ $activeSubscription->plan->name }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="content-card">
            {{-- En-tête --}}
            <div class="subscription-header">
                <div class="header-content">
                    <h1 class="header-title">Mon abonnement</h1>
                    <p class="header-subtitle">Gérez votre abonnement et consultez vos informations</p>
                </div>
                <div class="header-icon">
                    <i class="fas fa-tags"></i>
                </div>
            </div>

            <div class="subscription-grid">
                {{-- Colonne principale --}}
                <div class="main-column">
                    @if($activeSubscription)
                        {{-- Abonnement actuel --}}
                        <div class="subscription-card">
                            <div class="card-header">
                                <div class="header-left">
                                    <i class="fas fa-check-circle"></i>
                                    <h5>Abonnement actif</h5>
                                </div>
                                <span class="current-badge">Actuel</span>
                            </div>
                            <div class="card-body">
                                {{-- Carte du plan --}}
                                <div class="plan-card">
                                    <div class="plan-info">
                                        <span class="plan-label">Plan actuel</span>
                                        <h2 class="plan-name">{{ $activeSubscription->plan->name }}</h2>
                                        <p class="plan-price">
                                            <span class="price">{{ number_format($activeSubscription->plan->price, 0, ',', ' ') }}</span> F CFA/mois
                                        </p>
                                    </div>
                                    <div class="plan-badge">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span>{{ $activeSubscription->duration_months }} mois</span>
                                    </div>
                                </div>

                                {{-- Détails --}}
                                <div class="details-grid">
                                    <div class="detail-item">
                                        <div class="detail-icon"><i class="fas fa-chart-line"></i></div>
                                        <div>
                                            <p class="detail-label">Durée</p>
                                            <p class="detail-value">{{ $activeSubscription->duration_months }} mois</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-icon"><i class="fas fa-money-bill-wave"></i></div>
                                        <div>
                                            <p class="detail-label">Total payé</p>
                                            <p class="detail-value">{{ number_format($activeSubscription->total_amount, 0, ',', ' ') }} F CFA</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-icon"><i class="fas fa-calendar-plus"></i></div>
                                        <div>
                                            <p class="detail-label">Date de début</p>
                                            <p class="detail-value">{{ \Carbon\Carbon::parse($activeSubscription->starts_at)->translatedFormat('d F Y') }}</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-icon"><i class="fas fa-calendar-times"></i></div>
                                        <div>
                                            <p class="detail-label">Date de fin</p>
                                            <p class="detail-value">{{ \Carbon\Carbon::parse($activeSubscription->ends_at)->translatedFormat('d F Y') }}</p>
                                        </div>
                                    </div>
                                    <div class="detail-item detail-full">
                                        <div class="detail-icon"><i class="fas fa-chart-simple"></i></div>
                                        <div class="detail-progress">
                                            <p class="detail-label">Progression</p>
                                            @php
                                                $totalDays = \Carbon\Carbon::parse($activeSubscription->starts_at)->diffInDays(\Carbon\Carbon::parse($activeSubscription->ends_at));
                                                $daysPassed = \Carbon\Carbon::parse($activeSubscription->starts_at)->diffInDays(now());
                                                $progress = $totalDays > 0 ? min(100, ($daysPassed / $totalDays) * 100) : 0;
                                            @endphp
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: {{ $progress }}%"></div>
                                            </div>
                                            <span class="progress-text">{{ round($progress) }}%</span>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-icon"><i class="fas fa-shield-alt"></i></div>
                                        <div>
                                            <p class="detail-label">Statut</p>
                                            <span class="status-active">
                                                <i class="fas fa-circle"></i> Actif
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Expiration --}}
                                @php $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($activeSubscription->ends_at), false); @endphp
                                @if($daysLeft > 0)
                                    <div class="expiration-card">
                                        <div class="expiration-icon">
                                            <i class="fas fa-hourglass-half"></i>
                                        </div>
                                        <div class="expiration-info">
                                            <p class="expiration-label">Expiration dans</p>
                                            <p class="expiration-days">{{ floor($daysLeft) }} jours</p>
                                            <p class="expiration-date">le {{ \Carbon\Carbon::parse($activeSubscription->ends_at)->translatedFormat('d F Y') }}</p>
                                        </div>
                                        <a href="#" class="expiration-link">Renouveler <i class="fas fa-arrow-right"></i></a>
                                    </div>
                                @else
                                    <div class="expired-card">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <div>
                                            <p class="expired-title">Abonnement expiré</p>
                                            <p class="expired-text">Veuillez contacter l'administrateur pour renouveler votre abonnement.</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Historique des abonnements --}}
                        @if(isset($oldSubscriptions) && $oldSubscriptions->count() > 0)
                            <div class="history-card">
                                <div class="history-header">
                                    <i class="fas fa-history"></i>
                                    <h5>Historique des abonnements</h5>
                                </div>
                                <div class="history-table">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Plan</th>
                                                <th>Durée</th>
                                                <th>Montant</th>
                                                <th>Période</th>
                                                <th>Statut</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($oldSubscriptions as $old)
                                                <tr>
                                                    <td>
                                                        <div class="plan-cell">
                                                            <i class="fas {{ $old->plan->name == 'BUSINESS' ? 'fa-crown' : 'fa-gem' }}"></i>
                                                            <span>{{ $old->plan->name }}</span>
                                                        </div>
                                                    </td>
                                                    <td>{{ $old->duration_months }} mois</td>
                                                    <td class="amount">{{ number_format($old->total_amount, 0, ',', ' ') }} F CFA</td>
                                                    <td class="date-range">
                                                        {{ \Carbon\Carbon::parse($old->starts_at)->format('d/m/Y') }}
                                                        <i class="fas fa-arrow-right"></i>
                                                        {{ \Carbon\Carbon::parse($old->ends_at)->format('d/m/Y') }}
                                                    </td>
                                                    <td><span class="status-expired"><i class="fas fa-ban"></i> Résilié</span></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    @else
                        {{-- Aucun abonnement actif --}}
                        <div class="empty-subscription">
                            <div class="empty-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h3>Aucun abonnement actif</h3>
                            <p>Vous n'avez pas d'abonnement actif pour le moment.</p>
                            <a href="{{ route('subscription.payment', ['company' => Auth::user()->company->id]) }}" class="subscribe-btn">
                                <i class="fas fa-credit-card"></i> Souscrire un abonnement
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Colonne latérale --}}
                <div class="sidebar-column">
                    <div class="actions-card">
                        <div class="actions-header">
                            <i class="fas fa-bolt"></i>
                            <h5>Actions rapides</h5>
                        </div>
                        <div class="actions-list">
                            <a href="{{ route('subscription.renewal') }}" class="action-link">
                                <div class="action-left">
                                    <i class="fas fa-sync-alt"></i>
                                    <span>Renouveler</span>
                                </div>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                            <a href="{{ route('dashboard') }}" class="action-link">
                                <div class="action-left">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <span>Tableau de bord</span>
                                </div>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                            <a href="{{ route('subscription.invoices') }}" class="action-link">
                                <div class="action-left">
                                    <i class="fas fa-file-invoice"></i>
                                    <span>Factures</span>
                                </div>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="help-card">
                        <div class="help-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h5>Besoin d'aide ?</h5>
                        <p>Notre équipe est là pour vous accompagner</p>
                        <a href="{{ route('support.create') }}" class="help-btn">
                            <i class="fas fa-headset"></i> Contacter le support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* ============================================ */
        /* STYLES MODERNES POUR LA PAGE ABONNEMENT      */
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
        .subscription-header {
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
        .subscription-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 28px; }

        /* Cartes */
        .subscription-card, .history-card, .actions-card, .help-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--gl);
            overflow: hidden;
        }
        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            background: linear-gradient(135deg, #2e7d64, #1a5a48);
        }
        .header-left { display: flex; align-items: center; gap: 10px; color: white; font-weight: 600; }
        .current-badge { background: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; color: white; }
        .card-body { padding: 20px; }

        /* Plan card */
        .plan-card {
            background: linear-gradient(135deg, var(--vc), var(--gtc));
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .plan-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; color: var(--vp); }
        .plan-name { font-size: 1.5rem; font-weight: 800; color: var(--gf); margin: 8px 0 4px; }
        .plan-price .price { font-size: 1.8rem; font-weight: 800; color: var(--vp); }
        .plan-badge { background: white; padding: 8px 16px; border-radius: 40px; display: flex; align-items: center; gap: 8px; font-weight: 600; color: var(--vp); }

        /* Détails */
        .details-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 24px; }
        .detail-item { display: flex; align-items: center; gap: 12px; }
        .detail-full { grid-column: span 2; }
        .detail-icon { width: 40px; height: 40px; background: var(--vc); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--vp); }
        .detail-label { font-size: 0.65rem; font-weight: 600; text-transform: uppercase; color: var(--gm); margin-bottom: 2px; }
        .detail-value { font-size: 0.85rem; font-weight: 700; color: var(--gf); }
        .detail-progress { flex: 1; }
        .progress-bar { background: var(--gl); border-radius: 10px; height: 8px; margin: 8px 0 4px; }
        .progress-fill { background: var(--vp); border-radius: 10px; height: 100%; }
        .progress-text { font-size: 0.7rem; color: var(--gm); }
        .status-active { display: inline-flex; align-items: center; gap: 6px; background: #dcfce7; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; color: #166534; }

        /* Expiration */
        .expiration-card, .expired-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            padding: 16px;
            border-radius: 16px;
            gap: 12px;
        }
        .expiration-card { background: linear-gradient(135deg, var(--vc), var(--gtc)); border: 1px solid var(--gl); }
        .expired-card { background: #fef3c7; border: 1px solid #fde68a; }
        .expiration-icon { width: 48px; height: 48px; background: var(--vp); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; }
        .expiration-label { font-size: 0.65rem; color: var(--gm); }
        .expiration-days { font-size: 1.2rem; font-weight: 800; color: var(--vp); }
        .expiration-date { font-size: 0.7rem; color: var(--gm); }
        .expiration-link { color: var(--vp); font-weight: 600; text-decoration: none; }
        .expired-card i { font-size: 1.5rem; color: #d97706; margin-right: 12px; }
        .expired-title { font-weight: 700; color: #92400e; }
        .expired-text { font-size: 0.7rem; color: #92400e; }

        /* Historique */
        .history-header { display: flex; align-items: center; gap: 10px; padding: 16px 20px; background: var(--gtc); border-bottom: 1px solid var(--gl); }
        .history-header i { color: var(--vp); }
        .history-header h5 { margin: 0; font-weight: 700; color: var(--gf); }
        .history-table { overflow-x: auto; padding: 0; }
        .history-table table { width: 100%; border-collapse: collapse; font-size: 0.75rem; }
        .history-table th { text-align: left; padding: 12px 16px; background: #f9fafb; color: var(--gm); font-weight: 600; }
        .history-table td { padding: 12px 16px; border-bottom: 1px solid var(--gl); }
        .plan-cell { display: flex; align-items: center; gap: 8px; }
        .plan-cell i { color: var(--vp); }
        .status-expired { display: inline-flex; align-items: center; gap: 6px; background: #f3f4f6; padding: 3px 10px; border-radius: 20px; font-size: 0.65rem; color: #4b5563; }
        .date-range i { margin: 0 6px; font-size: 0.6rem; color: var(--gm); }

        /* Sidebar */
        .actions-card, .help-card { margin-bottom: 24px; }
        .actions-header { display: flex; align-items: center; gap: 10px; padding: 16px 20px; background: var(--gtc); border-bottom: 1px solid var(--gl); }
        .actions-header i { color: var(--vp); }
        .actions-header h5 { margin: 0; font-weight: 700; color: var(--gf); }
        .actions-list { padding: 8px; }
        .action-link { display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-radius: 12px; text-decoration: none; transition: var(--t); }
        .action-link:hover { background: var(--gtc); }
        .action-left { display: flex; align-items: center; gap: 12px; }
        .action-left i { color: var(--vp); }
        .action-left span { color: var(--gf); font-weight: 500; }

        .help-card { text-align: center; padding: 24px; background: linear-gradient(135deg, #2e7d64, #1a5a48); border: none; }
        .help-icon { width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
        .help-icon i { font-size: 1.5rem; color: white; }
        .help-card h5 { color: white; margin-bottom: 8px; }
        .help-card p { color: rgba(255,255,255,0.7); font-size: 0.75rem; margin-bottom: 20px; }
        .help-btn { display: inline-flex; align-items: center; gap: 8px; background: white; color: var(--vp); padding: 10px 20px; border-radius: 40px; text-decoration: none; font-weight: 600; font-size: 0.75rem; transition: var(--t); }
        .help-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }

        /* Empty state */
        .empty-subscription { text-align: center; padding: 60px 40px; background: #fff; border-radius: 20px; border: 1px solid var(--gl); }
        .empty-icon { width: 80px; height: 80px; background: #fef3c7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
        .empty-icon i { font-size: 2rem; color: #d97706; }
        .empty-subscription h3 { font-size: 1.2rem; margin-bottom: 8px; color: var(--gf); }
        .empty-subscription p { color: var(--gm); margin-bottom: 24px; }
        .subscribe-btn { display: inline-flex; align-items: center; gap: 8px; background: var(--vp); color: white; padding: 12px 28px; border-radius: 40px; text-decoration: none; font-weight: 600; transition: var(--t); }
        .subscribe-btn:hover { background: var(--vf); transform: translateY(-2px); }

        @media (max-width: 1280px) { .action-bar { left: 0; } }
        @media (max-width: 1024px) { .subscription-grid { grid-template-columns: 1fr; } }
        @media (max-width: 768px) {
            .page-content { padding: 46px 0 0 0; }
            .content-card { margin: 0 16px; padding: 16px; }
            .subscription-header { padding: 20px; }
            .plan-card { flex-direction: column; text-align: center; gap: 12px; }
            .details-grid { grid-template-columns: 1fr; }
            .detail-full { grid-column: span 1; }
            .expiration-card { flex-direction: column; text-align: center; }
            .history-table { font-size: 0.7rem; }
            .history-table th, .history-table td { padding: 8px 12px; }
        }
    </style>
</x-app-layout>
