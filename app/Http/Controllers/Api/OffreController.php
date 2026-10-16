<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Disponibilite;
use App\Models\Offre;
use App\Models\UserDeactivationReason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class OffreController extends Controller
{

    // public function index(Request $request)
    // {
    //     $user = $request->user();
    //     // Vérifier si l'utilisateur est un 'user' et s'il a une fonction
    //     if ($user->user_type === 'user' && !$user->employee_function) {
    //         return response()->json([
    //             'error' => 'Vous ne pouvez pas accéder aux offres sans une fonction attribuée.',
    //         ], 403);
    //     }

    //     // Vérifiez l'état de désactivation de l'utilisateur
    //     $deactivationReason = UserDeactivationReason::where('user_id', $user->id)->latest()->first();

    //     // Vérifiez si l'utilisateur est désactivé avec un motif de "Fin de Contrat"
    //     if ($deactivationReason && $deactivationReason->status === 'inactive' && $deactivationReason->reason === 'Fin de Contrat') {
    //         // Récupérer la liste des offres pour cet utilisateur
    //         $offres = Offre::join('sites', 'sites.id', '=', 'offres.site_id')
    //             ->join('fonctions', 'fonctions.id', '=', 'offres.poste_id')
    //             ->join('companies', 'companies.id', '=', 'offres.company_id')
    //             ->leftJoin('candidatures', function ($join) use ($user) {
    //                 $join->on('offres.id', '=', 'candidatures.offre_id')
    //                     ->where('candidatures.user_id', '=', $user->id);
    //             })
    //             ->where('offres.company_id', $user->company_id)
    //             ->where(function ($query) {
    //                 $query->whereNull('candidatures.status')
    //                     ->orWhereNotIn('candidatures.status', ['declined']);
    //             })
    //             ->select(
    //                 'offres.id',
    //                 'sites.name',
    //                 DB::raw('"" as horaire_travail'),
    //                 'sites.monday_checked', 'sites.start_time_monday', 'sites.end_time_monday',
    //                 'sites.tuesday_checked', 'sites.start_time_tuesday', 'sites.end_time_tuesday',
    //                 'sites.wednesday_checked', 'sites.start_time_wednesday', 'sites.end_time_wednesday',
    //                 'sites.thursday_checked', 'sites.start_time_thursday', 'sites.end_time_thursday',
    //                 'sites.friday_checked', 'sites.start_time_friday', 'sites.end_time_friday',
    //                 'sites.saturday_checked', 'sites.start_time_saturday', 'sites.end_time_saturday',
    //                 'sites.sunday_checked', 'sites.start_time_sunday', 'sites.end_time_sunday',
    //                 'sites.address',
    //                 'offres.taux_horaire',
    //                 'fonctions.nom_fonction',
    //                 'offres.demarrage',
    //                 'offres.start_date'
    //             )
    //             ->get()
    //             ->map(function ($offre) {
    //                 $jours = [
    //                     'Lundi'    => ['checked' => $offre->monday_checked,    'start' => $offre->start_time_monday,    'end' => $offre->end_time_monday],
    //                     'Mardi'    => ['checked' => $offre->tuesday_checked,   'start' => $offre->start_time_tuesday,   'end' => $offre->end_time_tuesday],
    //                     'Mercredi' => ['checked' => $offre->wednesday_checked, 'start' => $offre->start_time_wednesday, 'end' => $offre->end_time_wednesday],
    //                     'Jeudi'    => ['checked' => $offre->thursday_checked,  'start' => $offre->start_time_thursday,  'end' => $offre->end_time_thursday],
    //                     'Vendredi' => ['checked' => $offre->friday_checked,    'start' => $offre->start_time_friday,    'end' => $offre->end_time_friday],
    //                     'Samedi'   => ['checked' => $offre->saturday_checked,  'start' => $offre->start_time_saturday,  'end' => $offre->end_time_saturday],
    //                     'Dimanche' => ['checked' => $offre->sunday_checked,    'start' => $offre->start_time_sunday,    'end' => $offre->end_time_sunday],
    //                 ];

    //                 $jours_travail = [];
    //                 $last_start = null;
    //                 $last_end = null;
    //                 $range_start = null;
    //                 $previous_day = null;

    //                 foreach ($jours as $day => $info) {
    //                     if ($info['checked']) {
    //                         // Formatage des heures en HH:MM
    //                         $start_time = date('H:i', strtotime($info['start']));
    //                         $end_time = date('H:i', strtotime($info['end']));

    //                         if ($last_start === $start_time && $last_end === $end_time) {
    //                             $previous_day = $day;
    //                         } else {
    //                             if ($range_start) {
    //                                 $jours_travail[] = ($range_start === $previous_day)
    //                                     ? "$range_start $last_start-$last_end"
    //                                     : "$range_start - $previous_day $last_start-$last_end";
    //                             }
    //                             $range_start = $day;
    //                             $last_start = $start_time;
    //                             $last_end = $end_time;
    //                             $previous_day = $day;
    //                         }
    //                     }
    //                 }

    //                 if ($range_start) {
    //                     $jours_travail[] = ($range_start === $previous_day)
    //                         ? "$range_start $last_start-$last_end"
    //                         : "$range_start - $previous_day $last_start-$last_end";
    //                 }

    //                 // Suppression des informations horaires inutiles
    //                 unset($offre->monday_checked, $offre->start_time_monday, $offre->end_time_monday);
    //                 unset($offre->tuesday_checked, $offre->start_time_tuesday, $offre->end_time_tuesday);
    //                 unset($offre->wednesday_checked, $offre->start_time_wednesday, $offre->end_time_wednesday);
    //                 unset($offre->thursday_checked, $offre->start_time_thursday, $offre->end_time_thursday);
    //                 unset($offre->friday_checked, $offre->start_time_friday, $offre->end_time_friday);
    //                 unset($offre->saturday_checked, $offre->start_time_saturday, $offre->end_time_saturday);
    //                 unset($offre->sunday_checked, $offre->start_time_sunday, $offre->end_time_sunday);

    //                 $offre->horaire_travail = implode(', ', $jours_travail);

    //                 return $offre;
    //             });

    //         return response()->json($offres);
    //     } else {
    //         // Retourner un message indiquant que le contrat est en cours
    //         return response()->json([
    //             'error' => 'Vous ne pouvez pas accéder à cette ressource car votre contrat est en cours.',
    //         ], 403); // Code d'état HTTP 403 Forbidden
    //     }
    // }

    // public function index(Request $request)
    // {
    //     $user = $request->user();
    //     // Vérifier si l'utilisateur a une fonction
    //     if ($user->user_type === 'user' && !$user->employee_function) {
    //         return response()->json([
    //             'error' => 'Vous ne pouvez pas accéder aux offres sans une fonction attribuée.',
    //         ], 403);
    //     }

    //     // Vérifier si l'utilisateur est désactivé pour "Fin de Contrat"
    //     $deactivationReason = UserDeactivationReason::where('user_id', $user->id)->latest()->first();

    //     if (!$deactivationReason || $deactivationReason->status !== 'inactive' || $deactivationReason->reason !== 'Fin de Contrat') {
    //         return response()->json([
    //             'error' => 'Vous ne pouvez pas accéder à cette ressource car votre contrat est en cours.',
    //         ], 403);
    //     }

    //     // Récupérer la disponibilité de l'utilisateur depuis la table `disponibilites`
    //     $disponibilite = Disponibilite::where('user_id', $user->id)->latest()->first();

    //     if (!$disponibilite) {
    //         return response()->json([
    //             'error' => 'Disponibilité non définie pour cet utilisateur.',
    //         ], 403);
    //     }

    //     $userDisponibilite = $disponibilite->disponibilite; // Par exemple "A_partir_de" ou "immediat"
    //     $date_disponibilite = $disponibilite->date_disponibilite; // La date de disponibilité, si applicable

    //     // Récupérer les offres correspondant à la fonction et à la disponibilité de l'utilisateur
    //     $offres = Offre::join('sites', 'sites.id', '=', 'offres.site_id')
    //         ->join('fonctions', 'fonctions.id', '=', 'offres.poste_id')
    //         ->join('companies', 'companies.id', '=', 'offres.company_id')
    //         ->leftJoin('candidatures', function ($join) use ($user) {
    //             $join->on('offres.id', '=', 'candidatures.offre_id')
    //                 ->where('candidatures.user_id', '=', $user->id);
    //         })
    //         ->where('offres.company_id', $user->company_id)
    //         ->where('offres.poste_id', $user->employee_function) // Filtrer par fonction
    //         ->where(function ($query) use ($userDisponibilite, $date_disponibilite) {
    //             // Si l'utilisateur a une date de disponibilité spécifique (A_partir_de)
    //             if ($userDisponibilite === 'A_partir_de' && $date_disponibilite) {
    //                 $query->where('offres.demarrage', 'A_partir_de')
    //                       ->whereDate('offres.start_date', '>=', $date_disponibilite)
    //                       ->where('offres.demarrage', '!=', 'immediat'); // Exclure les offres immédiates
    //             } elseif ($userDisponibilite === 'immediat') {
    //                 // Si l'utilisateur est disponible immédiatement, il peut voir toutes les offres
    //                 $query->where(function ($q) {
    //                     $q->where('offres.demarrage', 'immediat')
    //                       ->orWhere('offres.demarrage', 'A_partir_de');
    //                 });
    //             }
    //         })
    //         ->where(function ($query) {
    //             $query->whereNull('candidatures.status')
    //                 ->orWhereNotIn('candidatures.status', ['declined']);
    //         })
    //         ->select(
    //             'offres.id',
    //             'sites.name',
    //             DB::raw('"" as horaire_travail'),
    //             'sites.monday_checked', 'sites.start_time_monday', 'sites.end_time_monday',
    //             'sites.tuesday_checked', 'sites.start_time_tuesday', 'sites.end_time_tuesday',
    //             'sites.wednesday_checked', 'sites.start_time_wednesday', 'sites.end_time_wednesday',
    //             'sites.thursday_checked', 'sites.start_time_thursday', 'sites.end_time_thursday',
    //             'sites.friday_checked', 'sites.start_time_friday', 'sites.end_time_friday',
    //             'sites.saturday_checked', 'sites.start_time_saturday', 'sites.end_time_saturday',
    //             'sites.sunday_checked', 'sites.start_time_sunday', 'sites.end_time_sunday',
    //             'sites.address',
    //             'offres.taux_horaire',
    //             'fonctions.nom_fonction',
    //             'offres.demarrage',
    //             'offres.start_date',
    //             'candidatures.status'
    //         )
    //         ->get()
    //         ->map(function ($offre) {
    //             $jours = [
    //                 'Lundi'    => ['checked' => $offre->monday_checked,    'start' => $offre->start_time_monday,    'end' => $offre->end_time_monday],
    //                 'Mardi'    => ['checked' => $offre->tuesday_checked,   'start' => $offre->start_time_tuesday,   'end' => $offre->end_time_tuesday],
    //                 'Mercredi' => ['checked' => $offre->wednesday_checked, 'start' => $offre->start_time_wednesday, 'end' => $offre->end_time_wednesday],
    //                 'Jeudi'    => ['checked' => $offre->thursday_checked,  'start' => $offre->start_time_thursday,  'end' => $offre->end_time_thursday],
    //                 'Vendredi' => ['checked' => $offre->friday_checked,    'start' => $offre->start_time_friday,    'end' => $offre->end_time_friday],
    //                 'Samedi'   => ['checked' => $offre->saturday_checked,  'start' => $offre->start_time_saturday,  'end' => $offre->end_time_saturday],
    //                 'Dimanche' => ['checked' => $offre->sunday_checked,    'start' => $offre->start_time_sunday,    'end' => $offre->end_time_sunday],
    //             ];

    //             $jours_travail = [];
    //             $last_start = null;
    //             $last_end = null;
    //             $range_start = null;
    //             $previous_day = null;

    //             foreach ($jours as $day => $info) {
    //                 if ($info['checked']) {
    //                     // Formatage des heures en HH:MM
    //                     $start_time = date('H:i', strtotime($info['start']));
    //                     $end_time = date('H:i', strtotime($info['end']));

    //                     if ($last_start === $start_time && $last_end === $end_time) {
    //                         $previous_day = $day;
    //                     } else {
    //                         if ($range_start) {
    //                             $jours_travail[] = ($range_start === $previous_day)
    //                                 ? "$range_start $last_start-$last_end"
    //                                 : "$range_start - $previous_day $last_start-$last_end";
    //                         }
    //                         $range_start = $day;
    //                         $last_start = $start_time;
    //                         $last_end = $end_time;
    //                         $previous_day = $day;
    //                     }
    //                 }
    //             }
    //             if ($range_start) {
    //                 $jours_travail[] = ($range_start === $previous_day)
    //                     ? "$range_start $last_start-$last_end"
    //                     : "$range_start - $previous_day $last_start-$last_end";
    //             }
    //             // Suppression des informations horaires inutiles
    //             unset($offre->monday_checked, $offre->start_time_monday, $offre->end_time_monday);
    //             unset($offre->tuesday_checked, $offre->start_time_tuesday, $offre->end_time_tuesday);
    //             unset($offre->wednesday_checked, $offre->start_time_wednesday, $offre->end_time_wednesday);
    //             unset($offre->thursday_checked, $offre->start_time_thursday, $offre->end_time_thursday);
    //             unset($offre->friday_checked, $offre->start_time_friday, $offre->end_time_friday);
    //             unset($offre->saturday_checked, $offre->start_time_saturday, $offre->end_time_saturday);
    //             unset($offre->sunday_checked, $offre->start_time_sunday, $offre->end_time_sunday);
    //             $offre->horaire_travail = implode(', ', $jours_travail);
    //             return $offre;
    //         });

    //     if ($offres->isEmpty()) {
    //         return response()->json([
    //             'message' => 'Aucune offre disponible pour votre fonction.',
    //         ]);
    //     }

    //     return response()->json($offres);
    // }

    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->user_type === 'user' && !$user->employee_function) {
            return response()->json([
                'error' => 'Vous ne pouvez pas accéder aux offres sans une fonction attribuée.',
            ], 403);
        }

        $deactivationReason = UserDeactivationReason::where('user_id', $user->id)->latest()->first();

        if (!$deactivationReason || $deactivationReason->status !== 'inactive' || $deactivationReason->reason !== 'Fin de Contrat') {
            return response()->json([
                'error' => 'Vous ne pouvez pas accéder à cette ressource car votre contrat est en cours.',
            ], 403);
        }

        $disponibilite = Disponibilite::where('user_id', $user->id)->latest()->first();

        if (!$disponibilite) {
            return response()->json([
                'error' => 'Disponibilité non définie pour cet utilisateur.',
            ], 403);
        }

        $userDisponibilite = $disponibilite->disponibilite;
        $date_disponibilite = $disponibilite->date_disponibilite;

        $offres = Offre::leftJoin('sites', 'sites.id', '=', 'offres.site_id')
            ->leftJoin('site_rotation', 'site_rotation.id', '=', 'offres.site_rotation_id')
            ->join('fonctions', 'fonctions.id', '=', 'offres.poste_id')
            ->join('companies', 'companies.id', '=', 'offres.company_id')
            ->leftJoin('candidatures', function ($join) use ($user) {
                $join->on('offres.id', '=', 'candidatures.offre_id')
                    ->where('candidatures.user_id', '=', $user->id);
            })
            ->where('offres.company_id', $user->company_id)
            ->where('offres.poste_id', $user->employee_function)
            ->where('offres.type_horaire', $user->type_horaire)
            ->where(function ($query) use ($userDisponibilite, $date_disponibilite) {
                if ($userDisponibilite === 'A_partir_de' && $date_disponibilite) {
                    $query->where('offres.demarrage', 'A_partir_de')
                        ->whereDate('offres.start_date', '>=', $date_disponibilite)
                        ->where('offres.demarrage', '!=', 'immediat');
                } elseif ($userDisponibilite === 'immediat') {
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
            ->select([
                'offres.id',
                'offres.taux_horaire',
                'offres.type_horaire',
                'offres.demarrage',
                'offres.start_date',
                'offres.site_rotation_id',
                'offres.site_id',
                'fonctions.nom_fonction',
                // Sites fixes
                'sites.name as site_name',
                'sites.address as site_address',
                'sites.monday_checked', 'sites.start_time_monday', 'sites.end_time_monday',
                'sites.tuesday_checked', 'sites.start_time_tuesday', 'sites.end_time_tuesday',
                'sites.wednesday_checked', 'sites.start_time_wednesday', 'sites.end_time_wednesday',
                'sites.thursday_checked', 'sites.start_time_thursday', 'sites.end_time_thursday',
                'sites.friday_checked', 'sites.start_time_friday', 'sites.end_time_friday',
                'sites.saturday_checked', 'sites.start_time_saturday', 'sites.end_time_saturday',
                'sites.sunday_checked', 'sites.start_time_sunday', 'sites.end_time_sunday',
                // Rotations
                'site_rotation.name as site_rotation_name',
                'site_rotation.address as site_rotation_address',
                // Pour candidature existante éventuelle
                'candidatures.status as candidature_status',
                // Placeholder pour horaire_travail
                DB::raw('"" as horaire_travail')
            ])
            ->get()

            ->map(function ($offre) {
            if ($offre->type_horaire === 'rotation' && $offre->site_rotation_id) {
                $rotation = DB::table('rotation_site')
                    ->join('rotations', 'rotations.id', '=', 'rotation_site.rotation_id')
                    ->where('rotation_site.site_id', $offre->site_rotation_id)
                    ->select('rotations.id as rotation_id', 'rotations.nom', 'rotations.duree_cycle')
                    ->first();

                if ($rotation) {
                    $repartitions = DB::table('repartitions')
                        ->where('rotation_id', $rotation->rotation_id)
                        ->get();

                    $periodes = [];
                    $somme_jours = 0;

                    foreach ($repartitions as $rep) {
                        $somme_jours += $rep->nombre_jours ?? 0;
                        $periodes[] = ucfirst($rep->periode) . ' : ' . date('H:i', strtotime($rep->heure_debut)) .
                                    ' - ' . date('H:i', strtotime($rep->heure_fin)) . ' (' . $rep->nombre_jours . ' jours)';
                    }

                    $jours_repos = $rotation->duree_cycle - $somme_jours;
                    if ($jours_repos > 0) {
                        $periodes[] = "Repos : $jours_repos jours";
                    }

                    $offre->horaire_travail = "Rotation : Cycle de {$rotation->duree_cycle} jours\n" . implode("\n", $periodes);
                } else {
                    $offre->horaire_travail = 'Rotation non définie pour ce site';
                }

                // On peut aussi renommer pour simplifier le front (optionnel)
               $offre->site_nom_affiche = $offre->type_horaire === 'rotation'
                    ? $offre->site_rotation_name
                    : $offre->site_name;

                $offre->site_adresse_affiche = $offre->type_horaire === 'rotation'
                    ? $offre->site_rotation_address
                    : $offre->site_address;

                    // Supprime les anciennes propriétés pour éviter la confusion
                unset($offre->site_name, $offre->site_address);
                unset($offre->site_rotation_name, $offre->site_rotation_address);
            } else {
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

                // Supprimer les colonnes inutiles
                unset($offre->monday_checked, $offre->start_time_monday, $offre->end_time_monday);
                unset($offre->tuesday_checked, $offre->start_time_tuesday, $offre->end_time_tuesday);
                unset($offre->wednesday_checked, $offre->start_time_wednesday, $offre->end_time_wednesday);
                unset($offre->thursday_checked, $offre->start_time_thursday, $offre->end_time_thursday);
                unset($offre->friday_checked, $offre->start_time_friday, $offre->end_time_friday);
                unset($offre->saturday_checked, $offre->start_time_saturday, $offre->end_time_saturday);
                unset($offre->sunday_checked, $offre->start_time_sunday, $offre->end_time_sunday);

                $offre->horaire_travail = implode(', ', $jours_travail);
            }

            return $offre;
        });


        if ($offres->isEmpty()) {
            return response()->json([
                'message' => 'Aucune offre disponible pour votre fonction.',
            ]);
        }

        return response()->json($offres);
    }

    // public function index(Request $request)
    // {
    //     $user = $request->user();

    //     // Vérifier si l'utilisateur a une fonction
    //     if ($user->user_type === 'user' && !$user->employee_function) {
    //         return response()->json([
    //             'error' => 'Vous ne pouvez pas accéder aux offres sans une fonction attribuée.',
    //         ], 403);
    //     }

    //     // Récupérer les sites liés à l'utilisateur via la table pivot site_user
    //     $userSites = $user->sites()->pluck('sites.id')->toArray();

    //     // Si l'utilisateur est actif et lié à un site, il est en cours de contrat
    //     if (!empty($userSites) && $user->status === 'active') {
    //         return response()->json([
    //             'message' => 'Vous êtes en cours de contrat.',
    //         ], 403);
    //     }

    //     // Vérifier si l'utilisateur a une raison de désactivation
    //     $deactivationReason = UserDeactivationReason::where('user_id', $user->id)->latest()->first();

    //     // Si l'utilisateur est inactif avec "Fin de Contrat", on le détache de tous ses sites
    //     if ($deactivationReason && $deactivationReason->status === 'inactive' && $deactivationReason->reason === 'Fin de Contrat') {
    //         $user->sites()->detach(); // Détacher tous les sites
    //     }

    //     // Vérifier si l'utilisateur a encore un site après détachement
    //     $userSites = $user->sites()->pluck('sites.id')->toArray();

    //     // S'il a encore un site, il a toujours un contrat en cours
    //     if (!empty($userSites)) {
    //         return response()->json([
    //             'message' => 'Vous avez encore un contrat en cours.',
    //         ], 403);
    //     }

    //     // À ce stade, l'utilisateur est détaché de tout site → il peut voir les offres

    //     // Vérifier la disponibilité de l'utilisateur
    //     $disponibilite = Disponibilite::where('user_id', $user->id)->latest()->first();
    //     if (!$disponibilite) {
    //         return response()->json([
    //             'error' => 'Disponibilité non définie pour cet utilisateur.',
    //         ], 403);
    //     }

    //     $userDisponibilite = $disponibilite->disponibilite;
    //     $date_disponibilite = $disponibilite->date_disponibilite;

    //     // Récupérer les offres correspondant à la fonction de l'utilisateur
    //     $offresQuery = Offre::join('fonctions', 'fonctions.id', '=', 'offres.poste_id')
    //         ->join('companies', 'companies.id', '=', 'offres.company_id')
    //         ->leftJoin('candidatures', function ($join) use ($user) {
    //             $join->on('offres.id', '=', 'candidatures.offre_id')
    //                 ->where('candidatures.user_id', '=', $user->id);
    //         })
    //         ->where('offres.company_id', $user->company_id)
    //         ->where('offres.poste_id', $user->employee_function) // Filtrer par fonction
    //         ->where(function ($query) use ($userDisponibilite, $date_disponibilite) {
    //             if ($userDisponibilite === 'A_partir_de' && $date_disponibilite) {
    //                 $query->where('offres.demarrage', 'A_partir_de')
    //                     ->whereDate('offres.start_date', '>=', $date_disponibilite)
    //                     ->where('offres.demarrage', '!=', 'immediat');
    //             } elseif ($userDisponibilite === 'immediat') {
    //                 $query->where(function ($q) {
    //                     $q->where('offres.demarrage', 'immediat')
    //                         ->orWhere('offres.demarrage', 'A_partir_de');
    //                 });
    //             }
    //         })
    //         ->where(function ($query) {
    //             $query->whereNull('candidatures.status')
    //                 ->orWhereNotIn('candidatures.status', ['declined']);
    //         });

    //     // Sélectionner les offres
    //     $offres = $offresQuery->select(
    //         'offres.id',
    //         DB::raw('COALESCE(sites.name, "Aucun site associé") as site_name'),
    //         DB::raw('"" as horaire_travail'),
    //         'offres.taux_horaire',
    //         'fonctions.nom_fonction',
    //         'offres.demarrage',
    //         'offres.start_date'
    //     )
    //     ->leftJoin('sites', 'sites.id', '=', 'offres.site_id')
    //     ->get();

    //     if ($offres->isEmpty()) {
    //         return response()->json([
    //             'message' => 'Aucune offre disponible pour votre fonction.',
    //         ]);
    //     }

    //     return response()->json($offres);
    // }









    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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


    // public function show(Request $request ,$id){
    //     $user = $request->user();
    //     $offre = Offre::join('sites', 'sites.id', '=', 'offres.site_id')
    //         ->join('fonctions', 'fonctions.id', '=', 'offres.poste_id')
    //         ->join('companies', 'companies.id', '=', 'offres.company_id')
    //         ->where('offres.company_id', $user->company_id)
    //         ->where('offres.id', $id) // Filtrer par l'ID de l'offre
    //         ->select(
    //             'offres.id',
    //             'offres.demarrage',
    //             'offres.start_date',
    //             'sites.name',
    //             'sites.monday_checked',
    //             'sites.start_time_monday',
    //             'sites.end_time_monday',

    //             'sites.tuesday_checked',
    //             'sites.start_time_tuesday',
    //             'sites.end_time_tuesday',

    //             'sites.wednesday_checked',
    //             'sites.start_time_wednesday',
    //             'sites.end_time_wednesday',

    //             'sites.thursday_checked',
    //             'sites.start_time_thursday',
    //             'sites.end_time_thursday',

    //             'sites.friday_checked',
    //             'sites.start_time_friday',
    //             'sites.end_time_friday',

    //             'sites.saturday_checked',
    //             'sites.start_time_saturday',
    //             'sites.end_time_saturday',

    //             'sites.sunday_checked',
    //             'sites.start_time_sunday',
    //             'sites.end_time_sunday',
    //             'sites.address',
    //             'offres.taux_horaire',
    //             'offres.description_offre'
    //             ) // Ajout de description si nécessaire
    //         ->first();
    //         if (!$offre) {
    //             return response()->json(['message' => 'Offre non trouvée'], 404);
    //         }
    //         // Convertir en tableau
    //         $offreArray = $offre->toArray();

    //         // Ajouter l'horaire regroupé
    //         $horaire = $this->formatSchedule($offreArray);
    //         unset(
    //             $offreArray['monday_checked'], $offreArray['start_time_monday'], $offreArray['end_time_monday'],
    //             $offreArray['tuesday_checked'], $offreArray['start_time_tuesday'], $offreArray['end_time_tuesday'],
    //             $offreArray['wednesday_checked'], $offreArray['start_time_wednesday'], $offreArray['end_time_wednesday'],
    //             $offreArray['thursday_checked'], $offreArray['start_time_thursday'], $offreArray['end_time_thursday'],
    //             $offreArray['friday_checked'], $offreArray['start_time_friday'], $offreArray['end_time_friday'],
    //             $offreArray['saturday_checked'], $offreArray['start_time_saturday'], $offreArray['end_time_saturday'],
    //             $offreArray['sunday_checked'], $offreArray['start_time_sunday'], $offreArray['end_time_sunday']
    //         );
    //         $offreArray = array_merge(
    //             array_slice($offreArray, 0, 3), // "id" et "demarrage"
    //             ['horaire' => $horaire], // Insérer "horaire"
    //             array_slice($offreArray, 2) // Le reste des données
    //         );
    //     return response()->json($offreArray);
    // }


    /**
     * Update the specified resource in storage.
     */


    public function show(Request $request, $id)
{
    $user = $request->user();

    $offre = Offre::leftJoin('sites', 'sites.id', '=', 'offres.site_id')
        ->leftJoin('site_rotation', 'site_rotation.id', '=', 'offres.site_rotation_id')
        ->join('fonctions', 'fonctions.id', '=', 'offres.poste_id')
        ->join('companies', 'companies.id', '=', 'offres.company_id')
        ->where('offres.company_id', $user->company_id)
        ->where('offres.id', $id)
        ->select(
            'offres.id',
            'offres.demarrage',
            'offres.start_date',
            'offres.taux_horaire',
            'offres.description_offre',
            'offres.type_horaire',
            'offres.site_rotation_id',

            // Champs horaires fixes
            'sites.name',
            'sites.address',
            'sites.monday_checked', 'sites.start_time_monday', 'sites.end_time_monday',
            'sites.tuesday_checked', 'sites.start_time_tuesday', 'sites.end_time_tuesday',
            'sites.wednesday_checked', 'sites.start_time_wednesday', 'sites.end_time_wednesday',
            'sites.thursday_checked', 'sites.start_time_thursday', 'sites.end_time_thursday',
            'sites.friday_checked', 'sites.start_time_friday', 'sites.end_time_friday',
            'sites.saturday_checked', 'sites.start_time_saturday', 'sites.end_time_saturday',
            'sites.sunday_checked', 'sites.start_time_sunday', 'sites.end_time_sunday',
            'site_rotation.name',
            'site_rotation.address'
        )
        ->first();

    if (!$offre) {
        return response()->json(['message' => 'Offre non trouvée'], 404);
    }

    $offreArray = $offre->toArray();

    if ($offre->type_horaire === 'rotation' && $offre->site_rotation_id) {
        $rotation = DB::table('rotation_site')
            ->join('rotations', 'rotations.id', '=', 'rotation_site.rotation_id')
            ->where('rotation_site.site_id', $offre->site_rotation_id)
            ->select('rotations.id as rotation_id', 'rotations.nom', 'rotations.duree_cycle')
            ->first();

        if ($rotation) {
            $repartitions = DB::table('repartitions')
                ->where('rotation_id', $rotation->rotation_id)
                ->get();

            $periodes = [];
            $somme_jours = 0;

            foreach ($repartitions as $rep) {
                $somme_jours += $rep->nombre_jours ?? 0;
                $periodes[] = ucfirst($rep->periode) . ' : ' . date('H:i', strtotime($rep->heure_debut)) .
                            ' - ' . date('H:i', strtotime($rep->heure_fin)) . ' (' . $rep->nombre_jours . ' jours)';
            }

            $jours_repos = $rotation->duree_cycle - $somme_jours;
            if ($jours_repos > 0) {
                $periodes[] = "Repos : $jours_repos jours";
            }

            $offreArray['horaire'] = "Rotation : Cycle de {$rotation->duree_cycle} jours\n" . implode("\n", $periodes);
        } else {
            $offreArray['horaire'] = 'Rotation non définie pour ce site';
        }

    } else {
        // Horaire fixe : même logique que formatSchedule()
        $horaire = $this->formatSchedule($offreArray);
        unset(
            $offreArray['monday_checked'], $offreArray['start_time_monday'], $offreArray['end_time_monday'],
            $offreArray['tuesday_checked'], $offreArray['start_time_tuesday'], $offreArray['end_time_tuesday'],
            $offreArray['wednesday_checked'], $offreArray['start_time_wednesday'], $offreArray['end_time_wednesday'],
            $offreArray['thursday_checked'], $offreArray['start_time_thursday'], $offreArray['end_time_thursday'],
            $offreArray['friday_checked'], $offreArray['start_time_friday'], $offreArray['end_time_friday'],
            $offreArray['saturday_checked'], $offreArray['start_time_saturday'], $offreArray['end_time_saturday'],
            $offreArray['sunday_checked'], $offreArray['start_time_sunday'], $offreArray['end_time_sunday']
        );
        $offreArray['horaire'] = $horaire;
    }

    return response()->json($offreArray);
}

    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
