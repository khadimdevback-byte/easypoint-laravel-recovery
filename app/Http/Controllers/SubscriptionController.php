<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Company;
use App\Models\Feature;
use App\Models\Payment;
use App\Models\Plan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str; // Importation manquante ajoutée ici
use Barryvdh\DomPDF\Facade\Pdf;
use FedaPay\FedaPay;
use FedaPay\Transaction;
use Exception;
use Illuminate\Support\Facades\DB;


class SubscriptionController extends Controller
{
    public function __construct()
    {
        FedaPay::setApiKey(config('services.fedapay.secret_key'));
        FedaPay::setEnvironment(config('services.fedapay.environment'));
    }

    /**
     * Liste toutes les souscriptions (Vue Super Admin)
     */
    // public function index()
    // {
    //     $subscriptions = Subscription::with(['company', 'plan'])
    //         ->orderBy('created_at', 'desc')
    //         ->paginate(10);

    //     return view('superadmin.subscriptions.index', compact('subscriptions'));
    // }
    public function index(Request $request)
    {
        // 1. Récupérer le statut sélectionné (par défaut 'active')
        $status = $request->get('status', 'active');

        // 2. Récupérer les souscriptions filtrées avec la pagination
        $subscriptions = Subscription::with(['company', 'plan'])
            ->where('status', $status)
            ->latest()
            ->paginate(10)
            ->withQueryString(); // Garde le paramètre ?status= dans les liens de pagination

        // 3. Compter le nombre d'éléments pour chaque catégorie (badge des onglets)
        $counts = [
            'active' => Subscription::where('status', 'active')->count(),
            'pending_payment' => Subscription::where('status', 'pending_payment')->count(),
            'canceled' => Subscription::where('status', 'canceled')->count(),
        ];

        return view('superadmin.subscriptions.index', compact('subscriptions', 'status', 'counts'));
    }

    /**
     * Formulaire de création d'une souscription
     */
    // public function create()
    // {
    //     $companies = Company::orderBy('compagny_name')->get();
    //     $plans = Plan::orderBy('price')->get();

    //     return view('superadmin.subscriptions.create', compact('companies', 'plans'));
    // }
    public function create()
    {
        $companies = Company::orderBy('compagny_name')->get();

        // 🌟 CORRECTION : On ne propose que les plans publics standards dans la liste classique
        $plans = Plan::where('is_visible', true)->orderBy('price')->get();

        // 🌟 NOUVEAU : Récupérer toutes les fonctionnalités pour les afficher sous forme de cases à cocher
        $features = Feature::where('is_active', true)->get();

        return view('superadmin.subscriptions.create', compact('companies', 'plans', 'features'));
    }

    /**
     * Enregistrement de la souscription avec calcul automatique
     */
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'company_id'      => 'required|exists:companies,id',
    //         'plan_id'         => 'required|exists:plans,id',
    //         'duration_months' => 'required|integer|min:1|max:60',
    //     ]);

    //     $plan = Plan::findOrFail($request->plan_id);
    //     $months = intval($request->duration_months);

    //     $totalAmount = $plan->price * $months;
    //     $startsAt = now();
    //     $endsAt = now()->addMonths($months);

    //     Subscription::where('company_id', $request->company_id)
    //         ->where('status', 'active')
    //         ->update(['status' => 'canceled']);

    //     Subscription::create([
    //         'company_id'      => $request->company_id,
    //         'plan_id'         => $request->plan_id,
    //         'duration_months' => $months,
    //         'total_amount'    => $totalAmount,
    //         'starts_at'       => $startsAt,
    //         'ends_at'         => $endsAt,
    //         'status'          => 'active',
    //     ]);

    //     return redirect()->route('subscriptions.index')
    //         ->with('success', __('Souscription créée avec succès. Montant total : ' . number_format($totalAmount, 0, ',', ' ') . ' ' . $plan->currency));
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'company_id'       => 'required|exists:companies,id',
    //         'plan_type'        => 'required|in:standard,custom',
    //         'plan_id'          => 'required_if:plan_type,standard|nullable|exists:plans,id',
    //         'duration_months'  => 'required|integer|min:1|max:60',

