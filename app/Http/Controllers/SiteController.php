<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\User;
use App\Models\Company;
use App\Models\Equipe;
use App\Models\Rotation;
use Illuminate\Http\Request;
use App\Models\SiteControllerUser;
use App\Services\OneSignalService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;


class SiteController extends Controller
{

    protected $oneSignal;

    public function __construct(OneSignalService $oneSignal)
    {
        $this->oneSignal = $oneSignal;
    }


    public function index(Request $request)
    {
        $search = $request->input('search');
        if (is_controller()) {


            // Récupérer les IDs des sites auxquels l'utilisateur est contrôleur
            $siteIds = SiteControllerUser::where('user_id', Auth::user()->id)
                ->pluck('site_id');

            // Charger les sites correspondants avec les relations
            $sites = Site::with(['company', 'customer', 'controller', 'users'])
                ->where('company_id', Auth::user()->company_id)
                ->whereIn('id', $siteIds) // Filtrer par les sites autorisés
                ->when($search, function ($query, $search) {
                    return $query->where('name', 'like', '%' . $search . '%');
                })->paginate(25);
        } else {
            $sites = Site::with(['company', 'customer', 'controller', 'users'])->where('company_id', Auth::user()->company_id)->when($search, function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })->paginate(25);
        }

        return view('sites.index', compact('sites'));
    }

    public function create()
    {
        $timezones = [];
        foreach (\DateTimeZone::listIdentifiers() as $timezone) {
            $timezones[$timezone] = $timezone;
        }

        $companies = Company::all();

        // Exclure les contrôleurs déjà assignés à un site de cette entreprise
        $assignedControllerIds = DB::table('site_controller')
            ->whereIn('site_id', Site::where('company_id', Auth::user()->company_id)->pluck('id'))
            ->pluck('user_id')
            ->toArray();

        // Exclure les employés déjà assignés à un site de cette entreprise
        $assignedUserIds = DB::table('site_user')
            ->whereIn('site_id', Site::where('company_id', Auth::user()->company_id)->pluck('id'))
            ->pluck('user_id')
            ->toArray();

        $all_controllers = User::where('user_type', 'controller')
            ->where('company_id', Auth::user()->company_id)
            ->whereNotIn('id', $assignedControllerIds)
            ->get();

        $all_users = User::where('user_type', 'user')
            ->where('company_id', Auth::user()->company_id)
            ->whereNotIn('id', $assignedUserIds)
            ->get();

        $controllers = $all_controllers->map(fn($c) => (object)[
            'id'         => $c->id,
            'name'       => $c->name,
            'first_name' => $c->first_name,
            'avatar'     => $c->avatar,
        ])->toArray();

        $users = $all_users->map(fn($u) => (object)[
            'id'         => $u->id,
            'name'       => $u->name,
            'first_name' => $u->first_name,
            'avatar'     => $u->avatar,
        ])->toArray();

        return view('sites.create', compact('companies', 'controllers', 'users', 'timezones'));
    }

    public function store(Request $request)
    {
        // Validation des données - customer_id devient optionnel
        $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'description' => 'nullable|string',
            'radius' => 'required|numeric|min:0',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'monday_checked' => 'sometimes|boolean',
            'start_time_monday' => 'date_format:H:i|required_if:monday_checked,true',
            'end_time_monday' => 'date_format:H:i|required_if:monday_checked,true|after:start_time_monday',
            'tuesday_checked' => 'sometimes|boolean',
            'start_time_tuesday' => 'date_format:H:i|required_if:tuesday_checked,true',
            'end_time_tuesday' => 'date_format:H:i|required_if:tuesday_checked,true|after:start_time_tuesday',
            'wednesday_checked' => 'sometimes|boolean',
            'start_time_wednesday' => 'date_format:H:i|required_if:wednesday_checked,true',
            'end_time_wednesday' => 'date_format:H:i|required_if:wednesday_checked,true|after:start_time_wednesday',
            'thursday_checked' => 'sometimes|boolean',
            'start_time_thursday' => 'date_format:H:i|required_if:thursday_checked,true',
            'end_time_thursday' => 'date_format:H:i|required_if:thursday_checked,true|after:start_time_thursday',
            'friday_checked' => 'sometimes|boolean',
            'start_time_friday' => 'date_format:H:i|required_if:friday_checked,true',
            'end_time_friday' => 'date_format:H:i|required_if:friday_checked,true|after:start_time_friday',
            'saturday_checked' => 'sometimes|boolean',
            'start_time_saturday' => 'date_format:H:i|required_if:saturday_checked,true',
            'end_time_saturday' => 'date_format:H:i|required_if:saturday_checked,true|after:start_time_saturday',
            'sunday_checked' => 'sometimes|boolean',
            'start_time_sunday' => 'date_format:H:i|required_if:sunday_checked,true',
            'end_time_sunday' => 'date_format:H:i|required_if:sunday_checked,true|after:start_time_sunday',
            'late_mark_duration' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        // Récupérer l'entreprise de l'utilisateur connecté
        $company = Auth::user()->company;

        // Vérifier la limite de sites selon le plan via l'abonnement actif
        $currentSitesCount = Site::where('company_id', $company->id)->count();
        $maxSites = $this->getMaxSitesBySubscription($company); // Correction ici

        // Vérifier si l'utilisateur peut ajouter un nouveau site
        if ($currentSitesCount >= $maxSites) {
            $planName = $this->getPlanName($company);
            return redirect()->back()
                ->withInput()
                ->with('error', "Vous avez atteint la limite de sites autorisés pour votre plan ($planName). Maximum: " . ($maxSites === 999999 ? 'Illimité' : $maxSites) . " sites.");
        }

        // Enregistrement sécurisé des données
        $site = new Site();
        $site->company_id = $company->id;
        $site->name = $request->input('name');
        $site->customer_id = null; // Pas de client, on met null
        $site->status = $request->input('status');
        $site->description = $request->input('description');
        $site->late_mark_duration = $request->input('late_mark_duration');
        $site->address = $request->input('address');
        $site->radius = $request->input('radius');
        $site->latitude = $request->input('latitude');
        $site->longitude = $request->input('longitude');
        $site->timezone = $request->input('timezone');
        $site->panier = $request->input('panier');

        // Générer un UUID unique pour le QR code
        $site->qr_code = (string) \Illuminate\Support\Str::uuid();

        // Ajout des jours
        foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
            $site->{$day . '_checked'} = $request->input($day . '_checked', false);
            $site->{'start_time_' . $day} = $request->input('start_time_' . $day);
            $site->{'end_time_' . $day} = $request->input('end_time_' . $day);
        }

        $site->save();

        // Liaison des employés
        if ($request->input('user_id')) {
            $syncResult = $site->users()->sync($request->input('user_id'));

            $attachedUsers = $syncResult['attached'];

            foreach ($attachedUsers as $userId) {
                $user = User::find($userId);
                if ($user && $user->one_signal_user_id) {
                    $this->oneSignal->sendToUser(
                        $user->one_signal_user_id,
                        "Nouveau site",
                        "Vous avez été ajouté à un site",
                        ['refresh_page' => 'contract']
                    );
                }
            }
        }

        if ($request->input('controller_id')) {
            $site->controller()->sync($request->input('controller_id'));
        }

        // Redirection en cas de succès
        return redirect()->route('sites.index')->with('success', 'Site créé avec succès.');
    }

