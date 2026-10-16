<?php

namespace App\Http\Controllers;

use App\Models\Rotation;
use App\Models\Repartition;
use App\Models\Site;
use App\Models\SiteRotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use DatePeriod;  // Ajout de l'import manquant
use DateInterval;
class RotationController extends Controller

{
    //

    public function create(){
        return view('rotations.create');
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'nom' => 'required|string|max:255',
    //         'systeme' => 'required|in:journalier,hebdomadaire,mensuel',
    //         'duree_cycle' => 'required|integer|min:1',
    //         'nombre_jours_travail' => 'required|integer|min:1',
    //         'nombre_repartitions' => 'required|integer|min:1',
    //         'date_debut' => 'required|date',
    //         // 'repartitions' => 'required|array|min:1',
    //         // 'repartitions.*.periode' => 'required|string|in:matin,apres-midi,nuit',
    //         // 'repartitions.*.heure_debut' => 'required|date_format:H:i',
    //         // 'repartitions.*.heure_fin' => 'required|date_format:H:i|after:repartitions.*.heure_debut',
    //         // 'repartitions.*.nombre_jours' => 'required|integer|min:1',
    //     ]);

    //     // Convertir la durée en entier pour éviter les erreurs
    //     $duree_cycle = (int) $request->duree_cycle;
    //     $jours_travail = (int) $request->nombre_jours_travail;
    //     $jours_repos = $duree_cycle - $jours_travail;

    //     if ($jours_repos < 0) {
    //         return redirect()->back()->withErrors(['nombre_jours_travail' => 'Le nombre de jours travaillés ne peut pas dépasser la durée du cycle.']);
    //     }

    //     // Déterminer la date de fin en fonction du système choisi
    //     $date_debut_cycle = Carbon::parse($request->date_debut);

    //     switch ($request->systeme) {
    //         case 'journalier':
    //             $date_fin_cycle = $date_debut_cycle->copy()->addDays($duree_cycle);
    //             break;
    //         case 'hebdomadaire':
    //             $date_fin_cycle = $date_debut_cycle->copy()->addWeeks($duree_cycle);
    //             break;
    //         case 'mensuel':
    //             $date_fin_cycle = $date_debut_cycle->copy()->addMonths($duree_cycle);
    //             break;
    //         default:
    //             return redirect()->back()->withErrors(['systeme' => 'Type de système invalide.']);
    //     }

    //     // Création de la rotation (UNE SEULE FOIS)
    //     $rotation = Rotation::create([
    //         'nom' => $request->nom,
    //         'systeme' => $request->systeme,
    //         'duree_cycle' => $duree_cycle,
    //         'nombre_jours_travail' => $jours_travail,
    //         'nombre_repartitions' => $request->nombre_repartitions,
    //         'nombre_jours_repos' => $jours_repos,
    //         'date_debut' => $date_debut_cycle,
    //         'statut' => 'active',
    //         'company_id' => Auth::user()->company_id,
    //     ]);

    //     // Création des répartitions liées à cette rotation
    //     foreach ($request->repartitions as $repartition) {
    //         Repartition::create([
    //             'rotation_id' => $rotation->id,
    //             'periode' => $repartition['periode'],
    //             'heure_debut' => $repartition['heure_debut'],
    //             'heure_fin' => $repartition['heure_fin'],
    //             'nombre_jours' => $repartition['nombre_jours'], // Assuré d'être présent via la validation
    //         ]);
    //     }

    //     return redirect()->back()->with('success', 'Rotation créée avec succès.');
    // }



public function store(Request $request)
{
    // Validation des données du formulaire
    $request->validate([
        'nom' => 'required|string|max:255',
        'systeme' => 'required|in:journalier,hebdomadaire,mensuel',
        'duree_cycle' => 'required|integer|min:1',
        'nombre_jours_travail' => 'required|integer|min:1',
        'nombre_repartitions' => 'required|integer|min:1',
        'date_debut' => 'required|date',
        'repartitions' => 'required|array|min:1',
        'repartitions.*.periode' => 'required|string',
        'repartitions.*.heure_debut' => 'required',
        'repartitions.*.heure_fin' => 'required',
        'repartitions.*.nombre_jours' => 'required|integer|min:1',
    ]);

    $duree_cycle = (int) $request->duree_cycle;
    $jours_travail = (int) $request->nombre_jours_travail;

    if ($request->systeme === 'journalier') {
        $somme_jours_repartitions = collect($request->repartitions)->sum('nombre_jours');

        if ($somme_jours_repartitions != $jours_travail) {
            return redirect()->back()->withErrors([
                'repartitions' => "La somme des jours des répartitions ($somme_jours_repartitions) doit être égale au nombre de jours travaillés ($jours_travail) pour le système journalier."
            ])->withInput();
        }
    }

    if ($request->systeme === 'hebdomadaire') {
        foreach ($request->repartitions as $rep) {
            if ((int) $rep['nombre_jours'] !== $jours_travail) {
                return redirect()->back()->withErrors([
                    'repartitions' => "Chaque répartition (équipe) doit avoir exactement $jours_travail jours de travail pour le système hebdomadaire."
                ])->withInput();
            }
        }

        // Vérifie aussi que nombre_repartitions == nombre d'équipes (cohérent)
        if ((int)$request->nombre_repartitions !== count($request->repartitions)) {
            return redirect()->back()->withErrors([
                'nombre_repartitions' => "Le nombre de répartitions ne correspond pas au nombre d'équipes spécifiées."
            ])->withInput();
        }
    }

    // Déterminer la date de fin du cycle
    $date_debut_cycle = Carbon::parse($request->date_debut);

    switch ($request->systeme) {
        case 'journalier':
            $total_jours_cycle = $duree_cycle;
            $date_fin_cycle = $date_debut_cycle->copy()->addDays($duree_cycle);
            break;
        case 'hebdomadaire':
            $total_jours_cycle = $duree_cycle * 7;
            $date_fin_cycle = $date_debut_cycle->copy()->addWeeks($duree_cycle);
            break;
        case 'mensuel':
            $total_jours_cycle = $duree_cycle * 30; // Ou utilise CarbonPeriod pour être plus précis
            $date_fin_cycle = $date_debut_cycle->copy()->addMonths($duree_cycle);
        break;
        default:
        return redirect()->back()->withErrors(['systeme' => 'Type de système invalide.']);
    }

    $jours_repos = $total_jours_cycle - $jours_travail;

    if ($jours_repos < 0) {
        return redirect()->back()->withErrors([
            'nombre_jours_travail' => 'Le nombre de jours travaillés ne peut pas dépasser la durée totale du cycle.'
        ]);
    }

    // Création de la rotation
    $rotation = Rotation::create([
        'nom' => $request->nom,
        'systeme' => $request->systeme,
        'duree_cycle' => $duree_cycle,
        'nombre_jours_travail' => $jours_travail,
        'nombre_repartitions' => $request->nombre_repartitions,
        'nombre_jours_repos' => $jours_repos,
        'date_debut' => $date_debut_cycle,
        'date_fin' => $date_fin_cycle, // ← ici
        'statut' => 'active',
        'company_id' => Auth::user()->company_id,
    ]);

    // Création des répartitions
    foreach ($request->repartitions as $repartition) {
        Repartition::create([
            'rotation_id' => $rotation->id,
            'periode' => $repartition['periode'],
            'heure_debut' => $repartition['heure_debut'],
            'heure_fin' => $repartition['heure_fin'],
            'nombre_jours' => $repartition['nombre_jours'],
        ]);
    }

    return redirect()->back()->with('success', 'Rotation et répartitions créées avec succès.');
}



    public function index()
    {
        $rotations = Rotation::with('repartitions')->get(); // Charger les répartitions avec chaque rotation
        return view('rotations.index', compact('rotations'));
    }

    public function edit($id)
    {
        // Récupérer la rotation à éditer
        $rotation = Rotation::findOrFail($id);

        // Récupérer les répartitions associées
        $repartitions = Repartition::where('rotation_id', $id)->get();

        // Retourner la vue d'édition avec les données nécessaires
        return view('rotations.edit', compact('rotation', 'repartitions'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'systeme' => 'required|in:journalier,hebdomadaire,mensuel',
            'duree_cycle' => 'required|integer|min:1',
            'nombre_jours_travail' => 'required|integer|min:1',
            'nombre_repartitions' => 'required|integer|min:1',
            'date_debut' => 'required|date',
            'repartitions' => 'required|array|min:1',
            'repartitions.*.periode' => 'required|string',
            'repartitions.*.heure_debut' => 'required',
            'repartitions.*.heure_fin' => 'required',
            'repartitions.*.nombre_jours' => 'required|integer|min:1',
        ]);

       $duree_cycle = (int) $request->duree_cycle;
    $jours_travail = (int) $request->nombre_jours_travail;

    if ($request->systeme === 'journalier') {
        $somme_jours_repartitions = collect($request->repartitions)->sum('nombre_jours');

        if ($somme_jours_repartitions != $jours_travail) {
            return redirect()->back()->withErrors([
                'repartitions' => "La somme des jours des répartitions ($somme_jours_repartitions) doit être égale au nombre de jours travaillés ($jours_travail) pour le système journalier."
            ])->withInput();
        }
    }

    if ($request->systeme === 'hebdomadaire') {
        foreach ($request->repartitions as $rep) {
            if ((int) $rep['nombre_jours'] !== $jours_travail) {
                return redirect()->back()->withErrors([
                    'repartitions' => "Chaque répartition (équipe) doit avoir exactement $jours_travail jours de travail pour le système hebdomadaire."
                ])->withInput();
            }
        }

        // Vérifie aussi que nombre_repartitions == nombre d'équipes (cohérent)
        if ((int)$request->nombre_repartitions !== count($request->repartitions)) {
            return redirect()->back()->withErrors([
                'nombre_repartitions' => "Le nombre de répartitions ne correspond pas au nombre d'équipes spécifiées."
            ])->withInput();
        }
    }

    // Déterminer la date de fin du cycle
    $date_debut_cycle = Carbon::parse($request->date_debut);

    switch ($request->systeme) {
        case 'journalier':
            $total_jours_cycle = $duree_cycle;
            $date_fin_cycle = $date_debut_cycle->copy()->addDays($duree_cycle);
            break;
        case 'hebdomadaire':
            $total_jours_cycle = $duree_cycle * 7;
            $date_fin_cycle = $date_debut_cycle->copy()->addWeeks($duree_cycle);
            break;
        case 'mensuel':
            $total_jours_cycle = $duree_cycle * 30; // Ou utilise CarbonPeriod pour être plus précis
            $date_fin_cycle = $date_debut_cycle->copy()->addMonths($duree_cycle);
        break;
        default:
        return redirect()->back()->withErrors(['systeme' => 'Type de système invalide.']);
    }

    $jours_repos = $total_jours_cycle - $jours_travail;

    if ($jours_repos < 0) {
        return redirect()->back()->withErrors([
            'nombre_jours_travail' => 'Le nombre de jours travaillés ne peut pas dépasser la durée totale du cycle.'
        ]);
    }
        // Mettre à jour la rotation
        $rotation = Rotation::findOrFail($id);
        $rotation->update([
            'nom' => $request->nom,
            'systeme' => $request->systeme,
            'duree_cycle' => $duree_cycle,
            'nombre_jours_travail' => $jours_travail,
            'nombre_repartitions' => $request->nombre_repartitions,
            'nombre_jours_repos' => $jours_repos,
            'date_debut' => $date_debut_cycle,
            'date_fin' => $date_fin_cycle,

        ]);

        // Mettre à jour les répartitions
        foreach ($request->repartitions as $index => $repartition) {
            $repartitionModel = Repartition::where('rotation_id', $rotation->id)->get()[$index];
            $repartitionModel->update([
                'periode' => $repartition['periode'],
                'heure_debut' => $repartition['heure_debut'],
                'heure_fin' => $repartition['heure_fin'],
                'nombre_jours' => $repartition['nombre_jours'],
            ]);
        }

        return redirect()->route('rotation.index')->with('success', 'Rotation mise à jour avec succès.');
    }


    public function destroy($id)
    {
        $rotation = Rotation::findOrFail($id);

        // Supprimer les relations dans rotation_equipe
        DB::table('rotation_equipe')->where('rotation_id', $id)->delete();

        // Supprimer les répartitions associées
        Repartition::where('rotation_id', $id)->delete();

        // Supprimer la rotation
        $rotation->delete();

        return redirect()->route('rotation.index')->with('success', 'Rotation supprimée avec succès.');
    }

    public function showForm()
    {
        $sites = Site::where(function ($query) {
            // Filtrer les sites qui n'ont pas d'horaires
            foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
                $query->whereNull("start_time_{$day}")
                    ->orWhereNull("end_time_{$day}")
                    ->orWhere("{$day}_checked", false);
            }
        })->get(); // Récupérer tous les sites
        $rotations = Rotation::all(); // Récupérer toutes les rotations

        // Passer les sites et rotations à la vue
        return view('rotations.assign_rotation', compact('sites', 'rotations'));
    }

