<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Offre;
use App\Models\Company;
use App\Models\Fonction;
use Illuminate\Http\Request;
use App\Services\OneSignalService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\OffreStoreRequest;
use App\Http\Requests\OffreUpdateRequest;
use App\Models\SiteRotation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OffreController extends Controller
{
    protected $oneSignal;

    public function __construct(OneSignalService $oneSignal)
    {
        $this->oneSignal = $oneSignal;
    }

    public function create()
    {
        $user = Auth::user();
        $companies = Company::all();
        $fonction = Fonction::whereNull('company_id')
            ->orwhere('company_id', $user->company_id)
            ->get();
        $site = Site::where('company_id', $user->company_id)->get();
        $site_rotation = SiteRotation::where('company_id',$user->company_id)->get();
        return view('offres.create', compact('fonction', 'site', 'companies','site_rotation'));
    }

    public function store(OffreStoreRequest $req, Offre $offre)
    {
        $offre = new Offre;
        $offre->company_id = $req->company_id;
        $offre->site_id = $req->site_id;
        $offre->site_rotation_id = $req->site_rotation_id;
        $offre->poste_id = $req->poste_id;
        $offre->taux_horaire = $req->taux_horaire;
        $offre->nombre_personne_recherche = $req->nombre_personne_recherche;
        $offre->type_horaire = $req->type_horaire;
        $offre->demarrage = $req->demarrage;
        $offre->start_date = $req->demarrage === 'immediat' ? null : $req->start_date;
        $offre->description_offre = $req->description_offre;

        $offre->save();

        // Récupérer les utilisateurs de la même entreprise avec la même fonction
        $users = User::where('company_id', $offre->company_id)
            ->where('employee_function', $offre->poste_id)
            ->where('status', 'active')
            ->get();

        // Envoyer la notification uniquement aux utilisateurs concernés
        foreach ($users as $user) {
            if ($user->one_signal_user_id) {
                $this->oneSignal->sendToUser(
                    $user->one_signal_user_id,
                    "Nouvelle offre",
                    "Une nouvelle offre correspondant à votre poste est disponible.",
                    [
                        'refresh_page' => 'notification',
                    ]
                );
            }
        }
        return to_route('offres.Liste')->with('success', 'Offre créer avec succès');
    }

    public function index()
    {
        // $offre = Offre::where('company_id',Auth::user()->company_id)
        // ->join('sites','sites.id','=','offres.site_id')
        // ->join('fonctions','fonctions.id','=','offres.poste_id')
        // ->join('companies','companies.id','=','offres.company_id')
        // ->select('fonctions.nom_fonction','fonctions.id','sites.name','sites.id','offres.*')
        // ->get();
       $user = Auth::user();

        $offre = DB::table('offres')
            ->leftJoin('sites', function ($join) {
                $join->on('sites.id', '=', 'offres.site_id')
                    ->where('offres.type_horaire', '=', 'fixe');
            })
            ->leftJoin('site_rotation', function ($join) {
                $join->on('site_rotation.id', '=', 'offres.site_rotation_id')
                    ->where('offres.type_horaire', '=', 'rotation');
            })
            ->join('fonctions', 'fonctions.id', '=', 'offres.poste_id')
            ->join('companies', 'companies.id', '=', 'offres.company_id')
            ->where('offres.company_id', $user->company_id)
            ->select(
                'fonctions.nom_fonction',
                'fonctions.id as fonction_id',
                DB::raw("COALESCE(sites.name, site_rotation.name) as site_name"),
                DB::raw("COALESCE(sites.id, site_rotation.id) as site_id"),
                'offres.*'
            )
            ->orderByDesc('offres.created_at')
            ->get();
                return view('offres.index', compact('offre'));
        }


    public function edit(Offre $offre)
    {
        $user = Auth::user();
        $site = Site::where('company_id', $user->company_id)->get();
        $fonction = Fonction::whereNull('company_id')
            ->orwhere('company_id', $user->company_id)
            ->get();
        return view('offres.edit', ['offre' => $offre], compact('site', 'fonction'));
    }

    public function update(OffreUpdateRequest $request, Offre $offre)
    {
        $offre->site_id = $request->site_id;
        $offre->poste_id = $request->poste_id;
        $offre->taux_horaire = $request->taux_horaire;
        $offre->nombre_personne_recherche = $request->nombre_personne_recherche;
        $offre->demarrage = $request->demarrage;
        $offre->start_date = $request->demarrage === 'immediat' ? null : $request->start_date;
        $offre->description_offre = $request->description_offre;
        $offre->save();
        return redirect()->route('offres.Liste')->with('success', 'Offres modifier avec succès');
    }

    public function destroy(Offre $offre)
    {
        $offre->delete();
        return redirect()->back()->with('success', 'Offres supprimer avec succès');
    }

    public function grouper()
    {
        $user = Auth::user();
        $offres = Offre::with(['site', 'fonction', 'company'])
            ->whereNotNull('poste_id')
            ->where('offres.company_id', $user->company_id)
            ->orderBy('site_id')
            ->get()
            ->groupBy('site_id');
        return view('offres.offreParSite', compact('offres'));
    }
}
