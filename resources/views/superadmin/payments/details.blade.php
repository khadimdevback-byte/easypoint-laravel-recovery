{{-- resources/views/superadmin/payments/details.blade.php --}}

<x-app-layout>
    {{-- ===================================================== --}}
    {{-- BARRE D'ACTIONS                                       --}}
    {{-- ===================================================== --}}
    <div class="action-bar">
        <div class="action-bar-inner">
            <a href="{{ route('dashboard.superadmin') }}" class="action-btn">
                <i class="fas fa-arrow-left"></i>
                <span>{{ __('Retour au tableau de bord') }}</span>
            </a>
            <div class="action-info">
                <i class="fas {{ $pageInfo['icon'] }}"></i>
                <span>{{ $pageInfo['title'] }}</span>
            </div>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- CONTENU PRINCIPAL                                     --}}
    {{-- ===================================================== --}}
    <div class="page-content">
        <div class="content-card">
            <div class="content-header">
                <div>
                    <h1 class="page-title">
                        <i class="fas {{ $pageInfo['icon'] }} text-{{ $pageInfo['color'] }}-500"></i>
                        {{ $pageInfo['title'] }}
                    </h1>
                    <p class="page-subtitle">{{ __('Détail des paiements par mode d\'encaissement') }}</p>
                </div>
                <div class="header-actions">
                    <span class="badge-custom badge-{{ $mode }}">
                        {{ $pageInfo['badge'] }}
                    </span>
                </div>
            </div>

            {{-- Statistiques --}}
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon stat-icon-blue">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="stat-content">
                        <p class="stat-label">Total des paiements</p>
                        <p class="stat-value">{{ number_format($totalAmount, 0, ',', ' ') }} FCFA</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon stat-icon-green">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div class="stat-content">
                        <p class="stat-label">Nombre de transactions</p>
                        <p class="stat-value">{{ $totalCount }}</p>
                    </div>
                </div>

                @if($mode === 'all')
                    <div class="stat-card">
                        <div class="stat-icon stat-icon-purple">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <div class="stat-content">
                            <p class="stat-label">FedaPay</p>
                            <p class="stat-value">{{ number_format($stats['fedapay'], 0, ',', ' ') }} FCFA</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon stat-icon-gold">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="stat-content">
                            <p class="stat-label">Chèque / Espèces</p>
                            <p class="stat-value">{{ number_format($stats['manual'], 0, ',', ' ') }} FCFA</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Filtres rapides --}}
            <div class="filter-tabs">
                <a href="{{ route('superadmin.payments.details', 'all') }}" 
                   class="filter-tab {{ $mode === 'all' ? 'active' : '' }}">
                    <i class="fas fa-credit-card"></i> Tous
                </a>
                <a href="{{ route('superadmin.payments.details', 'fedapay') }}" 
                   class="filter-tab {{ $mode === 'fedapay' ? 'active' : '' }}">
                    <i class="fas fa-mobile-alt"></i> FedaPay
                </a>
                <a href="{{ route('superadmin.payments.details', 'manual') }}" 
                   class="filter-tab {{ $mode === 'manual' ? 'active' : '' }}">
                    <i class="fas fa-money-bill-wave"></i> Chèque / Espèces
                </a>
            </div>

            {{-- Tableau des paiements --}}
            <div class="table-wrapper">
                <table class="data-table" id="export-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Entreprise') }}</th>
                            <th>{{ __('Plan') }}</th>
                            <th>{{ __('Montant') }}</th>
                            <th>{{ __('Méthode') }}</th>
                            <th>{{ __('Référence') }}</th>
                            <th>{{ __('Date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td class="text-muted">#{{ $payment->id }}</td>
                                <td>
                                    <div class="company-cell">
                                        <div class="company-logo-placeholder" style="background: #2e7d64;">
                                            {{ strtoupper(substr($payment->company->compagny_name ?? 'N/A', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="company-name">{{ $payment->company->compagny_name ?? 'N/A' }}</div>
                                            <div class="company-email">{{ $payment->company->compagny_email ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="plan-badge">
                                        {{ $payment->subscription->plan->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="amount-value">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</span>
                                </td>
                                <td>
                                    @if(str_contains(strtolower($payment->payment_method ?? ''), 'mobile') || str_contains(strtolower($payment->payment_method ?? ''), 'fedapay'))
                                        <span class="payment-method method-fedapay">
                                            <i class="fas fa-mobile-alt"></i> FedaPay
                                        </span>
                                    @elseif($payment->payment_method === 'Gratuit')
                                        <span class="payment-method method-free">
                                            <i class="fas fa-gift"></i> Gratuit
                                        </span>
                                    @else
                                        <span class="payment-method method-manual">
                                            <i class="fas fa-money-bill-wave"></i> Manuel
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="ref-badge">{{ $payment->fedapay_transaction_id ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <div class="date-cell">
                                        {{ $payment->created_at->format('d/m/Y') }}
                                        <span class="date-time">{{ $payment->created_at->format('H:i') }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="empty-row">
                                    <i class="fas fa-inbox" style="font-size: 2rem; display: block; margin-bottom: 10px; color: #d1d5db;"></i>
                                    {{ __('Aucun paiement trouvé pour ce mode.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                {{ $payments->links() }}
            </div>
        </div>
    </div>

    <style>
        /* ============================================ */
        /* STYLES POUR LA PAGE DES PAIEMENTS            */
        /* ============================================ */

        :root {
            --vp: #2e7d64;
            --vc: #e8f5f1;
            --vf: #1a5a48;
            --gl: #d4dfe2;
            --t: all 0.32s cubic-bezier(.4,0,.2,1);
        }

        .stat-icon-gold {
            background: linear-gradient(135deg, #f59e0b, #d97706);
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
            justify-content: space-between;
            padding: 0 24px;
            height: 46px;
        }

        .action-info {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: rgba(255,255,255,0.95);
            font-size: 0.85rem;
            font-weight: 500;
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
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }

        .page-subtitle {
            font-size: 0.8rem;
            color: #6b7280;
            margin-top: 4px;
        }

        .text-purple-500 { color: #8b5cf6; }
        .text-gold-500 { color: #d97706; }
        .text-blue-500 { color: #3b82f6; }

        .badge-custom {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-all { background: #e5e7eb; color: #374151; }
        .badge-fedapay { background: #ede9fe; color: #7c3aed; }
        .badge-manual { background: #fef3c7; color: #92400e; }

        /* Stats grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 20px;
            background: #fff;
            border: 1px solid var(--gl);
            border-radius: 12px;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
            flex-shrink: 0;
        }

        .stat-icon-blue { background: linear-gradient(135deg, #3b82f6, #1e40af); }
        .stat-icon-green { background: linear-gradient(135deg, #2e7d64, #1a5a48); }
        .stat-icon-purple { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }
        .stat-icon-gold { background: linear-gradient(135deg, #f59e0b, #d97706); }

        .stat-label {
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
        }

        .stat-value {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }

        /* Filter tabs */
        .filter-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--gl);
            padding-bottom: 8px;
        }

        .filter-tab {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 0.8rem;
            font-weight: 500;
            color: #6b7280;
            text-decoration: none;
            border-radius: 8px;
            transition: var(--t);
        }

        .filter-tab:hover { background: #f3f4f6; color: #1f2937; }
        .filter-tab.active {
            background: #e8f5f1;
            color: #2e7d64;
            font-weight: 600;
        }

        /* Tableau */
        .table-wrapper {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }

        .data-table th {
            background: #f9fafb;
            padding: 12px 16px;
            text-align: left;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
        }

        .data-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        .data-table tbody tr:hover { background: #f9fafb; }

        .text-muted { color: #9ca3af; }

        .company-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .company-logo-placeholder {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
            color: white;
            flex-shrink: 0;
        }

        .company-name { font-weight: 600; color: #1f2937; }
        .company-email { font-size: 0.7rem; color: #6b7280; }

        .plan-badge {
            display: inline-block;
            background: #e8f5f1;
            color: #2e7d64;
            padding: 3px 12px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .amount-value {
            font-weight: 700;
            color: #1f2937;
        }

        .payment-method {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 500;
        }

        .method-fedapay { background: #ede9fe; color: #7c3aed; }
        .method-manual { background: #fef3c7; color: #92400e; }
        .method-free { background: #dcfce7; color: #166534; }

        .ref-badge {
            font-family: 'Courier New', monospace;
            font-size: 0.7rem;
            color: #6b7280;
            background: #f3f4f6;
            padding: 2px 8px;
            border-radius: 4px;
        }

        .date-cell {
            display: flex;
            flex-direction: column;
        }
        .date-time {
            font-size: 0.65rem;
            color: #9ca3af;
        }

        .empty-row {
            text-align: center;
            padding: 40px 16px;
            color: #9ca3af;
        }

        .pagination-wrapper {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        @media (max-width: 1280px) { .action-bar { left: 0; } }

        @media (max-width: 768px) {
            .page-content { padding: 46px 0 0 0; }
            .content-card { margin: 0 16px; padding: 16px; }
            .action-bar-inner { padding: 0 14px; }
            .stats-grid { grid-template-columns: 1fr; }
            .filter-tabs { overflow-x: auto; }
            .data-table td, .data-table th { padding: 8px 12px; font-size: 0.75rem; }
            .content-header { flex-direction: column; align-items: flex-start; }
        }
    </style>

    <!-- ApexCharts pour les stats (optionnel) -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</x-app-layout>