    //         'custom_name'      => 'required_if:plan_type,custom|nullable|string|max:255',
    //         'custom_price'     => 'required_if:plan_type,custom|nullable|numeric|min:0',
    //         'custom_employees' => 'required_if:plan_type,custom|nullable|integer|min:-1',
    //         'features'         => 'required_if:plan_type,custom|nullable|array',
    //         'features.*'       => 'exists:features,id',

    //         'payment_mode'     => 'required|in:fedapay,manual',
    //     ]);

    //     $company = Company::findOrFail($request->company_id);
    //     $months = intval($request->duration_months);

    //     $startsAt = null;
    //     $endsAt = null;
    //     $status = ($request->payment_mode === 'manual') ? 'active' : 'pending_payment';

    //     if ($request->payment_mode === 'manual') {
    //         $startsAt = now();
    //         $endsAt = now()->addMonths($months);
    //     }

    //     // Sécurisation de l'ensemble des écritures en Base de données
    //     $subscription = DB::transaction(function () use ($request, $company, $months, $startsAt, $endsAt, $status) {

    //         // 🌟 DÉPLACÉ : On n'annule l'ancien abonnement que SI le paiement est direct/manuel
    //         if ($request->payment_mode === 'manual') {
    //             Subscription::where('company_id', $request->company_id)
    //                 ->where('status', 'active')
    //                 ->update(['status' => 'canceled']);
    //         }

    //         if ($request->plan_type === 'standard') {
    //             $plan = Plan::findOrFail($request->plan_id);
    //             $totalAmount = $plan->price * $months;
    //             $targetPlanId = $plan->id;
    //         } else {
    //             $totalAmount = $request->custom_price;

    //             // Création du plan sur mesure masqué
    //             $customPlan = Plan::create([
    //                 'name'             => $request->custom_name,
    //                 'slug'             => Str::slug($request->custom_name) . '-' . time(),
    //                 'price'            => $request->custom_price,
    //                 'currency'         => 'XOF',
    //                 'max_employees'    => $request->custom_employees,
    //                 'dashboard_type'   => 'Complet',
    //                 'history_duration' => 'Illimité',
    //                 'support_type'     => 'Dédié',
    //                 'is_active'        => true,
    //                 'is_visible'       => false, // Reste bien invisible
    //             ]);

    //             $customPlan->features()->attach($request->features);
    //             $targetPlanId = $customPlan->id;
    //         }

    //         // Créer la souscription
    //         $sub = Subscription::create([
    //             'company_id'      => $request->company_id,
    //             'plan_id'         => $targetPlanId,
    //             'duration_months' => $months,
    //             'total_amount'    => $totalAmount,
    //             'starts_at'       => $startsAt,
    //             'ends_at'         => $endsAt,
    //             'status'          => $status,
    //         ]);

    //         // Enregistrement du paiement si Cash/Chèque
    //         if ($request->payment_mode === 'manual') {
    //             Payment::create([
    //                 'company_id'             => $company->id,
    //                 'subscription_id'        => $sub->id,
    //                 'fedapay_transaction_id' => 'MANUAL-' . strtoupper(Str::random(8)),
    //                 'amount'                 => $totalAmount,
    //                 'payment_method'         => 'Chèque / Espèces',
    //                 'status'                 => 'approved',
    //             ]);
    //         }

    //         return $sub;
    //     });

    //     // Redirections adaptées selon le statut final de la souscription créée
    //     if ($subscription->status === 'active') {
    //         return redirect()->route('subscriptions.index', ['status' => 'active'])
    //             ->with('success', 'Plan sur mesure activé avec succès ! Ancienne souscription archivée.');
    //     }

    //     return redirect()->route('subscriptions.index', ['status' => 'pending_payment'])
    //         ->with('success', 'Offre générée avec succès. En attente de paiement FedaPay par le client.');
    // }

