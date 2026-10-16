<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidature;
use App\Models\Disponibilite;
use App\Models\User;
use App\Models\UserDeactivationReason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DisponibiliteController extends Controller
{

    public function checkdisponibilite(Request $request){
        $user = $request->user();

        if(!$user){
            return response()->json([
                'message'=>['error'=>'Utilisateur non Authentifié']
            ],401);
        }

        $disponibilite = Disponibilite::where('user_id',$user->id)->first();

        if(!$disponibilite){
            return response()->json([
                'message'=>'Aucune disponibilite enregistrée',
            ],200);
        }
        return response()->json([
            'disponibilite'=>$disponibilite
        ],200);
    }
    // Ajouter une disponibilité

    // public function store(Request $request)
    // {
    //     $user = $request->user();

    //     $request->validate([
    //         'disponibilite' => 'required|in:immediat,A_partir_de',
    //         'date_disponibilite' => 'required_if:disponibilite,A_partir_de|date'
    //     ], [
    //         'date_disponibilite.required_if' => 'La date de disponibilité est obligatoire pour une disponibilité à partir de.',
    //         'date_disponibilite.date' => 'La date de disponibilité doit être une date valide.',
    //     ]);

    //     // Vérifie si une disponibilité existante du même type existe déjà
    //     $existingDisponibilite = Disponibilite::where('user_id', $user->id)
    //         ->where('disponibilite', $request->disponibilite)
    //         ->when($request->disponibilite === 'immediat', function ($query) {
    //             return $query->whereNull('date_disponibilite');
    //         })
    //         ->when($request->disponibilite === 'A_partir_de', function ($query) use ($request) {
    //             return $query->where('date_disponibilite', $request->date_disponibilite);
    //         })
    //         ->first();

    //     if ($existingDisponibilite) {
    //         return response()->json(['message' => 'Vous avez déjà enregistré cette disponibilité.'], 409);
    //     }

    //     // Vérifier si l'utilisateur a une autre disponibilité existante (immediat ou A_partir_de)
    //     $oldDisponibilite = Disponibilite::where('user_id', $user->id)->first();

    //     // Supprimer l'ancienne disponibilité si elle existe
    //     if ($oldDisponibilite) {
    //         $oldDisponibilite->delete();
    //     }

    //     // Créer la nouvelle disponibilité
    //     $disponibilite = new Disponibilite;
    //     $disponibilite->user_id = $user->id;
    //     $disponibilite->company_id = $user->company_id;
    //     $disponibilite->disponibilite = $request->disponibilite;
    //     $disponibilite->date_disponibilite = $request->disponibilite === 'immediat' ? null : $request->date_disponibilite;
    //     $disponibilite->save();

    //     return response()->json([
    //         'message' => 'Disponibilité mise à jour avec succès !',
    //         'disponibilite' => $disponibilite
    //     ], 201);
    // }

//     public function store(Request $request)
// {
//     $user = $request->user();

//     // Vérifier si l'utilisateur est un 'user' et s'il a une fonction
//     if ($user->user_type === 'user' && !$user->employee_function) {
//         return response()->json([
//             'error' => 'Vous ne pouvez pas accéder aux Disponibilité sans une fonction attribuée.',
//         ], 403);
//     }

//     // Vérifiez l'état de désactivation de l'utilisateur
//     $deactivationReason = UserDeactivationReason::where('user_id', $user->id)->latest()->first();

//     // Vérifiez si l'utilisateur est désactivé avec un motif de "Fin de Contrat"
//     if ($deactivationReason && $deactivationReason->status === 'inactive' && $deactivationReason->reason === 'Fin de Contrat') {
//         // Validation de la disponibilité
//         $request->validate([
//             'disponibilite' => 'required|in:immediat,A_partir_de',
//             'date_disponibilite' => 'required_if:disponibilite,A_partir_de|date'
//         ], [
//             'date_disponibilite.required_if' => 'La date de disponibilité est obligatoire pour une disponibilité à partir de.',
//             'date_disponibilite.date' => 'La date de disponibilité doit être une date valide.',
//         ]);

//         // Vérifie si une disponibilité existante du même type existe déjà
//         $existingDisponibilite = Disponibilite::where('user_id', $user->id)
//             ->where('disponibilite', $request->disponibilite)
//             ->when($request->disponibilite === 'immediat', function ($query) {
//                 return $query->whereNull('date_disponibilite');
//             })
//             ->when($request->disponibilite === 'A_partir_de', function ($query) use ($request) {
//                 return $query->where('date_disponibilite', $request->date_disponibilite);
//             })
//             ->first();

//         if ($existingDisponibilite) {
//             return response()->json(['message' => 'Vous avez déjà enregistré cette disponibilité.'], 409);
//         }

//         // Vérifier si l'utilisateur a une autre disponibilité existante (immediat ou A_partir_de)
//         $oldDisponibilite = Disponibilite::where('user_id', $user->id)->first();

//         // Supprimer l'ancienne disponibilité si elle existe
//         if ($oldDisponibilite) {
//             $oldDisponibilite->delete();
//         }

//         // Créer la nouvelle disponibilité
//         $disponibilite = new Disponibilite;
//         $disponibilite->user_id = $user->id;
//         $disponibilite->company_id = $user->company_id;
//         $disponibilite->disponibilite = $request->disponibilite;
//         $disponibilite->date_disponibilite = $request->disponibilite === 'immediat' ? null : $request->date_disponibilite;
//         $disponibilite->save();

//         return response()->json([
//             'message' => 'Disponibilité mise à jour avec succès !',
//             'disponibilite' => $disponibilite
//         ], 201);
//     } elseif ($deactivationReason) {
//         // Pour tous les autres motifs de désactivation, renvoyer une erreur
//         return response()->json([
//             'error' => 'Vous êtes en cours de contrat, vous ne pouvez pas enregistré une disponibilité.',
//         ], 403);
//     }
// // }
// public function store(Request $request)
//     {
//         $user = $request->user();

//         // Vérifier si l'utilisateur est un 'user' et s'il a une fonction
//         if ($user->user_type === 'user' && !$user->employee_function) {
//             return response()->json([
//                 'error' => 'Vous ne pouvez pas accéder aux Disponibilité sans une fonction attribuée.',
//             ], 403);
//         }

//         // Vérifiez l'état de désactivation de l'utilisateur
//         $deactivationReason = UserDeactivationReason::where('user_id', $user->id)->latest()->first();

//         // Si l'utilisateur a un motif de "Fin de Contrat", afficher un message spécifique
//         if ($deactivationReason && $deactivationReason->status === 'inactive' && $deactivationReason->reason === 'Fin de Contrat') {
//             return response()->json([
//                 'error' => 'Vous n\'avez pas de contrat en cours, vous pouvez commencer l\'enregistrement de la disponibilité.',
//             ], 403);
//         }

//         // Si l'utilisateur a un contrat en cours, afficher un autre message
//         if ($deactivationReason && $deactivationReason->status === 'active' && $deactivationReason->reason !== 'Fin de Contrat') {
//             return response()->json([
//                 'error' => 'Vous êtes en cours de contrat, vous ne pouvez pas enregistrer une disponibilité.',
//             ], 403);
//         }

//         // Validation de la disponibilité
//         $request->validate([
//             'disponibilite' => 'required|in:immediat,A_partir_de',
//             'date_disponibilite' => 'required_if:disponibilite,A_partir_de|date'
//         ], [
//             'date_disponibilite.required_if' => 'La date de disponibilité est obligatoire pour une disponibilité à partir de.',
//             'date_disponibilite.date' => 'La date de disponibilité doit être une date valide.',
//         ]);

//         // Vérifie si une disponibilité existante du même type existe déjà
//         $existingDisponibilite = Disponibilite::where('user_id', $user->id)
//             ->where('disponibilite', $request->disponibilite)
//             ->when($request->disponibilite === 'immediat', function ($query) {
//                 return $query->whereNull('date_disponibilite');
//             })
//             ->when($request->disponibilite === 'A_partir_de', function ($query) use ($request) {
//                 return $query->where('date_disponibilite', $request->date_disponibilite);
//             })
//             ->first();

//         if ($existingDisponibilite) {
//             return response()->json(['message' => 'Vous avez déjà enregistré cette disponibilité.'], 409);
//         }

//         // Vérifier si l'utilisateur a une autre disponibilité existante (immediat ou A_partir_de)
//         $oldDisponibilite = Disponibilite::where('user_id', $user->id)->first();

//         // Supprimer l'ancienne disponibilité si elle existe
//         if ($oldDisponibilite) {
//             $oldDisponibilite->delete();
//         }

//         // Créer la nouvelle disponibilité
//         $disponibilite = new Disponibilite;
//         $disponibilite->user_id = $user->id;
//         $disponibilite->company_id = $user->company_id;
//         $disponibilite->disponibilite = $request->disponibilite;
//         $disponibilite->date_disponibilite = $request->disponibilite === 'immediat' ? null : $request->date_disponibilite;
//         $disponibilite->save();

//         return response()->json([
//             'message' => 'Disponibilité mise à jour avec succès !',
//             'disponibilite' => $disponibilite
//         ], 201);
//     }


// public function checkUserContract(Request $request)
// {
//     $user = $request->user();

//     // Vérifier si l'utilisateur est bien authentifié
//     if (!$user) {
//         return response()->json(['error' => 'Utilisateur non authentifié.'], 401);
//     }

//     // Vérifier l'état de désactivation de l'utilisateur
//     $deactivationReason = UserDeactivationReason::where('user_id', $user->id)->latest()->first();

//     // Récupérer la date de fin de contrat
//     $contractEndDate = $user->contract_end_date ?? null;

//     // Vérifier si le contrat est expiré (date de fin dépassée)
//     if ($contractEndDate && now()->gt($contractEndDate)) {
//         return response()->json([
//             'contractStatus' => 'inactive',
//             'contractStartDate' => $user->contract_start_date ?? null,
//             'message' => 'Le contrat est expiré.',
//         ], 200);
//     }

//     // Si aucun contrat ou motif de désactivation n'est trouvé, statut "pending"
//     if (!$deactivationReason) {
//         return response()->json([
//             'contractStatus' => 'pending',
//             'contractStartDate' => $user->contract_start_date ?? null,
//             'message' => 'Aucun contrat trouvé. L\'utilisateur est en attente.',
//         ], 200);
//     }

//     // Si l'utilisateur a un contrat actif (en cours)
//     if ($deactivationReason->status === 'active' && $deactivationReason->reason !== 'Fin de Contrat') {
//         return response()->json([
//             'contractStatus' => 'active',
//             'contractStartDate' => null,
//             'message' => 'Le contrat est actuellement actif.',
//         ], 200);
//     }

//     // Si l'utilisateur a un contrat terminé
//     if ($deactivationReason->status === 'inactive' && $deactivationReason->reason === 'Fin de Contrat') {
//         return response()->json([
//             'contractStatus' => 'inactive',
//             'contractStartDate' => null,
//             'message' => 'Le contrat est terminé.',
//         ], 200);
//     }

//     // Cas où le contrat est en attente (travaux non démarrés)
//     return response()->json([
//         'contractStatus' => 'pending',
//         'contractStartDate' => $user->contract_start_date ?? null,
//         'message' => 'Le contrat est en attente de démarrage.',
//     ], 200);
// }1

// public function checkUserContract(Request $request)
// {
//     $user = $request->user();

//     // Vérifier si l'utilisateur est bien authentifié
//     if (!$user) {
//         return response()->json(['error' => 'Utilisateur non authentifié.'], 401);
//     }

//     // Vérifier si l'utilisateur est relié à un site via la table site_user
//     $isAssignedToSite = DB::table('site_user')->where('user_id', $user->id)->exists();

//     // Si l'utilisateur n'est pas lié à un site, son contrat devient inactif
//     if (!$isAssignedToSite) {
//         // Créer une raison de désactivation indiquant "Pas de contrat en cours"
//         UserDeactivationReason::create([
//             'user_id' => $user->id,
//             'status' => 'inactive',
//             'reason' => 'Fin de Contrat',
//         ]);

//         // Retourner un message indiquant qu'il n'y a pas de contrat
//         return response()->json([
//             'contractStatus' => 'inactive',
//             'message' => 'L\'utilisateur n\'est pas lié à un site, contrat inactif.',
//         ], 200);
//     }

//     // Vérifier l'état de désactivation de l'utilisateur
//     $deactivationReason = UserDeactivationReason::where('user_id', $user->id)->latest()->first();

//     // Récupérer la date de fin de contrat
//     $contractEndDate = $user->contract_end_date ?? null;

//     // Vérifier si le contrat est expiré (date de fin dépassée)
//     if ($contractEndDate && now()->gt($contractEndDate)) {
//         return response()->json([
//             'contractStatus' => 'inactive',
//             'contractStartDate' => $user->contract_start_date ?? null,
//             'message' => 'Le contrat est expiré.',
//         ], 200);
//     }

//     // Si aucun contrat ou motif de désactivation n'est trouvé, statut "pending"
//     if (!$deactivationReason) {
//         return response()->json([
//             'contractStatus' => 'pending',
//             'contractStartDate' => $user->contract_start_date ?? null,
//             'message' => 'Aucun contrat trouvé. L\'utilisateur est en attente.',
//         ], 200);
//     }

//     // Si l'utilisateur a un contrat actif (en cours)
//     if ($deactivationReason->status === 'active' && $deactivationReason->reason !== 'Fin de Contrat') {
//         return response()->json([
//             'contractStatus' => 'active',
//             'contractStartDate' => null,
//             'message' => 'Le contrat est actuellement actif.',
//         ], 200);
//     }

//     // Si l'utilisateur a un contrat terminé
//     if ($deactivationReason->status === 'inactive' && $deactivationReason->reason === 'Fin de Contrat') {
//         return response()->json([
//             'contractStatus' => 'inactive',
//             'contractStartDate' => null,
//             'message' => 'Le contrat est terminé.',
//         ], 200);
//     }

//     // Cas où le contrat est en attente (travaux non démarrés)
//     return response()->json([
//         'contractStatus' => 'pending',
//         'contractStartDate' => $user->contract_start_date ?? null,
//         'message' => 'Le contrat est en attente de démarrage.',
//     ], 200);
// }
public function checkUserContract(Request $request) {
    $user = $request->user(); // Récupérer l'utilisateur connecté
    $now = now(); // Date actuelle

    // Vérifier si l'utilisateur est assigné à un site
    $isAssignedToSite = DB::table('site_user')->where('user_id', $user->id)->exists();

    // Vérifier s'il a déjà une raison de désactivation
    $deactivationReason = UserDeactivationReason::where('user_id', $user->id)->latest()->first();

    // Récupérer l'offre associée à l'utilisateur (si applicable)
    $offre = DB::table('offres')
        ->join('candidatures', 'offres.id', '=', 'candidatures.offre_id') // Liaison entre offres et candidatures
        ->where('candidatures.user_id', $user->id)
        ->select('offres.demarrage', 'offres.start_date')
        ->first();

    // Vérifier si l'offre a une date de démarrage
    if ($offre) {
        if ($offre->demarrage === 'A_partir_de' && $offre->start_date) {
            if ($now->lt($offre->start_date)) {
                return response()->json([
                    'contractStatus' => 'pending',
                    'contractStartDate' => $offre->start_date,
                    'message' => 'Le contrat est en attente de démarrage.',
                ], 200);
            } elseif ($now->gte($offre->start_date)) {
                // La date de démarrage est atteinte, on active le contrat
                $user->status = 'active';
                $user->save();

                // Mettre à jour ou créer la raison de désactivation comme "active"
                UserDeactivationReason::updateOrCreate(
                    ['user_id' => $user->id],
                    ['status' => 'active', 'reason' => 'Contrat activé']
                );

                return response()->json([
                    'contractStatus' => 'active',
                    'message' => 'Votre contrat est maintenant actif.',
                ], 200);
            }
        }
    }

    // Vérifier si l'utilisateur a une date de fin de contrat
    // $contractEndDate = $user->contract_end_date ?? null;

    // if ($contractEndDate && $now->gt($contractEndDate)) {
    //     return response()->json([
    //         'contractStatus' => 'inactive',
    //         'message' => 'Le contrat est expiré.',
    //     ], 200);
    // }

    // Si l'utilisateur n'est pas assigné à un site, il devient inactif
    if (!$isAssignedToSite) {
        if (!$deactivationReason || $deactivationReason->status !== 'inactive') {
            UserDeactivationReason::create([
                'user_id' => $user->id,
                'status' => 'inactive',
                'reason' => 'Fin de Contrat',
            ]);
        }

        return response()->json([
            'contractStatus' => 'inactive',
            'message' => 'L\'utilisateur n\'est pas lié à un site, contrat inactif.',
        ], 200);
    }

    // Si l'utilisateur a un contrat actif
    if ($deactivationReason && $deactivationReason->status === 'active' && $deactivationReason->reason !== 'Fin de Contrat') {
        return response()->json([
            'contractStatus' => 'active',
            'message' => 'Le contrat est actuellement actif.',
        ], 200);
    }

    if ($deactivationReason && $deactivationReason->status === 'inactive' && $deactivationReason->reason === 'Fin de Contrat' && $user->status === 'active') {
        return response()->json([
            'contractStatus' => 'active',
            'message' => 'Le contrat est actuellement actif.',
        ], 200);
    }

    // Si aucun contrat ou motif de désactivation n'est trouvé, statut "pending"
    // return response()->json([
    //     'contractStatus' => 'pending',
    //     'message' => 'Aucun contrat trouvé. L\'utilisateur est en attente.',
    // ], 200);

    return response()->json([
        'contractStatus' => 'active',
        'message' => 'Le contrat est actuellement actif.',
    ], 200);

}


public function store(Request $request)
{
    $user = $request->user();
    // Vérification du contrat avant d'autoriser l'enregistrement
    $contractCheck = $this->checkUserContract($request);

    // Si l'utilisateur est inactif en raison d'un contrat expiré ou d'un contrat en attente, ne pas autoriser l'enregistrement de la disponibilité
    if ($contractCheck->getStatusCode() !== 200 && $contractCheck->getData()->contractStatus == 'inactive') {
        return $contractCheck;
    }
    // Validation de la disponibilité
    $request->validate([
        'disponibilite' => 'required|in:immediat,A_partir_de',
        'date_disponibilite' => 'required_if:disponibilite,A_partir_de|date'
    ], [
        'date_disponibilite.required_if' => 'La date de disponibilité est obligatoire pour une disponibilité à partir de.',
        'date_disponibilite.date' => 'La date de disponibilité doit être une date valide.',
    ]);

    // Si la disponibilité n'est pas définie, on ne l'enregistre pas comme "immediat" par défaut
    if (!$request->has('disponibilite') || empty($request->disponibilite)) {
        return response()->json(['error' => 'La disponibilité doit être spécifiée.'], 400);
    }

    // Vérifie si une disponibilité existante du même type existe déjà
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
        return response()->json(['message' => 'Vous avez déjà enregistré cette disponibilité.'], 409);
    }

    // Vérifier si l'utilisateur a une autre disponibilité existante (immediat ou A_partir_de)
    $oldDisponibilite = Disponibilite::where('user_id', $user->id)->first();

    // Supprimer l'ancienne disponibilité si elle existe
    if ($oldDisponibilite) {
        $oldDisponibilite->delete();
    }

    // Créer la nouvelle disponibilité
    $disponibilite = new Disponibilite;
    $disponibilite->user_id = $user->id;
    $disponibilite->company_id = $user->company_id;
    $disponibilite->disponibilite = $request->disponibilite;
    $disponibilite->date_disponibilite = $request->disponibilite === 'immediat' ? null : $request->date_disponibilite;
    $disponibilite->save();

    return response()->json([
        'message' => 'Disponibilité mise à jour avec succès !',
        'disponibilite' => $disponibilite
    ], 201);
}




