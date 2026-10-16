<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuManagementController extends Controller
{
    public function edit($compagnieId)
{
    $compagnie = Company::findOrFail($compagnieId);

    // Regrouper les menus par "groupe"
    // Assure-toi que la table "menus" a une colonne "group" (par exemple : "Gestion des employés", "Rapports", etc.)
    $menus = Menu::all()->groupBy('group');

    return view('superadmin.menu-management.edit', compact('compagnie', 'menus'));
}

public function update(Request $request, $compagnieId)
{
    $compagnie = Company::findOrFail($compagnieId);

    // Mise à jour des menus sélectionnés
    $compagnie->menus()->sync($request->menus ?? []);

    return redirect()->route('companies.index')->with('success', 'Menus mis à jour avec succès.');
}

}