    public function store(Request $request)
    {
        // 🔍 Détecter si c'est un plan gratuit
        $isFreePlan = $request->plan_type === 'standard'
            && $request->plan_id
            && Plan::find($request->plan_id)?->price == 0;

        // 🔄 Si gratuit, forcer le mode manuel
        if ($isFreePlan) {
            $request->merge(['payment_mode' => 'manual']);
        }

        $request->validate([
            'company_id'       => 'required|exists:companies,id',
            'plan_type'        => 'required|in:standard,custom',
            'plan_id'          => 'required_if:plan_type,standard|nullable|exists:plans,id',
            'duration_months'  => 'required|integer|min:1|max:60',
            'custom_name'      => 'required_if:plan_type,custom|nullable|string|max:255',
            'custom_price'     => 'required_if:plan_type,custom|nullable|numeric|min:0',
            'custom_employees' => 'required_if:plan_type,custom|nullable|integer|min:-1',
            'features'         => 'required_if:plan_type,custom|nullable|array',
            'features.*'       => 'exists:features,id',
            'payment_mode'     => 'required|in:fedapay,manual',
        ]);

        $company = Company::findOrFail($request->company_id);
        $months = intval($request->duration_months);

        $startsAt = null;
        $endsAt = null;
        $status = ($request->payment_mode === 'manual') ? 'active' : 'pending_payment';

        if ($request->payment_mode === 'manual') {
            $startsAt = now();
            $endsAt = now()->addMonths($months);
        }

        $subscription = DB::transaction(function () use ($request, $company, $months, $startsAt, $endsAt, $status, $isFreePlan) {

            if ($request->payment_mode === 'manual') {
                Subscription::where('company_id', $request->company_id)
                    ->where('status', 'active')
                    ->update(['status' => 'canceled']);
            }

            if ($request->plan_type === 'standard') {
                $plan = Plan::findOrFail($request->plan_id);
                $totalAmount = $plan->price * $months;
                $targetPlanId = $plan->id;
            } else {
                $totalAmount = $request->custom_price;
                $customPlan = Plan::create([
                    'name'             => $request->custom_name,
                    'slug'             => Str::slug($request->custom_name) . '-' . time(),
                    'price'            => $request->custom_price,
                    'currency'         => 'XOF',
                    'max_employees'    => $request->custom_employees,
                    'dashboard_type'   => 'Complet',
                    'history_duration' => 'Illimité',
                    'support_type'     => 'Dédié',
                    'is_active'        => true,
                    'is_visible'       => false,
                ]);
                $customPlan->features()->attach($request->features);
                $targetPlanId = $customPlan->id;
            }

            $sub = Subscription::create([
                'company_id'      => $request->company_id,
                'plan_id'         => $targetPlanId,
                'duration_months' => $months,
                'total_amount'    => $totalAmount,
                'starts_at'       => $startsAt,
                'ends_at'         => $endsAt,
                'status'          => $status,
            ]);

            if ($request->payment_mode === 'manual') {
                Payment::create([
                    'company_id'             => $company->id,
                    'subscription_id'        => $sub->id,
                    'fedapay_transaction_id' => $isFreePlan
                        ? 'FREE-' . strtoupper(Str::random(8))
                        : 'MANUAL-' . strtoupper(Str::random(8)),
                    'amount'                 => $totalAmount,
                    'payment_method'         => $isFreePlan ? 'Gratuit' : 'Chèque / Espèces',
                    'status'                 => 'approved',
                ]);
            }

            return $sub;
        });

        if ($subscription->status === 'active') {
            $message = $isFreePlan
                ? '🎉 Plan gratuit activé avec succès !'
                : 'Plan sur mesure activé avec succès ! Ancienne souscription archivée.';

            return redirect()->route('subscriptions.index', ['status' => 'active'])
                ->with('success', $message);
        }

        return redirect()->route('subscriptions.index', ['status' => 'pending_payment'])
            ->with('success', 'Offre générée avec succès. En attente de paiement FedaPay par le client.');
    }


