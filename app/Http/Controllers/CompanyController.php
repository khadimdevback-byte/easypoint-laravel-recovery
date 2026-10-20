<?php

namespace App\Http\Controllers;

use DateTimeZone;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Notifications\SendCreateCompanyEmailNotification;
use Illuminate\Support\Facades\Log;
use App\Models\Plan;
use App\Mail\CompanyWelcomeMail;
use App\Models\PaymentArchive;
use Illuminate\Support\Facades\Mail;
use App\Services\PaymentArchiveService;

class CompanyController extends Controller
{
    protected $archiveService;

    public function __construct(PaymentArchiveService $archiveService)
    {
        $this->archiveService = $archiveService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::paginate(25);
        return view('companies.index', compact('companies'));
    }

        /**
     * Display a listing of the resource.
     */
    public function pendingIndex()
    {
        $companies = Company::where('status', 'pending')->paginate(25);
        return view('companies.pending_index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $timezones = [];
        foreach (\DateTimeZone::listIdentifiers() as $timezone) {
            $timezones[$timezone] = $timezone;
        }

        return view('companies.create', compact('timezones'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'compagny_name' => 'required|string',
            'compagny_email' => 'required|string|email|unique:companies',
            'phone' => 'nullable|string',
            'country'=> 'required|string',
            'currency'=> 'required|string',
            'website' => 'nullable|string',
            'timezone' => 'nullable|string',
            'address' => 'nullable|string',
            'password' => 'required|string|min:8',
            'logo' => 'nullable|file|mimetypes:image/*|max:2048',
        ]);

        // Mot de passe en clair pour l'email
        $plainPassword = $request->password;

        $company = new Company([
            'compagny_name' => $request->compagny_name,
            'compagny_email' => $request->compagny_email,
            'phone' => $request->phone,
            'country' => $request->country,
            'currency' => $request->currency,
            'website' => $request->website,
            'timezone' => $request->timezone,
            'address' => $request->address,
            'status' => 'active', // ← FORCER LE STATUT À ACTIF
        ]);

        $company->password = Hash::make($request->password);

        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            $avatarsDirectory = 'avatars';
            if (!Storage::disk('public')->exists($avatarsDirectory)) {
                Storage::disk('public')->makeDirectory($avatarsDirectory);
            }
            $filename = 'logo_' . time() . '.' . $request->file('logo')->getClientOriginalExtension();
            $company->logo = $request->file('logo')->storeAs($avatarsDirectory, $filename, 'public');
        }

        // Enregistrement de l'admin
        $user = new User([
            'user_type' => 'admin',
            'name' => $request->compagny_name,
            'email' => $request->compagny_email,
            'status' => 'active', // ← FORCER LE STATUT À ACTIF
            'avatar' => $company->logo,
        ]);
        $user->password = Hash::make($request->password);

        try {
            $company->save();
            $user->company_id = $company->id;
            $user->save();

            // Envoi de l'email
            try {
                Mail::to($company->compagny_email)->send(new CompanyWelcomeMail($company, $plainPassword));
                Log::info('Email de bienvenue envoyé à la société: ' . $company->compagny_email);
            } catch (\Exception $e) {
                Log::error('Erreur lors de l\'envoi de l\'email à la société: ' . $e->getMessage());
            }

            return redirect()
                ->route('companies.index')
                ->with('success', __('Compagnie créée avec succès. Un email a été envoyé avec les identifiants.'));

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('Échec de la création de la compagnie'));
        }
    }


    public function storeCompany(Request $request)
    {
        // Validation des données
        $validatedData = $request->validate([
            'compagny_name' => 'required|string|max:255',
            'compagny_email' => 'required|string|email|unique:companies',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'timezone' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'password' => 'required|string|min:8',
            'country' => 'required|string|max:100',
            'currency' => 'required|string|max:3',
            'logo' => 'nullable|file|mimetypes:image/*|max:2048',
            'plan_id' => 'required|exists:plans,id',
            'duration_months' => 'required|integer|in:1,12',

        ]);

        // Mot de passe en clair pour l'email
        $plainPassword = $validatedData['password'];

        // Création de l'entreprise
        $company = new Company([
            'compagny_name' => $validatedData['compagny_name'],
            'compagny_email' => $validatedData['compagny_email'],
            'phone' => $validatedData['phone'] ?? null,
            'country' => $validatedData['country'],
            'currency' => $validatedData['currency'],
            'website' => $validatedData['website'] ?? null,
            'timezone' => $validatedData['timezone'] ?? null,
            'address' => $validatedData['address'] ?? null,
            'password' => Hash::make($validatedData['password']),
            'status' => 'active',
        ]);

        // Gestion du logo
        if ($request->hasFile('logo')) {
            $logoFile = $request->file('logo');
            $avatarsDirectory = 'avatars';

            if (!Storage::disk('public')->exists($avatarsDirectory)) {
                Storage::disk('public')->makeDirectory($avatarsDirectory);
            }

            $filename = 'logo_' . time() . '_' . uniqid() . '.' . $logoFile->getClientOriginalExtension();
            $company->logo = $logoFile->storeAs($avatarsDirectory, $filename, 'public');
        }

        $company->save();

        // Création de l'utilisateur admin
        $user = new User([
            'user_type' => 'admin',
            'name' => $validatedData['compagny_name'],
            'email' => $validatedData['compagny_email'],
            'avatar' => $company->logo ?? null,
        ]);

        $user->password = Hash::make($validatedData['password']);
        $user->status = 'active';
        $user->company_id = $company->id;
        $user->save();

        // ═══════════════════════════════════════════════
        // ENVOI DE L'EMAIL À LA SOCIÉTÉ
        // ═══════════════════════════════════════════════
        try {
            Mail::to($company->compagny_email)->send(new CompanyWelcomeMail($company, $plainPassword));
            Log::info('Email de bienvenue envoyé à la société: ' . $company->compagny_email);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'email à la société: ' . $e->getMessage());
        }

        // Récupération du plan
        $plan = Plan::findOrFail($request->plan_id);
        $months = intval($request->duration_months);
        $totalAmount = $plan->price * $months;
        $startsAt = now();
        $endsAt = now()->addMonths($months);
        $subscriptionStatus = ($plan->price == 0) ? 'active' : 'pending_payment';

        // Création de l'abonnement
        $company->subscription()->create([
            'plan_id'         => $plan->id,
            'duration_months' => $months,
            'total_amount'    => $totalAmount,
            'starts_at'       => $startsAt,
            'ends_at'         => $endsAt,
            'status'          => $subscriptionStatus,
        ]);

        // Notification aux super admins
        $superAdmins = User::where('user_type', 'super_admin')->get();

        if ($superAdmins->isNotEmpty()) {
            $companyData = [
                'compagny_name' => $company->compagny_name,
                'compagny_email' => $company->compagny_email,
                'phone' => $company->phone,
                'country' => $company->country,
                'currency' => $company->currency,
                'website' => $company->website,
                'address' => $company->address,
                'status' => $company->status,
                'timezone' => $company->timezone,
                'plan_name' => $plan->name,
                'plan_price' => $plan->price,
            ];

            foreach ($superAdmins as $superAdmin) {
                try {
                    $superAdmin->notify(new SendCreateCompanyEmailNotification($companyData));
                } catch (\Exception $e) {
                    Log::error('Failed to send notification to super admin: ' . $superAdmin->email, [
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        // ═══════════════════════════════════════════════
        // REDIRECTION AVEC NOTIFICATION
        // ═══════════════════════════════════════════════

        $planName = $plan->name;
        $isFree = ($plan->price == 0);

        if ($isFree) {
            return redirect()
                ->route('siteVitrine.demonstration')
                ->with('registration_success', true)
                ->with('registration_type', 'free')
                ->with('registration_plan_name', $planName)
                ->with('registration_email', $company->compagny_email)
                ->with('registration_company_id', $company->id)
                ->with('success', 'Votre entreprise a été créée avec succès ! Un email vous a été envoyé avec vos identifiants.');
        } else {
            return redirect()
                ->route('siteVitrine.demonstration')
                ->with('registration_success', true)
                ->with('registration_type', 'pending_payment')
                ->with('registration_plan_name', $planName)
                ->with('registration_plan_price', $plan->price)
                ->with('registration_email', $company->compagny_email)
                ->with('registration_company_id', $company->id)
                ->with('welcome_message', 'Bienvenue sur votre dashboard EasyPoint !')
                ->with('info', 'Votre entreprise est enregistrée ! Un email vous a été envoyé avec vos identifiants. Veuillez procéder au paiement pour activer votre offre.');
        }
    }

    // public function storeCompany(Request $request)
    // {
    //     // Validation des données
    //     $validatedData = $request->validate([
    //         'compagny_name' => 'required|string|max:255',
    //         'compagny_email' => 'required|string|email|unique:companies',
    //         'phone' => 'nullable|string|max:20',
    //         'website' => 'nullable|url|max:255',
    //         'timezone' => 'nullable|string|max:255',
    //         'address' => 'nullable|string|max:500',
    //         'password' => 'required|string|min:8',
    //         'country' => 'required|string|max:100',
    //         'currency' => 'required|string|max:3',
    //         'logo' => 'nullable|file|mimetypes:image/*|max:2048',
    //         'plan_id' => 'required|exists:plans,id',
    //         'duration_months' => 'required|integer|in:1,12',
    //         'plan_name' => 'nullable|string|max:255',
    //         'plan_price' => 'nullable|numeric',
    //         'is_free' => 'nullable|boolean',
    //     ]);

    //     // Création de l'entreprise
    //     $company = new Company([
    //         'compagny_name' => $validatedData['compagny_name'],
    //         'compagny_email' => $validatedData['compagny_email'],
    //         'phone' => $validatedData['phone'] ?? null,
    //         'country' => $validatedData['country'],
    //         'currency' => $validatedData['currency'],
    //         'website' => $validatedData['website'] ?? null,
    //         'timezone' => $validatedData['timezone'] ?? null,
    //         'address' => $validatedData['address'] ?? null,
    //         'password' => Hash::make($validatedData['password']),
    //         'status' => 'active',
    //     ]);

    //     // Gestion du logo
    //     if ($request->hasFile('logo')) {
    //         $logoFile = $request->file('logo');
    //         $avatarsDirectory = 'avatars';

    //         if (!Storage::disk('public')->exists($avatarsDirectory)) {
    //             Storage::disk('public')->makeDirectory($avatarsDirectory);
    //         }

    //         $filename = 'logo_' . time() . '_' . uniqid() . '.' . $logoFile->getClientOriginalExtension();
    //         $company->logo = $logoFile->storeAs($avatarsDirectory, $filename, 'public');
    //     }

    //     $company->save();

    //     // Création de l'utilisateur admin
    //     $user = new User([
    //         'user_type' => 'admin',
    //         'name' => $validatedData['compagny_name'],
    //         'email' => $validatedData['compagny_email'],
    //         'avatar' => $company->logo ?? null,
    //     ]);

    //     $user->password = Hash::make($validatedData['password']);
    //     $user->status = 'active';
    //     $user->company_id = $company->id;
    //     $user->save();

    //     // Récupération du plan
    //     $plan = Plan::findOrFail($request->plan_id);
    //     $months = intval($request->duration_months);
    //     $totalAmount = $plan->price * $months;
    //     $startsAt = now();
    //     $endsAt = now()->addMonths($months);

    //     // Déterminer le statut de l'abonnement
    //     $isFree = $request->input('is_free', false) || $plan->price == 0;
    //     $subscriptionStatus = $isFree ? 'active' : 'pending_payment';

    //     // Création de l'abonnement
    //     $company->subscription()->create([
    //         'plan_id'         => $plan->id,
    //         'duration_months' => $months,
    //         'total_amount'    => $totalAmount,
    //         'starts_at'       => $startsAt,
    //         'ends_at'         => $endsAt,
    //         'status'          => $subscriptionStatus,
    //     ]);

    //     // Notification aux super admins
    //     $superAdmins = User::where('user_type', 'super_admin')->get();

    //     if ($superAdmins->isNotEmpty()) {
    //         $companyData = [
    //             'compagny_name' => $company->compagny_name,
    //             'compagny_email' => $company->compagny_email,
    //             'phone' => $company->phone,
    //             'country' => $company->country,
    //             'currency' => $company->currency,
    //             'website' => $company->website,
    //             'address' => $company->address,
    //             'status' => $company->status,
    //             'timezone' => $company->timezone,
    //             'plan_name' => $plan->name,
    //             'plan_price' => $plan->price,
    //             'is_free' => $isFree,
    //         ];

    //         foreach ($superAdmins as $superAdmin) {
    //             try {
    //                 $superAdmin->notify(new SendCreateCompanyEmailNotification($companyData));
    //             } catch (\Exception $e) {
    //                 Log::error('Failed to send notification to super admin: ' . $superAdmin->email, [
    //                     'error' => $e->getMessage()
    //                 ]);
    //             }
    //         }
    //     }

    //     // ═══════════════════════════════════════════════
    //     // REDIRECTION AVEC NOTIFICATION
    //     // ═══════════════════════════════════════════════

    //     $planName = $plan->name;

    //     if ($isFree) {
    //         // Plan GRATUIT → Rediriger vers la même page avec notification
    //         return redirect()
    //             ->route('demonstration.result', [
    //                 'status' => 'success',
    //                 'type' => 'free',
    //                 'plan_name' => $planName,
    //                 'company_id' => $company->id,
    //                 'email' => $company->compagny_email
    //             ])
    //             ->with('registration_success', true);
    //     } else {
    //         // Plan PAYANT → Rediriger vers la même page avec notification de paiement
    //         return redirect()
    //             ->route('demonstration.result', [
    //                 'status' => 'success',
    //                 'type' => 'paid',
    //                 'plan_name' => $planName,
    //                 'plan_price' => $plan->price,
    //                 'company_id' => $company->id,
    //                 'email' => $company->compagny_email
    //             ])
    //             ->with('registration_success', true);
    //     }
    // }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $company = Company::findOrFail($id);
         return view('companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $timezones = [];
        foreach (\DateTimeZone::listIdentifiers() as $timezone) {
            $timezones[$timezone] = $timezone;
        }
        $company = Company::findOrFail($id);

        return view('companies.edit', compact('company', 'timezones'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'compagny_name' => 'required|string',
            // 'n_siret' => 'required|string',
            // 'n_siren' => 'required|string',
            // 'iban' => 'required|string',
            'compagny_email' => 'required|string|email|unique:companies,compagny_email,' . $id,

            'phone' => 'nullable|string',
            'country'=>'required|string',
            'currency'=> 'required|string',
            'website' => 'nullable|string',
            'timezone' => 'nullable|string',
            'address' => 'nullable|string',
            'password' => 'nullable|string|min:8',
            'status' => 'required|in:active,inactive',
            'logo' => 'nullable|file|mimetypes:image/*|max:2048',
        ]);

        $company = Company::findOrFail($id);

        //Enregistrement de l'admin
        $user = User::where('email', $company->compagny_email)->first();

        $user->name = $request->compagny_name;
        $user->email = $request->compagny_email;
        $user->status = $request->status;

        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            $user->avatar = $company->logo;
        }
        if (!empty(trim($request->password))) {
            $user->password = Hash::make($request->password);
        }
        // Enregistrement de la compagnie
        $company->compagny_name = $request->compagny_name;

        // $company->n_siret = $request->n_siret;
        // $company->n_siren = $request->n_siren;
        // $company->iban = $request->iban;
        $company->compagny_email = $request->compagny_email;
        $company->phone = $request->phone;
        $company->country=$request->country;
        $company->currency=$request->currency;
        $company->website = $request->website;
        $company->timezone = $request->timezone;
        $company->address = $request->address;
        $company->status = $request->status;
        $company->badge_header_color = $request->badge_header_color;

        if (!empty(trim($request->password))) {
            $company->password = Hash::make($request->password);
        }

        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {

            $avatarsDirectory = 'avatars';
            if (!Storage::disk('public')->exists($avatarsDirectory)) {
                Storage::disk('public')->makeDirectory($avatarsDirectory);
            }
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }

            $filename = 'logo_' . time() . '.' . $request->file('logo')->getClientOriginalExtension();

            $company->logo = $request->file('logo')->storeAs($avatarsDirectory, $filename, 'public');
        }
        try {
            $user->save();
            //fin de l'enregistrement
            $company->save();
            return redirect()
                ->route('companies.index')
                ->with('success', __('Company updated successfully.'));
        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('Failed to update company'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */

    // public function destroy(string $id)
    // {
    //     $company = Company::findOrFail($id);
    //     $user = User::where('company_id', $id)->where('user_type', 'admin')->first();

    //     if ($company->logo) {
    //         Storage::disk('public')->delete($company->logo);
    //     }
    //     if ($company->avatar) {
    //         Storage::disk('public')->delete($company->avatar);
    //     }

    //     $company->delete();
    //     if ($user) {
    //         $user->delete();
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Company deleted successfully.'
    //     ]);
    // }

    public function destroy(string $id)
    {
        try {
            $company = Company::findOrFail($id);
            $user = User::where('company_id', $id)->where('user_type', 'admin')->first();
            $this->archiveService->handleCompanyDeletion($company);
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            if ($company->avatar) {
                Storage::disk('public')->delete($company->avatar);
            }
            $company->delete();

            if ($user) {
                $user->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Company deleted successfully. Payments have been archived.',
                'archived_payments_count' => PaymentArchive::where('company_id', $id)->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete company: ' . $e->getMessage()
            ], 500);
        }
    }

    public function disabled(Request $request, string $id)
    {
        $request->validate([
            'reason' => 'required|string',
        ]);

        $company = Company::findOrFail($id);
        $user = User::where('company_id', $id)->where('user_type', 'admin')->first();

        try {
            if (Auth::user()->user_type == 'super_admin') {

                $company->status = ($company->status === 'inactive' || $company->status === 'pending') ? 'active' : 'inactive';
                $user->status = ($user->status === 'inactive'|| $company->status === 'pending') ? 'active' : 'inactive';
                $company->reason = $request->reason;
                $user->reason = $request->reason;
                $company->save();
                $user->save();
            } else {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', __('Vous n\'êtes pas autorisé à désactiver cette société'));
            }

            return redirect()->back()->with('success', 'Société activé / désactivé avec succès.');
        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('Échec de la désactivation / activation de la société'));
        }
    }

    public function activate($id)
    {
        $company = Company::findOrFail($id);
        $company->status = 'active'; // ou true si c’est booléen
        $company->save();

        User::where('company_id', $company->id)
            ->where('user_type', 'admin')
            ->update(['status' => 'active']);

        return redirect()->back()->with('success', 'La société a été activée avec succès.');
    }

}
