<?php
namespace App\Services;

use App\Models\Pointage;
use App\Models\HeureMensuelle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CalculHeuresMensuellesService
{
    public function calculerPourUtilisateur($user, $mois, $annee)
    {

         if ($user->type_horaire !== 'rotation') {
            Log::info("Utilisateur ignoré (type_horaire != 'rotation') : ID {$user->id}");
            return;
        }
        // Récupérer tous les pointages du mois, groupés par jour
        $pointages = Pointage::where('user_id', $user->id)
            ->whereMonth('date_pointage', $mois)
            ->whereYear('date_pointage', $annee)
            ->orderBy('date_pointage')
            ->orderBy('heure_pointage')
            ->get()
            ->groupBy('date_pointage');

        $total = 0;

        foreach ($pointages as $jour => $journée) {
            // Chercher la première arrivée et le premier départ du jour
            $arrivee = $journée->firstWhere('type', 'arrivee');
            $depart = $journée->firstWhere('type', 'depart');

            if ($arrivee && $depart) {
                try {
                    // On récupère la date du jour, format Y-m-d
                    $dateStr = Carbon::parse($jour)->format('Y-m-d');

                    // Format des heures: on s'assure qu'on a bien une heure au format H:i:s
                    $heureArrivee = date('H:i:s', strtotime($arrivee->heure_pointage));
                    $heureDepart = date('H:i:s', strtotime($depart->heure_pointage));

                    // Fusion date + heure pour avoir un datetime complet
                    $debut = Carbon::parse("$dateStr $heureArrivee");
                    $fin = Carbon::parse("$dateStr $heureDepart");

                    // Si l'heure de départ est avant l'heure d'arrivée, on ajoute 1 jour au départ (travail de nuit)
                    if ($fin->lt($debut)) {
                        $fin->addDay();
                        Log::info("Travail de nuit détecté pour user {$user->id} le {$dateStr}, départ ajusté au lendemain.");
                    }

                    // Calcul de la différence en heures, valeur signée (false pour ne pas forcer la valeur absolue)
                    $heures = $fin->floatDiffInHours($debut, false);

                    // On s'assure que la valeur soit positive (sécurité)
                    if ($heures < 0) {
                        Log::warning("Heures négatives détectées, conversion en positif pour user {$user->id} jour {$jour}");
                        $heures = abs($heures);
                    }

                    Log::info("Calcul heures - Jour $jour, Arrivée : " . $debut->toDateTimeString() . ", Départ : " . $fin->toDateTimeString());
                    Log::info("Heures calculées : $heures");

                    $total += $heures;

                } catch (\Exception $e) {
                    Log::error("Erreur lors du calcul des heures pour user {$user->id} jour $jour : " . $e->getMessage());
                }
            } else {
                Log::info("Pointages incomplets pour user {$user->id} jour $jour : arrivée ou départ manquant.");
            }
        }

        // Stocker ou mettre à jour le total arrondi à 2 décimales
        HeureMensuelle::updateOrCreate(
            [
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'mois' => $mois,
                'annee' => $annee,
            ],
            ['total_heures' => round($total, 2)]
        );

        Log::info("Total heures pour user {$user->id} mois {$mois}/{$annee} : " . round($total, 2));
    }
}