    /**
     * 🌟 NOUVEAU : Valider manuellement un paiement FedaPay en attente (Cash/Chèque reçu physiquement)
     */
    public function acceptManualPayment(Subscription $subscription)
    {
        if ($subscription->status !== 'pending_payment') {
            return back()->with('error', 'Cette souscription n\'est pas en attente de paiement.');
        }

        // Utilisation d'une transaction DB pour s'assurer que TOUT s'exécute correctement ou rien
        DB::transaction(function () use ($subscription) {

            // 1. RÈGLE MÉTIER : Annuler TOUTES les autres souscriptions actives de cette compagnie
            Subscription::where('company_id', $subscription->company_id)
                ->where('status', 'active')
                ->update(['status' => 'canceled']);

            // 2. Calculer les dates et Activer la souscription actuelle
            $startsAt = now();
            $endsAt = $startsAt->copy()->addMonths($subscription->duration_months);

            $subscription->update([
                'status'    => 'active',
                'starts_at' => $startsAt,
                'ends_at'   => $endsAt,
            ]);

            // 3. Générer le reçu de paiement manuel
            Payment::create([
                'company_id'             => $subscription->company_id,
                'subscription_id'        => $subscription->id,
                'fedapay_transaction_id' => 'MANUAL-' . strtoupper(Str::random(8)),
                'amount'                 => $subscription->total_amount,
                'payment_method'         => 'Chèque / Espèces',
                'status'                 => 'approved',
            ]);
        });

        // Redirection vers l'onglet des souscriptions actives
        return redirect()->route('subscriptions.index', ['status' => 'active'])
            ->with('success', 'Abonnement débloqué manuellement avec succès et anciennes souscriptions annulées.');
    }



    /**
     * Formulaire d'édition
     */
    public function edit(Subscription $subscription)
    {
        $subscription->load(['company', 'plan']);

        return view('superadmin.subscriptions.edit', compact('subscription'));
    }
    
    public function update(Request $request, Subscription $subscription)
    {
        $request->validate([
            'status'          => 'required|in:active,pending_payment,expired,canceled',
            'duration_months' => 'required|integer|min:1|max:60',
            'total_amount'    => 'required|numeric|min:0',
            'ends_at'         => 'required|date',
        ]);

        $subscription->update([
            'status'          => $request->status,
            'duration_months' => $request->duration_months,
            'total_amount'    => $request->total_amount,
            'ends_at'         => Carbon::parse($request->ends_at),
        ]);

        return redirect()->route('superadmin.subscriptions.index')
            ->with('success', __('Souscription mise à jour avec succès.'));
    }

    /**
     * Suppression logique (Soft Delete)
     */
    public function destroy(Subscription $subscription)
    {
        $subscription->update(['status' => 'canceled']);
        $subscription->delete();

        return redirect()->route('subscriptions.index')
            ->with('success', __('La souscription a été archivée avec succès.'));
    }

    /**
     * Vue de changement de plan pour l'utilisateur connecté
     */
    public function upgradeView()
    {
        $company = Auth::user()->company;
        $activeSub = $company?->activeSubscription;
        $currentPlan = $activeSub?->plan;

        $plans = Plan::where('is_active', true)
            ->where('price', '>', 0)
            ->orderBy('price')
            ->get();

        return view('superadmin.subscriptions.upgrade', compact('plans', 'currentPlan'));
    }