// public function checkUserContract(Request $request)
// {
//     $user = $request->user();

//     // Vérifier si l'utilisateur est bien authentifié
//     if (!$user) {
//         return response()->json(['error' => 'Utilisateur non authentifié.'], 401);
//     }

//     // Vérifier si l'utilisateur est relié à un site via la table site_user
//     $isAssignedToSite = DB::table('site_user')->where('user_id', $user->id)->exists();

//     // Si l'utilisateur n'est pas lié à un site, son contrat devient inactif
//     if (!$isAssignedToSite) {
//         // Créer une raison de désactivation indiquant "Pas de contrat en cours"
//         UserDeactivationReason::create([
//             'user_id' => $user->id,
//             'status' => 'inactive',
//             'reason' => 'Fin de Contrat',
//         ]);

//         // Retourner un message indiquant qu'il n'y a pas de contrat
//         return response()->json([
//             'contractStatus' => 'inactive',
//             'message' => 'L\'utilisateur n\'est pas lié à un site, contrat inactif.',
//         ], 200);
//     }

//     // Vérifier l'état de désactivation de l'utilisateur
//     $deactivationReason = UserDeactivationReason::where('user_id', $user->id)->latest()->first();

//     // Récupérer la date de fin de contrat
//     $contractEndDate = $user->contract_end_date ?? null;

