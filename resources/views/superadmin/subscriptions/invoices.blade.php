<x-app-layout>
    {{-- Barre d'actions --}}
    <div class="action-bar">
        <div class="action-bar-inner">
            <div class="action-info">
                <i class="fas fa-file-invoice"></i>
                <span>{{ __('Mes factures') }}</span>
            </div>
            <div class="action-stats">
                <span class="stat-badge">
                    <i class="fas fa-chart-line"></i>
                    Total: {{ number_format($subscriptions->sum('total_amount'), 0, ',', ' ') }} F CFA
                </span>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="content-card">
            {{-- En-tête --}}
            <div class="invoices-header">
                <div class="header-content">
                    <h1 class="header-title">Mes factures</h1>
                    <p class="header-subtitle">Historique de vos paiements et factures</p>
                </div>
                <div class="header-icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
            </div>

            {{-- Statistiques --}}
            <div class="stats-mini-grid">
                <div class="stat-mini-card">
                    <div class="stat-mini-icon">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div class="stat-mini-info">
                        <span class="stat-mini-label">Total factures</span>
                        <span class="stat-mini-value">{{ $subscriptions->count() }}</span>
                    </div>
                </div>
                <div class="stat-mini-card">
                    <div class="stat-mini-icon stat-mini-icon-paid">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-mini-info">
                        <span class="stat-mini-label">Payées</span>
                        <span class="stat-mini-value">{{ $subscriptions->where('status', 'active')->count() }}</span>
                    </div>
                </div>
                <div class="stat-mini-card">
                    <div class="stat-mini-icon stat-mini-icon-total">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-mini-info">
                        <span class="stat-mini-label">Montant total</span>
                        <span class="stat-mini-value">{{ number_format($subscriptions->sum('total_amount'), 0, ',', ' ') }}</span>
                        <span class="stat-mini-currency">F CFA</span>
                    </div>
                </div>
            </div>

            {{-- Tableau --}}
            <div class="table-container">
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>N° Facture</th>
                                <th>Plan</th>
                                <th>Montant</th>
                                <th>Période</th>
                                <th>Statut</th>
                                <th style="width: 80px;">Facture</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subscriptions as $subscription)
                                <tr>
                                    <td>
                                        <span class="invoice-number">#{{ str_pad($subscription->id, 6, '0', STR_PAD_LEFT) }}</span>
                                    </td>
                                    <td>
                                        <div class="plan-cell">
                                            <i class="fas fa-tag"></i>
                                            <span>{{ $subscription->plan->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="amount">{{ number_format($subscription->total_amount, 0, ',', ' ') }} F CFA</span>
                                    </td>
                                    <td>
                                        <div class="period-cell">
                                            <span>{{ \Carbon\Carbon::parse($subscription->starts_at)->format('d/m/Y') }}</span>
                                            <i class="fas fa-arrow-right"></i>
                                            <span>{{ \Carbon\Carbon::parse($subscription->ends_at)->format('d/m/Y') }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($subscription->status == 'active')
                                            <span class="status-badge status-paid">
                                                <i class="fas fa-circle"></i> Payé
                                            </span>
                                        @elseif($subscription->status == 'pending_payment')
                                            <span class="status-badge status-pending">
                                                <i class="fas fa-clock"></i> En attente
                                            </span>
                                        @else
                                            <span class="status-badge status-cancelled">
                                                <i class="fas fa-ban"></i> Résilié
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($subscription->status == 'active')
                                            <a href="{{ route('subscription.download-invoice', $subscription) }}" class="download-link" title="Télécharger la facture">
                                                <i class="fas fa-download"></i>
                                                <span>PDF</span>
                                            </a>
                                        @else
                                            <span class="no-invoice">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="empty-row">
                                        <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                        <p>Aucune facture disponible</p>
                                        <span class="empty-sub">Les factures apparaîtront après vos paiements</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($subscriptions->hasPages())
                    <div class="pagination-wrapper">
                        {{ $subscriptions->links() }}
                    </div>
                @endif
            </div>

            {{-- Lien retour --}}
            <div class="back-link">
                <a href="{{ route('company.subscription') }}" class="back-link-btn">
                    <i class="fas fa-arrow-left"></i>
                    Retour à mon abonnement
                </a>
            </div>
        </div>
    </div>

    <style>
        /* ============================================ */
        /* STYLES MODERNES POUR LA PAGE FACTURES        */
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
        .invoices-header {
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

        /* Mini stats */
        .stats-mini-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 28px;
        }
        .stat-mini-card {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 20px;
            background: #fff;
            border: 1px solid var(--gl);
            border-radius: 16px;
            transition: var(--t);
        }
        .stat-mini-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .stat-mini-icon {
            width: 48px;
            height: 48px;
            background: var(--vc);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--vp);
            font-size: 1.2rem;
        }
        .stat-mini-icon-paid { background: #dcfce7; color: #166534; }
        .stat-mini-icon-total { background: #fef3c7; color: #d97706; }
        .stat-mini-info { flex: 1; }
        .stat-mini-label { font-size: 0.65rem; font-weight: 600; text-transform: uppercase; color: var(--gm); display: block; margin-bottom: 4px; }
        .stat-mini-value { font-size: 1.3rem; font-weight: 800; color: var(--gf); font-family: 'Syne', sans-serif; }
        .stat-mini-currency { font-size: 0.7rem; color: var(--gm); margin-left: 4px; }

        /* Tableau */
        .table-container {
            background: #fff;
            border-radius: 16px;
            border: 1px solid var(--gl);
            overflow: hidden;
            margin-bottom: 24px;
        }
        .table-wrapper { overflow-x: auto; }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
            min-width: 800px;
        }
        .data-table th {
            padding: 14px 20px;
            background: var(--gtc);
            font-weight: 600;
            color: var(--gf);
            border-bottom: 1px solid var(--gl);
            text-align: left;
        }
        .data-table td {
            padding: 14px 20px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }
        .data-table tr:hover td { background: #f9fafb; }

        .invoice-number {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            color: var(--gf);
            letter-spacing: 0.5px;
        }

        .plan-cell { display: flex; align-items: center; gap: 10px; }
        .plan-cell i { color: var(--vp); font-size: 0.9rem; }
        .plan-cell span { font-weight: 500; }

        .amount { font-weight: 700; color: var(--gf); }

        .period-cell { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .period-cell i { font-size: 0.6rem; color: var(--gm); }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .status-paid { background: #dcfce7; color: #166534; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-cancelled { background: #f3f4f6; color: #4b5563; }
        .status-badge i { font-size: 0.6rem; }

        .download-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: var(--vc);
            color: var(--vp);
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.7rem;
            font-weight: 600;
            transition: var(--t);
        }
        .download-link:hover {
            background: var(--vp);
            color: white;
            transform: translateY(-1px);
        }
        .no-invoice { color: var(--gm); font-size: 0.7rem; }

        .empty-row { text-align: center; padding: 60px 20px; color: #9ca3af; }
        .empty-sub { font-size: 0.7rem; display: block; margin-top: 8px; }

        .pagination-wrapper { display: flex; justify-content: flex-end; padding: 16px; border-top: 1px solid var(--gl); }

        /* Lien retour */
        .back-link { text-align: center; }
        .back-link-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--vp);
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 500;
            transition: var(--t);
        }
        .back-link-btn:hover { gap: 12px; color: var(--vf); }

        @media (max-width: 1280px) { .action-bar { left: 0; } }
        @media (max-width: 768px) {
            .page-content { padding: 46px 0 0 0; }
            .content-card { margin: 0 16px; padding: 16px; }
            .invoices-header { padding: 20px; }
            .stats-mini-grid { grid-template-columns: 1fr; gap: 12px; }
            .stat-mini-card { padding: 12px 16px; }
            .period-cell { flex-direction: column; align-items: flex-start; gap: 4px; }
            .period-cell i { display: none; }
        }
    </style>
</x-app-layout>