    /**
     * Callback de retour après paiement (Changement de Plan / Upgrade)
     */
    public function paymentCallback(Request $request)
    {
        $transactionId = $request->query('id'); // ← query() pas input()
        $planId = $request->query('plan_id');

        if (!$planId) {
            return redirect()->route('subscriptions.upgrade')
                ->with('error', __('Paramètre de plan manquant.'));
        }

        $plan = Plan::findOrFail($planId);

        if (!$transactionId) {
            return redirect()->route('subscriptions.upgrade')
                ->with('error', __('Le paiement a échoué ou a été annulé.'));
        }

        try {
            $transaction = Transaction::retrieve($transactionId);

            Log::info('FedaPay callback', [
                'transaction_id' => $transaction->id,
                'status'         => $transaction->status,
                'plan_id'        => $planId,
            ]);

            if ($transaction->status === 'approved') {
                $company = Auth::user()->company;
                $activeSub = $company?->activeSubscription;

                if ($activeSub) {
                    $activeSub->update([
                        'plan_id'      => $plan->id,
                        'total_amount' => $plan->price,
                    ]);

                    return redirect()->route('company.subscription')
                        ->with('success', 'Abonnement mis à jour vers le plan ' . $plan->name);
                }

                return redirect()->route('subscriptions.upgrade')
                    ->with('error', 'Aucun abonnement actif trouvé.');
            }

            // statut != approved (pending, declined, canceled...)
            return redirect()->route('subscriptions.upgrade')
                ->with('error', 'Paiement non approuvé. Statut : ' . $transaction->status);

        } catch (Exception $e) {
            Log::error('FedaPay callback error', ['message' => $e->getMessage()]);

            return redirect()->route('subscriptions.upgrade')
                ->with('error', 'Erreur validation paiement : ' . $e->getMessage());
        }
    }

    /**
     * Redirection immédiate vers le guichet de paiement FedaPay
     */
    public function showPaymentPage(Company $company)
    {
        $activeSub = $company->subscription()->where('status', 'active')->first();
        if ($activeSub) {
            return redirect()->route('dashboard')->with('success', 'Votre abonnement est actif.');
        }

        $subscription = $company->subscription()
            ->where('status', 'pending_payment')
            ->with('plan')
            ->latest()
            ->first();

        if ($subscription) {
            try {
                // Récupérer le user lié à la company
                $user = $company->user; // adapte selon ta relation

                $phone = $user?->phone ? preg_replace('/\D/', '', $user->phone) : '';
                if (str_starts_with($phone, '228')) {
                    $phone = substr($phone, 3);
                }

                $transactionData = [
                    'description'  => 'Paiement abonnement - ' . $subscription->plan->name,
                    'amount'       => (int) $subscription->total_amount,
                    'currency'     => ['iso' => 'XOF'], // ← format correct comme processUpgrade
                    'callback_url' => route('registration.payment.callback', ['company' => $company->id]),
                    'customer'     => [
                        'firstname' => $company->compagny_name,
                        'lastname'  => 'Entreprise',
                        'email'     => $company->email ?? 'contact@' . Str::slug($company->compagny_name) . '.com',
                    ]
                ];

                if (!empty($phone)) {
                    $transactionData['customer']['phone_number'] = [
                        'number'  => $phone,
                        'country' => 'TG'
                    ];
                }

                $transaction = Transaction::create($transactionData);
                $token = $transaction->generateToken();

                return redirect()->away($token->url);

            } catch (Exception $e) {
                Log::error('FedaPay showPaymentPage error', [
                    'message'    => $e->getMessage(),
                    'company_id' => $company->id,
                    'amount'     => (int) $subscription->total_amount,
                    'env'        => config('services.fedapay.environment'),
                    'key_set'    => !empty(config('services.fedapay.secret_key')),
                ]);

                return redirect()->route('login')
                    ->with('error', 'Impossible de générer le lien de paiement : ' . $e->getMessage());
            }
        }

        $canceledSub = $company->subscription()->where('status', 'canceled')->first();
        if ($canceledSub) {
            return redirect()->route('login')
                ->with('error', 'Votre abonnement a été résilié. Veuillez contacter le Super Administrateur.');
        }

        return redirect()->route('login')
            ->with('error', 'Vous n\'avez pas d\'abonnement. Veuillez contacter le Super Administrateur.');
    }