//     // Vérifier si le contrat est expiré (date de fin dépassée)
//     if ($contractEndDate && now()->gt($contractEndDate)) {
//         return response()->json([
//             'contractStatus' => 'inactive',
//             'contractStartDate' => $user->contract_start_date ?? null,
//             'message' => 'Le contrat est expiré.',
//         ], 200);
//     }

//     // Si aucun contrat ou motif de désactivation n'est trouvé, statut "pending"
//     if (!$deactivationReason) {
//         return response()->json([
//             'contractStatus' => 'pending',
//             'contractStartDate' => $user->contract_start_date ?? null,
//             'message' => 'Aucun contrat trouvé. L\'utilisateur est en attente.',
//         ], 200);
//     }

//     // Si l'utilisateur a un contrat actif (en cours)
//     if ($deactivationReason->status === 'active' && $deactivationReason->reason !== 'Fin de Contrat') {
//         return response()->json([
//             'contractStatus' => 'active',
//             'contractStartDate' => null,
//             'message' => 'Le contrat est actuellement actif.',
//         ], 200);
//     }

//     // Si l'utilisateur a un contrat terminé
//     if ($deactivationReason->status === 'inactive' && $deactivationReason->reason === 'Fin de Contrat') {
//         return response()->json([
//             'contractStatus' => 'inactive',
//             'contractStartDate' => null,
//             'message' => 'Le contrat est terminé.',
//         ], 200);
//     }

