<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Offre;
use App\Models\Candidature;
use Illuminate\Http\Request;
use App\Services\OneSignalService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\UserDeactivationReason;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class CandidatureAdminController extends Controller
{

    protected $oneSignal;

    public function __construct(OneSignalService $oneSignal)
    {
        $this->oneSignal = $oneSignal;
    }

    public function index(Request $request)
    {
        $user = $request->user();

        // Vérifier si l'utilisateur a une compagnie
        if (!$user->company_id) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas rattaché à une compagnie.');
        }

        // Récupérer les candidatures des utilisateurs appartenant à la même compagnie
        $candidatures = Candidature::with(['offre.fonction', 'user', 'company'])
            ->where('company_id', $user->company_id)
            ->get();

        return view('candidature.index', compact('candidatures'));
    }

    // public function accept($id, Request $request)
    // {
    //     $user = $request->user();

    //     // Vérifier si la candidature appartient à la compagnie de l'admin
    //     $candidature = Candidature::where('id', $id)
    //         ->where('company_id', $user->company_id)
    //         ->firstOrFail();

    //     // Mise à jour du statut
    //     $candidature->status = 'accepted';
    //     $candidature->save();

    //     return redirect()->back()->with('success', 'Candidature acceptée avec succès.');
    // }

    // public function accept($id, Request $request)
    // {
    //     $user = $request->user();

    //     // Vérifier si la candidature appartient à la compagnie de l'admin
    //     $candidature = Candidature::where('id', $id)
    //         ->where('company_id', $user->company_id)
    //         ->firstOrFail();

    //     // Mise à jour du statut
    //     $candidature->status = 'accepted';
    //     $candidature->save();

    //     // 🔗 Attacher automatiquement l'utilisateur au site de l'offre
    //     $employee = User::find($candidature->user_id);
    //     if ($employee) {
    //         $employee->sites()->sync([$candidature->offre->site_id]); // Assigner au site
    //     }

    //     return redirect()->back()->with('success', 'Candidature acceptée et employé assigné au site.');
    // }


    // public function accept($id, Request $request)
    // {
    //     $user = $request->user();

    //     // Vérification de la candidature avec l'offre
    //     $candidature = Candidature::where('id', $id)
    //         ->where('company_id', $user->company_id)
    //         ->with('offre') // Charger l'offre pour éviter une erreur d'accès
    //         ->firstOrFail();

    //     // Vérifier si l'offre et le site existent
    //     if (!$candidature->offre || !$candidature->offre->site_id) {
    //         return redirect()->back()->with('error', 'Le site associé à l\'offre n\'existe pas.');
    //     }

    //     $siteId = $candidature->offre->site_id;

    //     // Vérifier si le site existe dans la base de données
    //     $siteExists = DB::table('sites')->where('id', $siteId)->exists();
    //     if (!$siteExists) {
    //         return redirect()->back()->with('error', 'Le site associé à l\'offre n\'existe pas dans la base de données.');
    //     }

    //     // Mise à jour du statut de la candidature
    //     $candidature->status = 'accepted';
    //     $candidature->save();

    //     // Récupérer l'employé concerné
    //     $employee = User::find($candidature->user_id);

    //     if ($employee) {
    //         // Assigner l'employé au site lié à l'offre
    //         $employee->sites()->syncWithoutDetaching([$siteId]);

    //         // Réactiver l'utilisateur s'il était désactivé pour "Fin de contrat"
    //         $deactivationReason = UserDeactivationReason::where('user_id', $employee->id)
    //             ->where('reason', 'Fin de contrat')
    //             ->first();

    //         if ($deactivationReason) {
    //             // Créer une nouvelle raison pour l'activation
    //             UserDeactivationReason::create([
    //                 'user_id' => $employee->id,
    //                 'status' => 'active',
    //                 'reason' => 'Réactivation suite à l\'acceptation de l\'offre',
    //             ]);

    //             // Mettre à jour le statut de l'utilisateur en 'active'
    //             $employee->status = 'active';
    //             $employee->save();
    //         }
    //     }

    //     $this->oneSignal->sendToUser(
    //         $employee->one_signal_user_id,
    //         "Candidature acceptée",
    //         "Votre candidature a été acceptée et vous avez été assigné à un site.",
    //         ['refresh_page' => 'contract']
    //     );

    //     // Redirection avec succès
    //     return redirect()->back()->with('success', 'Candidature acceptée, employé assigné au site et réactivé.');
    // }

public function accept($id, Request $request)
{
    $user = $request->user();

    $candidature = Candidature::where('id', $id)
        ->where('company_id', $user->company_id)
        ->with('offre')
        ->firstOrFail();

    $offre = $candidature->offre;

    if (!$offre) {
        return redirect()->back()->with('error', 'Offre introuvable.');
    }

    $employee = User::find($candidature->user_id);
    if (!$employee) {
        return redirect()->back()->with('error', 'Utilisateur introuvable.');
    }
    Log::info("Employee type_horaire: '{$employee->type_horaire}'");
    Log::info("Offre type_horaire: '{$offre->type_horaire}'");

    // ✅ Vérification de compatibilité des types d'horaire
    if ($employee->type_horaire !== $offre->type_horaire) {
        return redirect()->back()->with('error', 'Le type d’horaire de l’utilisateur ne correspond pas à celui de l’offre.');
    }
    // Mise à jour du statut de la candidature
    $candidature->status = 'accepted';
    $candidature->save();
    // Affectation selon type_horaire
    if ($offre->type_horaire === 'fixe') {
        if (!$offre->site_id || !DB::table('sites')->where('id', $offre->site_id)->exists()) {
            return redirect()->back()->with('error', 'Le site associé à l\'offre n\'existe pas.');
        }
        $employee->sites()->syncWithoutDetaching([$offre->site_id]);

    } elseif ($offre->type_horaire === 'rotation') {
        if (!$offre->site_rotation_id) {
            return redirect()->back()->with('error', 'Le site de rotation de cette offre est manquant.');
        }

        $equipeIds = DB::table('site_equipe')
            ->where('site_id', $offre->site_rotation_id)
            ->pluck('equipe_id')
            ->toArray();

        $inEquipe = DB::table('equipe_user')
            ->whereIn('equipe_id', $equipeIds)
            ->where('user_id', $employee->id)
            ->exists();

        if (!$inEquipe) {
            return redirect()->back()->with('error', 'L\'utilisateur n\'est affecté à aucune équipe de ce site rotation.');
        }

        // Pas de sync ici, car les affectations rotation sont gérées par équipe
    }

    // Réactivation
    $deactivationReason = UserDeactivationReason::where('user_id', $employee->id)
        ->where('reason', 'Fin de contrat')
        ->first();

    if ($deactivationReason) {
        UserDeactivationReason::create([
            'user_id' => $employee->id,
            'status' => 'active',
            'reason' => 'Réactivation suite à l\'acceptation de l\'offre',
        ]);

        $employee->status = 'active';
        $employee->save();
    }

    // Notification OneSignal
    $this->oneSignal->sendToUser(
        $employee->one_signal_user_id,
        "Candidature acceptée",
        "Votre candidature a été acceptée.",
        ['refresh_page' => 'contract']
    );

    return redirect()->back()->with('success', 'Candidature acceptée avec succès.');
}









}