    /**
     * Callback de retour après paiement d'inscription d'une entreprise
     */
    public function registrationPaymentCallback(Request $request, Company $company)
    {
        $transactionId = $request->input('id');

        if ($transactionId) {
            try {
                $transaction = Transaction::retrieve($transactionId);

                if ($transaction->status === 'approved') {
                    $subscription = $company->subscription()
                        ->where('status', 'pending_payment')
                        ->latest() // 🟡 FIX : prendre le plus récent
                        ->first();

                    if ($subscription) {
                        $subscription->update([
                            'status'    => 'active',
                            'starts_at' => now(),
                            'ends_at'   => now()->addMonths($subscription->duration_months),
                        ]);

                        // 🟡 FIX : vider la session renewal si c'était un renouvellement
                        session()->forget('renewal_data');

                        return redirect()
                            ->route('login')
                            ->with('success', __('Votre entreprise a été créée et votre abonnement est désormais actif ! Vous pouvez vous connecter.'));
                    }
                }
            } catch (Exception $e) {
                return redirect()
                    ->route('subscription.payment', ['company' => $company->id])
                    ->with('error', __('Erreur lors de la confirmation : ') . $e->getMessage());
            }
        }

        return redirect()
            ->route('subscription.payment', ['company' => $company->id])
            ->with('error', __('Le paiement a échoué ou a été annulé. Veuillez réessayer.'));
    }

    /**
     * Affiche l'abonnement courant de l'entreprise
     */

        public function paymentSuccess(Request $request, $companyId)
        {
            try {
                // Récupérer la notification stockée
                $notificationData = session('pending_payment_notification');

                if ($notificationData) {
                    // Marquer le paiement comme réussi
                    $company = Company::find($companyId);
                    if ($company && $company->subscription) {
                        $company->subscription->update(['status' => 'active']);
                    }

                    // Stocker la notification de succès
                    session()->flash('payment_success_notification', [
                        'plan_name' => $notificationData['plan_name'] ?? 'sélectionné',
                        'company_id' => $companyId,
                    ]);

                    session()->forget('pending_payment_notification');
                }

                // Rediriger vers le dashboard avec notification
                return redirect()->route('dashboard')
                    ->with('success', 'Paiement effectué avec succès !')
                    ->with('show_payment_notification', true);

            } catch (Exception $e) {
                Log::error('Payment success error', [
                    'message' => $e->getMessage(),
                    'company_id' => $companyId
                ]);

                return redirect()->route('dashboard')
                    ->with('error', 'Une erreur est survenue lors de la validation du paiement.');
            }
        }



    public function showCurrentSubscription()
    {
        $user = Auth::user();
        $company = $user->company;

        $activeSubscription = $company->subscription()
            ->where('status', 'active')
            ->with('plan')
            ->first();

        // FIX : uniformisé → subscriptions() (relation hasMany)
        $oldSubscriptions = $company->subscriptions()
            ->whereIn('status', ['canceled', 'expired'])
            ->with('plan')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('superadmin.subscriptions.show', compact('activeSubscription', 'oldSubscriptions'));
    }
    /**
     * Affiche le formulaire de renouvellement
     */
    public function renewalForm()
    {
        $user = Auth::user();
        $company = $user->company;

        $activeSubscription = $company->subscription()
            ->where('status', 'active')
            ->with('plan')
            ->first();

        if (!$activeSubscription) {
            return redirect()->route('company.subscription')
                ->with('error', 'Vous n\'avez pas d\'abonnement actif à renouveler.');
        }

        $plans = Plan::where('price', '>', 0)->get();

        return view('superadmin.subscriptions.renewal', compact('activeSubscription', 'plans'));
    }

