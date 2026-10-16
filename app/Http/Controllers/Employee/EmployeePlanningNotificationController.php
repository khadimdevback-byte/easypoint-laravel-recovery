<?php

namespace App\Http\Controllers\Employee;

use App\Models\Offre;
use App\Models\Candidature;
use Illuminate\Http\Request;
use App\Models\Disponibilite;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\UserDeactivationReason;

class EmployeePlanningNotificationController extends Controller
{
    function index(Request $request)
    {
        $disponibilite = $this->checkdisponibilite($request);

        //dd($disponibilite);

        $offres = [];

        if ($disponibilite) {
            $offres = $this->getOffres($request);
        }

        // dd($offres);

        return view('employee.planning_notification', [
            'disponibilite' => $disponibilite,
            'offres' => $offres,
        ]);
    }

    private function checkdisponibilite(Request $request)
    {
        $user = $request->user();

        if (!$user) {

            return null;
        }

        $disponibilite = Disponibilite::where('user_id', $user->id)->first();

        return $disponibilite;
    }

    public function store(Request $request)
    {
        $user = $request->user();

        // Vérification du contrat
        $contractCheck = $this->checkUserContract($request);

        // Si le contrat est inactif, on redirige avec un message d'erreur
        if ($contractCheck['contractStatus'] === 'inactive') {

            return redirect()->route('employee.planning_notification')
                ->with('error', $contractCheck['message']);
        }

        // Validation
        $request->validate([
            'disponibilite' => 'required|in:immediat,A_partir_de',
            'date_disponibilite' => 'required_if:disponibilite,A_partir_de|date'
        ], [
            'date_disponibilite.required_if' => 'La date de disponibilité est obligatoire pour une disponibilité à partir de.',
            'date_disponibilite.date' => 'La date de disponibilité doit être une date valide.',
        ]);

        // Vérifie si une disponibilité identique existe déjà
        $existingDisponibilite = Disponibilite::where('user_id', $user->id)
            ->where('disponibilite', $request->disponibilite)
            ->when($request->disponibilite === 'immediat', function ($query) {
                return $query->whereNull('date_disponibilite');
            })
            ->when($request->disponibilite === 'A_partir_de', function ($query) use ($request) {
                return $query->where('date_disponibilite', $request->date_disponibilite);
            })
            ->first();

        if ($existingDisponibilite) {

            return redirect()->route('employee.planning_notification')
                ->with('error', 'Vous avez déjà enregistré cette disponibilité.');
        }

        // Supprimer l'ancienne disponibilité si elle existe
        Disponibilite::where('user_id', $user->id)->delete();

        // Enregistrer la nouvelle
        $disponibilite = new Disponibilite();
        $disponibilite->user_id = $user->id;
        $disponibilite->company_id = $user->company_id;
        $disponibilite->disponibilite = $request->disponibilite;
        $disponibilite->date_disponibilite = $request->disponibilite === 'immediat' ? null : $request->date_disponibilite;
        $disponibilite->save();

        return redirect()->route('employee.planning_notification')
            ->with('success', 'Disponibilité mise à jour avec succès !');
    }


