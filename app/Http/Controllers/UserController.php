<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Site;
use App\Models\User;
use App\Models\Fonction;
use App\Models\SiteUser;
use App\Models\Company;
use App\Models\Attendance;
use App\Models\Candidature;
use Illuminate\Http\Request;
use App\Models\Disponibilite;
use App\Models\SiteControllerUser;
use App\Services\OneSignalService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\UserDeactivationReason;
use App\Http\Requests\UserStoreRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UserUpdateRequest;
use App\Notifications\SendCreateUserEmailNotification;
use Illuminate\Support\Facades\Log;
use App\Mail\UserWelcomeMail;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    protected $oneSignal;

    public function __construct(OneSignalService $oneSignal)
    {
        $this->oneSignal = $oneSignal;
    }

    public function index(Request $request, $user_type)
    {
        $search = $request->input('search');

        $validUserType = ['super_admin', 'admin', 'manager', 'controller', 'user', 'customer'];
        if (!in_array($user_type, $validUserType)) {
            abort(404, 'Rôle non trouvé');
        }

        if (Auth::user()->user_type == 'super_admin') {
            $users = User::where('user_type', $user_type)->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('first_name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%");
                });
            })->paginate(25);
        } elseif (is_controller()) {
            $authorizeControllers = SiteControllerUser::where('user_id', Auth::user()->id)->get();
            $siteIds = $authorizeControllers->pluck('site_id');

            $users = User::whereIn('id', function ($query) use ($siteIds) {
                $query->select('user_id')
                    ->from('site_user')
                    ->whereIn('site_id', $siteIds);
            })
                ->where('company_id', Auth::user()->company_id)
                ->where('user_type', 'user')
                ->where('type_horaire', 'fixe')
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%")
                            ->orWhere('first_name', 'like', "%$search%")
                            ->orWhere('email', 'like', "%$search%");
                    });
                })
                ->paginate(25);
        } else {
            $users = User::where('user_type', $user_type)
                ->when($user_type == 'user', function ($query) {
                    $query->where('type_horaire', 'fixe');
                })
                ->where('company_id', Auth::user()->company_id)
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%")
                            ->orWhere('first_name', 'like', "%$search%")
                            ->orWhere('email', 'like', "%$search%");
                    });
                })->paginate(25);
        }

        if ($request->ajax()) {
            return view('users._tableBody', compact('users', 'user_type'));
        }

        return view('users.index', compact('users', 'user_type'));
    }

    public function index_sans_horaire(Request $request, $user_type)
    {
        $search = $request->input('search');

        $validUserType = ['super_admin', 'admin', 'manager', 'controller', 'user', 'customer'];
        if (!in_array($user_type, $validUserType)) {
            abort(404, 'Rôle non trouvé');
        }

        if (Auth::user()->user_type == 'super_admin') {
            $users = User::where('user_type', $user_type)->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('first_name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%");
                });
            })->paginate(25);
        } elseif (is_controller()) {
            $authorizeControllers = SiteControllerUser::where('user_id', Auth::user()->id)->get();
            $siteIds = $authorizeControllers->pluck('site_id');

            $users = User::whereIn('id', function ($query) use ($siteIds) {
                $query->select('user_id')
                    ->from('site_user')
                    ->whereIn('site_id', $siteIds);
            })
                ->where('company_id', Auth::user()->company_id)
                ->where('user_type', 'user')
                ->where('type_horaire', 'rotation')
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%")
                            ->orWhere('first_name', 'like', "%$search%")
                            ->orWhere('email', 'like', "%$search%");
                    });
                })
                ->paginate(25);
        } else {
            $users = User::where('user_type', $user_type)
                ->when($user_type == 'user', function ($query) {
                    $query->where('type_horaire', 'rotation');
                })
                ->where('company_id', Auth::user()->company_id)
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%")
                            ->orWhere('first_name', 'like', "%$search%")
                            ->orWhere('email', 'like', "%$search%");
                    });
                })->paginate(25);
        }

        if ($request->ajax()) {
            return view('users._tableBody', compact('users', 'user_type'));
        }

        return view('users.index_sans_horaire', compact('users', 'user_type'));
    }

    public function createSimple($user_type)
    {
        if ($user_type == 'user') {
            $user = Auth::user();
            $fonction = Fonction::whereNull('company_id')
                ->orWhere('company_id', $user->company_id)
                ->get();
            return view('users.employees.create_simple', compact('fonction', 'user_type'));
        }
    }

    public function create($user_type)
    {
        if ($user_type == 'user') {
            $user = Auth::user();
            $fonction = Fonction::whereNull('company_id')
                ->orWhere('company_id', $user->company_id)
                ->orderBy('nom_fonction', 'asc')
                ->get();
            return view('users.employees.create', compact('user_type', 'fonction'));
        } elseif ($user_type == 'customer') {
            return view('users.customers.create', compact('user_type'));
        } else {
            return view('users.create', compact('user_type'));
        }
    }

    private function generateMatriculeUnique()
    {
        do {
            $matricule = 'MPT02 ' . rand(10, 99) . ' ' . rand(1000, 9999) . ' ' . rand(10, 99) . ' ' . rand(100, 999);
        } while (User::where('matricule', $matricule)->exists());

        return $matricule;
    }

    private function generateMatriculeUniqueFixe()
    {
        do {
            $matricule = 'MPT02 ' . rand(10, 99) . ' ' . rand(1000, 9999) . ' ' . rand(10, 99) . ' ' . rand(100, 999);
        } while (User::where('matricule', $matricule)->exists());

        return $matricule;
    }

    public function store_simple(UserStoreRequest $request)
    {
        $employeeSimple = new User();
        $employeeSimple->user_type = $request->user_type;
        $employeeSimple->company_id = $request->company_id;
        $employeeSimple->n_social = $request->n_social;
        $employeeSimple->name = $request->name;
        $employeeSimple->first_name = $request->first_name;
        $employeeSimple->username = $request->username;
        $employeeSimple->email = $request->email;
        $employeeSimple->phone = $request->phone;
        $employeeSimple->status = 'inactive';
        $employeeSimple->panier = $request->panier;
        $employeeSimple->address = $request->address;
        $employeeSimple->employee_function = $request->employee_function;
        $employeeSimple->qualification = $request->qualification;
        $employeeSimple->employee_quotient = $request->employee_quotient;
        $employeeSimple->identity_document_type = $request->identity_document_type;
        $employeeSimple->id_number = $request->id_number;
        $employeeSimple->customer_type = $request->customer_type;
        $employeeSimple->salary_rate = $request->salary_rate;
        $employeeSimple->type_horaire = $request->type_horaire;
        $employeeSimple->emergency_contact_name = $request->emergency_contact_name;
        $employeeSimple->emergency_contact_email = $request->emergency_contact_email;
        $employeeSimple->emergency_contact_phone = $request->emergency_contact_phone;
        $employeeSimple->emergency_contact_relationship = $request->emergency_contact_relationship;
        $employeeSimple->emergency_contact_address = $request->emergency_contact_address;
        $employeeSimple->raison_sociale = $request->customer_type === 'legal_person' ? $request->raison_sociale : null;
        $employeeSimple->contract_start_date = $request->contract_start_date;
        $employeeSimple->contract_end_date = $request->contract_end_date;
        $employeeSimple->password = Hash::make($request->password);
        $employeeSimple->biometric_data = $request->biometric_data;
        $employeeSimple->biometric_system = $request->biometric_system;
        $employeeSimple->exception_fiche_fixe = $request->has('exception_fiche_fixe');
        $employeeSimple->matricule = $this->generateMatriculeUnique();

        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $avatarsDirectory = 'avatars';
            if (!Storage::disk('public')->exists($avatarsDirectory)) {
                Storage::disk('public')->makeDirectory($avatarsDirectory);
            }
            $filename = 'avatar_' . time() . '.' . $request->file('avatar')->getClientOriginalExtension();
            $employeeSimple->avatar = $request->file('avatar')->storeAs($avatarsDirectory, $filename, 'public');
        }

        $employeeSimple->save();

        UserDeactivationReason::create([
            'user_id' => $employeeSimple->id,
            'status' => 'inactive',
            'reason' => 'Fin de Contrat',
        ]);

        return redirect()->route('users.index_sans_horaire', $request->user_type)
            ->with('success', __('User created successfully.'));
    }

    public function store(UserStoreRequest $request)
    {
        if ($request->user_type === 'user') {
            $company = Auth::user()->company;
            $activeSub = $company?->activeSubscription;
            $plan = $activeSub?->plan;

            if (!$plan) {
                return redirect()->back()->withInput()->with('error', __('Aucun abonnement actif trouvé pour votre entreprise.'));
            }

            if ($plan->max_employees !== -1) {
                $currentEmployeesCount = User::where('company_id', $company->id)
                    ->where('user_type', 'user')
                    ->count();

                if ($currentEmployeesCount >= $plan->max_employees) {
                    return redirect()->back()->withInput()->with('error', __(
                        'Limite atteinte ! Votre plan ":plan" ne vous permet pas d\'avoir plus de :max employés.',
                        ['plan' => $plan->name, 'max' => $plan->max_employees]
                    ));
                }
            }
        }

        // Gestion du contact d'urgence
        $hasEmergencyContact = $request->input('has_emergency_contact', false);
        if (!$hasEmergencyContact) {
            $request->merge([
                'emergency_contact_name' => null,
                'emergency_contact_email' => null,
                'emergency_contact_phone' => null,
                'emergency_contact_relationship' => null,
                'emergency_contact_address' => null,
            ]);
        }

        // Gestion de la pièce d'identité
        $hasIdentityDocument = $request->input('has_identity_document', false);
        if (!$hasIdentityDocument) {
            $request->merge([
                'identity_document_type' => null,
                'id_number' => null,
                'cni_number' => null,
                'passport_number' => null,
                'permis_number' => null,
            ]);
        } else {
            $idNumber = $request->input('id_number');
            if (empty($idNumber)) {
                $docType = $request->input('identity_document_type');
                if ($docType === 'cni') {
                    $idNumber = $request->input('cni_number');
                } elseif ($docType === 'passport') {
                    $idNumber = $request->input('passport_number');
                } elseif ($docType === 'driving_licenses') {
                    $idNumber = $request->input('permis_number');
                }
                $request->merge(['id_number' => $idNumber]);
            }
        }

        // ============================================
        // GESTION DES HORAIRES - CORRIGÉE
        // ============================================
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $schedule = [];

        foreach ($days as $day) {
            $checkedField = $day . '_checked';
            $startField = 'start_time_' . $day;
            $endField = 'end_time_' . $day;

            // Les colonnes *_checked sont non nulles en base.
            if ($request->has($checkedField) && in_array($request->input($checkedField), ['on', '1', 1, true])) {
                $schedule[$checkedField] = 1;
                $schedule[$startField] = $request->input($startField);
                $schedule[$endField] = $request->input($endField);
            } else {
                $schedule[$checkedField] = 0;
                $schedule[$startField] = null;
                $schedule[$endField] = null;
            }
        }

        // Mot de passe en clair pour l'email
        $plainPassword = $request->password;

        $userData = [
            'user_type' => $request->user_type,
            'company_id' => $request->company_id,
            'name' => $request->name,
            'first_name' => $request->first_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => 'active', // <-- CHANGÉ : 'inactive' → 'active'
            'address' => $request->address,
            'employee_function' => $request->employee_function,
            'identity_document_type' => $request->identity_document_type,
            'id_number' => $request->id_number,
            'customer_type' => $request->customer_type,
            'salary_rate' => $request->salary_rate,
            'type_horaire' => $request->type_horaire,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_email' => $request->emergency_contact_email,
            'emergency_contact_phone' => $request->emergency_contact_phone,
            'emergency_contact_relationship' => $request->emergency_contact_relationship,
            'emergency_contact_address' => $request->emergency_contact_address,
            'raison_sociale' => $request->customer_type === 'legal_person' ? $request->raison_sociale : null,
            'contract_start_date' => $request->contract_start_date,
            'contract_end_date' => $request->contract_end_date,
            'matricule' => $this->generateMatriculeUniqueFixe(),
            'salaire_fixe' => $request->salaire_fixe,
            'has_emergency_contact' => $hasEmergencyContact,
            'has_identity_document' => $hasIdentityDocument,
        ];

        $user = new User(array_merge($userData, $schedule));
        $user->password = Hash::make($request->password);

        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $avatarsDirectory = 'avatars';
            if (!Storage::disk('public')->exists($avatarsDirectory)) {
                Storage::disk('public')->makeDirectory($avatarsDirectory);
            }
            $filename = 'avatar_' . time() . '.' . $request->file('avatar')->getClientOriginalExtension();
            $user->avatar = $request->file('avatar')->storeAs($avatarsDirectory, $filename, 'public');
        }

        try {
            if (Auth::user()->user_type == 'admin' || (Auth::user()->user_type == 'manager' && Auth::user()->company_id == $request->company_id)) {
                $user->save();

                // PAS DE CRÉATION DE UserDeactivationReason puisque l'utilisateur est ACTIF
                // UserDeactivationReason::create([...]) ← SUPPRIMÉ

                if ($request->user_type != "customer") {
                    try {
                        $company = Company::find($request->company_id);
                        Mail::to($user->email)->send(new UserWelcomeMail($user, $company, $plainPassword));
                        Log::info('Email de bienvenue envoyé à l\'utilisateur: ' . $user->email);
                    } catch (\Exception $e) {
                        Log::error('Erreur lors de l\'envoi de l\'email à l\'utilisateur: ' . $e->getMessage());
                    }
                }

            } else {
                return redirect()->back()->withInput()->with('error', __('You are not authorized to create this user'));
            }

            return redirect()->route('users.index', $request->user_type)
                ->with('success', __('User created successfully. Un email a été envoyé avec les identifiants.'));

        } catch (\Exception $e) {
            Log::error('User creation failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', __('Failed to create user: ') . $e->getMessage());
        }
    }

    public function show(Request $request, string $id)
    {
        $user = User::with('fonction')->findOrFail($id);
        $userDeactivationReasons = UserDeactivationReason::where('user_id', $id)->get();
        $sites = collect();

        if ($user->user_type == 'customer') {
            $sites = Site::with(['company', 'customer', 'controller', 'users'])
                ->where('company_id', Auth::user()->company_id)
                ->where('customer_id', $id)
                ->where('status', "active")
                ->paginate(25);
        } elseif ($user->user_type == 'controller') {
            $siteIds = SiteControllerUser::where('user_id', $user->id)->pluck('site_id');
            $sites = Site::with(['company', 'customer', 'controller', 'users'])
                ->where('company_id', Auth::user()->company_id)
                ->whereIn('id', $siteIds)
                ->paginate(25);
        } elseif ($user->user_type == 'user') {
            $siteIds = SiteUser::where('user_id', $user->id)->pluck('site_id');
            $sites = Site::with(['company', 'customer', 'controller', 'users'])
                ->where('company_id', Auth::user()->company_id)
                ->whereIn('id', $siteIds)
                ->paginate(25);
        }

        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

        $rawAttendances = Attendance::where('company_id', Auth::user()->company_id)
            ->where('user_id', $id)
            ->whereYear('timestamp', $year)
            ->whereMonth('timestamp', $month)
            ->orderBy('timestamp')
            ->get();

        $attendances = [];
        foreach ($rawAttendances as $attendance) {
            $day = Carbon::parse($attendance->timestamp)->day;
            $userId = $attendance->user_id;

            if (!isset($attendances[$userId])) {
                $attendances[$userId] = [];
            }

            if (!isset($attendances[$userId][$day])) {
                $attendances[$userId][$day] = [
                    'timestamp' => $attendance->timestamp,
                    'type' => $attendance->type,
                    'status' => $attendance->status,
                ];
            }
        }

        return view('users.show', compact('user', 'userDeactivationReasons', 'attendances', 'sites', 'year', 'month', 'daysInMonth'));
    }

    /* === AJOUT DU PARAMÈTRE TO INTENTIONALY FIX THE 404 === */
    public function edit(string $user_type, string $id)
    {
        $user = User::findOrFail($id);
        if ($user->user_type == 'user') {
            $fonction = Fonction::whereNull('company_id')
                ->orwhere('company_id', $user->company_id)
                ->get();
            return view('users.employees.edit', compact('user', 'fonction', 'user_type'));
        } elseif ($user->user_type == 'customer') {
            return view('users.customers.edit', compact('user', 'user_type'));
        } else {
            return view('users.edit', compact('user', 'user_type'));
        }
    }

    /* === AJOUT DU PARAMÈTRE TO INTENTIONALY FIX THE 404 === */
    public function edit_simple(string $user_type, string $id)
    {
        $user = User::findOrFail($id);

        if ($user->user_type == 'user') {
            $fonction = Fonction::whereNull('company_id')
                ->orwhere('company_id', $user->company_id)
                ->get();
            return view('users.employees.edit_simple', compact('user', 'fonction', 'user_type'));
        } elseif ($user->user_type == 'customer') {
            return view('users.customers.edit', compact('user', 'user_type'));
        } else {
            return view('users.edit', compact('user', 'user_type'));
        }
    }

    public function update(UserUpdateRequest $request, $user_type, $id)
    {
        try {
            $user = User::findOrFail($id);

            // Gestion du contact d'urgence
            $hasEmergencyContact = $request->input('has_emergency_contact', false);

            // Gestion de la pièce d'identité
            $hasIdentityDocument = $request->input('has_identity_document', false);

            // Données de base (sans type_horaire)
            $user->user_type = $request->user_type;
            $user->company_id = $request->company_id;
            $user->name = $request->name;
            $user->first_name = $request->first_name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->status = $request->status;
            $user->employee_function = $request->employee_function;
            $user->address = $request->address;
            $user->salary_rate = $request->salary_rate;
            $user->salaire_fixe = $request->salaire_fixe;
            $user->contract_start_date = $request->contract_start_date;
            $user->contract_end_date = $request->contract_end_date;
            // ❌ type_horaire enlevé

            // Gestion de la pièce d'identité
            if (!$hasIdentityDocument) {
                $user->identity_document_type = null;
                $user->id_number = null;
            } else {
                // Récupérer le numéro d'identité selon le type
                $idNumber = $request->input('id_number');
                if (empty($idNumber)) {
                    $docType = $request->input('identity_document_type');
                    if ($docType === 'cni') {
                        $idNumber = $request->input('cni_number');
                    } elseif ($docType === 'passport') {
                        $idNumber = $request->input('passport_number');
                    } elseif ($docType === 'driving_licenses') {
                        $idNumber = $request->input('permis_number');
                    }
                }
                $user->identity_document_type = $request->input('identity_document_type');
                $user->id_number = $idNumber;
            }

            // Gestion du contact d'urgence
            if (!$hasEmergencyContact) {
                $user->emergency_contact_name = null;
                $user->emergency_contact_email = null;
                $user->emergency_contact_phone = null;
                $user->emergency_contact_relationship = null;
                $user->emergency_contact_address = null;
            } else {
                $user->emergency_contact_name = $request->emergency_contact_name;
                $user->emergency_contact_email = $request->emergency_contact_email;
                $user->emergency_contact_phone = $request->emergency_contact_phone;
                $user->emergency_contact_relationship = $request->emergency_contact_relationship;
                $user->emergency_contact_address = $request->emergency_contact_address;
            }

            // ============================================
            // GESTION DES HORAIRES - CORRIGÉE
            // ============================================
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

            foreach ($days as $day) {
                $checkedField = $day . '_checked';
                $startField = 'start_time_' . $day;
                $endField = 'end_time_' . $day;

                // Les colonnes *_checked sont non nulles en base.
                if ($request->has($checkedField) && in_array($request->input($checkedField), ['on', '1', 1, true])) {
                    $user->{$checkedField} = 1;
                    $user->{$startField} = $request->input($startField);
                    $user->{$endField} = $request->input($endField);
                } else {
                    $user->{$checkedField} = 0;
                    $user->{$startField} = null;
                    $user->{$endField} = null;
                }
            }

            // Gestion du mot de passe
            if (!empty(trim($request->password))) {
                $user->password = Hash::make($request->password);
            }

            // Gestion de l'avatar
            if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
                $avatarsDirectory = 'avatars';
                if (!Storage::disk('public')->exists($avatarsDirectory)) {
                    Storage::disk('public')->makeDirectory($avatarsDirectory);
                }
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $filename = 'avatar_' . time() . '.' . $request->file('avatar')->getClientOriginalExtension();
                $user->avatar = $request->file('avatar')->storeAs($avatarsDirectory, $filename, 'public');
            }

            $user->save();

            return redirect()
                ->route('users.index', $request->user_type)
                ->with('success', __('User updated successfully.'));

        } catch (\Exception $e) {
            Log::error('User update failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', __('Failed to update user: ') . $e->getMessage());
        }
    }

    public function update_simple(UserUpdateRequest $request, string $user_type, string $id)
    {
        $user = User::findOrFail($id);

        $user->user_type = $request->user_type;
        $user->company_id = $request->company_id;
        $user->n_social = $request->n_social;
        $user->name = $request->name;
        $user->first_name = $request->first_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->status = $request->status;
        $user->employee_function = $request->employee_function;
        $user->qualification = $request->qualification;
        $user->employee_quotient = $request->employee_quotient;
        $user->identity_document_type = $request->identity_document_type;
        $user->id_number = $request->id_number;
        $user->customer_type = $request->customer_type;
        $user->address = $request->address;
        $user->salary_rate = $request->salary_rate;
        $user->panier = $request->panier;
        $user->contract_start_date = $request->contract_start_date;
        $user->contract_end_date = $request->contract_end_date;

        $user->save();

        return redirect()->route('users.index_sans_horaire', $request->user_type)
            ->with('success', __('User updated successfully.'));
    }

    /**
     * CORRECTION : Suppression (AJAX / Unitaire)
     */
    public function destroy($user_type, $id)
    {
        try {
            $user = User::findOrFail($id);

            // Supprimer l'avatar si existant
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => __('User deleted successfully.')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to delete user.')
            ], 500);
        }
    }

    /**
     * CORRECTION : Désactivation temporaire ou définitive via motif (AJAX)
     */
    public function disabled(Request $request, $user_type, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->status = 'inactive';
            $user->save();

            // Création de la trace du motif
            UserDeactivationReason::create([
                'user_id' => $user->id,
                'status'  => 'inactive',
                'reason'  => $request->input('reason', 'Non spécifié'),
            ]);

            return response()->json([
                'success' => true,
                'message' => __('User deactivated successfully.')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to deactivate user.')
            ], 500);
        }
    }

    /**
     * CORRECTION : Réactivation depuis le menu contextuel (Formulaire standard POST)
     */
    public function reactivate($user_type, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->status = 'active';
            $user->save();

            // Création de la trace du motif
            UserDeactivationReason::create([
                'user_id' => $user->id,
                'status'  => 'active',
                'reason'  => 'Réactivation du compte',
            ]);

            return redirect()->back()->with('success', __('User reactivated successfully.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to reactivate user.'));
        }
    }
}