    /**
     * Traite le renouvellement
     */
    public function processRenewal(Request $request, Subscription $subscription)
    {
        $request->validate([
            'duration_months' => 'required|integer|min:1|max:36',
            'plan_id'         => 'required|exists:plans,id',
        ]);

        $user = Auth::user()->company;
        $plan = Plan::findOrFail($request->plan_id);
        $months = intval($request->duration_months);
        $totalAmount = $plan->price * $months;

        // 1. Créer la souscription en attente de paiement
        $newSubscription = Subscription::create([
            'company_id'      => $user->id,
            'plan_id'         => $plan->id,
            'duration_months' => $months,
            'total_amount'    => $totalAmount,
            'status'          => 'pending_payment',
            'starts_at'       => null,
            'ends_at'         => null,
        ]);

        // 2. Session pour le traitement du webhook ou callback FedaPay
        session([
            'renewal_data' => [
                'old_subscription_id' => $subscription->id,
                'new_subscription_id' => $newSubscription->id,
                'plan_id'             => $plan->id,
                'duration_months'     => $months,
                'total_amount'        => $totalAmount,
            ]
        ]);

        // 3. Appel de l'API FedaPay
        try {
            // Remplacer par vos configurations FedaPay (ou vos variables de fichier .env)
            \FedaPay\FedaPay::setApiKey(config('services.fedapay.secret_key'));
            \FedaPay\FedaPay::setEnvironment(config('services.fedapay.environment'));

            $transaction = \FedaPay\Transaction::create([
                'description' => "Renouvellement " . $plan->name . " (" . $months . " mois)",
                'amount' => $totalAmount,
                'currency' => ['iso' => 'XOF'],
                'callback_url' => route('subscriptions.callback'), // Votre route callback de retour
                'customer' => [
                    'firstname' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ]
            ]);

            $token = $transaction->generateToken();

            // On redirige directement vers l'URL sécurisée générée par FedaPay
            return redirect()->away($token->url);

        } catch (\Exception $e) {
            // En cas de crash de l'API, on nettoie la BDD et on revient en arrière avec l'erreur
            $newSubscription->delete();
            return back()->with('error', 'Échec de l\'initialisation du paiement FedaPay : ' . $e->getMessage());
        }
    }

    /**
     * Traite le changement de plan immédiat (Upgrade)
     */
    public function processUpgrade(Request $request, $planId)
    {
        $plan = Plan::findOrFail($planId);
        $user = Auth::user();

        try {
            $phone = $user->phone ? preg_replace('/\D/', '', $user->phone) : '';
            if (str_starts_with($phone, '228')) {
                $phone = substr($phone, 3);
            }

            $transactionData = [
                'description'  => 'Changement de plan - ' . $plan->name,
                'amount'       => (int) $plan->price,
                'currency'     => ['iso' => 'XOF'],
                'callback_url' => route('subscriptions.callback', ['plan_id' => $plan->id]),
                'customer'     => [
                    'firstname' => $user->firstname ?? 'Client',
                    'lastname'  => $user->lastname ?? 'Abonne',
                    'email'     => $user->email,
                ]
            ];

            if (!empty($phone)) {
                $transactionData['customer']['phone_number'] = [
                    'number'  => $phone,
                    'country' => 'TG'
                ];
            }
            // dd(config('services.fedapay.secret_key'), config('services.fedapay.environment'));
            $transaction = Transaction::create($transactionData);
            // dd($transaction->status, $transaction->id);
            $token = $transaction->generateToken();

            return redirect()->away($token->url);

        } catch (Exception $e) {
            Log::error('FedaPay error', [
                'message' => $e->getMessage(),
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'amount'  => (int) $plan->price,
                'phone'   => $phone,
                'env'     => config('services.fedapay.environment'),
                'key_set' => !empty(config('services.fedapay.secret_key')),
            ]);
            return back()->with('error', $e->getMessage());
        }
    }


        /**
     * Affiche la liste des factures
     */
    public function invoices()
    {
        $user = Auth::user();
        $company = $user->company;

        $subscriptions = $company->subscriptions()
            ->with('plan')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('superadmin.subscriptions.invoices', compact('subscriptions'));
    }

