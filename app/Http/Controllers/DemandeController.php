<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemandeController extends Controller
{
    // =========================================================================
    // PARTIE 1 : AXE MOBILE / EMPLOYÉ (API)
    // =========================================================================

    /**
     * API : Récupérer la liste des demandes de l'employé connecté
     */
    public function listeEmploye(Request $request)
    {
        $demandes = Demande::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'succes' => true,
            'donnees' => $demandes
        ], 200);
    }

    /**
     * API : Récupérer les détails d'une demande spécifique
     */
    public function detailEmploye(Request $request, $id)
    {
        $demande = Demande::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        return response()->json([
            'succes' => true,
            'donnees' => $demande
        ], 200);
    }

    /**
     * API : Soumettre une nouvelle demande depuis l'application mobile
     */
    public function enregistrerParEmploye(Request $request)
    {
        $validation = $request->validate([
            'categorie' => 'required|in:conge,permission',
            'date_debut' => 'required|date|after_or_equal:today',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'motif' => 'nullable|string',
        ]);

        $demande = Demande::create([
            'company_id' => $request->user()->company_id,
            'user_id' => $request->user()->id,
            'categorie' => $validation['categorie'],
            'date_debut' => $validation['date_debut'],
            'date_fin' => $validation['date_fin'],
            'motif' => $validation['motif'] ?? null,
            'statut' => 'en_attente',
        ]);

        return response()->json([
            'succes' => true,
            'message' => 'Votre demande a été soumise avec succès.',
            'donnees' => $demande
        ], 201);
    }

    /**
     * API : Modifier une demande (seulement si elle est encore en attente)
     */
    public function modifierEmploye(Request $request, $id)
    {
        $demande = Demande::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        // Vérifier si la demande peut être modifiée (seulement en attente)
        if ($demande->statut !== 'en_attente') {
            return response()->json([
                'succes' => false,
                'message' => 'Cette demande ne peut plus être modifiée car elle a déjà été traitée.'
            ], 403);
        }

        $validation = $request->validate([
            'categorie' => 'sometimes|in:conge,permission',
            'date_debut' => 'sometimes|date|after_or_equal:today',
            'date_fin' => 'sometimes|date|after_or_equal:date_debut',
            'motif' => 'nullable|string',
        ]);

        $demande->update($validation);

        return response()->json([
            'succes' => true,
            'message' => 'Demande modifiée avec succès.',
            'donnees' => $demande
        ], 200);
    }

    /**
     * API : Supprimer une demande (seulement si elle est encore en attente)
     */
    public function supprimerEmploye(Request $request, $id)
    {
        $demande = Demande::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();
        if($demande->statut !== 'en_attente'){
            return response()->json([
                'success'=> false,
                'message'=>'Cette demande ne peut plus être supprimée car elle a déja été traitée .'
            ] , 403);
        }

        $demande->delete();
        return response()->json([
            'succes' => true,
            'message' => 'Demande supprimée avec succès.'
        ], 200);
    }

    /**
     * API : Annuler une demande (seulement si elle est en attente)
     */
    public function annulerEmploye(Request $request, $id)
    {
        $demande = Demande::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        if ($demande->statut !== 'en_attente') {
            return response()->json([
                'succes' => false,
                'message' => 'Cette demande ne peut plus être annulée.'
            ], 403);
        }

        $demande->update(['statut' => 'annule']);

        return response()->json([
            'succes' => true,
            'message' => 'Demande annulée avec succès.'
        ], 200);
    }


    // =========================================================================
    // PARTIE 2 : AXE WEB / ADMINISTRATEUR (WEB)
    // =========================================================================

    /**
     * WEB : Liste des demandes reçues au sein de l'entreprise
     */
    public function listeEntreprise()
    {
        if (!in_array(Auth::user()->user_type, ['super_admin', 'admin', 'manager'])) {
            abort(403, 'Action non autorisée.');
        }

        $compagnieId = Auth::user()->company_id;

        $demandes = Demande::with('employe:id,name,first_name')
            ->where('company_id', $compagnieId)
            ->orderBy('statut', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.demandes.index', compact('demandes'));
    }

    /**
     * WEB : Formulaire de création d'une demande par l'admin
     */
    public function create()
    {
        if (!in_array(Auth::user()->user_type, ['super_admin', 'admin', 'manager'])) {
            abort(403, 'Action non autorisée.');
        }

        // Uniquement les utilisateurs de l'entreprise ayant le rôle strict d'employé
        $employes = User::where('company_id', Auth::user()->company_id)
                        ->where('user_type', 'user')
                        ->get();

        return view('admin.demandes.create', compact('employes'));
    }

    /**
     * WEB : Enregistrement et approbation directe d'une demande par l'admin
     */
    public function enregistrerParAdmin(Request $request)
    {
        if (!in_array(Auth::user()->user_type, ['super_admin', 'admin', 'manager'])) {
            abort(403, 'Action non autorisée.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'categorie' => 'required|in:conge,permission',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'motif' => 'nullable|string',
        ]);

        try {
            Demande::create([
                'company_id' => Auth::user()->company_id,
                'user_id' => $request->user_id,
                'categorie' => $request->categorie,
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'motif' => $request->motif ?? null,
                'statut' => 'approuve',
                'valide_par' => Auth::id(),
            ]);

            return redirect()->route('admin.demandes.index')->with('success', 'Demande enregistrée et approuvée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Une erreur est survenue lors de l\'enregistrement.');
        }
    }

    /**
     * WEB : Affichage du formulaire de traitement (changement de statut)
     */
    public function edit($id)
    {
        if (!in_array(Auth::user()->user_type, ['super_admin', 'admin', 'manager'])) {
            abort(403, 'Action non autorisée.');
        }
        // 1. On récupère la demande à modifier
        $demande = Demande::where('company_id', Auth::user()->company_id)->findOrFail($id);

        // 2. On récupère TOUS les employés pour le <select> (comme dans ton create)
        $employes = User::where('company_id', Auth::user()->company_id)
                        ->where('user_type', 'user')
                        ->get();

        // 3. On passe les deux variables à la vue
        return view('admin.demandes.edit', compact('demande', 'employes'));
    }

    /**
     * WEB : Enregistrement de la décision finale (Approuver / Rejeter)
     */
    public function changerStatut(Request $request, $id)
    {
        if (!in_array(Auth::user()->user_type, ['super_admin', 'admin', 'manager'])) {
            abort(403, 'Action non autorisée.');
        }

        $request->validate([
            'statut' => 'required|in:approuve,rejete,en_attente',
            'commentaire_admin' => 'nullable|string',
        ]);

        try {
            $demande = Demande::where('company_id', Auth::user()->company_id)->findOrFail($id);

            $demande->update([
                'statut' => $request->statut,
                'valide_par' => Auth::id(),
                'commentaire_admin' => $request->commentaire_admin ?? null,
            ]);

            return redirect()->route('admin.demandes.index')->with('success', 'Le statut de la demande a été mis à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Une erreur est survenue lors de la mise à jour.');
        }
    }

    /**
     * WEB : Suppression pure d'une demande
     */
    public function destroy($id)
    {
        if (!in_array(Auth::user()->user_type, ['super_admin', 'admin', 'manager'])) {
            abort(403, 'Action non autorisée.');
        }

        try {
            $demande = Demande::where('company_id', Auth::user()->company_id)->findOrFail($id);
            $demande->delete();

            return redirect()->route('admin.demandes.index')->with('success', 'Demande supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('admin.demandes.index')->with('error', 'Impossible de supprimer cette demande.');
        }
    }
}