    public function checkUserContract(Request $request)
    {
        $user = $request->user();
        $now = now();

        $isAssignedToSite = DB::table('site_user')->where('user_id', $user->id)->exists();
        $deactivationReason = UserDeactivationReason::where('user_id', $user->id)->latest()->first();

        $offre = DB::table('offres')
            ->join('candidatures', 'offres.id', '=', 'candidatures.offre_id')
            ->where('candidatures.user_id', $user->id)
            ->select('offres.demarrage', 'offres.start_date')
            ->first();

        if ($offre && $offre->demarrage === 'A_partir_de' && $offre->start_date) {
            if ($now->lt($offre->start_date)) {
                return [
                    'contractStatus' => 'pending',
                    'contractStartDate' => $offre->start_date,
                    'message' => 'Le contrat est en attente de démarrage.'
                ];
            } elseif ($now->gte($offre->start_date)) {
                $user->status = 'active';
                $user->save();

                UserDeactivationReason::updateOrCreate(
                    ['user_id' => $user->id],
                    ['status' => 'active', 'reason' => 'Contrat activé']
                );

                return [
                    'contractStatus' => 'active',
                    'message' => 'Votre contrat est maintenant actif.'
                ];
            }
        }

        if (!$isAssignedToSite) {
            if (!$deactivationReason || $deactivationReason->status !== 'inactive') {
                UserDeactivationReason::create([
                    'user_id' => $user->id,
                    'status' => 'inactive',
                    'reason' => 'Fin de Contrat',
                ]);
            }

            return [
                'contractStatus' => 'inactive',
                'message' => 'L\'utilisateur n\'est pas lié à un site, contrat inactif.'
            ];
        }

        if ($deactivationReason && $deactivationReason->status === 'active' && $deactivationReason->reason !== 'Fin de Contrat') {
            return [
                'contractStatus' => 'active',
                'message' => 'Le contrat est actuellement actif.'
            ];
        }

        if ($deactivationReason && $deactivationReason->status === 'inactive' && $deactivationReason->reason === 'Fin de Contrat' && $user->status === 'active') {
            return [
                'contractStatus' => 'active',
                'message' => 'Le contrat est actuellement actif.'
            ];
        }

        return [
            'contractStatus' => 'active',
            'message' => 'Le contrat est actuellement actif.'
        ];
    }








    //Offres