    /**
     * Télécharge une facture (PDF)
     */
    public function downloadInvoice(Subscription $subscription)
    {
        $user = Auth::user();

        if ($subscription->company_id !== $user->company->id) {
            abort(403, 'Accès non autorisé.');
        }

        $pdf = Pdf::loadView('superadmin.subscriptions.invoice-pdf', compact('subscription'));

        return $pdf->download('facture_' . $subscription->id . '_' . now()->format('Ymd') . '.pdf');
    }

    public function handleWebhook(Request $request)
    {
        // 1. Récupérer la signature FedaPay depuis les entêtes HTTP
        $sigHeader = $request->header('X-Fedapay-Signature');
        $payload = $request->getContent();
        $secret = env('FEDAPAY_WEBHOOK_SECRET');

        try {
            // 2. Vérifier et construire l'événement de manière sécurisée
            // Cela lève une exception automatiquement si la clé ou la signature est fausse
            $event = \FedaPay\Webhook::constructEvent($payload, $sigHeader, $secret);

        } catch (\UnexpectedValueException $e) {
            Log::error('Payload invalide : ' . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\FedaPay\Error\SignatureVerification $e) {
            Log::error('Signature Webhook FedaPay invalide !');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // 3. Si la signature est validée, on extrait les données sereinement
        if ($event->name === 'transaction.approved') {
            // Le SDK FedaPay stocke l'objet de la transaction dans la propriété 'data' de l'événement
            $transactionData = $event->data;

            // Extraction du statut et des infos clients (gestion tableau ou objet selon le SDK)
            $status = is_array($transactionData) ? $transactionData['status'] : $transactionData->status;
            $customer = is_array($transactionData) ? $transactionData['customer'] : $transactionData->customer;
            $customerEmail = is_array($customer) ? ($customer['email'] ?? null) : ($customer->email ?? null);

            if ($status === 'approved') {
                // Rechercher la souscription en attente de l'entreprise associée à cet utilisateur
                $subscription = Subscription::where('status', 'pending_payment')
                    ->whereHas('company.users', function($query) use ($customerEmail) {
                        $query->where('email', $customerEmail);
                    })
                    ->latest()
                    ->first();

                if ($subscription) {
                    $company = $subscription->company;
                    $activeSub = $company->subscriptions()->where('status', 'active')->first();

                    // Déterminer la date de début de l'abonnement
                    $startsAt = Carbon::now();
                    if ($activeSub && Carbon::parse($activeSub->ends_at)->isFuture()) {
                        $startsAt = Carbon::parse($activeSub->ends_at);
                        $activeSub->update(['status' => 'expired']);
                    }

                    // Calculer la date de fin
                    $endsAt = $startsAt->copy()->addMonths($subscription->duration_months);

                    // Activer le nouvel abonnement
                    $subscription->update([
                        'status'    => 'active',
                        'starts_at' => $startsAt,
                        'ends_at'   => $endsAt,
                    ]);

                    // 🌟 NOUVEAU : Stocker le paiement reçu pour le mini-graphe et l'historique
                    Payment::create([
                        'company_id'             => $subscription->company_id,
                        'subscription_id'        => $subscription->id,
                        'fedapay_transaction_id' => is_array($transactionData) ? $transactionData['id'] : $transactionData->id,
                        'amount'                 => $subscription->total_amount,
                        'payment_method'         => is_array($transactionData) ? ($transactionData['mode'] ?? 'mobile_money') : ($transactionData->mode ?? 'mobile_money'),
                        'status'                 => 'approved',
                    ]);

                    Log::info("Abonnement activé et paiement enregistré automatiquement via Webhook pour l'entreprise ID: {$subscription->company_id}");

                    return response()->json(['status' => 'success'], 200);
                } else {
                    Log::warning("Webhook FedaPay : Aucune souscription 'pending_payment' trouvée pour le mail client : {$customerEmail}");
                }
            }
        }

        // Toujours renvoyer un statut 200 à FedaPay pour accuser réception de la notification
        return response()->json(['status' => 'ignored'], 200);
    }
}
