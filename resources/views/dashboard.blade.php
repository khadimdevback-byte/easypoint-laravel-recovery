<x-app-layout>
    <div class="dashboard-container">
        {{-- Barre d'actions --}}
        <div class="action-bar">
            <div class="action-bar-inner">
                <div class="action-info">
                    <i class="fas fa-chart-line"></i>
                    <span>{{ __('Tableau de bord') }}</span>
                </div>
                <div class="action-date">
                    <i class="fas fa-calendar-alt"></i>
                    <span>{{ now()->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>

        <div class="dashboard-content">
            {{-- Cartes de statistiques --}}
            <div class="stats-grid">
                <!-- Carte Retard -->
                <div class="stat-card stat-card-danger">
                    <div class="stat-icon">
                        <svg width="48" height="48" viewBox="0 0 84 84" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="42" cy="42" r="42" fill="rgba(220,38,38,0.1)" />
                            <path d="M38.0302 41.0229C37.8552 41.0054 37.6452 41.0054 37.4527 41.0229C33.2877 40.8829 29.9802 37.4704 29.9802 33.2704C29.9802 28.9829 33.4452 25.5004 37.7502 25.5004C42.0377 25.5004 45.5202 28.9829 45.5202 33.2704C45.5027 37.4704 42.1952 40.8829 38.0302 41.0229Z" stroke="#dc2626" stroke-width="2.625" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M50.7172 28.9996C54.1122 28.9996 56.8422 31.7471 56.8422 35.1246C56.8422 38.4321 54.2172 41.1271 50.9447 41.2496C50.8047 41.2321 50.6472 41.2321 50.4897 41.2496" stroke="#dc2626" stroke-width="2.625" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M29.2805 47.4796C25.0455 50.3146 25.0455 54.9346 29.2805 57.7521C34.093 60.9721 41.9855 60.9721 46.798 57.7521C51.033 54.9171 51.033 50.2971 46.798 47.4796C42.003 44.2771 34.1105 44.2771 29.2805 47.4796Z" stroke="#dc2626" stroke-width="2.625" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M54.0947 57.0004C55.3547 56.7379 56.5447 56.2304 57.5247 55.4779C60.2547 53.4304 60.2547 50.0529 57.5247 48.0054C56.5622 47.2704 55.3897 46.7804 54.1472 46.5004" stroke="#dc2626" stroke-width="2.625" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <p class="stat-label">Retard</p>
                        <p class="stat-value">{{ $lateToday }}</p>
                        <p class="stat-trend {{ $lateDifference < 0 ? 'trend-down' : 'trend-up' }}">
                            @if ($lateDifference < 0)
                                <i class="fas fa-arrow-down"></i> {{ number_format(abs($lateDifference), 1) }}%
                            @else
                                <i class="fas fa-arrow-up"></i> {{ number_format($lateDifference, 1) }}%
                            @endif
                            <span>aujourd'hui</span>
                        </p>
                    </div>
                </div>

                <!-- Carte Absent -->
                <div class="stat-card stat-card-warning">
                    <div class="stat-icon">
                        <svg width="48" height="48" viewBox="0 0 84 84" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="42" cy="42" r="42" fill="rgba(245,158,11,0.1)" />
                            <path d="M46.0667 53.75L48.6 56.2833L53.6667 51.2167" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M42.2671 40.117C42.1004 40.1003 41.9004 40.1003 41.7171 40.117C37.7504 39.9837 34.6004 36.7337 34.6004 32.7337C34.5837 28.6503 37.9004 25.3337 41.9837 25.3337C46.0671 25.3337 49.3837 28.6503 49.3837 32.7337C49.3837 36.7337 46.2171 39.9837 42.2671 40.117Z" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M41.9832 58.3498C38.9498 58.3498 35.9332 57.5832 33.6332 56.0498C29.5998 53.3498 29.5998 48.9498 33.6332 46.2665C38.2165 43.1998 45.7332 43.1998 50.3165 46.2665" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <p class="stat-label">Absent</p>
                        <p class="stat-value">{{ $absentToday }}</p>
                        <p class="stat-trend {{ $absentDifference < 0 ? 'trend-down' : 'trend-up' }}">
                            @if ($absentDifference < 0)
                                <i class="fas fa-arrow-down"></i> {{ number_format(abs($absentDifference), 1) }}%
                            @else
                                <i class="fas fa-arrow-up"></i> {{ number_format($absentDifference, 1) }}%
                            @endif
                            <span>aujourd'hui</span>
                        </p>
                    </div>
                </div>

                <!-- Carte Actif -->
                <div class="stat-card stat-card-success">
                    <div class="stat-icon">
                        <svg width="48" height="48" viewBox="0 0 84 84" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="42" cy="42" r="42" fill="rgba(46,125,100,0.1)" />
                            <path d="M32.27 24.5H51.7125C57.9425 24.5 59.5 26.0575 59.5 32.27V43.3475C59.5 49.5775 57.9425 51.1175 51.73 51.1175H32.27C26.0575 51.135 24.5 49.5775 24.5 43.365V32.27C24.5 26.0575 26.0575 24.5 32.27 24.5Z" stroke="#2e7d64" stroke-width="2.625" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M42 51.1345V59.4995" stroke="#2e7d64" stroke-width="2.625" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M24.5 43.75H59.5" stroke="#2e7d64" stroke-width="2.625" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M34.125 59.5H49.875" stroke="#2e7d64" stroke-width="2.625" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <p class="stat-label">Actif</p>
                        <p class="stat-value">{{ $isActif }}</p>
                        <div class="avatar-group">
                            @foreach ($firstFiveUsers as $user)
                                <img src="{{ asset('storage/' . $user['avatar']) }}"
                                     class="avatar-item"
                                     alt="{{ $user['name'] }}"
                                     title="{{ $user['name'] }}">
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Graphiques --}}
            <div class="charts-grid">
                <!-- Graphique présence -->
                <div class="chart-card">
                    <div class="chart-header">
                        <div>
                            <h3 class="chart-title">Statistique de présence</h3>
                            <p class="chart-subtitle">Des 7 derniers jours</p>
                        </div>
                        <div class="chart-badge">
                            <i class="fas fa-chart-pie"></i> Répartition
                        </div>
                    </div>
                    <div class="chart-body">
                        <canvas id="orderTimeChart" class="chart-canvas"></canvas>
                    </div>
                    <div class="chart-stats">
                        <div class="stat-item">
                            <span class="stat-dot" style="background: #dc2626;"></span>
                            <span class="stat-label-chart">Retard</span>
                            <span class="stat-number">{{ $lateSevenDays }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-dot" style="background: #1a5a48;"></span>
                            <span class="stat-label-chart">Absent</span>
                            <span class="stat-number">{{ $absentSevenDays }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-dot" style="background: #2e7d64;"></span>
                            <span class="stat-label-chart">À l'heure</span>
                            <span class="stat-number">{{ $onTimeSevenDays }}</span>
                        </div>
                    </div>
                </div>

                <!-- Graphique répartition utilisateurs -->
                <div class="chart-card">
                    <div class="chart-header">
                        <div>
                            <h3 class="chart-title">Taux de répartition des utilisateurs</h3>
                            <p class="chart-subtitle">Par type d'utilisateur</p>
                        </div>
                        <div class="chart-badge">
                            <i class="fas fa-users"></i> Effectifs
                        </div>
                    </div>
                    <div class="chart-body">
                        <canvas id="ratingChart" class="chart-canvas"></canvas>
                    </div>
                    <div class="chart-stats chart-stats-grid">
                        <div class="stat-item">
                            <span class="stat-dot" style="background: #2e7d64;"></span>
                            <span class="stat-label-chart">Gestionnaires</span>
                            <span class="stat-number">{{ $managerCount }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-dot" style="background: #6a4c7c;"></span>
                            <span class="stat-label-chart">Contrôleurs</span>
                            <span class="stat-number">{{ $controllerCount }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-dot" style="background: #f1c40f;"></span>
                            <span class="stat-label-chart">Clients</span>
                            <span class="stat-number">{{ $customerCount }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-dot" style="background: #2980b9;"></span>
                            <span class="stat-label-chart">Lieux de travail</span>
                            <span class="stat-number">{{ $siteCount }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-dot" style="background: #bdc3c7;"></span>
                            <span class="stat-label-chart">Employé(e)s</span>
                            <span class="stat-number">{{ $userCount }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* ============================================ */
        /* STYLES MODERNES POUR LE DASHBOARD            */
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

        .dashboard-container {
            padding-top: 46px;
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

        .dashboard-content {
            padding: 24px;
        }

        /* Stats grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            display: flex;
            align-items: center;
            gap: 20px;
            background: #fff;
            border-radius: 20px;
            padding: 24px;
            transition: var(--t);
            border: 1px solid var(--gl);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.1);
        }

        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-card-danger .stat-icon { background: rgba(220,38,38,0.1); }
        .stat-card-warning .stat-icon { background: rgba(245,158,11,0.1); }
        .stat-card-success .stat-icon { background: rgba(46,125,100,0.1); }

        .stat-info {
            flex: 1;
        }

        .stat-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--gm);
            margin-bottom: 4px;
        }

        .stat-card-danger .stat-label { color: #dc2626; }
        .stat-card-warning .stat-label { color: #f59e0b; }
        .stat-card-success .stat-label { color: #2e7d64; }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--gf);
            font-family: 'Syne', sans-serif;
            line-height: 1;
            margin-bottom: 6px;
        }

        .stat-trend {
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .trend-up { color: #dc2626; }
        .trend-down { color: #2e7d64; }

        .avatar-group {
            display: flex;
            align-items: center;
            margin-top: 8px;
        }

        .avatar-item {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid white;
            margin-left: -8px;
            object-fit: cover;
            transition: var(--t);
        }

        .avatar-item:first-child { margin-left: 0; }
        .avatar-item:hover { transform: translateY(-2px); z-index: 10; }

        /* Charts grid */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }

        .chart-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--gl);
            overflow: hidden;
        }

        .chart-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 24px;
            background: var(--gtc);
            border-bottom: 1px solid var(--gl);
        }

        .chart-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--gf);
            margin: 0;
        }

        .chart-subtitle {
            font-size: 0.7rem;
            color: var(--gm);
            margin-top: 2px;
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
            padding: 24px;
            display: flex;
            justify-content: center;
        }

        .chart-canvas {
            max-width: 280px;
            max-height: 280px;
        }

        .chart-stats {
            display: flex;
            justify-content: center;
            gap: 24px;
            padding: 16px 24px 24px;
            border-top: 1px solid var(--gl);
        }

        .chart-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 12px;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .stat-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .stat-label-chart {
            font-size: 0.7rem;
            color: var(--gm);
            flex: 1;
        }

        .stat-number {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--gf);
        }

        /* Responsive */
        @media (max-width: 1280px) {
            .action-bar { left: 0; }
        }

        @media (max-width: 1024px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .dashboard-content { padding: 16px; }
            .stats-grid { grid-template-columns: 1fr; gap: 16px; }
            .stat-card { padding: 16px; }
            .stat-value { font-size: 1.5rem; }
            .chart-header { flex-direction: column; gap: 8px; align-items: flex-start; }
            .chart-stats { flex-wrap: wrap; }
            .chart-stats-grid { grid-template-columns: 1fr; }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const orderTimeData = {
            labels: ['Retard', 'Absent', 'À l\'heure'],
            datasets: [{
                data: [{{ $lateSevenDays }}, {{ $absentSevenDays }}, {{ $onTimeSevenDays }}],
                backgroundColor: ['#dc2626', '#1a5a48', '#2e7d64'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        };

        const ratingTimeData = {
            labels: ['Gestionnaires', 'Contrôleurs', 'Clients', 'Lieux de travail', 'Employés'],
            datasets: [{
                data: [{{ $managerCount }}, {{ $controllerCount }}, {{ $customerCount }}, {{ $siteCount }}, {{ $userCount }}],
                backgroundColor: ['#2e7d64', '#6a4c7c', '#f1c40f', '#2980b9', '#bdc3c7'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        };

        new Chart(document.getElementById('orderTimeChart'), {
            type: 'doughnut',
            data: orderTimeData,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: function(tooltipItem) { return tooltipItem.raw + ' personnes'; } } }
                },
                cutout: '65%'
            }
        });

        new Chart(document.getElementById('ratingChart'), {
            type: 'doughnut',
            data: ratingTimeData,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: function(tooltipItem) { return tooltipItem.raw + ' utilisateurs'; } } }
                },
                cutout: '65%'
            }
        });
    </script>
</x-app-layout>
