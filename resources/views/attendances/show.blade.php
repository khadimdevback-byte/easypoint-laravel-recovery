@php
    use Carbon\Carbon;
    $firstRetardDisplayed = false;
@endphp

<x-app-layout>
    {{-- Barre d'actions --}}
    <div class="action-bar">
        <div class="action-bar-inner">
            <div class="action-info">
                <i class="fas fa-clock"></i>
                <span>{{ __('Détail du pointage') }}</span>
            </div>
            <div class="action-date">
                <i class="fas fa-calendar-alt"></i>
                <span>{{ Carbon::create($year, $month, $day)->translatedFormat('d F Y') }}</span>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="content-card">
            {{-- En-tête --}}
            <div class="detail-header">
                <div class="header-content">
                    <h1 class="header-title">{{ __('Pointage du ' . Carbon::create($year, $month, $day)->translatedFormat('d F Y')) }}</h1>
                    <p class="header-subtitle">Détail des pointages pour {{ $user->first_name }} {{ $user->name }}</p>
                </div>
                <div class="header-icon">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>

            <div class="detail-grid">
                {{-- Colonne gauche --}}
                <div class="detail-left">
                    {{-- Carte Utilisateur --}}
                    <div class="info-card">
                        <div class="info-card-header">
                            <div class="header-icon-small">
                                <i class="fas fa-user"></i>
                            </div>
                            <h3 class="info-card-title">Informations utilisateur</h3>
                        </div>
                        <div class="info-card-body">
                            <div class="user-profile">
                                <img class="user-avatar" src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar">
                                <div class="user-details">
                                    <h4 class="user-name">{{ $user->first_name }} <span class="uppercase">{{ $user->name }}</span></h4>
                                    <p class="user-function">{{ $user->employee_function }}</p>
                                    <p class="user-email">{{ __($user->email) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Carte Durée totale --}}
                    <div class="info-card">
                        <div class="info-card-header">
                            <div class="header-icon-small header-icon-small-success">
                                <i class="fas fa-stopwatch"></i>
                            </div>
                            <h3 class="info-card-title">Durée totale de travail</h3>
                        </div>
                        <div class="info-card-body text-center">
                            <div class="total-duration">{{ $formattedWorkDuration }}</div>
                            <div class="duration-details">
                                <div class="duration-item">
                                    <span class="duration-label">Durée attendue</span>
                                    <span class="duration-value">{{ $formattedExpectedDuration }}</span>
                                </div>
                                <div class="duration-item">
                                    <span class="duration-label">Pauses</span>
                                    <span class="duration-value">{{ $formattedPauseDuration }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Colonne droite - Timeline --}}
                <div class="detail-right">
                    <div class="timeline-card">
                        <div class="timeline-header">
                            <i class="fas fa-chart-line"></i>
                            <h3 class="timeline-title">Activité du jour</h3>
                        </div>
                        <div class="timeline-body">
                            @foreach ($attendances as $attendance)
                                <div class="timeline-item">
                                    <div class="timeline-marker">
                                        <div class="marker-dot"></div>
                                        @if(!$loop->last)
                                            <div class="marker-line"></div>
                                        @endif
                                    </div>
                                    <div class="timeline-content">
                                        <div class="timeline-time">
                                            {{ Carbon::parse($attendance->timestamp)->format('H:i') }}
                                        </div>
                                        <div class="timeline-type">
                                            @switch($attendance->type)
                                                @case('arrivee')
                                                    <span class="type-badge type-arrivee">
                                                        <i class="fas fa-sign-in-alt"></i> Pointage d'entrée
                                                    </span>
                                                    @break
                                                @case('pause')
                                                    <span class="type-badge type-pause">
                                                        <i class="fas fa-coffee"></i> Début de pause
                                                    </span>
                                                    @break
                                                @case('reprise')
                                                    <span class="type-badge type-reprise">
                                                        <i class="fas fa-briefcase"></i> Reprise de travail
                                                    </span>
                                                    @break
                                                @case('depart')
                                                    <span class="type-badge type-depart">
                                                        <i class="fas fa-sign-out-alt"></i> Pointage de sortie
                                                    </span>
                                                    @break
                                            @endswitch
                                        </div>
                                        <div class="timeline-location">
                                            <i class="fas fa-location-dot"></i> {{ $attendance->site->name }}
                                        </div>
                                        @if ($attendance->type === 'arrivee' && $totalRetard > 0 && !$firstRetardDisplayed)
                                            <div class="timeline-delay">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                @php
                                                    $firstRetardDisplayed = true;
                                                @endphp
                                                @if ($totalRetard < 1)
                                                    @php $secondes = round($totalRetard * 60); @endphp
                                                    Retard : {{ $secondes }} seconde{{ $secondes > 1 ? 's' : '' }}
                                                @elseif ($totalRetard < 60)
                                                    Retard : {{ $totalRetard }} minute{{ $totalRetard > 1 ? 's' : '' }}
                                                @else
                                                    @php
                                                        $heures = floor($totalRetard / 60);
                                                        $minutes = $totalRetard % 60;
                                                        $retardText = 'Retard : ' . $heures . ' heure' . ($heures > 1 ? 's' : '');
                                                        if ($minutes > 0) {
                                                            $retardText .= ' ' . $minutes . ' minute' . ($minutes > 1 ? 's' : '');
                                                        }
                                                    @endphp
                                                    {{ $retardText }}
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            @if($attendances->isEmpty())
                                <div class="empty-timeline">
                                    <i class="fas fa-clock fa-3x mb-3 opacity-50"></i>
                                    <p>Aucun pointage enregistré pour ce jour</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bouton retour --}}
            @if (!is_user())
                <div class="back-button">
                    <a href="{{ route('attendances.index') }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Retour au tableau des pointages
                    </a>
                </div>
            @endif
        </div>
    </div>

    <style>
        /* ============================================ */
        /* STYLES MODERNES - DÉTAIL POINTAGE           */
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

        /* En-tête */
        .detail-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(135deg, #2e7d64, #1a5a48);
            border-radius: 20px;
            padding: 24px 28px;
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
        }
        .header-content { position: relative; z-index: 2; }
        .header-title {
            font-size: 1.3rem;
            font-weight: 800;
            color: white;
            margin: 0 0 6px 0;
            font-family: 'Syne', sans-serif;
        }
        .header-subtitle {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.7);
            margin: 0;
        }
        .header-icon {
            position: relative;
            z-index: 1;
            font-size: 3.5rem;
            opacity: 0.15;
            color: white;
        }
        .header-icon i { font-size: 3.5rem; }

        /* Grille */
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 24px;
        }

        /* Cartes info */
        .info-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid var(--gl);
            overflow: hidden;
            margin-bottom: 24px;
        }
        .info-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 20px;
            background: var(--gtc);
            border-bottom: 1px solid var(--gl);
        }
        .header-icon-small {
            width: 40px;
            height: 40px;
            background: var(--vp);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
        }
        .header-icon-small-success {
            background: linear-gradient(135deg, #10b981, #047857);
        }
        .info-card-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--gf);
            margin: 0;
        }
        .info-card-body {
            padding: 20px;
        }

        /* User profile */
        .user-profile {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .user-avatar {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            object-fit: cover;
            border: 2px solid var(--gl);
        }
        .user-details {
            flex: 1;
        }
        .user-name {
            font-size: 1rem;
            font-weight: 700;
            color: var(--gf);
            margin: 0 0 4px 0;
        }
        .user-function {
            font-size: 0.7rem;
            color: var(--gm);
            margin: 0 0 2px 0;
        }
        .user-email {
            font-size: 0.7rem;
            color: var(--vp);
            margin: 0;
        }

        /* Durée totale */
        .total-duration {
            font-size: 2rem;
            font-weight: 800;
            color: var(--vp);
            font-family: 'Syne', sans-serif;
            margin-bottom: 20px;
        }
        .duration-details {
            display: flex;
            justify-content: space-around;
            padding-top: 16px;
            border-top: 1px solid var(--gl);
        }
        .duration-item {
            text-align: center;
        }
        .duration-label {
            display: block;
            font-size: 0.65rem;
            color: var(--gm);
            margin-bottom: 4px;
        }
        .duration-value {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--gf);
        }

        /* Timeline */
        .timeline-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid var(--gl);
            overflow: hidden;
            height: 100%;
        }
        .timeline-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 16px 20px;
            background: var(--gtc);
            border-bottom: 1px solid var(--gl);
        }
        .timeline-header i {
            color: var(--vp);
            font-size: 1rem;
        }
        .timeline-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--gf);
            margin: 0;
        }
        .timeline-body {
            padding: 20px;
            max-height: 500px;
            overflow-y: auto;
        }

        /* Timeline items */
        .timeline-item {
            display: flex;
            position: relative;
            margin-bottom: 24px;
        }
        .timeline-item:last-child {
            margin-bottom: 0;
        }
        .timeline-marker {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 30px;
            position: relative;
        }
        .marker-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--vp);
            border: 2px solid white;
            box-shadow: 0 0 0 2px var(--vp);
            z-index: 2;
        }
        .marker-line {
            width: 2px;
            flex: 1;
            background: linear-gradient(to bottom, var(--gl), transparent);
            margin: 4px 0;
        }
        .timeline-content {
            flex: 1;
            padding-left: 16px;
            padding-bottom: 8px;
        }
        .timeline-time {
            font-size: 0.7rem;
            color: var(--gm);
            margin-bottom: 4px;
        }
        .timeline-type {
            margin-bottom: 4px;
        }
        .type-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .type-arrivee { background: #dcfce7; color: #166534; }
        .type-pause { background: #fef3c7; color: #92400e; }
        .type-reprise { background: #e0f2fe; color: #0369a1; }
        .type-depart { background: #fee2e2; color: #991b1b; }
        .timeline-location {
            font-size: 0.7rem;
            color: var(--gm);
            margin-top: 4px;
        }
        .timeline-location i {
            font-size: 0.65rem;
            margin-right: 4px;
        }
        .timeline-delay {
            margin-top: 8px;
            padding: 6px 10px;
            background: #fef3c7;
            border-radius: 8px;
            font-size: 0.7rem;
            color: #92400e;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .timeline-delay i {
            font-size: 0.7rem;
        }

        .empty-timeline {
            text-align: center;
            padding: 40px 20px;
            color: #9ca3af;
        }

        /* Bouton retour */
        .back-button {
            margin-top: 32px;
            text-align: center;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--vp);
            color: white;
            border: none;
            border-radius: 40px;
            padding: 12px 28px;
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
            transition: var(--t);
        }
        .btn-back:hover {
            background: var(--vf);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(46,125,100,0.3);
        }

        @media (max-width: 1280px) { .action-bar { left: 0; } }
        @media (max-width: 1024px) {
            .detail-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }
        @media (max-width: 768px) {
            .page-content { padding: 46px 0 0 0; }
            .content-card { margin: 0 16px; padding: 16px; }
            .detail-header { padding: 20px; }
            .header-icon { display: none; }
            .user-profile { flex-direction: column; text-align: center; }
            .duration-details { flex-direction: column; gap: 12px; }
            .timeline-body { max-height: 400px; }
        }
    </style>
</x-app-layout>
