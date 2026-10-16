<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRotation;
use App\Models\User;
use App\Models\Equipe;
use App\Models\Site;
use App\Models\Company;
use App\Models\Pointage;
use App\Models\Repartition;
use App\Models\SiteRotation;
use App\Services\RotationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Models\HeureMensuelle;

class AttendanceRotationController extends Controller
{
     protected $rotationService;

    public function __construct(RotationService $rotationService)
    {
        $this->rotationService = $rotationService;
    }
    /**
     * Affiche le planning de pointage pour une équipe sélectionnée,
     * en gérant les rotations journalières et hebdomadaires.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
public function pointage(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->company_id;
        $now = Carbon::now();

        $year = $request->input('annee', $now->year);
        $month = $request->input('mois', $now->month);
        $currentDate = Carbon::create($year, $month, 1)->startOfDay();
        $daysInMonth = $currentDate->daysInMonth;

        Log::info("--- DÉBUT pointage pour l'année {$year} et le mois {$month}. ---");
        Log::info("pointage - Mois affiché (début): {$currentDate->toDateString()}");
        Log::info("pointage - Mois affiché (fin): {$currentDate->copy()->endOfMonth()->toDateString()}");


        // Charger sites + équipes + rotations + répartitions + users
        // --- DÉBUT DE LA MODIFICATION CRUCIALE DANS pointage ---
        // Ajoutez orderBy('id') directement dans la closure de chargement des équipes.
        // Cela garantit que toutes les collections d'équipes seront triées par ID.
        $sites = SiteRotation::with([
            'equipes' => function ($query) use ($companyId) {
                $query->where('equipes.company_id', $companyId)
                    ->with(['rotations.repartitions', 'users'])
                    ->orderBy('id'); // <<< C'EST LA LIGNE CLÉ POUR LA COHÉRENCE
            }
        ])->where('company_id', $companyId)->get();
        // --- FIN DE LA MODIFICATION CRUCIALE ---

        $selectedEquipe = null;
        $periodesParJour = [];
        $repartitionsParJour = [];
        $attendances = [];

        if ($request->filled('equipe_id')) {
            // Puisque $sites->equipes est maintenant trié par ID, $flatMap->equipes le sera aussi.
            $selectedEquipe = $sites->flatMap->equipes->where('id', $request->equipe_id)->first();

            if (!$selectedEquipe) {
                Log::warning("pointage - Équipe ID {$request->equipe_id} introuvable ou n'appartient pas à la compagnie {$companyId}.");
                return back()->with('error', 'Équipe introuvable ou n\'appartient pas à votre compagnie.');
            }

            Log::info("pointage - Équipe sélectionnée: {$selectedEquipe->nom} (ID: {$selectedEquipe->id}).");

            $userIds = $selectedEquipe->users->pluck('id');
            $debutMois = $currentDate->copy()->startOfMonth();
            $finMois = $currentDate->copy()->endOfMonth();

            Log::info("pointage - Chargement des pointages pour les utilisateurs de l'équipe du {$debutMois->toDateString()} au {$finMois->toDateString()}.");
            $pointages = Pointage::whereIn('user_id', $userIds)
                ->whereBetween('date_pointage', [$debutMois, $finMois])
                ->get();

            foreach ($pointages as $p) {
                $jour = Carbon::parse($p->date_pointage)->day;
                $attendances[$p->user_id][$jour] = [
                    'statut' => $p->statut,
                    'heure_pointage' => $p->heure_pointage,
                ];
            }
            Log::info("pointage - Nombre de pointages chargés: " . count($pointages));

            $rotation = $selectedEquipe->rotations->first();
            if (!$rotation) {
                Log::warning("pointage - Aucune rotation configurée pour l'équipe {$selectedEquipe->id}.");
                return back()->with('error', 'Aucune rotation configurée pour cette équipe.');
            }
            Log::info("pointage - Rotation trouvée pour l'équipe: ID {$rotation->id}, Système: {$rotation->systeme}.");

            $repartitionsRaw = $rotation->repartitions;

            if ($repartitionsRaw->isEmpty()) {
                Log::warning("pointage - Aucune répartition trouvée pour la rotation {$rotation->id}.");
                return back()->with('error', 'Aucune répartition trouvée pour cette rotation.');
            }
            Log::info("pointage - Nombre de répartitions chargées (brutes): " . count($repartitionsRaw) . ". IDs: " . json_encode($repartitionsRaw->pluck('id')->toArray()));

            $ordrePeriodes = [];
            $repartitions = collect();

            if ($rotation->systeme === 'hebdomadaire') {
                $repartitionsTriees = $repartitionsRaw->sortBy('id');
                $ordrePeriodes = $repartitionsTriees->pluck('id')->toArray();
                $repartitions = $repartitionsTriees->keyBy('id');
                Log::debug("pointage - Système hebdomadaire: Ordre des IDs de répartition (periodes) par ID: " . json_encode($ordrePeriodes));
            } else { // Système journalier
                $ordrePeriodes = $repartitionsRaw->pluck('id')->toArray();
                $joursTravailRotationForDaily = $repartitionsRaw->sum('nombre_jours');
                $nombreJoursReposForDaily = $rotation->nombre_jours_repos ?? ($rotation->duree_cycle - $joursTravailRotationForDaily);
                if ($nombreJoursReposForDaily > 0 && !in_array('Repos', $ordrePeriodes)) {
                    $ordrePeriodes[] = 'Repos';
                }
                $repartitions = $repartitionsRaw->keyBy('id');
                Log::debug("pointage - Système journalier: Ordre des IDs de répartition (periodes): " . json_encode($ordrePeriodes));
            }

            Log::debug("pointage - Nombre de periodes disponibles: " . count($ordrePeriodes));

            if (empty($ordrePeriodes)) {
                Log::error("pointage - Aucune période valide définie dans la rotation, impossible de générer le pointage.");
                return back()->with('error', 'Aucune période de rotation définie. Veuillez configurer les répartitions.');
            }

            $dateDebutRotation = Carbon::parse($rotation->date_debut)->startOfDay();
            Log::info("pointage - Date de début de rotation: {$dateDebutRotation->toDateString()}.");

            // Cette partie reste la même, car $sites->flatMap->equipes est déjà triée par l'orderBy('id') ci-dessus
            $equipesSite = $sites->flatMap->equipes
                ->where('site_rotation_id', $selectedEquipe->site_rotation_id)
                ->values(); // Important pour réinitialiser les clés numériques après filtrage

            $indexEquipe = $equipesSite->search(fn($eq) => $eq->id === $selectedEquipe->id);
            if ($indexEquipe === false) {
                Log::error("pointage - Erreur critique: L'équipe sélectionnée n'a pas été trouvée dans la liste des équipes de son site.");
                $indexEquipe = 0;
            }
            Log::info("pointage - Index de l'équipe sélectionnée dans la liste des équipes du site: {$indexEquipe}.");

            $systeme = $rotation->systeme;
            $nombreJoursRepos = null;

            if ($systeme === 'hebdomadaire') {
                $nombreJoursTravail = $rotation->nombre_jours_travail;
                $joursTravail = json_decode($rotation->jours_travail_hebdo ?? '[]', true);
                if (empty($joursTravail)) {
                    $joursTravail = range(1, $nombreJoursTravail);
                    Log::info("pointage - Jours de travail hebdomadaires non configurés, par défaut: Jours " . implode(', ', $joursTravail) . " (selon nombre_jours_travail).");
                } else {
                    Log::info("pointage - Jours de travail hebdomadaires configurés: " . json_encode($joursTravail));
                }

                $initialPeriodeIndex = $indexEquipe % count($ordrePeriodes);
                $initialPeriodeId = $ordrePeriodes[$initialPeriodeIndex];

                $etatEquipe = [
                    'periode_actuelle' => $initialPeriodeId,
                    'equipe_nom' => $selectedEquipe->nom,
                    'jours_travailles' => $joursTravail,
                    'index_equipe' => $indexEquipe,
                    'periode_index' => $initialPeriodeIndex
                ];
                Log::info("pointage - État initial de l'équipe à la DATE DE DÉBUT DE ROTATION ({$dateDebutRotation->toDateString()}): " . json_encode($etatEquipe));

                // --- Avancement rapide de l'état de l'équipe jusqu'à la veille du début du mois d'affichage ---
                $dateCouranteAvancement = $dateDebutRotation->copy();
                $finAvancement = $currentDate->copy()->startOfMonth()->subDay();

                Log::info("pointage - Début de l'avancement rapide de l'état de l'équipe de {$dateCouranteAvancement->toDateString()} à {$finAvancement->toDateString()}.");
                while ($dateCouranteAvancement->lte($finAvancement)) {
                    if ($dateCouranteAvancement->dayOfWeekIso === Carbon::MONDAY && $dateCouranteAvancement->gt($dateDebutRotation->copy()->startOfWeek(Carbon::MONDAY))) {
                        $nouvelIndex = ($etatEquipe['periode_index'] + 1) % count($ordrePeriodes);
                        $etatEquipe['periode_actuelle'] = $ordrePeriodes[$nouvelIndex];
                        $etatEquipe['periode_index'] = $nouvelIndex;
                        Log::debug("pointage - Avancement rapide [{$dateCouranteAvancement->toDateString()}]: Lundi, nouvelle période ID: {$etatEquipe['periode_actuelle']}, nouvel index: {$etatEquipe['periode_index']}.");
                    }
                    $dateCouranteAvancement->addDay();
                }
                Log::info("pointage - État de l'équipe après avancement rapide (à la veille du 1er jour du mois d'affichage): " . json_encode($etatEquipe));

                // --- Génération des périodes pour chaque jour du mois d'affichage ---
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = Carbon::create($year, $month, $day)->startOfDay();
                    $jourSemaine = $date->dayOfWeekIso;

                    Log::debug("pointage - Traitement du jour {$date->toDateString()} (jour de la semaine ISO: {$jourSemaine}).");

                    if ($date->lt($dateDebutRotation)) {
                        $periodesParJour[$day] = 'Avant début rotation';
                        $repartitionsParJour[$day] = null;
                        Log::debug("pointage - Jour {$date->toDateString()}: Période définie comme 'Avant début rotation'.");
                        continue;
                    }

                    if ($jourSemaine === Carbon::MONDAY && $date->gt($dateDebutRotation->copy()->startOfWeek(Carbon::MONDAY))) {
                        $nouvelIndex = ($etatEquipe['periode_index'] + 1) % count($ordrePeriodes);
                        $etatEquipe['periode_actuelle'] = $ordrePeriodes[$nouvelIndex];
                        $etatEquipe['periode_index'] = $nouvelIndex;
                        Log::debug("pointage - Génération calendrier [{$date->toDateString()}]: Lundi, rotation d'équipe effectuée. Nouvelle période ID: {$etatEquipe['periode_actuelle']}, nouvel index: {$etatEquipe['periode_index']}.");
                    }

                    $periodeId = $etatEquipe['periode_actuelle'];
                    $estJourTravail = in_array($jourSemaine, $etatEquipe['jours_travailles']);

                    if ($estJourTravail) {
                        $repartition = $repartitions->get($periodeId);
                        if ($repartition) {
                            $periodesParJour[$day] = $repartition->periode;
                            $repartitionsParJour[$day] = $repartition;
                            Log::debug("pointage - Jour {$date->toDateString()}: Période calculée: {$repartition->periode} (ID: {$periodeId}). C'est un jour de travail.");
                        } else {
                            $periodesParJour[$day] = 'Répartition introuvable pour ID ' . $periodeId;
                            $repartitionsParJour[$day] = null;
                            Log::warning("pointage - Jour {$date->toDateString()}: Répartition introuvable pour l'ID {$periodeId} calculé. Ceci pourrait indiquer une donnée manquante.");
                        }
                    } else {
                        $periodesParJour[$day] = 'Repos';
                        $repartitionsParJour[$day] = null;
                        Log::debug("pointage - Jour {$date->toDateString()}: Jour de repos calculé (jour de la semaine ISO: {$jourSemaine}).");
                    }
                }
            } else { // Système journalier (inchangé)
                $dureeCycle = $rotation->duree_cycle;
                $joursTravailRotation = $repartitions->sum('nombre_jours');
                $nombreJoursRepos = $rotation->nombre_jours_repos ?? ($dureeCycle - $joursTravailRotation);

                $ordrePeriodesJournalier = $repartitions->keys()->toArray();
                if ($nombreJoursRepos > 0) $ordrePeriodesJournalier[] = 'Repos';
                Log::info("pointage - Système journalier. Durée du cycle: {$dureeCycle}, Jours de travail rotation: {$joursTravailRotation}, Jours de repos: {$nombreJoursRepos}.");
                Log::debug("pointage - Ordre des périodes journalières: " . json_encode($ordrePeriodesJournalier));

                $decalageInitial = 0;
                for ($i = 0; $i < $indexEquipe; $i++) {
                    $periodId = $ordrePeriodesJournalier[$i % count($ordrePeriodesJournalier)];
                    $duree = ($periodId === 'Repos')
                        ? $nombreJoursRepos
                        : ($repartitions->get($periodId)->nombre_jours ?? 0);
                    $decalageInitial += $duree;
                }
                $posDansCycleDebutRotation = $decalageInitial % $dureeCycle;
                Log::info("pointage - Décalage initial de l'équipe au début de la rotation: {$decalageInitial}, Position dans le cycle: {$posDansCycleDebutRotation}.");

                $etatEquipe = [
                    'periode_actuelle' => null,
                    'jours_restants' => 0
                ];

                $cumul = 0;
                foreach ($ordrePeriodesJournalier as $id) {
                    $duree = ($id === 'Repos')
                        ? $nombreJoursRepos
                        : ($repartitions->get($id)->nombre_jours ?? 0);
                    if ($posDansCycleDebutRotation < $cumul + $duree) {
                        $etatEquipe['periode_actuelle'] = $id;
                        $etatEquipe['jours_restants'] = $duree - ($posDansCycleDebutRotation - $cumul);
                        break;
                    }
                    $cumul += $duree;
                }
                Log::info("pointage - État initial de l'équipe au début de la rotation: Période ID {$etatEquipe['periode_actuelle']}, Jours restants {$etatEquipe['jours_restants']}.");

                $dateAvancementRapide = $dateDebutRotation->copy();
                $dateFinAvancementRapide = $currentDate->copy()->startOfMonth()->subDay();
                Log::info("pointage - Avancement rapide de l'état de l'équipe de {$dateAvancementRapide->toDateString()} à {$dateFinAvancementRapide->toDateString()}.");

                while ($dateAvancementRapide->lte($dateFinAvancementRapide)) {
                    $etatEquipe['jours_restants']--;
                    if ($etatEquipe['jours_restants'] <= 0) {
                        $this->mettreAJourEtatEquipe($etatEquipe, $ordrePeriodesJournalier, $repartitions, $nombreJoursRepos);
                    }
                    $dateAvancementRapide->addDay();
                }
                Log::info("pointage - État de l'équipe après avancement rapide (à la veille du 1er jour du mois): Période ID {$etatEquipe['periode_actuelle']}, Jours restants {$etatEquipe['jours_restants']}.");

                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = Carbon::create($year, $month, $day)->startOfDay();
                    if ($date->lt($dateDebutRotation)) {
                        $periodesParJour[$day] = 'Avant début rotation';
                        $repartitionsParJour[$day] = null;
                        Log::debug("pointage - Jour {$date->toDateString()}: Avant début rotation.");
                        continue;
                    }

                    $periodeId = $etatEquipe['periode_actuelle'];
                    $repartition = $repartitions->get($periodeId);

                    $periodesParJour[$day] = $periodeId === 'Repos' ? 'Repos' : ($repartition ? $repartition->periode : 'Inconnue (ID: ' . $periodeId . ')');
                    $repartitionsParJour[$day] = $repartition;
                    Log::debug("pointage - Jour {$date->toDateString()}: Période ID {$periodeId}, Jours restants: {$etatEquipe['jours_restants']}.");

                    $etatEquipe['jours_restants']--;
                    if ($etatEquipe['jours_restants'] <= 0) {
                        $this->mettreAJourEtatEquipe($etatEquipe, $ordrePeriodesJournalier, $repartitions, $nombreJoursRepos);
                        Log::debug("pointage - Jour {$date->toDateString()}: Changement de période pour le jour suivant. Nouvelle période: {$etatEquipe['periode_actuelle']}, Nouveaux jours restants: {$etatEquipe['jours_restants']}.");
                    }
                }
            }
        }
        Log::info("--- FIN pointage ---");

        return view('attendanceRotation.index', compact(
            'sites', 'selectedEquipe', 'periodesParJour', 'repartitionsParJour',
            'year', 'month', 'currentDate', 'daysInMonth', 'attendances'
        ));
    }


    protected function validateAttendanceWithRepartition($attendance, ?Repartition $repartition, string $date): bool
    {
        if (!$attendance || !$repartition) {
            Log::warning("Validation d'absence: Pointage ou Répartition manquant(e) pour la date {$date}.");
            return false;
        }

        try {
            $heureDebut = Carbon::parse($date . ' ' . $this->normalizeTimeString($repartition->heure_debut));
            $heureFin = Carbon::parse($date . ' ' . $this->normalizeTimeString($repartition->heure_fin));

            $startTime = Carbon::parse($date . ' ' . $this->normalizeTimeString($attendance->heure_pointage));
            $endTime = property_exists($attendance, 'end_time') && $attendance->end_time ? Carbon::parse($date . ' ' . $this->normalizeTimeString($attendance->end_time)) : null;

            if ($heureFin->lt($heureDebut)) $heureFin->addDay();

            if ($endTime && $endTime->lt($startTime)) $endTime->addDay();

            $marge = 15; // Marge de tolérance en minutes
            $heureDebutMarge = $heureDebut->copy()->subMinutes($marge);
            $heureFinMarge = $heureFin->copy()->addMinutes($marge);

            $isValid = false;
            if ($endTime) {
                $isValid = $startTime->between($heureDebutMarge, $heureFinMarge) &&
                           $endTime->between($heureDebutMarge, $heureFinMarge) &&
                           $startTime <= $endTime;
            } else {
                $isValid = $startTime->between($heureDebutMarge, $heureFinMarge);
            }

            if (!$isValid) {
                Log::debug("Pointage invalide pour date {$date}. Pointage: {$attendance->heure_pointage}, Répartition: {$repartition->heure_debut}-{$repartition->heure_fin}");
            }
            return $isValid;

        } catch (\Exception $e) {
            Log::error("Erreur dans validateAttendanceWithRepartition pour date " . $date . ": " . $e->getMessage(), ['exception' => $e]);
            return false;
        }
    }

    protected function normalizeTimeString(?string $time): string
    {
        if (empty($time)) return '00:00:00';

        $time = str_replace(['h', 'H'], ':', $time);
        $time = preg_replace('/[^0-9:]/', '', $time);

        $parts = explode(':', $time);
        $hour = $parts[0] ?? '00';
        $minute = $parts[1] ?? '00';
        $second = $parts[2] ?? '00';

        return sprintf('%02d:%02d:%02d', $hour, $minute, $second);
    }

    protected function determinerStatus($currentTime, $heureDebut, $heureFin)
    {
        if ($currentTime < $heureDebut) {
            return 'À venir';
        } elseif ($currentTime >= $heureDebut && $currentTime <= $heureFin) {
            return 'En cours';
        } else {
            return 'Terminé';
        }
    }

    protected function calculerTempsRestant($currentTime, $heureDebut, $heureFin, $estAujourdhui, $date)
    {
        if (!$estAujourdhui) {
            return $date->isPast() ? 'Terminé' : 'À venir';
        }

        if ($currentTime < $heureDebut) {
            $diff = $heureDebut->diff($currentTime);
            return 'Débute dans ' . $diff->h . 'h' . str_pad($diff->i, 2, '0', STR_PAD_LEFT) . 'm';
        } elseif ($currentTime >= $heureDebut && $currentTime <= $heureFin) {
            $diff = $heureFin->diff($currentTime);
            return $diff->h . 'h' . str_pad($diff->i, 2, '0', STR_PAD_LEFT) . 'm restant';
        } else {
            return 'Terminé';
        }
    }

    protected function genererCalendrierVide($site, $debutMois, $finMois)
    {
        Log::info("Génération d'un calendrier vide pour le site {$site->id} du {$debutMois->toDateString()} au {$finMois->toDateString()}.");
        $planning = [];
        for ($date = $debutMois->copy(); $date->lte($finMois); $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $planning[$dateStr] = [];

            foreach ($site->equipes as $equipe) {
                $planning[$dateStr][$equipe->nom] = [
                    'periode' => null,
                    'status' => 'N/A',
                    'heure_debut' => null,
                    'heure_fin' => null,
                    'raw_data' => null,
                    'time_info' => 'Hors rotation'
                ];
            }
        }
        return view('rotations.calendrier', [
            'planning' => $planning,
            'site' => $site,
            'debutMois' => $debutMois,
            'finMois' => $finMois,
            'currentTime' => Carbon::now()->format('Y-m-d H:i:s'),
            'showRotation' => false
        ]);
    }

    // Fonction helper pour le système journalier
protected function mettreAJourEtatEquipe(&$etat, $ordrePeriodes, $repartitions, $nombreJoursRepos)
{
    // Trouvez l'index actuel de la période dans $ordrePeriodes
    $currentIndex = array_search($etat['periode_actuelle'], $ordrePeriodes);
    if ($currentIndex === false) {
        Log::error("Erreur: Période actuelle ({$etat['periode_actuelle']}) introuvable dans ordrePeriodes. Réinitialisation à la première période.");
        $currentIndex = 0; // Fallback pour éviter des erreurs
    }

    // Calculez le prochain index de période
    $nextIndex = ($currentIndex + 1) % count($ordrePeriodes);
    $nextPeriodeId = $ordrePeriodes[$nextIndex];

    // Mettez à jour la période actuelle
    $etat['periode_actuelle'] = $nextPeriodeId;

    // Mettez à jour les jours restants pour la nouvelle période
    if ($nextPeriodeId === 'Repos') {
        $etat['jours_restants'] = $nombreJoursRepos;
    } else {
        $repartition = $repartitions->get($nextPeriodeId);
        $etat['jours_restants'] = $repartition ? $repartition->nombre_jours : 0;
    }
    Log::debug("mettreAJourEtatEquipe: Période passée à ID: {$etat['periode_actuelle']}, Jours restants: {$etat['jours_restants']}.");
}

public function showDetail(Request $request, Equipe $equipe, User $user, int $year, int $month, int $day)
{
    $currentUser = Auth::user();

    // Vérification d'accès
    if ($currentUser->company_id !== $equipe->company_id || $currentUser->company_id !== $user->company_id) {
        abort(403, 'Accès non autorisé à cette équipe ou à cet utilisateur.');
    }

    // Configuration des dates
    $currentDate = Carbon::create($year, $month, $day)->startOfDay()->setTimezone(config('app.timezone'));
    $debutMois = Carbon::create($year, $month, 1)->startOfDay()->setTimezone(config('app.timezone'));
    $finMois = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay()->setTimezone(config('app.timezone'));

    // Calcul des heures travaillées du mois
    $totalSeconds = $this->getTotalWorkedSecondsForUser($user, $debutMois, $finMois);
    $daysWorked = Pointage::where('user_id', $user->id)
        ->whereBetween('date_pointage', [$debutMois, $finMois])
        ->where('type', 'arrivee')
        ->count();

    $totalHeuresMois = [
        'seconds' => $totalSeconds,
        'formatted' => $this->formatSecondsToHoursAndMinutesExtended($totalSeconds),
        'days_worked' => $daysWorked,
    ];

    // Initialisation des détails du pointage
    $detailsPointage = [
        'totalWorkedSeconds' => 0,
        'formattedWorkDuration' => '00h 00m',
        'retardMinutes' => 0,
        'heuresSupMinutes' => 0,
        'heureEntreePrevue' => null,
        'heureSortiePrevue' => null,
        'heureEntreeReelle' => null,
        'heureSortieReelle' => null,
        'periode_nom' => null,
        'is_working_day' => false,
        'reason' => 'no_data',
        'formattedRetard' => '00h 00m',
        'formattedHeuresSup' => '00h 00m',
        'etat' => 'Absent',
    ];

    // Récupération des informations de rotation
    $rotationInfo = $this->rotationService->getCurrentPeriod($equipe, $currentDate);
    $periode = $rotationInfo['periode'] ?? null;
    $isWorkingDay = $rotationInfo['is_working_day'] ?? false;
    $detailsPointage['is_working_day'] = $isWorkingDay;
    $detailsPointage['reason'] = $rotationInfo['reason'] ?? null;

    // Traitement des pointages du jour
    $pointagesDuJour = Pointage::where('user_id', $user->id)
        ->whereDate('date_pointage', $currentDate->toDateString())
        ->whereIn('type', ['arrivee', 'depart'])
        ->with(['user', 'site'])
        ->orderBy('heure_pointage')
        ->get();

    $arrivee = $pointagesDuJour->where('type', 'arrivee')->first();
    $depart = $pointagesDuJour->where('type', 'depart')->last();

    if ($isWorkingDay && $periode) {
        $this->processWorkingDayDetails($detailsPointage, $currentDate, $periode, $arrivee, $depart);
    } else {
        $this->processNonWorkingDayDetails($detailsPointage);
    }

    // Préparation des données d'affichage
    $attendances = $this->prepareAttendanceData($pointagesDuJour, $user);

    return view('attendanceRotation.show-detail-rotation', compact(
        'user', 'equipe', 'year', 'month', 'day', 'currentDate',
        'attendances', 'detailsPointage', 'totalHeuresMois'
    ));
}

// Méthodes supplémentaires extraites pour meilleure lisibilité
private function processWorkingDayDetails(array &$details, Carbon $date, $periode, $arrivee, $depart): void
{
    $details['periode_nom'] = $periode->periode;

    $heureEntreePrevue = Carbon::parse($date->toDateString() . ' ' . $periode->heure_debut)
        ->setTimezone(config('app.timezone'));
    $heureSortiePrevue = Carbon::parse($date->toDateString() . ' ' . $periode->heure_fin)
        ->setTimezone(config('app.timezone'));

    if ($heureSortiePrevue->lte($heureEntreePrevue)) {
        $heureSortiePrevue->addDay();
    }

    $details['heureEntreePrevue'] = $heureEntreePrevue->format('H:i');
    $details['heureSortiePrevue'] = $heureSortiePrevue->format('H:i');

    if ($arrivee) {
        $heureEntreeReelle = Carbon::parse($arrivee->date_pointage->format('Y-m-d') . ' ' . $this->normalizeTime($arrivee->heure_pointage))
            ->setTimezone(config('app.timezone'));
        $details['heureEntreeReelle'] = $heureEntreeReelle->format('H:i');

        if ($heureEntreeReelle->gt($heureEntreePrevue)) {
            $details['retardMinutes'] = max(0, $heureEntreePrevue->diffInMinutes($heureEntreeReelle, false));
            $details['formattedRetard'] = $this->formatMinutesToHoursAndMinutes($details['retardMinutes']);
        }
    }

    if ($depart) {
        $heureSortieReelle = Carbon::parse($depart->date_pointage->format('Y-m-d') . ' ' . $this->normalizeTime($depart->heure_pointage))
            ->setTimezone(config('app.timezone'));

        if (isset($heureEntreeReelle) && $heureSortieReelle->lt($heureEntreeReelle)) {
            $heureSortieReelle->addDay();
        }
        $details['heureSortieReelle'] = $heureSortieReelle->format('H:i');
    }

    if (isset($heureEntreeReelle, $heureSortieReelle)) {
        $details['totalWorkedSeconds'] = abs($heureSortieReelle->diffInSeconds($heureEntreeReelle));
        $details['formattedWorkDuration'] = $this->formatSecondsToHoursAndMinutesExtended($details['totalWorkedSeconds']);

        if ($heureSortieReelle->gt($heureSortiePrevue)) {
            $details['heuresSupMinutes'] = abs($heureSortieReelle->diffInMinutes($heureSortiePrevue));
            $details['formattedHeuresSup'] = $this->formatMinutesToHoursAndMinutes($details['heuresSupMinutes']);
        }
    }

    $details['etat'] = match (true) {
        !$arrivee && !$depart => 'Absent',
        $arrivee && !$depart => 'Pointage partiel',
        default => 'Présence complète',
    };
}

private function processNonWorkingDayDetails(array &$details): void
{
    $details['etat'] = match ($details['reason']) {
        'weekly_off_day' => 'Jour de repos hebdomadaire',
        'daily_off_day' => 'Jour de repos journalier',
        'before_rotation_start' => 'Avant le début de la rotation',
        'no_repartitions_found' => 'Rotation non configurée',
        'period_not_found_for_index' => 'Période non trouvée',
        'jour_ferie' => 'Jour férié',
        default => 'Non applicable ou non défini',
    };
}

private function prepareAttendanceData($pointagesDuJour, $user): \Illuminate\Support\Collection
{
    return $pointagesDuJour->map(function ($pointage) use ($user) {
        return [
            'id' => $pointage->id,
            'type' => $pointage->type,
            'timestamp' => Carbon::parse($pointage->date_pointage->format('Y-m-d') . ' ' . $pointage->heure_pointage)
                ->setTimezone(config('app.timezone')),
            'site_name' => $pointage->site->name ?? 'N/A',
            'user_id' => $user->id,
            'statut' => $pointage->statut,
            'latitude' => $pointage->latitude,
            'longitude' => $pointage->longitude,
        ];
    });
}

// Méthode utilitaire de formatage secondes en hh mm
private function formatSecondsToHoursAndMinutesExtended(int $totalSeconds): string
{
    if ($totalSeconds <= 0) return '00h 00m';
    $hours = floor($totalSeconds / 3600);
    $minutes = floor(($totalSeconds % 3600) / 60);
    return sprintf('%02dh %02dm', $hours, $minutes);
}
// Méthode utilitaire de formatage minutes en hh mm
private function formatMinutesToHoursAndMinutes(int $totalMinutes): string
{
    $totalMinutes = max(0, $totalMinutes);
    $hours = floor($totalMinutes / 60);
    $minutes = $totalMinutes % 60;
    return sprintf('%02dh %02dm', $hours, $minutes);
}

private function getTotalWorkedSecondsForUser(User $user, Carbon $from, Carbon $to): int
{
    $pointages = Pointage::where('user_id', $user->id)
        ->whereBetween('date_pointage', [
            $from->copy()->startOfDay(),
            $to->copy()->endOfDay()
        ])
        ->whereIn('type', ['arrivee', 'depart'])
        ->orderBy('date_pointage')
        ->orderBy('heure_pointage')
        ->get()
        ->groupBy(function($item) {
            return $item->date_pointage->format('Y-m-d');
        });

    $totalSeconds = 0;

    foreach ($pointages as $jour => $points) {
        try {
            $arrivee = $points->where('type', 'arrivee')->first();
            $depart = $points->where('type', 'depart')->last();

            if ($arrivee && $depart) {
                // Conversion en timestamps Unix précis
                $tsArrivee = Carbon::parse(
                    $arrivee->date_pointage->format('Y-m-d') . ' ' .
                    $this->normalizeTime($arrivee->heure_pointage),
                    config('app.timezone')
                )->timestamp;

                $tsDepart = Carbon::parse(
                    $depart->date_pointage->format('Y-m-d') . ' ' .
                    $this->normalizeTime($depart->heure_pointage),
                    config('app.timezone')
                )->timestamp;

                // Calcul direct en secondes
                $dureeSecondes = $tsDepart - $tsArrivee;

                // Validation réaliste
                if ($dureeSecondes > 0 && $dureeSecondes <= 16 * 3600) { // Max 16h
                    $totalSeconds += $dureeSecondes;
                    Log::debug("Durée validée pour $jour: " . gmdate('H:i:s', $dureeSecondes));
                } else {
                    Log::warning("Durée rejetée pour $jour: " . gmdate('H:i:s', $dureeSecondes));
                }
            }
        } catch (\Exception $e) {
            Log::error("Erreur calcul durée $jour: " . $e->getMessage());
            continue;
        }
    }

    return $totalSeconds;
}

private function normalizeTime(string $time): string
{
    $time = trim($time);

    // Format HH:mm
    if (preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/', $time)) {
        return $time . ':00';
    }

    // Format HH:mm:ss
    if (preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $time)) {
        return $time;
    }

    throw new \InvalidArgumentException("Format de temps invalide: $time");
}


    // Dans AttendanceRotationController.php

 // ajouter en haut de ton fichier



public function bilan(Request $request)
{
    $currentUserCompanyId = Auth::user()->company_id;
    $sites = SiteRotation::where('company_id', $currentUserCompanyId)->get();
    $timezone = 'Africa/Lome';

    Carbon::setLocale('fr');
    Carbon::setToStringFormat('Y-m-d H:i:s');

    // Détermination de la période à analyser
    $periodType = $request->input('period', 'month');
    $startDate = null;
    $endDate = null;

    switch ($periodType) {
        case 'day':
            $date = $request->input('day') ? Carbon::parse($request->input('day'), $timezone) : Carbon::now($timezone);
            $startDate = $date->copy()->startOfDay();
            $endDate = $date->copy()->endOfDay();
            break;
        case 'week':
            $date = $request->input('week') ? Carbon::parse($request->input('week'), $timezone)->startOfWeek() : Carbon::now($timezone)->startOfWeek();
            $startDate = $date->copy()->startOfDay();
            $endDate = $date->copy()->endOfWeek()->endOfDay();
            break;
        case 'month':
            $date = $request->input('month') ? Carbon::parse($request->input('month') . '-01', $timezone) : Carbon::now($timezone);
            $startDate = $date->copy()->startOfMonth();
            $endDate = $date->copy()->endOfMonth();
            break;
        case 'year':
            $date = $request->input('year') ? Carbon::parse($request->input('year') . '-01-01', $timezone) : Carbon::now($timezone);
            $startDate = $date->copy()->startOfYear();
            $endDate = $date->copy()->endOfYear();
            break;
        default:
            $startDate = Carbon::now($timezone)->startOfMonth();
            $endDate = Carbon::now($timezone)->endOfMonth();
            break;
    }

    Log::info("Période sélectionnée : {$startDate->toDateString()} - {$endDate->toDateString()}");

    // Récupération des utilisateurs
    $usersQuery = User::where('user_type', 'user')
        ->where('type_horaire', 'rotation')
        ->where('company_id', $currentUserCompanyId)
        ->with('fonction')
        ->orderBy('name')
        ->orderBy('first_name');

    $users = $usersQuery->paginate(10);
    $metrics = [];

    foreach ($users as $user) {
        Log::info("Traitement utilisateur : {$user->id} - {$user->name}");

        // Compter les statuts (sans filtrer par type de pointage)
        $pointages = Pointage::where('user_id', $user->id)
            ->whereBetween('date_pointage', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();

        $presenceCount = $pointages->where('statut', 'present')->count();
        $retardCount = $pointages->where('statut', 'retard')->count();

        // Total des heures travaillées
        $heureMensuelle = HeureMensuelle::where('user_id', $user->id)
            ->where('annee', $startDate->year)
            ->where('mois', $startDate->month)
            ->first();

        $totalHours = $heureMensuelle ? $heureMensuelle->total_heures : 0;

        Log::info("{$user->name} - Présents : {$presenceCount}, Retards : {$retardCount}, Heures : {$totalHours}");
        $absentCount = 0;
        $metrics[$user->id] = [
            'total_present' => $presenceCount,
            'total_late' => $retardCount,
            'total_worked_hours' => round($totalHours, 2),
            'total_absent' => $absentCount,
        ];
    }
    $selectedSiteName = $request->input('type');

    return view('attendanceRotation.bilan', compact(
        'users', 'metrics', 'sites',
        'startDate', 'endDate', 'periodType','selectedSiteName'
    ));
}








    /**
     * Formate un nombre de jours.
     *
     * @param int $days
     * @return string
     */
    private function formatDays(int $days): string
    {
        return $days . ' jour' . ($days > 1 ? 's' : '');
    }

    /**
     * Formate un nombre total de secondes en "HHh MMm".
     *
     * @param int $totalSeconds
     * @return string
     */


    /**
     * Formate un nombre total de minutes en "HHh MMm".
     *
     * @param int $totalMinutes
     * @return string
     */

}
