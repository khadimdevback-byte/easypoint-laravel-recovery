<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\{Equipe, Repartition, Rotation, SiteRotation};

class RotationService
{
    public function getCurrentPeriod(Equipe $equipe, Carbon $date = null): array
    {
        $date = $date ?? Carbon::now();
        Log::debug('RotationService - Début getCurrentPeriod', ['equipe_id' => $equipe->id, 'date_demandee' => $date->toDateString()]);

        try {
            $rotation = $equipe->rotations()
                ->with(['sites.equipes', 'repartitions'])
                ->firstOrFail();

            $dateDebutRotation = Carbon::parse($rotation->date_debut)->startOfDay();

            if ($date->lt($dateDebutRotation)) {
                return ['periode' => null, 'rotation' => $rotation, 'is_working_day' => false, 'reason' => 'before_rotation_start'];
            }

            $site = $rotation->sites->first();
            if (!$site) {
                throw new \RuntimeException('Aucun site associé à la rotation', 404);
            }

            $equipesSite = $site->equipes->sortBy('id');
            $indexEquipe = $this->getEquipeIndex($equipe, $equipesSite);

            // ======================================================
            // DÉBUT MODIFICATION CRITIQUE (SYSTÈME HEBDOMADAIRE)
            // ======================================================
            if ($rotation->systeme === 'hebdomadaire') {
                $ordreNomsPeriodes = ['Matin', 'apres-midi', 'nuit'];
                $repartitions = $rotation->repartitions->sortBy(function ($rep) use ($ordreNomsPeriodes) {
                    $index = array_search($rep->periode, $ordreNomsPeriodes);
                    return $index === false ? PHP_INT_MAX : $index;
                })->values();
            } else {
                $repartitions = $rotation->repartitions->sortBy('id')->values();
            }
            // ======================================================
            // FIN MODIFICATION
            // ======================================================

            if ($repartitions->isEmpty()) {
                return ['periode' => null, 'rotation' => $rotation, 'is_working_day' => false, 'reason' => 'no_repartitions_found'];
            }

            $isWorkingDay = true;
            $periode = null;
            $reason = null;

            if ($rotation->systeme === 'hebdomadaire') {
                // $joursTravailHebdoConfig = json_decode($rotation->jours_travail_hebdo ?? '[]', true);
                $joursTravailHebdoConfig = [1, 2, 3, 4, 5]; 
                if (empty($joursTravailHebdoConfig)) {
                    $joursTravailHebdoConfig = range(Carbon::MONDAY, Carbon::FRIDAY);
                }

                $currentDayOfWeekIso = $date->dayOfWeekIso;
                if (!in_array($currentDayOfWeekIso, $joursTravailHebdoConfig)) {
                    $isWorkingDay = false;
                    $reason = 'weekly_off_day';
                } else {
                    $startOfWeekRotation = $dateDebutRotation->copy()->startOfWeek(Carbon::MONDAY);
                    $startOfWeekCurrentDate = $date->copy()->startOfWeek(Carbon::MONDAY);
                    $semainesEcoulees = $startOfWeekRotation->diffInWeeks($startOfWeekCurrentDate, true);
                    $periodeIndex = ($semainesEcoulees + $indexEquipe) % $repartitions->count();
                    $periode = $repartitions->get($periodeIndex);
                }
            } else {
                // Logique inchangée pour le système journalier
                $totalDaysInCycle = $this->calculateTotalDaysInCycle($repartitions, $rotation->nombre_jours_repos ?? 0);
                $joursPasses = $dateDebutRotation->diffInDays($date, false);
                $globalJourDansCycle = $joursPasses % $totalDaysInCycle;
                $globalJourDansCycle = ($globalJourDansCycle < 0) ? ($globalJourDansCycle + $totalDaysInCycle) : $globalJourDansCycle;

                $startIndexes = $this->getStartIndexes($repartitions, $rotation->nombre_jours_repos ?? 0);
                $offsetForEquipe = $startIndexes[$indexEquipe % count($startIndexes)] ?? 0;

                $periodeAssigneeIndex = ($globalJourDansCycle + $offsetForEquipe) % $totalDaysInCycle;
                $periodeAssigneeIndex = ($periodeAssigneeIndex < 0) ? ($periodeAssigneeIndex + $totalDaysInCycle) : $periodeAssigneeIndex;

                $periodeCandidat = $this->buildCycleEtenduPeriodes($repartitions, $rotation->nombre_jours_repos ?? 0)
                    ->get($periodeAssigneeIndex);

                if ($periodeCandidat === 'Repos') {
                    $isWorkingDay = false;
                    $reason = 'daily_off_day';
                } else {
                    $periode = $periodeCandidat;
                }
            }

            if ($isWorkingDay && $periode === null) {
                throw new \RuntimeException('Impossible de déterminer la période de travail', 400);
            }

            return ['periode' => $periode, 'rotation' => $rotation, 'is_working_day' => $isWorkingDay, 'reason' => $reason];

        } catch (\Exception $e) {
            Log::error('RotationService - Erreur inattendue', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    private function getEquipeIndex(Equipe $equipe, $equipes): int
    {
        $index = $equipes->search(fn($eq) => $eq->id === $equipe->id);
        if ($index === false) {
            throw new \RuntimeException('Équipe non trouvée dans le site', 404);
        }
        return $index;
    }

    private function calculateTotalDaysInCycle($repartitions, $nombreJoursRepos): int
    {
        return $repartitions->sum('nombre_jours') + $nombreJoursRepos;
    }

    private function getStartIndexes($repartitions, $nombreJoursRepos): array
    {
        $startIndexes = [];
        $currentIndex = 0;
        foreach ($repartitions as $repartition) {
            $startIndexes[] = $currentIndex;
            $currentIndex += $repartition->nombre_jours ?? 1;
        }
        if ($nombreJoursRepos > 0) {
            $startIndexes[] = $currentIndex;
        }
        return $startIndexes;
    }

    private function buildCycleEtenduPeriodes($repartitions, $nombreJoursRepos)
    {
        $cycleEtenduPeriodes = collect();
        foreach ($repartitions as $repartition) {
            $jours = $repartition->nombre_jours ?? 1;
            for ($i = 0; $i < $jours; $i++) {
                $cycleEtenduPeriodes->push($repartition);
            }
        }
        for ($i = 0; $i < $nombreJoursRepos; $i++) {
            $cycleEtenduPeriodes->push('Repos');
        }
        return $cycleEtenduPeriodes;
    }

    public function validatePointageTime(Repartition $periode, int $margeMinutes): string
    {
        $now = Carbon::now();
        $heureDebut = Carbon::parse($periode->heure_debut);
        $heureFin = Carbon::parse($periode->heure_fin);

        if ($heureFin->lte($heureDebut)) {
            $heureFin->addDay();
        }

        $plageArrivee = [
            $heureDebut->copy()->subMinutes($margeMinutes),
            $heureDebut->copy()->addMinutes($margeMinutes)
        ];

        $plageDepart = [
            $heureFin->copy()->subMinutes($margeMinutes),
            $heureFin->copy()->addMinutes($margeMinutes)
        ];

        if ($now->between(...$plageArrivee)) return 'arrivee';
        if ($now->between(...$plageDepart)) return 'depart';
        return $now->lt($heureDebut) ? 'arrivee' : 'depart';
    }

    public function getTimeRemaining(?Repartition $periode, Carbon $date = null): int
    {
        if (!$periode) return 0;

        $date = $date ?? Carbon::now();
        $fin = Carbon::parse($periode->heure_fin)->setDateFrom($date);
        $debut = Carbon::parse($periode->heure_debut)->setDateFrom($date);

        if ($fin->lte($debut)) $fin->addDay();
        return max(0, $date->diffInSeconds($fin, false));
    }

    public function getPeriodStatus(?Repartition $periode, Carbon $date = null): string
    {
        if (!$periode) return 'futur';

        $date = $date ?? Carbon::now();
        $debut = Carbon::parse($periode->heure_debut)->setDateFrom($date);
        $fin = Carbon::parse($periode->heure_fin)->setDateFrom($date);

        if ($fin->lte($debut)) $fin->addDay();
        if ($date->lt($debut)) return 'futur';
        if ($date->lte($fin)) return 'en_cours';
        return 'termine';
    }
}