//     // Cas où le contrat est en attente (travaux non démarrés)
//     return response()->json([
//         'contractStatus' => 'pending',
//         'contractStartDate' => $user->contract_start_date ?? null,
//         'message' => 'Le contrat est en attente de démarrage.',
//     ], 200);
// }2

// {
//     // Vérifier l'état de désactivation de l'utilisateur
//     $deactivationReason = UserDeactivationReason::where('user_id', $user->id)->latest()->first();
//     $contractEndDate = $user->contract_end_date ?? null;

//     // Vérifier si le contrat est expiré
//     if ($contractEndDate && now()->gt($contractEndDate)) {
//         return [
//             'contractStatus' => 'inactive',
//             'message' => 'Le contrat est expiré.'
//         ];
//     }

//     // Si aucun contrat ou motif de désactivation n'est trouvé, statut "pending"
//     if (!$deactivationReason) {
//         return [
//             'contractStatus' => 'pending',
//             'message' => 'Aucun contrat trouvé. L\'utilisateur est en attente.'
//         ];
//     }

//     // Si l'utilisateur a un contrat actif
//     if ($deactivationReason->status === 'active' && $deactivationReason->reason !== 'Fin de Contrat') {
//         return [
//             'contractStatus' => 'active',
//             'message' => 'Le contrat est actuellement actif.'
//         ];
//     }

