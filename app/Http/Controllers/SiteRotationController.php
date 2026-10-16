<?php

namespace App\Http\Controllers;

use App\Models\Equipe;
use App\Models\Rotation;
use App\Models\SiteRotation;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class SiteRotationController extends Controller
{
    /**
     * Display a listing of the resource.
     */

public function index()
{
    $sites = SiteRotation::with('company')
        ->where('company_id', Auth::user()->company_id)
        ->paginate(10);

    return view('site_rotation.index', compact('sites'));
}

public function create()
{
    $timezones = [];
    foreach (\DateTimeZone::listIdentifiers() as $timezone) {
        $timezones[$timezone] = $timezone;
    }
    return view('site_rotation.create',compact('timezones'));
}


public function qrCode(SiteRotation $site)
{
    $qr = QrCode::size(300)->generate(json_encode([
        'site_id' => $site->id,
        'latitude' => $site->latitude,
        'longitude' => $site->longitude,
        'qr_code' => $site->qr_code,
    ]));

    return view('site_rotation.qr', compact('site', 'qr'));
}

public function store(Request $request)
{
    $data = $request->validate([
        'name' => 'required|string',
        'address' => 'required|string',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'radius' => 'required|numeric',
        'company_id' => 'required|exists:companies,id',
        // Ne pas valider qr_code ici car il est généré automatiquement
        'heure_debut_matin' => 'nullable',
        'heure_fin_matin' => 'nullable',
        'heure_debut_apres_midi' => 'nullable',
        'heure_fin_apres_midi' => 'nullable',
        'heure_debut_nuit' => 'nullable',
        'heure_fin_nuit' => 'nullable',
    ]);

    // Ajouter les champs générés
    $data['qr_code'] = 'SR-' . strtoupper(Str::random(10)); // Utilise `Str` correctement
    $data['status'] = 'active';

    SiteRotation::create($data);

    return redirect()->route('site_rotation.index')->with('success', 'Site rotatif créé avec succès.');
}


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $site = SiteRotation::findOrFail($id);
        return view('site_rotation.show', compact('site'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
         $timezones = [];
        foreach (\DateTimeZone::listIdentifiers() as $timezone) {
            $timezones[$timezone] = $timezone;
        }
        $siteRotation = SiteRotation::findOrFail($id);
        return view('site_rotation.edit', compact('siteRotation','timezones'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $site = SiteRotation::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|numeric',
            'heure_debut_matin' => 'nullable',
            'heure_fin_matin' => 'nullable',
            'heure_debut_apres_midi' => 'nullable',
            'heure_fin_apres_midi' => 'nullable',
            'heure_debut_nuit' => 'nullable',
            'heure_fin_nuit' => 'nullable',
            'late_mark_duration' => 'nullable|numeric',
        ]);

        $site->update($data);

        return redirect()->route('site_rotation.index')->with('success', 'Site mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $site = SiteRotation::findOrFail($id);
        $site->delete();

        return redirect()->route('site_rotation.index')->with('success', 'Site supprimé avec succès.');
    }

    public function disabled($id)
    {
        $site = SiteRotation::findOrFail($id);
        $site->actif = !$site->actif;
        $site->save();

        return redirect()->back()->with('success', 'Le site a été mis à jour avec succès.');
    }


        public function assignEquipesToSite(Request $request)
    {
        // Validation des entrées
        $request->validate([
            'site_id' => 'required|exists:site_rotation,id',
            'rotation_id' => 'required|exists:rotations,id',
            'equipes' => 'required|array',
            'equipes.*' => 'exists:equipes,id',
        ]);

        $site = SiteRotation::findOrFail($request->site_id);
        $rotation = Rotation::findOrFail($request->rotation_id);

        // Assigner la rotation au site (table pivot rotation_site)
        $site->rotations()->syncWithoutDetaching([$rotation->id]);

        // Assigner les équipes au site (table pivot site_equipe)
        $site->equipes()->syncWithoutDetaching($request->equipes);

        // Assigner les équipes à la rotation (table pivot rotation_equipe)
        foreach ($request->equipes as $equipe_id) {
            $equipe = Equipe::find($equipe_id);
            if ($equipe) {
                $equipe->rotations()->syncWithoutDetaching([$rotation->id]);
            }
        }

        return redirect()->back()->with('success', 'Les équipes et la rotation ont été assignées au site avec succès.');
    }

    public function VoireFormulaire()
    {
         $sites = SiteRotation::with('company')
        ->where('company_id', Auth::user()->company_id)
        ->get();

        // Récupérer les équipes qui ne sont assignées à aucun site
        $equipes = Equipe::whereDoesntHave('sites')->get();
         // Récupérer toutes les rotations disponibles
        $rotations = Rotation::where('company_id', Auth::user()->company_id)->get();

        return view('site_rotation.assign_equipes', compact('sites', 'equipes','rotations'));
    }


    public function getEquipes($siteId)
    {
        // Trouver le site avec ses équipes
        $site = SiteRotation::with('equipes')->find($siteId);

        return response()->json([
            'equipes' => $site ? $site->equipes : []
        ]);
    }

// public function editAssignForm($site_id)
// {
//     $rotations = Rotation::all();
//     $site = SiteRotation::with('rotations')->findOrFail($site_id); // Ajout de la relation rotation

//     // Récupérer toutes les équipes assignées à ce site
//     $equipes = $site->equipes()->with('users', 'rotations')->get();

//     // Récupérer les utilisateurs qui ne sont pas déjà assignés à une équipe
//     $users = User::where('user_type', 'user')
//     ->where('company_id', Auth::user()->company_id)
//     ->where(function ($query) use ($equipes) {
//         $query->whereDoesntHave('equipes')
//             ->orWhereHas('equipes', function ($q) use ($equipes) {
//                 $q->whereIn('equipes.id', $equipes->pluck('id'));
//             });
//     })
//     ->get();
//     // Récupérer les rotations assignées aux équipes de ce site
//     $rotations = $equipes->flatMap(function ($equipe) {
//         return $equipe->rotations; // Récupérer les rotations de chaque équipe
//     })->unique('id'); // S'assurer qu'il n'y a pas de doublons

//     // Ajouter la rotation du site aux rotations disponibles si elle n'est pas déjà dedans
//     if ($site->rotation && !$rotations->contains('id', $site->rotation->id)) {
//         $rotations->push($site->rotation);
//     }

//     // Récupérer les employés déjà assignés à chaque équipe
//     $usersAssigned = [];
//     foreach ($equipes as $equipe) {
//         $usersAssigned[$equipe->id] = $equipe->users->pluck('id')->toArray();
//     }

//     return view('site_rotation.edit_assign_equipes', compact('site', 'users', 'equipes', 'usersAssigned', 'rotations'));
// }



public function editAssignForm($site_id)
{
    $site = SiteRotation::with('rotation', 'equipes.rotations', 'equipes.users')->findOrFail($site_id);

    // Récupérer toutes les rotations (pour le select)
    $rotations = Rotation::all();

    // Ici tu récupères les équipes assignées au site
    $equipes = $site->equipes;

    // Utilisateurs qui ne sont pas assignés à une équipe de ce site
    $users = User::where('user_type', 'user')
        ->where('company_id', Auth::user()->company_id)
        ->where(function ($query) use ($equipes) {
            $query->whereDoesntHave('equipes')
                ->orWhereHas('equipes', function ($q) use ($equipes) {
                    $q->whereIn('equipes.id', $equipes->pluck('id'));
                });
        })->get();

    // Emplacements des utilisateurs déjà assignés à chaque équipe
    $usersAssigned = [];
    foreach ($equipes as $equipe) {
        $usersAssigned[$equipe->id] = $equipe->users->pluck('id')->toArray();
    }

    return view('site_rotation.edit_assign_equipes', compact('site', 'users', 'equipes', 'usersAssigned', 'rotations'));
}


    // public function updateAssignEquipesToSite(Request $request, $site_id)
    // {
    //     // Validation des données
    //     $request->validate([
    //         'equipes' => 'required|array',
    //         'equipes.*' => 'exists:equipes,id',
    //         'rotation_id' => 'required|exists:rotations,id',
    //     ]);

    //     // Récupération des modèles
    //     $site = SiteRotation::findOrFail($site_id);
    //     $rotation = Rotation::findOrFail($request->rotation_id);

    //     // 🔹 Mettre à jour les équipes assignées au site
    //     $site->equipes()->sync($request->equipes);

    //     // 🔹 Assigner la rotation à toutes les équipes sélectionnées
    //     foreach ($request->equipes as $equipe_id) {
    //         $equipe = Equipe::findOrFail($equipe_id);

    //         // Vérifie si la rotation est déjà assignée pour éviter les doublons
    //         $equipe->rotations()->syncWithoutDetaching([$rotation->id]);
    //     }

    //     return to_route('site_rotation.voireSite')->with('success', 'Les équipes ont été mises à jour avec succès.');
    // }



public function updateAssignEquipesToSite(Request $request, $site_id)
{
    // Validation des données
    $request->validate([
        'equipes' => 'required|array',
        'equipes.*' => 'exists:equipes,id',
        'rotation_id' => 'required|exists:rotations,id',
    ]);

    // Récupération des modèles
    $site = SiteRotation::findOrFail($site_id);
    $rotation = Rotation::findOrFail($request->rotation_id);

    // 🔹 Mettre à jour les équipes assignées au site
    $site->equipes()->sync($request->equipes);

    // 🔹 Assigner la rotation à toutes les équipes sélectionnées
    foreach ($request->equipes as $equipe_id) {
        $equipe = Equipe::findOrFail($equipe_id);

        // Evite les doublons dans la table pivot rotation_equipe
        $equipe->rotations()->syncWithoutDetaching([$rotation->id]);
    }

    // 🔹 Mettre à jour la rotation principale du site
    $site->rotation_id = $rotation->id;
    $site->save();

    return to_route('site_rotation.voireSite')->with('success', 'Les équipes et la rotation ont été mises à jour avec succès.');
}


    public function supprimer($siteId, $equipeId)
    {
       // Récupérer le site et l'équipe associée
        $site = SiteRotation::findOrFail($siteId);
        $equipe = Equipe::findOrFail($equipeId);

        // Supprimer les relations dans la table pivot `rotation_equipe`
        DB::table('rotation_equipe')->where('equipe_id', $equipeId)->delete();

        // Détacher l'équipe des employés (table pivot Equipe_user)
        $equipe->users()->detach();

        // Supprimer l'équipe
        $equipe->delete();

        // Supprimer l'association entre le site et l'équipe si nécessaire
        $site->equipes()->detach($equipeId);

        return redirect()->route('site_rotation.voireSite')->with('success', 'Equipe supprimée avec succès.');
    }



    public function voireSite()
    {
        // Récupérer tous les sites
        $sites = SiteRotation::with('rotations')->get();

        // Passer les sites à la vue
        return view('site_rotation.detail', compact('sites'));
    }
}