    private function getOffres(Request $request)
    {
        $user = $request->user();

        // Vérifier si l'utilisateur a une fonction
        // if ($user->user_type === 'user' && !$user->employee_function) {
        //     return response()->json([
        //         'error' => 'Vous ne pouvez pas accéder aux offres sans une fonction attribuée.',
        //     ], 403);
        // }

        // Vérifier si l'utilisateur est désactivé pour "Fin de Contrat"
        // $deactivationReason = UserDeactivationReason::where('user_id', $user->id)->latest()->first();

        // if (!$deactivationReason || $deactivationReason->status !== 'inactive' || $deactivationReason->reason !== 'Fin de Contrat') {
        //     return response()->json([
        //         'error' => 'Vous ne pouvez pas accéder à cette ressource car votre contrat est en cours.',
        //     ], 403);
        // }

        // Récupérer la disponibilité de l'utilisateur depuis la table `disponibilites`
        $disponibilite = Disponibilite::where('user_id', $user->id)->latest()->first();

        // if (!$disponibilite) {
        //     return response()->json([
        //         'error' => 'Disponibilité non définie pour cet utilisateur.',
        //     ], 403);
        // }

        $userDisponibilite = $disponibilite->disponibilite; // Par exemple "A_partir_de" ou "immediat"
        $date_disponibilite = $disponibilite->date_disponibilite; // La date de disponibilité, si applicable

        // Récupérer les offres correspondant à la fonction et à la disponibilité de l'utilisateur
        $offres = Offre::join('sites', 'sites.id', '=', 'offres.site_id')
            ->join('fonctions', 'fonctions.id', '=', 'offres.poste_id')
            ->join('companies', 'companies.id', '=', 'offres.company_id')
            ->leftJoin('candidatures', function ($join) use ($user) {
                $join->on('offres.id', '=', 'candidatures.offre_id')
                    ->where('candidatures.user_id', '=', $user->id);
            })
            ->where('offres.company_id', $user->company_id)
            ->where('offres.poste_id', $user->employee_function) // Filtrer par fonction
            ->where(function ($query) use ($userDisponibilite, $date_disponibilite) {
                // Si l'utilisateur a une date de disponibilité spécifique (A_partir_de)
                if ($userDisponibilite === 'A_partir_de' && $date_disponibilite) {
                    $query->where('offres.demarrage', 'A_partir_de')
                        ->whereDate('offres.start_date', '>=', $date_disponibilite)
                        ->where('offres.demarrage', '!=', 'immediat'); // Exclure les offres immédiates
                } elseif ($userDisponibilite === 'immediat') {
                    // Si l'utilisateur est disponible immédiatement, il peut voir toutes les offres
                    $query->where(function ($q) {
                        $q->where('offres.demarrage', 'immediat')
                            ->orWhere('offres.demarrage', 'A_partir_de');
                    });
                }
            })
            ->where(function ($query) {
                $query->whereNull('candidatures.status')
                    ->orWhereNotIn('candidatures.status', ['declined']);
            })
            ->select(
                'offres.id',
                'sites.name',
                DB::raw('"" as horaire_travail'),
                'sites.monday_checked',
                'sites.start_time_monday',
                'sites.end_time_monday',
                'sites.tuesday_checked',
                'sites.start_time_tuesday',
                'sites.end_time_tuesday',
                'sites.wednesday_checked',
                'sites.start_time_wednesday',
                'sites.end_time_wednesday',
                'sites.thursday_checked',
                'sites.start_time_thursday',
                'sites.end_time_thursday',
                'sites.friday_checked',
                'sites.start_time_friday',
                'sites.end_time_friday',
                'sites.saturday_checked',
                'sites.start_time_saturday',
                'sites.end_time_saturday',
                'sites.sunday_checked',
                'sites.start_time_sunday',
                'sites.end_time_sunday',
                'sites.address',
                'offres.taux_horaire',
                'fonctions.nom_fonction',
                'offres.demarrage',
                'offres.start_date',
                'candidatures.status'
            )
            ->get()
            ->map(function ($offre) {
                $jours = [
                    'Lundi'    => ['checked' => $offre->monday_checked,    'start' => $offre->start_time_monday,    'end' => $offre->end_time_monday],
                    'Mardi'    => ['checked' => $offre->tuesday_checked,   'start' => $offre->start_time_tuesday,   'end' => $offre->end_time_tuesday],
                    'Mercredi' => ['checked' => $offre->wednesday_checked, 'start' => $offre->start_time_wednesday, 'end' => $offre->end_time_wednesday],
                    'Jeudi'    => ['checked' => $offre->thursday_checked,  'start' => $offre->start_time_thursday,  'end' => $offre->end_time_thursday],
                    'Vendredi' => ['checked' => $offre->friday_checked,    'start' => $offre->start_time_friday,    'end' => $offre->end_time_friday],
                    'Samedi'   => ['checked' => $offre->saturday_checked,  'start' => $offre->start_time_saturday,  'end' => $offre->end_time_saturday],
                    'Dimanche' => ['checked' => $offre->sunday_checked,    'start' => $offre->start_time_sunday,    'end' => $offre->end_time_sunday],
                ];

                $jours_travail = [];
                $last_start = null;
                $last_end = null;
                $range_start = null;
                $previous_day = null;

                foreach ($jours as $day => $info) {
                    if ($info['checked']) {
                        // Formatage des heures en HH:MM
                        $start_time = date('H:i', strtotime($info['start']));
                        $end_time = date('H:i', strtotime($info['end']));

                        if ($last_start === $start_time && $last_end === $end_time) {
                            $previous_day = $day;
                        } else {
                            if ($range_start) {
                                $jours_travail[] = ($range_start === $previous_day)
                                    ? "$range_start $last_start-$last_end"
                                    : "$range_start - $previous_day $last_start-$last_end";
                            }
                            $range_start = $day;
                            $last_start = $start_time;
                            $last_end = $end_time;
                            $previous_day = $day;
                        }
                    }
                }

                if ($range_start) {
                    $jours_travail[] = ($range_start === $previous_day)
                        ? "$range_start $last_start-$last_end"
                        : "$range_start - $previous_day $last_start-$last_end";
                }

                // Suppression des informations horaires inutiles
                unset($offre->monday_checked, $offre->start_time_monday, $offre->end_time_monday);
                unset($offre->tuesday_checked, $offre->start_time_tuesday, $offre->end_time_tuesday);
                unset($offre->wednesday_checked, $offre->start_time_wednesday, $offre->end_time_wednesday);
                unset($offre->thursday_checked, $offre->start_time_thursday, $offre->end_time_thursday);
                unset($offre->friday_checked, $offre->start_time_friday, $offre->end_time_friday);
                unset($offre->saturday_checked, $offre->start_time_saturday, $offre->end_time_saturday);
                unset($offre->sunday_checked, $offre->start_time_sunday, $offre->end_time_sunday);

                $offre->horaire_travail = implode(', ', $jours_travail);
                return $offre;
            });

        // if ($offres->isEmpty()) {
        //     return response()->json([
        //         'message' => 'Aucune offre disponible pour votre fonction.',
        //     ]);
        // }

        return $offres ?? [];
    }