//     // Si l'utilisateur a un contrat terminé (Fin de Contrat)
//     if ($deactivationReason->status === 'inactive' && $deactivationReason->reason === 'Fin de Contrat') {
//         return [
//             'contractStatus' => 'inactive',
//             'message' => 'Le contrat est terminé.'
//         ];
//     }

//     return [
//         'contractStatus' => 'pending',
//         'message' => 'Le contrat est en attente de démarrage.'
//     ];
// }




// public function store(Request $request)
// {
//     $user = $request->user();

//     // Vérification du contrat avant d'autoriser l'enregistrement
//     $contractCheck = $this->checkUserContract($request);

//     // Si l'utilisateur est inactif en raison d'un contrat expiré ou d'un contrat en attente, ne pas autoriser l'enregistrement de la disponibilité
//     if ($contractCheck->getStatusCode() !== 200 && $contractCheck->getData()->contractStatus == 'inactive') {
//         return $contractCheck;
//     }
//     // Validation de la disponibilité
//     $request->validate([
//         'disponibilite' => 'required|in:immediat,A_partir_de',
//         'date_disponibilite' => 'required_if:disponibilite,A_partir_de|date'
//     ], [
//         'date_disponibilite.required_if' => 'La date de disponibilité est obligatoire pour une disponibilité à partir de.',
//         'date_disponibilite.date' => 'La date de disponibilité doit être une date valide.',
//     ]);

