<x-app-layout>
    {{-- Barre d'actions --}}
    <div class="action-bar">
        <div class="action-bar-inner">
            <div class="action-info">
                <i class="fas fa-chart-line"></i>
                <span>{{ __('Tableau de bord Contrôleur') }}</span>
            </div>
            <div class="action-date">
                <i class="fas fa-calendar-alt"></i>
                <span>{{ now()->translatedFormat('d F Y') }}</span>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="content-card">
            {{-- Cartes statistiques --}}
            <div class="stats-grid">
                <!-- Carte Lieux de travail -->
                <div class="stat-card">
                    <div class="stat-icon stat-icon-primary">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <div class="stat-info">
                        <p class="stat-label">Lieux de travail assignés</p>
                        <p class="stat-value">{{ $totalSite }}</p>
                    </div>
                </div>

                <!-- Carte Employés assignés -->
                <div class="stat-card">
                    <div class="stat-icon stat-icon-secondary">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <p class="stat-label">Employés assignés</p>
                        <p class="stat-value">{{ $totalUser }}</p>
                    </div>
                </div>

                <!-- Carte Présents aujourd'hui -->
                <div class="stat-card">
                    <div class="stat-icon stat-icon-success">
                        <i class="fa-solid fa-person-chalkboard"></i>
                    </div>
                    <div class="stat-info">
                        <p class="stat-label">Présents aujourd'hui</p>
                        <p class="stat-value">{{ $totalUsersToday }}</p>
                    </div>
                </div>
            </div>

            {{-- Graphique --}}
            <div class="chart-container">
                <div class="chart-header">
                    <div>
                        <h3 class="chart-title">Statistiques des pointages</h3>
                        <p class="chart-subtitle">Évolution des présences, retards et absences sur les 12 derniers mois</p>
                    </div>
                    <div class="chart-legend">
                        <div class="legend-dot legend-dot-late"></div>
                        <span class="legend-text">Retard</span>
                        <div class="legend-dot legend-dot-present"></div>
                        <span class="legend-text">Présent</span>
                        <div class="legend-dot legend-dot-absent"></div>
                        <span class="legend-text">Absent</span>
                    </div>
                </div>
                <div class="chart-body">
                    <div id="basicChart"></div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* ============================================ */
        /* STYLES MODERNES - DASHBOARD CONTROLLER       */
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
        .action-date { background: rgba(255,255,255,0.15); padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; color: white; }

        .page-content { padding-top: 46px; }
        .content-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--gl);
            padding: 24px;
            margin: 0 24px;
        }

        /* Stats grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 24px;
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--gl);
            transition: var(--t);
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.08);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .stat-icon-primary { background: linear-gradient(135deg, #2e7d64, #1a5a48); }
        .stat-icon-secondary { background: linear-gradient(135deg, #3b82f6, #1e40af); }
        .stat-icon-success { background: linear-gradient(135deg, #10b981, #047857); }

        .stat-info {
            flex: 1;
        }

        .stat-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--gm);
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--gf);
            font-family: 'Syne', sans-serif;
            margin: 0;
        }

        /* Chart container */
        .chart-container {
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--gl);
            overflow: hidden;
        }

        .chart-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
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
            margin-top: 4px;
        }

        .chart-legend {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }

        .legend-dot-late { background: #f59e0b; }
        .legend-dot-present { background: #10b981; }
        .legend-dot-absent { background: #ef4444; }

        .legend-text {
            font-size: 0.7rem;
            font-weight: 500;
            color: var(--gf);
            margin-left: 4px;
            margin-right: 8px;
        }

        .chart-body {
            padding: 20px;
        }

        @media (max-width: 1280px) { .action-bar { left: 0; } }
        @media (max-width: 768px) {
            .page-content { padding: 46px 0 0 0; }
            .content-card { margin: 0 16px; padding: 16px; }
            .stats-grid { grid-template-columns: 1fr; gap: 16px; }
            .stat-card { padding: 16px; }
            .stat-icon { width: 50px; height: 50px; font-size: 1.2rem; }
            .stat-value { font-size: 1.5rem; }
            .chart-header { flex-direction: column; align-items: flex-start; }
        }
    </style>

    <script>
        var options = {
            series: [{
                name: 'Retard',
                data: @json($lateData)
            }, {
                name: 'Présent',
                data: @json($presentData)
            }, {
                name: 'Absent',
                data: @json($absentData)
            }],
            chart: {
                type: 'bar',
                height: 380,
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
                    borderRadiusApplication: 'end',
                    colors: {
                        backgroundBarColors: ['#f3f4f6'],
                        backgroundBarOpacity: 1,
                        backgroundBarRadius: 8,
                    }
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: @json($months),
                labels: {
                    style: {
                        fontSize: '11px',
                        fontWeight: 500
                    }
                }
            },
            yaxis: {
                title: {
                    text: '(employés)',
                    style: {
                        fontSize: '11px',
                        fontWeight: 500
                    }
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " employés";
                    }
                }
            },
            colors: ['#f59e0b', '#10b981', '#ef4444'],
            legend: {
                position: 'bottom',
                horizontalAlign: 'center',
                markers: {
                    radius: 8
                }
            },
            grid: {
                borderColor: '#f1f1f1',
                strokeDashArray: 5
            }
        };

        var chart = new ApexCharts(document.querySelector("#basicChart"), options);
        chart.render();
    </script>
</x-app-layout>
