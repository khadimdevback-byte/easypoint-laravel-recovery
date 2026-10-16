<?php

namespace App\Http\Controllers;

use App\Models\Disponibilite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Fonction;
use Illuminate\Support\Facades\DB;

class DisponibiliteController extends Controller
{
    //
    // public function index(Request $request){
    //     $user = Auth::user();
    //     // Récupérer les disponibilités des employés et inclure la fonction
    //     $disponibilites = Disponibilite::where('disponibilites.company_id', $user->company_id)
    //         ->join('users', 'disponibilites.user_id', '=', 'users.id')
    //         ->join('fonctions', 'users.employee_function', '=', 'fonctions.id') // Joindre la table des fonctions avec la clé étrangère
    //         ->select(
    //             'users.name',
    //             'users.email',
    //             'users.address',
    //             'users.phone',
    //             'users.employee_function',
    //             'disponibilites.date_disponibilite',
    //             'disponibilites.disponibilite',
    //             'fonctions.nom_fonction',
    //         )
    //         ->get();

    //     // Récupérer les fonctions de l'entreprise
    //     $fonction = Fonction::whereNull('company_id')
    //         ->orwhere('company_id', $user->company_id)
    //         ->get();

    //     return view('disponibilite.index', compact('disponibilites', 'fonction'));
    // }

    public function index(Request $request){
        $user = Auth::user();
        // Récupérer les disponibilités des employés et inclure la fonction
        $query = Disponibilite::where('disponibilites.company_id', $user->company_id)
            ->join('users', 'disponibilites.user_id', '=', 'users.id')
            ->leftJoin('fonctions', 'users.employee_function', '=', 'fonctions.id') // Joindre la table des fonctions avec la clé étrangère
            ->select(
                'users.name',
                'users.email',
                'users.address',
                'users.phone',
                'users.employee_function',
                'disponibilites.date_disponibilite',
                'disponibilites.disponibilite',
                'fonctions.nom_fonction', // Sélectionner le nom de la fonction
            )
            ->groupBy(
                'users.id',
                'users.name',
                'users.email',
                'users.address',
                'users.phone',
                'users.employee_function',
                'disponibilites.date_disponibilite',
                'disponibilites.disponibilite',
                'fonctions.nom_fonction')
            ->orderBy('users.name');
            // 🎭 Filtrage par Fonction
            if ($request->filled('fonction')) {
                $query->where('users.employee_function', $request->fonction);
            }

            // 📅 Filtrage par Date
            if ($request->filled('date')) {
                $query->whereDate('disponibilites.date_disponibilite', $request->date);
            }

    // Exécuter la requête et récupérer les disponibilités
    $disponibilites = $query->get();

        // Récupérer les fonctions de l'entreprise
        $fonction = Fonction::whereNull('company_id')
            ->orwhere('company_id', $user->company_id)
            ->get();

        return view('disponibilite.index', compact('disponibilites', 'fonction'));
    }

    
}