    public function assignRotation(Request $request)
    {
        // Validation des données
        $validatedData = $request->validate([
            'rotation_id' => 'required|exists:rotations,id',
            'equipe_id' => 'required|array',
            'equipe_id.*' => 'exists:equipes,id'
        ]);

        // Récupérer la rotation
        $rotation = Rotation::findOrFail($validatedData['rotation_id']);

        // Assigner les équipes à la rotation (relation Many-to-Many)
        $rotation->equipes()->sync($validatedData['equipe_id']);

        return redirect()->back()->with('success', 'Rotation assignée avec succès.');
    }

    // public function liste_rotation_site()
    // {
    //     $sites = Site::with(['equipes.rotations'])->get();
    //     return view('rotations.list_assign_rotation', compact('sites'));
    // }

    public function liste_rotation_site()
    {
        $sites = SiteRotation::with(['equipes.rotations'])->get();

        // Vérifie la structure des données

        return view('rotations.list_assign_rotation', compact('sites'));
    }




public function genererCalendrier($siteId, $mois = null)
{
    $site = SiteRotation::with('equipes.users', 'rotations.repartitions')->findOrFail($siteId);
    $rotation = $site->rotations->first();

    if (!$rotation) {
        return back()->with('error', 'Aucune rotation trouvée pour ce site.');
    }

    $equipes = $site->equipes;
    $dureeCycle = $rotation->duree_cycle;
    $dateDebutRotation = Carbon::parse($rotation->date_debut);
    $systeme = $rotation->systeme;
    $dateActuelle = $mois ? Carbon::parse($mois) : Carbon::now();
    $debutMois = $dateActuelle->copy()->startOfMonth();
    $finMois = $dateActuelle->copy()->endOfMonth();

    if ($finMois->lt($dateDebutRotation->copy()->startOfMonth())) {
        return $this->genererCalendrierVide($site, $debutMois, $finMois);
    }

    $debutAffichage = $debutMois->lte($dateDebutRotation) && $finMois->gte($dateDebutRotation)
        ? $dateDebutRotation->copy()
        : $debutMois->copy();

    // =================================================================
    // DÉBUT DE LA MODIFICATION (SYSTÈME HEBDOMADAIRE UNIQUEMENT)
    // =================================================================
    if ($systeme === 'hebdomadaire') {
        // Tri des répartitions selon l'ordre sémantique (identique à pointage())
        $ordreNomsPeriodes = ['Matin', 'apres-midi', 'nuit'];
        $repartitions = $rotation->repartitions->sortBy(function ($rep) use ($ordreNomsPeriodes) {
            $index = array_search($rep->periode, $ordreNomsPeriodes);
            return $index === false ? PHP_INT_MAX : $index; // Périodes inconnues en dernier
        })->keyBy('id');
    } else {
        // Système journalier : comportement original (tri par ID)
        $repartitions = $rotation->repartitions->sortBy('id')->keyBy('id');
    }
    // =================================================================
    // FIN DE LA MODIFICATION
    // =================================================================

    $ordrePeriodes = $repartitions->pluck('id')->toArray();

    // Initialisation des variables spécifiques au système
    $nombreJoursRepos = null;
    $joursTravail = [];

    if ($systeme === 'hebdomadaire') {
        $nombreJoursTravail = $rotation->nombre_jours_travail;
        $joursTravail = range(1, $nombreJoursTravail);

        if (count($equipes) > count($ordrePeriodes)) {
            throw new \Exception("Nombre d'équipes supérieur aux répartitions disponibles pour la rotation hebdomadaire.");
        }
    } else {
        // Système journalier (inchangé)
        $nombreJoursRepos = $rotation->nombre_jours_repos ?? ($dureeCycle - $rotation->nombre_jours_travail);
        $ordrePeriodes[] = 'Repos';
    }

    // --- Initialisation de l'état des équipes ---
    $etatEquipes = [];
    foreach ($equipes as $index => $equipe) {
        if ($systeme === 'hebdomadaire') {
            $periodeIndex = $index % count($ordrePeriodes);
            $periodeId = $ordrePeriodes[$periodeIndex];

            $etatEquipes[$equipe->id] = [
                'periode_actuelle' => $periodeId,
                'equipe_nom' => $equipe->nom,
                'jours_travailles' => $joursTravail,
                'index_equipe' => $index,
                'periode_index' => $periodeIndex
            ];
        } else {
            $periodeIndex = $index % count($ordrePeriodes);
            $periodeId = $ordrePeriodes[$periodeIndex];
            $jours = ($periodeId === 'Repos')
                ? $nombreJoursRepos
                : $repartitions[$periodeId]->nombre_jours;

            $etatEquipes[$equipe->id] = [
                'periode_actuelle' => $periodeId,
                'jours_restants' => $jours,
                'equipe_nom' => $equipe->nom,
                'index_equipe' => $index
            ];
        }
    }

    // --- Avancement rapide de l'état des équipes ---
    $dateCouranteAvancement = $dateDebutRotation->copy();
    $finAvancement = $debutAffichage->copy()->subDay();

    while ($dateCouranteAvancement->lte($finAvancement)) {
        if ($systeme === 'hebdomadaire') {
            if ($dateCouranteAvancement->dayOfWeekIso === 1 && $dateCouranteAvancement->gt($dateDebutRotation->copy()->startOfWeek(Carbon::MONDAY))) {
                foreach ($etatEquipes as &$etat) {
                    $nouvelIndex = ($etat['periode_index'] + 1) % count($ordrePeriodes);
                    $etat['periode_actuelle'] = $ordrePeriodes[$nouvelIndex];
                    $etat['periode_index'] = $nouvelIndex;
                }
                unset($etat);
            }
        } else {
            foreach ($etatEquipes as &$etat) {
                $etat['jours_restants']--;
                if ($etat['jours_restants'] <= 0) {
                    $this->mettreAJourEtatEquipe($etat, $ordrePeriodes, $repartitions, $nombreJoursRepos);
                }
            }
            unset($etat);
        }
        $dateCouranteAvancement->addDay();
    }

    // --- Génération du calendrier ---
    $planning = [];
    $currentTime = Carbon::now();

    for ($date = $debutAffichage->copy(); $date->lte($finMois); $date->addDay()) {
        $dateStr = $date->format('Y-m-d');
        $planning[$dateStr] = [];
        $estAujourdhui = $date->isToday();
        $jourSemaine = $date->dayOfWeekIso;

        if ($systeme === 'hebdomadaire' && $jourSemaine === 1 && $date->gt($debutAffichage->copy()->startOfWeek(Carbon::MONDAY))) {
            foreach ($etatEquipes as &$etat) {
                $nouvelIndex = ($etat['periode_index'] + 1) % count($ordrePeriodes);
                $etat['periode_actuelle'] = $ordrePeriodes[$nouvelIndex];
                $etat['periode_index'] = $nouvelIndex;
            }
            unset($etat);
        }

        foreach ($etatEquipes as $equipeId => &$etat) {
            if ($systeme === 'hebdomadaire') {
                $periodeId = $etat['periode_actuelle'];
                $repartition = $repartitions[$periodeId];
                $estJourTravail = in_array($jourSemaine, $etat['jours_travailles']);

                if ($estJourTravail) {
                    $heureDebut = Carbon::parse($date->format('Y-m-d') . ' ' . $repartition->heure_debut);
                    $heureFin = Carbon::parse($date->format('Y-m-d') . ' ' . $repartition->heure_fin);
                    if ($heureFin <= $heureDebut) {
                        $heureFin->addDay();
                    }

                    $timeInfo = $this->calculerTempsRestant($currentTime, $heureDebut, $heureFin, $estAujourdhui, $date);
                    $planning[$dateStr][$etat['equipe_nom']] = [
                        'periode' => $repartition->periode,
                        'status' => $estAujourdhui
                            ? $this->determinerStatus($currentTime, $heureDebut, $heureFin)
                            : ($date->isPast() ? 'Terminé' : 'À venir'),
                        'heure_debut' => $repartition->heure_debut,
                        'heure_fin' => $repartition->heure_fin,
                        'raw_data' => [
                            'start' => $heureDebut->format('Y-m-d H:i:s'),
                            'end' => $heureFin->format('Y-m-d H:i:s')
                        ],
                        'time_info' => $timeInfo
                    ];
                } else {
                    $planning[$dateStr][$etat['equipe_nom']] = [
                        'periode' => 'Repos',
                        'status' => 'Repos',
                        'heure_debut' => null,
                        'heure_fin' => null,
                        'raw_data' => null,
                        'time_info' => 'Repos'
                    ];
                }
            } else {
                // Logique inchangée pour le système journalier
                $periodeId = $etat['periode_actuelle'];
                $repartition = $repartitions[$periodeId] ?? null;

                if (!$repartition) {
                    $planning[$dateStr][$etat['equipe_nom']] = [
                        'periode' => 'Repos',
                        'status' => 'Repos',
                        'heure_debut' => null,
                        'heure_fin' => null,
                        'raw_data' => null,
                        'time_info' => 'Repos'
                    ];
                } else {
                    $heureDebut = Carbon::parse($date->format('Y-m-d') . ' ' . $repartition->heure_debut);
                    $heureFin = Carbon::parse($date->format('Y-m-d') . ' ' . $repartition->heure_fin);
                    if ($heureFin <= $heureDebut) {
                        $heureFin->addDay();
                    }

                    $timeInfo = $this->calculerTempsRestant($currentTime, $heureDebut, $heureFin, $estAujourdhui, $date);
                    $planning[$dateStr][$etat['equipe_nom']] = [
                        'periode' => $repartition->periode,
                        'status' => $estAujourdhui
                            ? $this->determinerStatus($currentTime, $heureDebut, $heureFin)
                            : ($date->isPast() ? 'Terminé' : 'À venir'),
                        'heure_debut' => $repartition->heure_debut,
                        'heure_fin' => $repartition->heure_fin,
                        'raw_data' => [
                            'start' => $heureDebut->format('Y-m-d H:i:s'),
                            'end' => $heureFin->format('Y-m-d H:i:s')
                        ],
                        'time_info' => $timeInfo
                    ];
                }
                $etat['jours_restants']--;
                if ($etat['jours_restants'] <= 0) {
                    $this->mettreAJourEtatEquipe($etat, $ordrePeriodes, $repartitions, $nombreJoursRepos);
                }
            }
        }
        unset($etat);
    }

    return view('rotations.calendrier', [
        'planning' => $planning,
        'site' => $site,
        'debutMois' => $debutMois,
        'finMois' => $finMois,
        'currentTime' => $currentTime->format('Y-m-d H:i:s'),
        'showRotation' => true,
        'systeme' => $systeme
    ]);
}

