<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\HeureMensuelle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CalculHeuresFixesMensuellesService
{
    public function calculerPourUtilisateur($user, $mois, $annee)
    {

        if ($user->type_horaire !== 'fixe') {
            Log::info("Utilisateur ignoré (type_horaire ≠ fixe) : ID {$user->id}");
            return;
        }
        // Récupérer tous les pointages (attendances) du mois, groupés par jour
        $attendances = Attendance::where('user_id', $user->id)
            ->whereMonth('timestamp', $mois)
            ->whereYear('timestamp', $annee)
            ->orderBy('timestamp')
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->timestamp)->toDateString();
            });

        $totalHeures = 0;

        foreach ($attendances as $jour => $records) {
            $arrivee = $records->firstWhere('type', 'arrivee');
            $depart = $records->firstWhere('type', 'depart');

            if ($arrivee && $depart) {
                try {
                    $heureArrivee = Carbon::parse($arrivee->timestamp);
                    $heureDepart = Carbon::parse($depart->timestamp);

                    if ($heureDepart->lt($heureArrivee)) {
                        $heureDepart->addDay(); // Cas travail de nuit
                        Log::info("Travail de nuit détecté pour user {$user->id} le {$jour}, ajustement de l'heure de départ.");
                    }

                    $heures = $heureDepart->floatDiffInHours($heureArrivee);

                    if ($heures < 0) {
                        Log::warning("Heures négatives détectées pour user {$user->id} le {$jour}. Conversion en positif.");
                        $heures = abs($heures);
                    }

                    $totalHeures += $heures;

                    Log::info("[$jour] Heures calculées pour user {$user->id} : $heures");

                } catch (\Exception $e) {
                    Log::error("Erreur lors du calcul des heures (fixes) pour user {$user->id} jour $jour : " . $e->getMessage());
                }
            } else {
                Log::info("Pointages incomplets pour user {$user->id} jour $jour.");
            }
        }

        // Stocker ou mettre à jour
        HeureMensuelle::updateOrCreate(
            [
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'mois' => $mois,
                'annee' => $annee,
            ],
            ['total_heures' => round($totalHeures, 2)]
        );

        Log::info("Total heures fixes pour user {$user->id} mois $mois/$annee : " . round($totalHeures, 2));
    }
}