    public function postuler(Request $request, $id)
    {
        $user = $request->user();

        // Vérifier si l'utilisateur a déjà postulé à une offre (quelle qu'elle soit)
        if (Candidature::where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'Vous avez déjà postulé à une offre et ne pouvez plus postuler à d\'autres.'
            ], 403);
        }

        // Vérifier si l'offre existe
        $offre = Offre::where('id', $id)->where('company_id', $user->company_id)->first();
        if (!$offre) {
            return response()->json(['message' => 'Offre introuvable.'], 404);
        }

        // Vérifier si l'employé appartient bien à la même compagnie que l'offre
        if (!$user->company || $user->company->id !== $offre->company_id) {
            return response()->json(['message' => 'Vous ne pouvez postuler qu’aux offres de votre compagnie.'], 403);
        }

        // Vérifier si l'utilisateur a déjà décliné cette offre
        $candidature = Candidature::where('offre_id', $offre->id)
            ->where('user_id', $user->id)
            ->where('status', 'declined')
            ->first();

        if ($candidature) {
            return response()->json(['message' => 'Vous avez déjà décliné cette offre et ne pouvez plus postuler.'], 403);
        }

        // Enregistrer la candidature
        Candidature::create([
            'user_id' => $user->id,
            'offre_id' => $id,
            'company_id' => $offre->company_id,
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Votre candidature a été envoyée avec succès.'], 201);
    }


    public function decliner(Request $request, $id)
    {
        $user = $request->user();

        // Vérification de l'authentification
        if (!$user) {
            return response()->json(['message' => 'Utilisateur non authentifié.'], 401);
        }

        if (!$user->company_id) {
            return response()->json(['message' => 'Company ID manquant.'], 400);
        }

        // Récupération de l'offre avec candidature de l'utilisateur
        $offre = Offre::where('id', $id)
            ->where('company_id', $user->company_id)
            ->with(['candidatures' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->first();

        if (!$offre) {
            return response()->json(['message' => 'Offre introuvable.'], 404);
        }

        $candidature = $offre->candidatures->first();

        if ($candidature && $candidature->status === 'declined') {
            return response()->json(['message' => 'Vous avez déjà refusé cette offre.'], 400);
        }

        // Validation du motif de rejet
        $request->validate([
            'motif_rejet' => 'required|string|max:255',
        ]);

        if ($candidature) {
            // Mise à jour de la candidature existante
            $candidature->update([
                'status' => 'declined',
                'motif_rejet' => $request->motif_rejet,
            ]);
        } else {
            // Création d'une nouvelle candidature
            Candidature::create([
                'offre_id' => $offre->id,
                'user_id' => $user->id,
                'company_id' => $offre->company_id,
                'status' => 'declined',
                'motif_rejet' => $request->motif_rejet,
            ]);
        }

        return response()->json(['message' => 'Offre déclinée avec succès.'], 200);
    }


    public function show(Request $request, $id)
    {
        $user = $request->user();
        $offre = Offre::join('sites', 'sites.id', '=', 'offres.site_id')
            ->join('fonctions', 'fonctions.id', '=', 'offres.poste_id')
            ->join('companies', 'companies.id', '=', 'offres.company_id')
            ->where('offres.company_id', $user->company_id)
            ->where('offres.id', $id) // Filtrer par l'ID de l'offre
            ->select(
                'offres.id',
                'offres.demarrage',
                'offres.start_date',
                'sites.name',

                'sites.monday_checked',
                'sites.start_time_monday',
                'sites.end_time_monday',

                'sites.tuesday_checked',
                'sites.start_time_tuesday',
                'sites.end_time_tuesday',

                'sites.wednesday_checked',
                'sites.start_time_wednesday',
                'sites.end_time_wednesday',

                'sites.thursday_checked',
                'sites.start_time_thursday',
                'sites.end_time_thursday',

                'sites.friday_checked',
                'sites.start_time_friday',
                'sites.end_time_friday',

                'sites.saturday_checked',
                'sites.start_time_saturday',
                'sites.end_time_saturday',

                'sites.sunday_checked',
                'sites.start_time_sunday',
                'sites.end_time_sunday',
                'sites.address',
                'offres.taux_horaire',
                'offres.description_offre'
            ) // Ajout de description si nécessaire
            ->first();
        if (!$offre) {
            return response()->json(['message' => 'Offre non trouvée'], 404);
        }
        // Convertir en tableau
        $offreArray = $offre->toArray();

        // Ajouter l'horaire regroupé
        $horaire = $this->formatSchedule($offreArray);
        unset(
            $offreArray['monday_checked'],
            $offreArray['start_time_monday'],
            $offreArray['end_time_monday'],
            $offreArray['tuesday_checked'],
            $offreArray['start_time_tuesday'],
            $offreArray['end_time_tuesday'],
            $offreArray['wednesday_checked'],
            $offreArray['start_time_wednesday'],
            $offreArray['end_time_wednesday'],
            $offreArray['thursday_checked'],
            $offreArray['start_time_thursday'],
            $offreArray['end_time_thursday'],
            $offreArray['friday_checked'],
            $offreArray['start_time_friday'],
            $offreArray['end_time_friday'],
            $offreArray['saturday_checked'],
            $offreArray['start_time_saturday'],
            $offreArray['end_time_saturday'],
            $offreArray['sunday_checked'],
            $offreArray['start_time_sunday'],
            $offreArray['end_time_sunday']
        );
        $offreArray = array_merge(
            array_slice($offreArray, 0, 3), // "id" et "demarrage"
            ['horaire' => $horaire], // Insérer "horaire"
            array_slice($offreArray, 2) // Le reste des données
        );

        return view('employee.offre_show', [

            'offre' => $offreArray,
        ]);
    }

    private function formatSchedule($offre)
    {
        $days = [
            'monday' => 'Lundi',
            'tuesday' => 'Mardi',
            'wednesday' => 'Mercredi',
            'thursday' => 'Jeudi',
            'friday' => 'Vendredi',
            'saturday' => 'Samedi',
            'sunday' => 'Dimanche'
        ];

        $grouped = [];
        $currentGroup = null;

        foreach ($days as $key => $dayName) {
            if ($offre["{$key}_checked"]) {
                $start = $offre["start_time_{$key}"];
                $end = $offre["end_time_{$key}"];
                $timeRange = "$start - $end";

                if ($currentGroup && $currentGroup['time'] === $timeRange) {
                    // Continue le groupe
                    $currentGroup['days'][] = $dayName;
                } else {
                    // Sauvegarde l'ancien groupe et commence un nouveau
                    if ($currentGroup) {
                        $grouped[] = $this->formatGroup($currentGroup);
                    }
                    $currentGroup = ['days' => [$dayName], 'time' => $timeRange];
                }
            }
        }

        // Ajouter le dernier groupe
        if ($currentGroup) {
            $grouped[] = $this->formatGroup($currentGroup);
        }

        return implode(', ', $grouped);
    }

    private function formatGroup($group)
    {
        $days = $group['days'];
        $time = $group['time'];

        if (count($days) > 1) {
            return "{$days[0]} - " . end($days) . " $time";
        }

        return "{$days[0]} $time";
    }
}
