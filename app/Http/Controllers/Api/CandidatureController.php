<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Candidature;
use App\Models\Offre;
// use App\Models\Offre;
use App\Models\User;
use App\Notifications\NouvelleCandidatureNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class CandidatureController extends Controller
{
    // public function postuler(Request $request, $id)
    // {
    //     $user = $request->user();
    //     // Vérifier si l'offre existe
    //     $offre = Offre::where('id', $id)->where('company_id', $user->company_id)->first();
    //     if (!$offre) {
    //         return response()->json(['message' => 'Offre introuvable.'], 404);
    //     }
    //     // Vérifier si l'employé appartient bien à la même compagnie que l'offre
    //     if (!$user->company || $user->company->id !== $offre->company_id) {
    //         return response()->json(['message' => 'Vous ne pouvez postuler qu’aux offres de votre compagnie.'], 403);
    //     }
    //      // Vérifier si l'utilisateur a déjà décliné l'offre
    //         $candidature = Candidature::where('offre_id', $offre->id)
    //         ->where('user_id', $user->id)
    //         ->where('status', 'declined') // Vérifie si l'offre a été déclinée
    //         ->first();

    //     if ($candidature) {
    //         return response()->json(['message' => 'Vous avez déjà décliné cette offre et ne pouvez plus postuler.'], 403);
    //     }
    //     // Vérifier si l'utilisateur a déjà postulé à cette offre
    //     if (Candidature::where('user_id', $user->id)->where('offre_id', $id)->exists()) {
    //         return response()->json(['message' => 'Vous avez déjà postulé à cette offre.'], 400);
    //     }
    //     // Enregistrer la candidature
    //     Candidature::create([
    //         'user_id' => $user->id,
    //         'offre_id' => $id,
    //         'company_id' => $offre->company_id,
    //         'status' => 'pending', // Ajout du statut
    //     ]);

    //     return response()->json(['message' => 'Votre candidature a été envoyée avec succès.'], 201);
    // }

    // public function decliner(Request $request, $id)
    // {
    //     $user = $request->user();

    //     // Vérifier si la candidature existe
    //     $candidature = Candidature::where('id', $id)
    //         ->where('user_id', $user->id)
    //         ->first();

    //     if (!$candidature) {
    //         return response()->json(['message' => 'Candidature introuvable.'], 404);
    //     }

    //     // Vérifier si la candidature est déjà refusée
    //     if ($candidature->status === 'declined') {
    //         return response()->json(['message' => 'Cette offre a déjà été déclinée.'], 400);
    //     }

    //     // Valider la présence du motif
    //     $request->validate([
    //         'motif_rejet' => 'required|string|max:255',
    //     ]);

    //     // Mettre à jour la candidature
    //     $candidature->update([
    //         'status' => 'declined',
    //         'motif_rejet' => $request->motif_rejet,
    //     ]);

    //     return response()->json(['message' => 'Offre déclinée avec succès.'], 200);
    // }


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

    // Vérifier la compatibilité des types d'horaire
    if (strtolower(trim($user->type_horaire)) !== strtolower(trim($offre->type_horaire))) {
        return response()->json([
            'message' => 'Vous ne pouvez postuler qu’aux offres correspondant à votre type d’horaire.'
        ], 403);
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
    $candidature = Candidature::create([
        'user_id' => $user->id,
        'offre_id' => $id,
        'company_id' => $offre->company_id,
        'status' => 'pending',
    ]);

    $recipients = User::where('company_id', $offre->company_id)
        ->whereIn('user_type', ['admin', 'manager'])
        ->get();

    Notification::send(
        $recipients,
        new NouvelleCandidatureNotification($user, $candidature->load('offre.fonction'))
    );

    return response()->json(['message' => 'Votre candidature a été envoyée avec succès.'], 201);
}


    // public function decliner(Request $request, $id)
    // {
    //     $user = $request->user();

    //     // Vérifier si l'offre existe
    //     $offre = Offre::where('id', $id)->where('company_id', $user->company_id)->first();
    //     if (!$offre) {
    //         return response()->json(['message' => 'Offre introuvable.'], 404);
    //     }

    //     // Vérifier si l'utilisateur a déjà refusé cette offre
    //     $candidature = Candidature::where('offre_id', $offre->id)
    //         ->where('user_id', $user->id)
    //         ->first();

    //     if ($candidature && $candidature->status === 'declined') {
    //         return response()->json(['message' => 'Vous avez déjà refusé cette offre.'], 400);
    //     }

    //     // Valider la présence du motif
    //     $request->validate([
    //         'motif_rejet' => 'required|string|max:255',
    //     ]);

    //     if ($candidature) {
    //         // Mise à jour si une candidature existe déjà
    //         $candidature->update([
    //             'status' => 'declined',
    //             'motif_rejet' => $request->motif_rejet,
    //         ]);
    //     } else {
    //         // Créer une nouvelle candidature avec le statut 'declined'
    //         Candidature::create([
    //             'offre_id' => $offre->id,
    //             'user_id' => $user->id,
    //             'company_id' => $offre->company_id, // Ajout du company_id
    //             'status' => 'declined',
    //             'motif_rejet' => $request->motif_rejet,
    //         ]);
    //     }

    //     return response()->json(['message' => 'Offre déclinée avec succès.'], 200);
    // }


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


    public function Candidatures(Request $request){
        $user = $request->user();
        // Récupérer les candidatures de l'utilisateur avec les détails de l'offre et de la compagnie
        $candidatures = Candidature::join('offres', 'candidatures.offre_id', '=', 'offres.id')
            ->join('fonctions', 'offres.poste_id', '=', 'fonctions.id')
            ->join('companies', 'offres.company_id', '=', 'companies.id') // Joindre la table des compagnies
            ->where('candidatures.user_id', $user->id)
            ->where('companies.id', $user->company_id) // Filtrer par la compagnie de l'utilisateur
            ->select('candidatures.id', 'offres.id', 'fonctions.nom_fonction', 'candidatures.status', 'candidatures.motif_rejet') // Ajouter le nom de la compagnie
            ->get();

        return response()->json($candidatures);
    }

    // public function checkEmploymentStatus(Request $request)
    // {
    //     $user = $request->user(); // Récupérer l'utilisateur connecté

    //     // Vérifier si l'utilisateur est lié à un site
    //     $site = $user->sites()->first();

    //     if (!$site) {
    //         return response()->json([
    //             'message' => 'Vous n\'avez pas encore de contrat actif.',
    //             'status' => 'no_contract'
    //         ], 200);
    //     }

    //     // Récupérer la dernière candidature acceptée de l'utilisateur
    //     $candidature = Candidature::where('user_id', $user->id)
    //         ->where('status', 'accepted')
    //         ->latest()
    //         ->first();

    //     // Vérifier si la candidature existe et a une offre associée
    //     if (!$candidature || !$candidature->offre) {
    //         return response()->json([
    //             'message' => 'Aucune offre active trouvée.',
    //             'status' => 'no_offer'
    //         ], 200);
    //     }

    //     $offre = $candidature->offre;

    //     // Vérifier si l'offre est immédiate ou à une date future
    //     if ($offre->demarrage !== 'immédiat' && $offre->start_date > now()) {
    //         return response()->json([
    //             'message' => 'Offre en attente de démarrage.',
    //             'status' => 'pending',
    //             'start_date' => $offre->start_date
    //         ], 200);
    //     }

    //     return response()->json([
    //         'message' => 'Vous êtes actif sur le site et pouvez voir les offres.',
    //         'status' => 'active'
    //     ], 200);
    // }




    // public function checkEmploymentStatus(Request $request)
    // {
    //     $user = $request->user(); // Récupérer l'utilisateur connecté

    //     // Vérifier si l'utilisateur est lié à un site
    //     $site = $user->sites()->first();

    //     if (!$site) {
    //         return response()->json([
    //             'message' => 'Vous n\'avez pas encore de contrat actif.',
    //             'status' => 'no_contract'
    //         ], 200);
    //     }

    //     // Récupérer la dernière candidature acceptée de l'utilisateur
    //     $candidature = Candidature::where('user_id', $user->id)
    //         ->where('status', 'accepted')
    //         ->latest()
    //         ->first();

    //     // Vérifier si la candidature existe et a une offre associée
    //     if (!$candidature || !$candidature->offre) {
    //         return response()->json([
    //             'message' => 'Aucune offre active trouvée.',
    //             'status' => 'no_offer'
    //         ], 200);
    //     }

    //     $offre = $candidature->offre; // Accéder à l'offre via la relation

    //     // Vérifier si l'offre est immédiate ou à une date future
    //     if ($offre->demarrage !== 'immediat' && Carbon::parse($offre->start_date)->isFuture()) {
    //         // Mettre à jour le statut de la candidature à "pending"
    //         $candidature->status = 'pending';
    //         $candidature->save(); // Sauvegarder la mise à jour de la candidature

    //         return response()->json([
    //             'message' => 'Offre en attente de démarrage.',
    //             'status' => 'pending',
    //             'start_date' => Carbon::parse($offre->start_date)->toDateString()
    //         ], 200);
    //     }

    //     // Si tout est bon, l'utilisateur est actif
    //     return response()->json([
    //         'message' => 'Vous êtes actif sur le site et pouvez voir les offres.',
    //         'status' => 'active'
    //     ], 200);
    // }




    public function checkEmploymentStatus(Request $request)
{
    $user = $request->user(); // Récupérer l'utilisateur connecté

    // Récupérer la dernière candidature acceptée de l'utilisateur
    $candidature = Candidature::where('user_id', $user->id)
        ->where('status', 'accepted')
        ->with('offre')
        ->latest()
        ->first();

    if (!$candidature || !$candidature->offre) {
        return response()->json([
            'message' => 'Aucune offre active trouvée.',
            'status' => 'no_offer'
        ], 200);
    }

    $offre = $candidature->offre;

    // Vérifier si l'offre est différée dans le temps
    if ($offre->demarrage !== 'immediat' && Carbon::parse($offre->start_date)->isFuture()) {
        // Mettre à jour le statut de la candidature à "pending"
        $candidature->status = 'pending';
        $candidature->save();

        return response()->json([
            'message' => 'Offre en attente de démarrage.',
            'status' => 'pending',
            'start_date' => Carbon::parse($offre->start_date)->toDateString()
        ], 200);
    }

    // ✅ Vérification selon le type_horaire
    if ($offre->type_horaire === 'fixe') {
        $site = $user->sites()->first();

        if (!$site) {
            return response()->json([
                'message' => 'Vous n\'avez pas encore de contrat actif.',
                'status' => 'no_contract'
            ], 200);
        }

        return response()->json([
            'message' => 'Vous êtes actif sur le site fixe.',
            'status' => 'active',
            'site_type' => 'fixe'
        ], 200);
    }

    if ($offre->type_horaire === 'rotation') {
        // Chercher les équipes de l'utilisateur qui sont reliées à un site_rotation
        $hasEquipeInSiteRotation = DB::table('equipe_user')
            ->join('site_equipe', 'equipe_user.equipe_id', '=', 'site_equipe.equipe_id')
            ->where('equipe_user.user_id', $user->id)
            ->where('site_equipe.site_id', $offre->site_rotation_id)
            ->exists();

        if (!$hasEquipeInSiteRotation) {
            return response()->json([
                'message' => 'Aucune équipe liée au site de rotation trouvée pour vous.',
                'status' => 'no_contract'
            ], 200);
        }

        return response()->json([
            'message' => 'Vous êtes actif sur le site de rotation.',
            'status' => 'active',
            'site_type' => 'rotation'
        ], 200);
    }

    return response()->json([
        'message' => 'Type d\'horaire inconnu.',
        'status' => 'unknown'
    ], 200);
}

}
