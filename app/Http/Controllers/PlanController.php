<?php
namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    /**
     * INDEX : Afficher la liste de tous les plans
     */
    public function index()
    {
        // On récupère tous les plans avec leurs fonctionnalités chargées (Eager Loading)
        // $plans = Plan::with('features')->get();

        $plans  = Plan::with('features')->paginate(10);

        return view('plans.index', compact('plans'));
    }

    /**
     * CREATE : Afficher le formulaire de création d'un plan
     */
    public function create()
    {
        $features = Feature::where('is_active', true)->get();

        return view('plans.create', compact('features'));
    }

    /**
     * STORE : Enregistrer un nouveau plan
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:plans,name',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'max_employees' => 'required|integer|min:-1',
            'dashboard_type' => 'required|string|in:Basique,Avancé,Complet',
            'history_duration' => 'required|string|max:255',
            'support_type' => 'required|string|in:Email,WhatsApp,Prioritaire,Dédié',
            'features' => 'required|array',
            'features.*' => 'exists:features,id',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $plan = Plan::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'price' => $validated['price'],
            'currency' => $validated['currency'],
            'max_employees' => $validated['max_employees'],
            'dashboard_type' => $validated['dashboard_type'],
            'history_duration' => $validated['history_duration'],
            'support_type' => $validated['support_type'],
        ]);

        // Attacher les fonctionnalités dans la table pivot
        $plan->features()->attach($request->features);

        return redirect()->route('plans.index')->with('success', 'Le plan a été créé avec succès !');
    }

    /**
     * EDIT : Afficher le formulaire de modification d'un plan
     */
    public function edit(Plan $plan)
    {
        $features = Feature::where('is_active', true)->get();

        // On récupère les IDs des fonctionnalités déjà rattachées à ce plan pour les pré-cocher
        $planFeaturesIds = $plan->features->pluck('id')->toArray();

        return view('plans.edit', compact('plan', 'features', 'planFeaturesIds'));
    }

    /**
     * UPDATE : Enregistrer les modifications du plan
     */
    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:plans,name,' . $plan->id,
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'max_employees' => 'required|integer|min:-1',
            'dashboard_type' => 'required|string|in:Basique,Avancé,Complet',
            'history_duration' => 'required|string|max:255',
            'support_type' => 'required|string|in:Email,WhatsApp,Prioritaire,Dédié',
            'features' => 'required|array',
            'features.*' => 'exists:features,id',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $plan->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'price' => $validated['price'],
            'currency' => $validated['currency'],
            'max_employees' => $validated['max_employees'],
            'dashboard_type' => $validated['dashboard_type'],
            'history_duration' => $validated['history_duration'],
            'support_type' => $validated['support_type'],
        ]);

        // `sync` va automatiquement supprimer les anciennes fonctionnalités retirées
        // et ajouter les nouvelles cochées dans la table pivot
        $plan->features()->sync($request->features);

        return redirect()->route('plans.index')->with('success', 'Le plan a été mis à jour avec succès !');
    }

    /**
     * DESTROY : Supprimer un plan
     */
    public function destroy(Plan $plan)
    {
        // Grâce à onDelete('cascade') défini dans notre migration pivot,
        // les liaisons dans feature_plan seront automatiquement nettoyées.
        $plan->delete();

        return redirect()->route('plans.index')->with('success', 'Le plan a été supprimé avec succès !');
    }
}
