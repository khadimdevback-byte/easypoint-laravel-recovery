<x-app-layout>
    {{-- ===================================================== --}}
    {{-- BARRE D'ACTIONS — collée sous la navbar               --}}
    {{-- ===================================================== --}}
    <div class="action-bar">
        <div class="action-bar-inner">
            <div class="action-info">
                <i class="fas fa-chart-line"></i>
                <span>{{ __('Tableau de bord Super Admin') }}</span>
            </div>
            <div class="action-date">
                <i class="fas fa-calendar-alt"></i>
                <span>{{ now()->format('d/m/Y') }}</span>
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
                    <h1 class="page-title">{{ __('Tableau de bord') }}</h1>
                    <p class="page-subtitle">{{ __('Bienvenue sur votre espace d\'administration') }}</p>
                </div>
            </div>

            {{-- Cartes de statistiques --}}
            <div class="stats-grid">
                <a href="{{ route('users.index', 'super_admin') }}" class="stat-card">
                    <div class="stat-icon stat-icon-purple">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="stat-content">
                        <p class="stat-label">Super admins</p>
                        <p class="stat-value">{{ $totalSuperAdmin }}</p>
                    </div>
                    <div class="stat-hover-effect"></div>
                </a>

                <a href="{{ route('companies.index') }}" class="stat-card">
                    <div class="stat-icon stat-icon-blue">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-content">
                        <p class="stat-label">Entreprises</p>
                        <p class="stat-value">{{ $totalCompany }}</p>
                    </div>
                    <div class="stat-hover-effect"></div>
                </a>

                <a href="{{ route('companies.index') }}" class="stat-card">
                    <div class="stat-icon stat-icon-green">
                        <i class="fas fa-map-location-dot"></i>
                    </div>
                    <div class="stat-content">
                        <p class="stat-label">Sociétés actives</p>
                        <p class="stat-value">{{ $totalActiveCompany }}</p>
                    </div>
                    <div class="stat-hover-effect"></div>
                </a>

                <a href="{{ route('companies.index') }}" class="stat-card">
                    <div class="stat-icon stat-icon-orange">
                        <i class="fas fa-user-gear"></i>
                    </div>
                    <div class="stat-content">
                        <p class="stat-label">Sociétés désactivées</p>
                        <p class="stat-value">{{ $totalInactiveCompany }}</p>
                    </div>
                    <div class="stat-hover-effect"></div>
                </a>
                <a href="{{ route('superadmin.payments.details', 'all') }}" class="stat-card">
                    <div class="stat-icon stat-icon-gold">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-content">
                        <p class="stat-label">Chiffre d'affaires total</p>
                        <p class="stat-value" style="font-size: 15px">
                            @if($totalRevenue > 0)
                                {{ number_format($totalRevenue, 0, ',', ' ') }} FCFA
                            @else
                                0 FCFA
                            @endif
                        </p>
                    </div>
                    <div class="stat-hover-effect"></div>
                </a>
            </div>

            {{-- Graphiques existants --}}
            <div class="charts-grid">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Statistiques des utilisateurs</h3>
                        <div class="chart-badge">
                            <i class="fas fa-chart-pie"></i> Répartition
                        </div>
                    </div>
                    <div class="chart-body">
                        <div id="basicChart"></div>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Sociétés créées durant les 12 derniers mois</h3>
                        <div class="chart-badge">
                            <i class="fas fa-chart-bar"></i> Évolution
                        </div>
                    </div>
                    <div class="chart-body">
                        <div id="companyChart"></div>
                    </div>
                </div>
            </div>

            {{-- ===================================================== --}}
            {{-- 🌟 BLOC DE SUIVI DES PAIEMENTS ET REVENUS              --}}
            {{-- ===================================================== --}}
            <div class="charts-grid mt-4">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Évolution des Revenus Encaissés</h3>
                        <div class="chart-badge">
                            <i class="fas fa-money-bill-wave"></i> Chiffre d'affaires
                        </div>
                    </div>
                    <div class="chart-body">
                        <div id="revenueChart"></div>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Derniers Paiements Reçus</h3>
                        <div class="chart-badge">
                            <i class="fas fa-history"></i> Flux FedaPay
                        </div>
                    </div>
                    <div class="chart-body p-0">
                        <div class="table-responsive">
                            <table class="table-payments">
                                <thead>
                                    <tr>
                                        <th>Entreprise</th>
                                        <th>Montant</th>
                                        <th>Mode</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($latestPayments as $payment)
                                        <tr>
                                            <td><strong>{{ $payment->company->compagny_name ?? 'N/A' }}</strong></td>
                                            <td class="payment-amount">{{ number_format($payment->amount, 0, ',', ' ') }} F CFA</td>
                                            <td>
                                                <span class="payment-badge">{{ $payment->payment_method ?? 'Mobile Money' }}</span>
                                            </td>
                                            <td class="payment-date">{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted p-4">Aucun paiement enregistré pour le moment.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* ============================================ */
        /* STYLES MODERNES POUR LE DASHBOARD SUPER ADMIN */
        /* ============================================ */

        .stat-icon-gold {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

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

        .action-info i {
            font-size: 1rem;
        }

        .action-date {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.15);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            color: white;
        }

        /* Page content */
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
            margin-bottom: 24px;
        }
        .page-title { font-size: 1.35rem; font-weight: 700; color: #1f2937; margin: 0; }
        .page-subtitle { font-size: 0.8rem; color: #6b7280; margin-top: 4px; }

        /* Stats grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            position: relative;
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 20px;
            background: #fff;
            border: 1px solid var(--gl);
            border-radius: 16px;
            transition: var(--t);
            text-decoration: none;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.1);
            border-color: var(--vp);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            flex-shrink: 0;
        }

        .stat-icon-purple { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }
        .stat-icon-blue { background: linear-gradient(135deg, #3b82f6, #1e40af); }
        .stat-icon-green { background: linear-gradient(135deg, var(--vp), var(--vf)); }
        .stat-icon-orange { background: linear-gradient(135deg, #f59e0b, #b45309); }

        .stat-content {
            flex: 1;
        }

        .stat-label {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--gm);
            margin-bottom: 4px;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--gf);
            font-family: 'Syne', sans-serif;
            margin: 0;
        }

        .stat-hover-effect {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--vp);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .stat-card:hover .stat-hover-effect {
            transform: scaleX(1);
        }

        /* Charts grid */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }

        .mt-4 { margin-top: 1.5rem; }

        .chart-card {
            background: #fff;
            border: 1px solid var(--gl);
            border-radius: 16px;
            overflow: hidden;
        }

        .chart-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            background: var(--gtc);
            border-bottom: 1px solid var(--gl);
        }

        .chart-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--gf);
            margin: 0;
        }

        .chart-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 500;
            color: var(--vp);
            border: 1px solid var(--gl);
        }

        .chart-body {
            padding: 20px;
        }

        .p-0 { padding: 0 !important; }

        /* Styles spécifiques au tableau des paiements */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .table-payments {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
            text-align: left;
        }

        .table-payments th {
            background: #f8fafc;
            padding: 14px 18px;
            color: #64748b;
            font-weight: 600;
            border-bottom: 1px solid var(--gl);
        }

        .table-payments td {
            padding: 14px 18px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }

        .table-payments tbody tr:hover {
            background-color: #f8fafc;
        }

        .payment-amount {
            font-weight: 700;
            color: #0f172a;
        }

        .payment-badge {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: capitalize;
        }

        .payment-date {
            color: #64748b;
            font-size: 0.8rem;
        }

        .text-center { text-align: center; }
        .text-muted { color: #6b7280; }
        .p-4 { padding: 16px; }

        /* Responsive */
        @media (max-width: 1280px) {
            .action-bar { left: 0; }
        }

        @media (max-width: 1024px) {
            .charts-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }

        @media (max-width: 768px) {
            .page-content { padding: 46px 0 0 0; }
            .content-card { margin: 0 16px; padding: 16px; }
            .action-bar-inner { padding: 0 14px; }
            .stats-grid { grid-template-columns: 1fr; gap: 12px; }
            .stat-card { padding: 16px; }
            .stat-icon { width: 48px; height: 48px; font-size: 1.2rem; }
            .stat-value { font-size: 1.4rem; }
            .chart-header { flex-direction: column; gap: 8px; align-items: flex-start; }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Graphique 1 : Utilisateurs (Donut)
            var options = {
                series: @json($userCounts),
                chart: {
                    type: 'donut',
                    height: 350,
                    toolbar: {
                        show: true,
                        tools: {
                            download: true,
                            selection: false,
                            zoom: false,
                            zoomin: false,
                            zoomout: false,
                            pan: false,
                            reset: false
                        }
                    }
                },
                labels: @json($userTypes),
                dataLabels: { enabled: true },
                tooltip: {
                    y: { formatter: function(val) { return val + " utilisateurs"; } }
                },
                colors: ['#8b5cf6', '#f59e0b', '#2e7d64', '#3b82f6', '#ef4444'],
                legend: { position: 'bottom' }
            };

            var chart = new ApexCharts(document.querySelector("#basicChart"), options);
            chart.render();

            // Graphique 2 : Sociétés créées (Barres)
            var options2 = {
                series: [{
                    name: 'Sociétés',
                    data: @json($counts)
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: true,
                        tools: {
                            download: true,
                            selection: false,
                            zoom: false,
                            zoomin: false,
                            zoomout: false,
                            pan: false,
                            reset: false
                        }
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        borderRadius: 8,
                        borderRadiusApplication: 'end'
                    }
                },
                dataLabels: { enabled: false },
                stroke: { show: true, width: 2, colors: ['transparent'] },
                xaxis: { categories: @json($months) },
                yaxis: { title: { text: '(Nombre de sociétés)' } },
                fill: { opacity: 1 },
                tooltip: { y: { formatter: function(val) { return val + " sociétés"; } } },
                colors: ['#2e7d64']
            };

            var chart2 = new ApexCharts(document.querySelector("#companyChart"), options2);
            chart2.render();

            // 🌟 Graphique 3 : Évolution des revenus
            var revenueData = @json($revenues);
            var revenueMonths = @json($financialMonths);

            console.log('📊 Données des revenus:', revenueData);
            console.log('📅 Mois:', revenueMonths);

            // Vérifier et nettoyer les données
            if (!revenueData || revenueData.length === 0) {
                revenueData = [0, 0, 0, 0, 0, 0];
                revenueMonths = ['Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet'];
            }

            // S'assurer que toutes les valeurs sont des nombres
            revenueData = revenueData.map(function(value) {
                return parseFloat(value) || 0;
            });

            var optionsRevenue = {
                series: [{
                    name: 'Revenus',
                    data: revenueData
                }],
                chart: {
                    type: 'area',
                    height: 350,
                    toolbar: {
                        show: true,
                        tools: {
                            download: true,
                            selection: false,
                            zoom: false,
                            zoomin: false,
                            zoomout: false,
                            pan: false,
                            reset: false
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val.toLocaleString('fr-FR') + ' F CFA';
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 3,
                    colors: ['#6f42c1']
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.1,
                        stops: [0, 90, 100]
                    }
                },
                xaxis: {
                    categories: revenueMonths,
                    labels: {
                        style: {
                            fontSize: '12px',
                            fontWeight: 'bold'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                            if (value >= 1000000) {
                                return (value / 1000000).toFixed(1) + ' M F CFA';
                            }
                            return value.toLocaleString('fr-FR') + ' F CFA';
                        }
                    }
                },
                colors: ['#6f42c1'],
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val.toLocaleString('fr-FR') + ' F CFA';
                        }
                    }
                },
                grid: {
                    borderColor: '#f1f1f1',
                    row: {
                        colors: ['#f8f9fa', 'transparent'],
                        opacity: 0.5
                    }
                },
                markers: {
                    size: 5,
                    colors: ['#6f42c1'],
                    strokeColors: '#fff',
                    strokeWidth: 2,
                    hover: {
                        size: 7
                    }
                }
            };

            var revenueChartElement = document.querySelector("#revenueChart");
            if (revenueChartElement) {
                try {
                    var revenueChart = new ApexCharts(revenueChartElement, optionsRevenue);
                    revenueChart.render();
                    console.log('✅ Graphique des revenus rendu avec succès');
                } catch (error) {
                    console.error('❌ Erreur lors du rendu du graphique:', error);
                }
            }
        });
    </script>
</x-app-layout>
