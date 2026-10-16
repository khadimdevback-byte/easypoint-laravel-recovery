<?php

namespace App\Http\Controllers;

use App\Models\Equipe;
use App\Models\Rotation;
use App\Models\Site;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EquipeController extends Controller
{
    //
    public function create()
    {
        $user = Auth::user();
        $users_sans_equipe = User::where('user_type', 'user')
            ->where('company_id', $user->company_id) // Filtrer par entreprise
            ->whereDoesntHave('equipes')      // L'utilisateur n'a pas encore d'équipe
            ->where('type_horaire', 'rotation')
            ->get();
        return view('equipe.create', compact('users_sans_equipe'));
    }

    public function store(Request $request)
{
    $request->validate([
        'nom' => 'required|string|max:255|unique:equipes,nom',
        'employees' => 'required|array',
        'employees.*' => 'exists:users,id'
    ]);

    // try {
        // Création de l'équipe avec compagnie_id
        $equipe = Equipe::create([
            'nom' => $request->nom,
            'company_id' => $request->company_id
        ]);

        // Assigner les employés à cette équipe
        $equipe->users()->sync($request->employees);

        return redirect()->route('equipe.index')->with('success', 'Équipe créée et employés assignés avec succès !');
    // } catch (\Exception $e) {
    //     return redirect()->back()->with('error', 'Une erreur est survenue lors de la création de l\'équipe.');
    // }
}



    // public function store(Request $request,Equipe $equipe){
    //     $request->validate([
    //         'nom' => 'required|string|max:255|unique:equipes,nom',
    //         'employees' => 'required|array',
    //         'employees.*' => 'exists:users,id'
    //     ]);
    //     $equipe = new Equipe();
    //     $equipe->nom=$request->nom;
    //     $equipe->save();
    //       // Créer l'équipe
    //       $equipe = Equipe::create(['nom' => $request->nom]);

    //     //   Assigner les employés à cette équipe
    //       User::whereIn('id', $request->employees)->update(['equipe_id' => $equipe->id]);

    //     return redirect()->back()->with('success', 'Équipe créée et employés assignés avec succès !');
    // }


    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'nom' => 'required|string|max:255|unique:equipes,nom',
    //         'employees' => 'required|array',
    //         'employees.*' => 'exists:users,id'
    //     ]);

    //     try {
    //         // Création de l'équipe
    //         $equipe = Equipe::create(['nom' => $request->nom]);

    //         // Assigner les employés à cette équipe dans la table pivot
    //         $equipe->users()->sync($request->employees);

    //         return redirect()->route('equipe.index')->with('success', 'Équipe créée et employés assignés avec succès !');
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Une erreur est survenue lors de la création de l\'équipe.');
    //     }
    // }




    public function index(){
        $equipe = Equipe::with('users')->get();
        return view('equipe.index', compact('equipe'));
    }

    public function show($id)
    {
        $equipe = Equipe::with('users')->findOrFail($id);

        // dd($equipe->users); // Débogage : voir si les employés sont récupérés

        return view('equipe.show', compact('equipe'));
    }

    public function detachEmployee($equipeId, $employeeId)
    {
        $equipe = Equipe::findOrFail($equipeId);
        $equipe->users()->detach($employeeId); // Suppression de la relation

        return redirect()->back()->with('success', 'Employé détaché de l’équipe avec succès.');
    }

    public function edit($id)
    {
        $user = Auth::user();
        // Récupérer l'équipe avec ses utilisateurs assignés
        $equipe = Equipe::findOrFail($id);
        $users_assignes = $equipe->users; // Liste des utilisateurs assignés à l'équipe

        // Récupérer les utilisateurs qui ne sont pas assignés à une équipe et qui n'ont pas d'horaires définis
        $users_sans_equipe = User::where('user_type', 'user')
            ->where('company_id', $user->company_id)
            ->whereDoesntHave('equipes')
            ->where('type_horaire','rotation')
            ->get();

        // Fusionner les utilisateurs assignés et ceux sans équipe
        $users_disponibles = $users_assignes->merge($users_sans_equipe);

        return view('equipe.edit', compact('equipe', 'users_assignes', 'users_disponibles'));
    }





    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'employees' => 'required|array',
            'employees.*' => 'exists:users,id',
        ]);

        // Récupérer l'équipe
        $equipe = Equipe::findOrFail($id);

        // Mise à jour du nom de l'équipe
        $equipe->nom = $request->input('nom');
        $equipe->save();

        // Synchroniser les employés
        $equipe->users()->sync($request->input('employees'));

        return redirect()->route('equipe.index')->with('success', 'L\'équipe a été mise à jour avec succès.');
    }




}
