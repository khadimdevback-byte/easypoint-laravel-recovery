<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{User, Equipe, Repartition, Pointage, Rotation};
use Carbon\Carbon;
use App\Services\RotationService; // Assurez-vous d'utiliser votre RotationService
use Illuminate\Support\Facades\Log;

class CheckAndMarkAbsences extends Command
{
    /**
     * Le nom et la signature de la commande console.
     *
     * @var string
     */
    protected $signature = 'absences:check {--date= : La date à vérifier pour les absences (AAAA-MM-JJ). Par défaut, hier.}';

    /**
     * La description de la commande console.
     *
     * @var string
     */
    protected $description = 'Vérifie les pointages d\'arrivée manquants pour les équipes terminées et marque les utilisateurs comme absents.';

    protected RotationService $rotationService;

    public function __construct(RotationService $rotationService)
    {
        parent::__construct();
        $this->rotationService = $rotationService;
    }

    /**
     * Exécute la commande console.
     */
    public function handle()
    {
        // La date à vérifier est soit celle fournie en option, soit hier.
        // `startOfDay()` est important pour que les comparaisons de date soient cohérentes,
        // surtout avec `date_pointage`.
        $dateToCheck = $this->option('date') ? Carbon::parse($this->option('date'))->startOfDay() : Carbon::yesterday()->startOfDay();

        $this->info("Vérification des absences pour la date : " . $dateToCheck->toDateString());

        // Récupérer toutes les rotations actives
        $rotations = Rotation::where('statut', 'active')->with('equipes.users', 'repartitions', 'sites')->get();

        foreach ($rotations as $rotation) {
            foreach ($rotation->equipes as $equipe) {
                // Déterminer la période pour cette équipe à la dateToCheck
                try {
                    $infosRotation = $this->rotationService->getCurrentPeriod($equipe, $dateToCheck);
                    $periode = $infosRotation['periode'];
                    $isWorkingDay = $infosRotation['is_working_day'];

                    if ($isWorkingDay && $periode) {
                        // Vérifier si la période pour ce jour est effectivement terminée
                        $heureFinPeriode = Carbon::parse($periode->heure_fin)->setDateFrom($dateToCheck);
                        $heureDebutPeriode = Carbon::parse($periode->heure_debut)->setDateFrom($dateToCheck);

                        // Gérer les périodes qui traversent minuit (ex: 22h00-06h00)
                        if ($heureFinPeriode->lte($heureDebutPeriode)) {
                            $heureFinPeriode->addDay(); // La fin est le lendemain
                        }

                        // Nous ne marquons l'absence que si la période est entièrement passée
                        // Comparaison avec le temps actuel
                        if (Carbon::now()->greaterThan($heureFinPeriode)) {
                            foreach ($equipe->users as $user) {
                                // Vérifier s'il y a eu un pointage d'arrivée pour cette période et ce jour
                                $hasArrived = Pointage::where('user_id', $user->id)
                                                    ->where('date_pointage', $dateToCheck)
                                                    ->where('periode_id', $periode->id)
                                                    ->where('type', 'arrivee')
                                                    ->exists();

                                if (!$hasArrived) {
                                    // Vérifier si un pointage de type 'absence' ou 'repos' existe déjà pour éviter les doublons
                                    $existingAbsenceOrRepos = Pointage::where('user_id', $user->id)
                                                                    ->where('date_pointage', $dateToCheck)
                                                                    ->where('periode_id', $periode->id)
                                                                    ->whereIn('statut', ['absence', 'repos'])
                                                                    ->exists();

                                    if (!$existingAbsenceOrRepos) {
                                        Pointage::create([
                                            'user_id' => $user->id,
                                            'equipe_id' => $equipe->id,
                                            'site_id' => $rotation->sites->first()->id, // Supposant qu'une rotation a au moins un site
                                            'company_id' => $user->company_id,
                                            'rotation_id' => $rotation->id,
                                            'periode_id' => $periode->id,
                                            'date_pointage' => $dateToCheck,
                                            'heure_pointage' => $periode->heure_fin, // Utiliser l'heure de fin de période pour l'enregistrement d'absence
                                            'type' => 'arrivee', // Utiliser 'arrivee' comme type, le statut 'absence' est la clé
                                            'statut' => 'absence',
                                            // Les coordonnées peuvent être nulles ou par défaut selon vos préférences pour les entrées générées automatiquement
                                            'latitude' => null,
                                            'longitude' => null,
                                        ]);
                                        $this->info("Utilisateur {$user->name} marqué comme ABSENT pour la période {$periode->periode} le {$dateToCheck->toDateString()}.");
                                    } else {
                                        $this->line("L'utilisateur {$user->name} a déjà une entrée d'absence/repos pour la période {$periode->periode} le {$dateToCheck->toDateString()}. Ignoré.");
                                    }
                                }
                            }
                        } else {
                            $this->line("La période pour l'équipe {$equipe->nom} ({$periode->periode}) le {$dateToCheck->toDateString()} n'est pas encore terminée. Vérification d'absence ignorée.");
                        }
                    } else {
                        $this->line("L'équipe {$equipe->nom} ne travaille pas ou n'a pas de période définie pour le {$dateToCheck->toDateString()}. Ignoré.");
                    }
                } catch (\Exception $e) {
                    $this->error("Erreur lors de la vérification des absences pour l'équipe {$equipe->nom} le {$dateToCheck->toDateString()} : " . $e->getMessage());
                    Log::error("Erreur de vérification d'absence", ['equipe_id' => $equipe->id, 'date' => $dateToCheck->toDateString(), 'error' => $e->getMessage()]);
                }
            }
        }
        $this->info("Vérification des absences terminée.");
    }
}
