<?php
use App\Http\Controllers\Admin\MissingDepartureController;
use App\Http\Controllers\FichePaieController;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\Superadmin\MenuManagementController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\UserController;
use App\Notifications\ResetPasswordCode;
use App\Http\Controllers\OffreController;
use App\Http\Controllers\EquipeController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FonctionController;
use App\Http\Controllers\RotationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceRotationController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Superadmin\SuperadmineController;
use App\Http\Controllers\BalanceSheetController;
use App\Http\Controllers\CandidatureAdminController;
use App\Http\Controllers\DisponibiliteController;
use App\Http\Controllers\MenuSettingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RepartitionController;
use App\Http\Controllers\RotationEquipeController;
use App\Http\Controllers\TypeRotationController;
use App\Http\Controllers\BadgeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CotisationSocialeController;
use App\Http\Controllers\DemandeController;
use App\Http\Controllers\Employee\EmployeeBilanController;
use App\Http\Controllers\Employee\EmployeeAccountController;
use App\Http\Controllers\Employee\EmployeeDashboardController;
use App\Http\Controllers\Employee\EmployeePlanningNotificationController;
use App\Http\Controllers\FooterController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\SiteRotationController;
use App\Http\Controllers\SiteVitrineController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\PaymentArchiveController;

// Routes pour exécuter les commandes Artisan
Route::get('admin/artisan', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    return "All Clear";
});

Route::get('/payment-success/{companyId}', [SubscriptionController::class, 'paymentSuccess'])
    ->name('payment.success');

Route::get('/inscription-resultat', [SiteVitrineController::class, 'demonstrationResult'])
    ->name('demonstration.result');


// Route pour vérifier les notifications en session
// Route pour vérifier les notifications en session
Route::get('/check-plan-notification', function () {
    // Vérifier les notifications de plan
    $notification = session('plan_notification');
    $showNotification = session('show_plan_notification', false);

    if ($notification && $showNotification) {
        session()->forget('show_plan_notification');
        return response()->json([
            'show' => true,
            'data' => $notification
        ]);
    }

    // Vérifier les notifications de paiement réussi
    $paymentNotification = session('payment_success_notification');
    if ($paymentNotification) {
        return response()->json([
            'show' => true,
            'data' => [
                'planType' => 'paid_success',
                'plan_name' => $paymentNotification['plan_name'],
                'company_id' => $paymentNotification['company_id'],
            ]
        ]);
    }

    return response()->json(['show' => false]);
})->name('check.plan.notification');

Route::get('admin/migrate', function () {
    Artisan::call('migrate');
    return "done";
});

Route::get('admin/seeder', function () {
    Artisan::call('db:seed');
    return "done";
});

Route::get('admin/migrate/notifications', function () {
    Artisan::call('notifications:table');
    Artisan::call('migrate');
    return "done";
});

// Rediriger la racine vers la page de login
Route::get('/', function () {
    return redirect('acceuil');
});

// Route pour le tableau de bord
Route::get('/dashboard', [DashboardController::class, 'dashboard'])->middleware(['auth', 'verified','check.subscription'])->name('dashboard');