//     // Vérifie si une disponibilité existante du même type existe déjà
//     $existingDisponibilite = Disponibilite::where('user_id', $user->id)
//         ->where('disponibilite', $request->disponibilite)
//         ->when($request->disponibilite === 'immediat', function ($query) {
//             return $query->whereNull('date_disponibilite');
//         })
//         ->when($request->disponibilite === 'A_partir_de', function ($query) use ($request) {
//             return $query->where('date_disponibilite', $request->date_disponibilite);
//         })
//         ->first();

//     if ($existingDisponibilite) {
//         return response()->json(['message' => 'Vous avez déjà enregistré cette disponibilité.'], 409);
//     }

//     // Vérifier si l'utilisateur a une autre disponibilité existante (immediat ou A_partir_de)
//     $oldDisponibilite = Disponibilite::where('user_id', $user->id)->first();

//     // Supprimer l'ancienne disponibilité si elle existe
//     if ($oldDisponibilite) {
//         $oldDisponibilite->delete();
//     }

//     // Créer la nouvelle disponibilité
//     $disponibilite = new Disponibilite;
//     $disponibilite->user_id = $user->id;
//     $disponibilite->company_id = $user->company_id;
//     $disponibilite->disponibilite = $request->disponibilite;
//     $disponibilite->date_disponibilite = $request->disponibilite === 'immediat' ? null : $request->date_disponibilite;
//     $disponibilite->save();