    private function determinerStatus($currentTime, $heureDebut, $heureFin)
    {
        if ($currentTime < $heureDebut) {
            return 'À venir';
        } elseif ($currentTime >= $heureDebut && $currentTime <= $heureFin) {
            return 'En cours';
        } else {
            return 'Terminé';
        }
    }

    private function calculerTempsRestant($currentTime, $heureDebut, $heureFin, $estAujourdhui, $date)
    {
        // Si ce n'est pas aujourd'hui, le calcul du temps restant n'a pas de sens dynamique
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

    private function mettreAJourEtatEquipe(&$etat, $ordrePeriodes, $repartitions, $nombreJoursRepos)
    {
        $currentIndex = array_search($etat['periode_actuelle'], $ordrePeriodes);
        $nextIndex = ($currentIndex + 1) % count($ordrePeriodes);
        $nextPeriode = $ordrePeriodes[$nextIndex];
        $etat['periode_actuelle'] = $nextPeriode;
        $etat['jours_restants'] = ($nextPeriode === 'Repos')
            ? $nombreJoursRepos
            : $repartitions[$nextPeriode]->nombre_jours;
    }

    private function genererCalendrierVide($site, $debutMois, $finMois)
    {
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
            'showRotation' => false,
            'systeme' => 'N/A' // Pour un calendrier vide, le système n'est pas pertinent
        ]);
    }

}