Route::middleware(['auth'])->group(function () {
    Route::get('/demandes', [DemandeController::class, 'listeEntreprise'])->name('admin.demandes.index');
    Route::get('/demandes/ajouter', [DemandeController::class, 'create'])->name('demandes.ajouter');
    Route::post('/demandes/store', [DemandeController::class, 'enregistrerParAdmin'])->name('demandes.store');
    Route::get('/demandes/{id}/modifier', [DemandeController::class, 'edit'])->name('demandes.edit');
    Route::put('/demandes/{id}/update', [DemandeController::class, 'changerStatut'])->name('demandes.update');
    Route::delete('/demandes/{id}/delete', [DemandeController::class, 'destroy'])->name('demandes.delete');

    Route::get('/dashboard/controller', [DashboardController::class, 'controllerDashboard'])->name('dashboard.controller');
    Route::get('/dashboard/super-admin', [DashboardController::class, 'superAdminDashboard'])->name('dashboard.superadmin');
    Route::get('/payments/details/{mode}', [DashboardController::class, 'paymentDetails'])->name('superadmin.payments.details');

    // Routes pour la gestion du profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/company', [ProfileController::class, 'editCompany'])->name('profile.edit.company');
    Route::patch('/profile/company', [ProfileController::class, 'updateCompany'])->name('profile.update.company');

    // Routes pour les super-admin
    Route::resource('super-admin', SuperAdminController::class);
    Route::patch('/companies/disabled/{id}', [CompanyController::class, 'disabled'])->name('companies.disabled');
    Route::post('/companies/{company}/activate', [CompanyController::class, 'activate'])->name('companies.activate');

    // Routes pour la gestion des entreprises
    Route::resource('companies', CompanyController::class);
    Route::get('compagnie/{compagnie}/menus/edit', [MenuManagementController::class, 'edit'])->name('superadmin.menu-management.edit');
    Route::post('compagnie/{compagnie}/menus/update', [MenuManagementController::class, 'update'])->name('superadmin.menu-management.update');

    Route::prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::get('/subscriptions/create', [SubscriptionController::class, 'create'])->name('subscriptions.create');
        Route::post('/subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');
        Route::get('/subscriptions/{subscription}/edit', [SubscriptionController::class, 'edit'])->name('subscriptions.edit');
        Route::put('/subscriptions/{subscription}', [SubscriptionController::class, 'update'])->name('subscriptions.update');
        Route::delete('/subscriptions/{subscription}', [SubscriptionController::class, 'destroy'])->name('subscriptions.destroy');
        Route::resource('menus', \App\Http\Controllers\Superadmin\MenuController::class)->except(['show']);
    });

    Route::get('/companies-pending', [CompanyController::class, 'pendingIndex'])->name('companies.pending.index');

    // Routes pour la gestion des sites
    Route::resource('sites', SiteController::class);
    Route::get('/sites/{qr}', [SiteController::class, 'qr'])->name('sites.qr');
    Route::patch('sites/disabled/{id}', [SiteController::class, 'disabled'])->name('sites.disabled');

    // ============================================= //
    // ROUTES POUR LA GESTION DES UTILISATEURS       //
    // ============================================= //
    Route::get('/users/{user_type}', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/index_sans_horaire/{user_type}', [UserController::class, 'index_sans_horaire'])->name('users.index_sans_horaire');
    Route::post('users/store.simple',[UserController::class,'store_simple'])->name('users.store.simple');
    Route::get('/users/create.simple/{user_type}',[UserController::class,'createSimple'])->name('users.create.simple');
    Route::get('/users/{user_type}/edit.simple', [UserController::class, 'edit_simple'])->name('users.edit.simple');
    Route::patch('/users/{user_type}/update', [UserController::class, 'update_simple'])->name('users.update.simple');

    Route::get('/users/create/{user_type}', [UserController::class, 'create'])->name('users.create');
    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');

    // ROUTES CORRIGÉES avec paramètre id
    Route::get('/users/{user_type}/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::get('/users/{user_type}/show', [UserController::class, 'show'])->name('users.show');
    Route::patch('/users/{user_type}/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user_type}/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::patch('/users/disabled/{user_type}/{id}', [UserController::class, 'disabled'])->name('users.disabled');
    Route::post('/users/reactivate/{user_type}/{id}', [UserController::class, 'reactivate'])->name('users.reactivate');

    Route::get('/users/{user}/badge', [App\Http\Controllers\UserController::class, 'showBadge'])->name('users.badge');
    Route::get('/badges/select', [BadgeController::class, 'selectForm'])->name('badges.select');
    Route::post('/badges/generate', [BadgeController::class, 'generateBadges'])->name('badges.generate');

    Route::get('/fichepaie/fixe/generer', [FichePaieController::class, 'showFormGenererFixe'])->name('fichepaie.showFormGenererFixe');
    Route::post('/fichepaie/fixe/generer', [FichePaieController::class, 'genererFixe'])->name('fichepaie.genererFixe');
    Route::get('/fichepaie/rotatif/generer', [FichePaieController::class, 'showFormGenererRotatif'])->name('fichepaie.showFormGenererRotatif');
    Route::post('/fichepaie/rotatif/generer', [FichePaieController::class, 'genererRotatif'])->name('fichepaie.genererRotatif');
    Route::get('/fichepaie/edit/{fichePaie}', [FichePaieController::class, 'edit'])->name('fichepaie.edit');
    Route::put('fichepaie/{id}', [FichePaieController::class, 'update'])->name('fichepaie.update');
    Route::get('/fiche-paies/finaliser', [FichePaieController::class, 'formulaire'])->name('fichepaie.formulaire');
    Route::get('/fichepaies/fixes', [FichePaieController::class, 'indexFixe'])->name('fichepaie.fixe');
    Route::get('/fichepaies/rotatifs', [FichePaieController::class, 'indexRotatif'])->name('fichepaie.rotatif');
    Route::get('/fichepaie/{id}/detail', [FichePaieController::class, 'show'])->name('fichepaie.show');
    Route::get('/fichepaie/{id}', [FichePaieController::class, 'delete'])->name('fichepaie.delete');

    // Routes pour la gestion des présences
    Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('/attendances/{user}', [AttendanceController::class, 'show'])->name('attendances.show');
    Route::get('/borne-pointage/{siteId}', [AttendanceController::class, 'borneIndex'])->name('borne.index');
    Route::post('/borne/set-site', [AttendanceController::class, 'setBorneSite'])->name('pointage.set-site');
    Route::post('/pointage/badge', [AttendanceController::class, 'recordBadgeAttendance'])->name('pointages.badge.store');


    Route::get('/pointage', [AttendanceRotationController::class, 'pointage'])->name('pointage.attendance.rotation');
    Route::get('/equipes/{equipe}/users/{user}/pointages/{year}/{month}/{day}', [AttendanceRotationController::class, 'showDetail'])->name('pointage.attendances.show-detail-rotation');
    Route::get('/pointage/bilan', [AttendanceRotationController::class, 'bilan'])->name('pointage.bilan');

    // Routes pour la gestion des notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllNotificationsAsRead']);

    // Routes pour la gestion des bilans
    Route::get('/balance-sheet', [BalanceSheetController::class, 'index'])->name('balance.sheet.index');
    Route::get('/superadmin-balance-sheet', [BalanceSheetController::class, 'superAdminIndex'])->name('balance.sheet.superadmin.index');

    //Routes pour la liste des Disponibilités
    Route::get('/disponibilites', [DisponibiliteController::class, 'index'])->name('disponibilite.index');
    Route::get('/Listecandidatures', [CandidatureAdminController::class, 'index'])->name('candidatures.index');
    Route::post('/candidatures/{id}/accept', [CandidatureAdminController::class, 'accept'])->name('candidatures.accept');

    //Routes pour la gestion des Offres d'emploi
    Route::controller(OffreController::class)->prefix('offres')->group(function () {
        Route::get('/Ajouteroffre', 'create')->name('offres.Ajouter');
        Route::post('/Ajouteroffre', 'store')->name('offres.store');
        Route::get('/Listeoffre', 'index')->name('offres.Liste');
        Route::get('/offreparSite', 'grouper')->name('offres.grouper');
        Route::get('/modifierOffre/{offre}', 'edit')->name('offres.edit');
        Route::put('/updateOffre/{offre}', 'update')->name('offres.update');
        Route::delete('/supprimerOffre/{offre}', 'destroy')->name('offres.delete');
    });

    //Routes pour enregistrer les Fonctions Employé
    Route::controller(FonctionController::class)->prefix('fonctions')->group(function () {
        Route::get('/ajouterFonction', 'create')->name('fonctions.ajouter');
        Route::post('/Fonction', 'store')->name('fonctions.store');
        Route::get('/listeFonction', 'index')->name('fonctions.liste');
        Route::get('/modifierFonction/{fonction}', 'edit')->name('fonctions.edit');
        Route::put('/updateFonction/{fonction}', 'update')->name(('fonction.update'));
        Route::delete('/supprimerFonction/{fonction}', 'delete')->name('fonctions.delete');
    });

    Route::controller(EquipeController::class)->prefix('equipe')->group(function () {
        Route::get('/ajouter', 'create')->name('equipe.create');
        Route::post('/ajouter', 'store')->name('equipe.store');
        Route::get('/liste', 'index')->name('equipe.index');
    });

    //Route employee
    Route::get('/dashboard/employee', [EmployeeDashboardController::class, 'index'])->name('employee.dashboard');
    Route::get('/bilan/employee', [EmployeeBilanController::class, 'index'])->name('employee.bilan');
    Route::get('/account/employee', [EmployeeAccountController::class, 'index'])->name('employee.account');
    Route::get('/password/employee', [EmployeeAccountController::class, 'password'])->name('employee.password');
    Route::get('/planning/employee', [EmployeePlanningNotificationController::class, 'index'])->name('employee.planning_notification');
    Route::post('/planning/employee', [EmployeePlanningNotificationController::class, 'store'])->name('employee.planning_notification.store');
    Route::get('/offre/employee/{id}', [EmployeePlanningNotificationController::class, 'show'])->name('employee.planning_notification.show');
    Route::post('offre/employee/{id}/postuler', [EmployeePlanningNotificationController::class, 'postuler'])->name('employee.planning_notification.postuler');
    Route::post('candidature/employee/{id}/decliner', [EmployeePlanningNotificationController::class, 'decliner'])->name('employee.planning_notification.decliner');

    Route::get('/rotations/calendrier/{site_id}/{mois?}', [RotationController::class, 'genererCalendrier'])->name('rotations.calendrier');

    Route::controller(RepartitionController::class)->prefix('repartition')->group(function(){
        Route::get('Liste','index')->name('repartition.index');
    });

    Route::get('/plans/create', [PlanController::class, 'create'])->name('plans.create');
    Route::post('/plans', [PlanController::class, 'store'])->name('plans.store');
    Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
    Route::get('/plans/{plan}/edit', [PlanController::class, 'edit'])->name('plans.edit');
    Route::put('/plans/{plan}', [PlanController::class, 'update'])->name('plans.update');
    Route::delete('/plans/{plan}', [PlanController::class, 'destroy'])->name('plans.destroy');

    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('/subscriptions/create', [SubscriptionController::class, 'create'])->name('subscriptions.create');
    Route::post('/subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');
    Route::get('/subscriptions/{subscription}/edit', [SubscriptionController::class, 'edit'])->name('subscriptions.edit');
    Route::put('/subscriptions/{subscription}', [SubscriptionController::class, 'update'])->name('subscriptions.update');
    Route::delete('/subscriptions/{subscription}', [SubscriptionController::class, 'destroy'])->name('subscriptions.destroy');

    Route::get('/upgrade-plan', [SubscriptionController::class, 'upgradeView'])->name('subscriptions.upgrade');
    Route::post('/upgrade-plan/{plan}', [SubscriptionController::class, 'processUpgrade'])->name('subscriptions.processUpgrade');
    Route::get('/upgrade-plan/callback', [SubscriptionController::class, 'paymentCallback'])->name('subscriptions.callback');
    Route::get('/mon-abonnement', [SubscriptionController::class, 'showCurrentSubscription'])->name('company.subscription');
    Route::get('/renouveler-abonnement', [SubscriptionController::class, 'renewalForm'])->name('subscription.renewal');
    Route::post('/renouveler-abonnement/{subscription}', [SubscriptionController::class, 'processRenewal'])->name('subscription.process-renewal');
    Route::get('/mes-factures', [SubscriptionController::class, 'invoices'])->name('subscription.invoices');
    Route::get('/facture/{subscription}/download', [SubscriptionController::class, 'downloadInvoice'])->name('subscription.download-invoice');
    Route::post('/subscriptions/{subscription}/accept-manual', [SubscriptionController::class, 'acceptManualPayment'])->name('subscriptions.accept_manual');

    Route::get('/support', [SupportController::class, 'index'])->name('support.index');
    Route::get('/support/nouveau', [SupportController::class, 'create'])->name('support.create');
    Route::post('/support', [SupportController::class, 'store'])->name('support.store');
    Route::get('/support/{support}', [SupportController::class, 'show'])->name('support.show');
    Route::post('/support/{support}/reply', [SupportController::class, 'reply'])->name('support.reply');
    Route::delete('/support/{support}', [SupportController::class, 'destroy'])->name('support.destroy');

    Route::middleware(['superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('/support', [SuperadmineController::class, 'index'])->name('support.index');
        Route::get('/support/{support}', [SuperadmineController::class, 'show'])->name('support.show');
        Route::post('/support/{support}/reply', [SuperadmineController::class, 'reply'])->name('support.reply');
        Route::post('/support/{support}/status', [SuperadmineController::class, 'status'])->name('support.status');
        Route::delete('/support/{support}', [SuperadmineController::class, 'destroy'])->name('support.destroy');
    });

    // Routes pour le superadmin
    Route::middleware(['superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
        // Gestion du footer
        Route::get('/footer', [FooterController::class, 'index'])->name('footer.index');
        Route::get('/footer/edit', [FooterController::class, 'edit'])->name('footer.edit'); // Nouvelle route
        Route::put('/footer', [FooterController::class, 'update'])->name('footer.update');
        Route::post('/footer/reset', [FooterController::class, 'reset'])->name('footer.reset');
    });

    Route::resource('cotisations-sociales', CotisationSocialeController::class);
    Route::get('/missing-departures', [MissingDepartureController::class, 'index'])->name('admin.missing-departures');
    Route::post('/missing-departures/{id}/approve', [MissingDepartureController::class, 'approve'])->name('admin.missing-departures.approve');
    Route::post('/missing-departures/{id}/reject', [MissingDepartureController::class, 'reject'])->name('admin.missing-departures.reject');

    Route::prefix('superadmin')->middleware(['auth', 'superadmin'])->group(function () {
        Route::get('/payments-archive', [PaymentArchiveController::class, 'index'])->name('payments-archive.index');
        Route::get('/payments-archive/export', [PaymentArchiveController::class, 'export'])->name('payments-archive.export');
        Route::delete('/payments-archive/bulk-destroy', [PaymentArchiveController::class, 'bulkDestroy'])->name('payments-archive.bulk-destroy');
        Route::get('/payments-archive/{archive}', [PaymentArchiveController::class, 'show'])->name('payments-archive.show');
        Route::delete('/payments-archive/{archive}', [PaymentArchiveController::class, 'destroy'])->name('payments-archive.destroy');
    });
});

require __DIR__ . '/auth.php';

Route::get('/companies/create_company', [CompanyController::class, 'createCompany'])->name('companies.create.company');
Route::post('/companies/store_company', [CompanyController::class, 'storeCompany'])->name('companies.store.company');

Route::get('/acceuil',[SiteVitrineController::class,'acceuil'])->name('siteVitrine.acceuil');
Route::get('/apropos',[SiteVitrineController::class,'about'])->name('siteVitrine.apropos');
Route::get('/contact',[SiteVitrineController::class,'contact'])->name('siteVitrine.contact');
Route::get('/fonctionnalites',[SiteVitrineController::class,'fonctionnalites'])->name('siteVitrine.fonctionnalites');
Route::get('/demonstration',[SiteVitrineController::class,'demonstration'])->name('siteVitrine.demonstration');
Route::get('/tarifs',[SiteVitrineController::class,'tarifs'])->name('siteVitrine.tarifs');

Route::get('/subscription-payment/{company}', [SubscriptionController::class, 'showPaymentPage'])->name('subscription.payment');
Route::get('/registration-payment-callback/{company}', [SubscriptionController::class, 'registrationPaymentCallback'])->name('registration.payment.callback');

Route::prefix('contact')->name('contact.')->group(function () {
    Route::post('/submit', [ContactController::class, 'submit'])->name('submit');
});

Route::prefix('contacts')->name('contacts.')->group(function () {
    Route::get('/', [ContactController::class, 'adminIndex'])->name('index');
    Route::get('/{id}', [ContactController::class, 'adminShow'])->name('show');
    Route::put('/{id}/status', [ContactController::class, 'adminStatus'])->name('status');
    Route::delete('/{id}', [ContactController::class, 'adminDestroy'])->name('destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/notifications/unread', [ContactController::class, 'getUnreadNotifications']);
    Route::post('/admin/notifications/{id}/read', [ContactController::class, 'markNotificationAsRead']);
    Route::post('/admin/notifications/mark-all-read', [ContactController::class, 'markAllNotificationsAsRead']);
    Route::get('/admin/contacts', [ContactController::class, 'adminIndex'])->name('admin.contacts.index');
    Route::get('/admin/contacts/{id}', [ContactController::class, 'adminShow'])->name('admin.contacts.show');
    Route::put('/admin/contacts/{id}/status', [ContactController::class, 'adminStatus']);
    Route::delete('/admin/contacts/{id}', [ContactController::class, 'adminDestroy']);
});
