<?php

namespace App\Http\Controllers;

use App\Http\Requests\FonctionStoreRequest;
use App\Http\Requests\FonctionUpdateRequest;
use App\Models\Fonction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\FonctionUpdated;
use App\Events\FonctionCreated;

class FonctionController extends Controller
{
    public function create()
    {
        // Plus besoin de récupérer les compagnies
        return view('fonctions.create');
    }

    public function store(FonctionStoreRequest $request)
    {
        $fonction = new Fonction;
        // On ne set plus company_id
        $fonction->nom_fonction = $request->nom_fonction;
        $fonction->description_fonction = $request->description_fonction;
        $fonction->save();

        return redirect()->back()->with('success', $fonction->nom_fonction . ' a été ajouté avec succès');
    }

    public function index(Request $request)
    {
        // Récupérer toutes les fonctions classées par ordre alphabétique
        $fonction = Fonction::orderBy('nom_fonction', 'asc')->paginate(7);

        if ($request->ajax()) {
            return view('fonctions._tableBody', compact('fonction'));
        }

        return view('fonctions.index', compact('fonction'));
    }

    public function search(Request $request)
    {
        $search = $request->query('filtrerFonction');

        if ($search) {
            $fonction = Fonction::where('nom_fonction', 'like', "%{$search}%")
                ->orderByDesc('created_at')
                ->paginate(10);
        } else {
            $fonction = Fonction::orderByDesc('created_at')->paginate(10);
        }

        return view('fonctions.index', compact('fonction'));
    }

    public function edit(Fonction $fonction)
    {
        return view('fonctions.edit', ['fonction' => $fonction]);
    }

    public function update(FonctionUpdateRequest $req, Fonction $fonction)
    {
        $fonction->nom_fonction = $req->nom_fonction;
        $fonction->description_fonction = $req->description_fonction;
        $fonction->update();

        broadcast(new FonctionUpdated($fonction))->toOthers();

        return to_route('fonctions.liste')->with('success', $fonction->nom_fonction . ' a été modifié avec succès');
    }

    public function delete(Fonction $fonction)
    {
        try {
            $nom = $fonction->nom_fonction;
            $fonction->delete();

            broadcast(new FonctionUpdated($fonction))->toOthers();

            // ✅ Retourner une réponse JSON pour AJAX
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $nom . ' a été supprimé avec succès'
                ]);
            }

            return redirect()->back()->with('success', $nom . ' a été supprimé avec succès');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la suppression'
                ], 500);
            }
            return redirect()->back()->with('error', 'Erreur lors de la suppression');
        }
    }
}