/**
 * Déterminer le nombre maximum de sites autorisés selon le plan
 */
    private function getMaxSitesBySubscription($company)
    {
        // Récupérer le dernier abonnement actif
        $subscription = $company->subscription()
            ->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->first();

        if (!$subscription || !$subscription->plan) {
            return 2;
        }

        $planName = strtolower($subscription->plan->name);

        if ($planName === 'business') {
            return 999999;
        }

        return 2;
    }

    private function getPlanName($company)
    {
        $subscription = $company->activeSubscription;

        if (!$subscription || !$subscription->plan) {
            return 'Free';
        }

        return $subscription->plan->name;
    }



    public function show(string $id)
    {
        $site = Site::with(['company', 'customer', 'controller', 'users'])->findOrFail($id);


        return view('sites.show', compact('site'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Récupération du site
        $site = Site::findOrFail($id);

        // Récupération des utilisateurs pour les listes déroulantes
        $companies = Company::all();

        // Get timezones
        $timezones = \DateTimeZone::listIdentifiers();

        // Exclure les contrôleurs déjà assignés à d'autres sites de cette entreprise
        $assignedControllerIds = DB::table('site_controller')
            ->whereIn('site_id', Site::where('company_id', Auth::user()->company_id)
                ->where('id', '!=', $id) // Exclure le site en cours d'édition
                ->pluck('id'))
            ->pluck('user_id') // Utiliser 'user_id' comme dans votre create()
            ->toArray();

        // Exclure les employés déjà assignés à d'autres sites de cette entreprise
        $assignedUserIds = DB::table('site_user')
            ->whereIn('site_id', Site::where('company_id', Auth::user()->company_id)
                ->where('id', '!=', $id) // Exclure le site en cours d'édition
                ->pluck('id'))
            ->pluck('user_id') // Utiliser 'user_id' comme dans votre create()
            ->toArray();

        // Pour les contrôleurs - inclure ceux déjà assignés au site actuel
        $all_controllers = User::where('user_type', 'controller')
            ->where('company_id', Auth::user()->company_id)
            ->where(function($query) use ($site, $assignedControllerIds) {
                $query->whereNotIn('id', $assignedControllerIds)
                    ->orWhereIn('id', $site->controller->pluck('id')->toArray());
            })
            ->get();

        // Pour les employés - inclure ceux déjà assignés au site actuel
        $all_users = User::where('user_type', 'user')
            ->where('company_id', Auth::user()->company_id)
            ->where(function($query) use ($site, $assignedUserIds) {
                $query->whereNotIn('id', $assignedUserIds)
                    ->orWhereIn('id', $site->users->pluck('id')->toArray());
            })
            ->get();

        $controllers = $all_controllers->map(fn($c) => (object)[
            'id'         => $c->id,
            'name'       => $c->name,
            'first_name' => $c->first_name,
            'avatar'     => $c->avatar,
        ])->toArray();

        $users = $all_users->map(fn($u) => (object)[
            'id'         => $u->id,
            'name'       => $u->name,
            'first_name' => $u->first_name,
            'avatar'     => $u->avatar,
        ])->toArray();

        return view('sites.edit', compact('site', 'companies', 'controllers', 'users', 'timezones'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'description' => 'nullable|string',
            'radius' => 'required|numeric|min:0',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'timezone' => 'required',
            'panier' => 'nullable', // Rendre nullable si ce n'est pas requis
            'status' => 'required|in:active,inactive',

            'monday_checked' => 'sometimes|boolean',
            'start_time_monday' => 'date_format:H:i|required_if:monday_checked,true',
            'end_time_monday' => 'date_format:H:i|required_if:monday_checked,true|after:start_time_monday',

            'tuesday_checked' => 'sometimes|boolean',
            'start_time_tuesday' => 'date_format:H:i|required_if:tuesday_checked,true',
            'end_time_tuesday' => 'date_format:H:i|required_if:tuesday_checked,true|after:start_time_tuesday',

            'wednesday_checked' => 'sometimes|boolean',
            'start_time_wednesday' => 'date_format:H:i|required_if:wednesday_checked,true',
            'end_time_wednesday' => 'date_format:H:i|required_if:wednesday_checked,true|after:start_time_wednesday',

            'thursday_checked' => 'sometimes|boolean',
            'start_time_thursday' => 'date_format:H:i|required_if:thursday_checked,true',
            'end_time_thursday' => 'date_format:H:i|required_if:thursday_checked,true|after:start_time_thursday',

            'friday_checked' => 'sometimes|boolean',
            'start_time_friday' => 'date_format:H:i|required_if:friday_checked,true',
            'end_time_friday' => 'date_format:H:i|required_if:friday_checked,true|after:start_time_friday',

            'saturday_checked' => 'sometimes|boolean',
            'start_time_saturday' => 'date_format:H:i|required_if:saturday_checked,true',
            'end_time_saturday' => 'date_format:H:i|required_if:saturday_checked,true|after:start_time_saturday',

            'sunday_checked' => 'sometimes|boolean',
            'start_time_sunday' => 'date_format:H:i|required_if:sunday_checked,true',
            'end_time_sunday' => 'date_format:H:i|required_if:sunday_checked,true|after:start_time_sunday',

            'late_mark_duration' => 'nullable|integer|min:0',
        ]);

        $site = Site::findOrFail($id);

        try {
            // Mise à jour des données (sans customer_id)
            $site->name = $request->input('name');
            $site->status = $request->input('status');
            $site->description = $request->input('description');
            $site->late_mark_duration = $request->input('late_mark_duration');
            $site->address = $request->input('address');
            $site->radius = $request->input('radius');
            $site->latitude = $request->input('latitude');
            $site->longitude = $request->input('longitude');
            $site->timezone = $request->input('timezone');
            $site->panier = $request->input('panier');

            // Mise à jour des horaires
            foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
                $site->{$day . '_checked'} = $request->input($day . '_checked', false);
                $site->{'start_time_' . $day} = $request->input('start_time_' . $day);
                $site->{'end_time_' . $day} = $request->input('end_time_' . $day);
            }

            $site->save();

            // Liaison des employés
            if ($request->has('user_id')) {
                $syncResult = $site->users()->sync($request->input('user_id'));

                $attachedUsers = $syncResult['attached'];

                foreach ($attachedUsers as $userId) {
                    $user = User::find($userId);
                    if ($user && $user->one_signal_user_id) {
                        $this->oneSignal->sendToUser(
                            $user->one_signal_user_id,
                            "Nouveau site",
                            "Vous avez été ajouté à un site",
                            ['refresh_page' => 'contract']
                        );
                    }
                }
            }

            // Liaison des contrôleurs
            if ($request->has('controller_id')) {
                $site->controller()->sync($request->input('controller_id'));
            }

            // Redirection en cas de succès
            return redirect()->route('sites.index')->with('success', 'Site mis à jour avec succès.');

        } catch (\Exception $e) {
            // Gestion des exceptions
            return back()->with('error', 'Une erreur est survenue lors de la modification du site. Veuillez réessayer.')->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $site = Site::findOrFail($id);
        $site->delete();

        return response()->json(['success' => true, 'message' => 'Site supprimé avec succès.']);
    }

    public function qr(string $id)
    {
        $site = Site::findOrFail($id);

        $data = $site->qr_code;
        $text = $site->name;

        // Générer le QR code comme une image PNG
        $qrImage = QrCode::format('png')->size(200)->generate($data);

        // Convertir l'image finale en réponse téléchargeable
        return response()->stream(function () use ($qrImage) {
            echo $qrImage;
        }, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="qrcode.png"',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function disabled(Request $request, string $id)
    {

        $request->validate([
            'reason' => 'required|string',
        ]);

        $site = Site::findOrFail($id);

        try {
            if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'manager') {

                $site->status = $site->status === 'active' ? 'inactive' : 'active';
                $site->reason = $request->reason;
                $site->save();
            } else {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', __('Vous n\'êtes pas autorisé à désactiver cet utilisateur'));
            }

            return redirect()->back()->with('success', 'Utilisateur activé / désactivé avec succès.');
        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('Échec de la désactivation / activation de l\'utilisateur '));
        }
    }



}
