<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BadgeController extends Controller
{
    // Formulaire de sélection
    public function selectForm()
    {
        $companyId = Auth::user()->company_id;

        $users = User::with('fonction', 'company', 'sites')
                    ->where('user_type', 'user')
                    ->where('company_id', $companyId)
                    ->orderBy('name')
                    ->get();

        return view('badges.select', compact('users'));
    }

    // Génération des badges
    public function generateBadges(Request $request)
    {
        try {
            $userIds = $request->input('user_ids', []);

            if (empty($userIds)) {
                return redirect()->back()->with('error', 'Veuillez sélectionner au moins un employé.');
            }

            // Récupérer les utilisateurs avec les relations
            $users = User::with('company', 'fonction', 'sites')
                        ->whereIn('id', $userIds)
                        ->get();

            // Vérifier les matricules manquants
            $missingMatriculeUsers = $users->filter(function ($user) {
                return empty($user->matricule);
            });

            if ($missingMatriculeUsers->isNotEmpty()) {
                $names = $missingMatriculeUsers->pluck('name')->join(', ');
                return redirect()->back()
                                ->with('error', "Les employés suivants n'ont pas de matricule : {$names}. Veuillez compléter avant de générer les badges.");
            }

            return view('badges.print-multiple', compact('users'));

        } catch (\Exception $e) {
            Log::error('Badge generation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la génération des badges.');
        }
    }
}
