<?php

namespace App\Http\Controllers;

use App\Models\CotisationSociale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CotisationSocialeController extends Controller
{
    public function index()
    {
        $cotisations = CotisationSociale::where('company_id', Auth::user()->company_id)->get();
        return view('cotisations.index', compact('cotisations'));
    }

    public function create()
    {
        return view('cotisations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'libelle' => 'required|string',
            'taux_salarial' => 'required|numeric|min:0|max:100',
            'taux_employeur' => 'nullable|numeric|min:0|max:100',
        ]);

        CotisationSociale::create([
            'company_id' => Auth::user()->company_id,
            'libelle' => $request->libelle,
            'taux_salarial' => $request->taux_salarial,
            'taux_employeur' => $request->taux_employeur,
        ]);

        return redirect()->route('cotisations-sociales.index')->with('success', 'Cotisation ajoutée.');
    }

   public function edit($id)
    {
        // Chargement sécurisé : seulement les cotisations de la société connectée
        $cotisationSociale = CotisationSociale::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->firstOrFail();

        return view('cotisations.edit', compact('cotisationSociale'));
    }

    public function update(Request $request, $id)
    {
        $cotisationSociale = CotisationSociale::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->firstOrFail();

        $request->validate([
            'libelle' => 'required|string',
            'taux_salarial' => 'required|numeric|min:0|max:100',
            'taux_employeur' => 'nullable|numeric|min:0|max:100',
        ]);

        $cotisationSociale->update($request->only([
            'libelle',
            'taux_salarial',
            'taux_employeur'
        ]));

        return redirect()
            ->route('cotisations-sociales.index')
            ->with('success', 'Cotisation mise à jour.');
    }

    public function destroy($id)
    {
        $cotisationSociale = CotisationSociale::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->firstOrFail();

        $cotisationSociale->delete();

        return redirect()
            ->route('cotisations-sociales.index')
            ->with('success', 'Cotisation supprimée.');
    }
}