//     return response()->json([
//         'message' => 'Disponibilité mise à jour avec succès !',
//         'disponibilite' => $disponibilite
//     ], 201);
// }



    public function mesDisponibilites(Request $request)
    {
        $user = $request->user();
        $disponibilites = Disponibilite::select('id', 'date_disponibilite','disponibilite')
            ->where('user_id', $user->id)
            ->orderBy('date_disponibilite', 'asc')
            ->get();
        return response()->json($disponibilites);
    }

     // Modifier une disponibilité
     public function update(Request $request, $id)
    {
        $user = $request->user();  // Utilisateur authentifié

        // Validation des données envoyées dans la requête
        $request->validate([
            'disponibilite' => 'required|in:immediat,A_partir_de', // Validation du type de disponibilité
            'date_disponibilite' => 'required_if:disponibilite,A_partir_de|date' // Validation de la date si la disponibilité est "A_partir_de"
        ], [
            'date_disponibilite.required_if' => 'La date de disponibilité est obligatoire pour une disponibilité à partir de.',
            'date_disponibilite.date' => 'La date de disponibilité doit être une date valide.',
        ]);
        // Trouver la disponibilité à modifier
        $disponibilite = Disponibilite::where('user_id', $user->id)
                                    ->where('id', $id) // Trouver la disponibilité avec l'ID
                                    ->first();

        if (!$disponibilite) {
            return response()->json(['message' => 'Disponibilité non trouvée.'], 404);  // Si la disponibilité n'existe pas
        }

        // Appliquer les changements
        $disponibilite->disponibilite = $request->disponibilite;
        $disponibilite->date_disponibilite = $request->disponibilite === 'immediat' ? null : $request->date_disponibilite;
        $disponibilite->save();  // Sauvegarder les changements

        return response()->json([
            'message' => 'Disponibilité mise à jour avec succès !',
            'disponibilite' => $disponibilite
        ], 200);
    }

 // Supprimer une disponibilité

     public function destroy(Request $request, $id)
    {
        $user = $request->user();  // Utilisateur authentifié

        // Trouver la disponibilité à supprimer
        $disponibilite = Disponibilite::where('user_id', operator: $user->id)
            ->where('id', $id)  // Trouver la disponibilité avec l'ID
            ->first();

        if (!$disponibilite) {
            return response()->json(['message' => 'Disponibilité non trouvée.'], 404);  // Si la disponibilité n'existe pas
        }
        // Supprimer la disponibilité
        $disponibilite->delete();

        return response()->json([
            'message' => 'Disponibilité supprimée avec succès !'
        ], 200);
    }

}
