<?php

use App\Models\Company;
use App\Models\Fonction;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('demo seeder creates one active account for every role and a linked company', function () {
    $this->seed();

    $company = Company::where('compagny_email', 'contact@sunuttech.example.test')->firstOrFail();
    $accounts = User::whereIn('email', [
        'superadmin@example.com',
        'admin.demo@example.test',
        'manager.demo@example.test',
        'controller.demo@example.test',
        'user.demo@example.test',
        'customer.demo@example.test',
    ])->get();

    expect($accounts)->toHaveCount(6);

    foreach ($accounts as $account) {
        expect($account->status)->toBe('active')
            ->and($account->email_verified_at)->not->toBeNull()
            ->and(Hash::check('password', $account->password))->toBeTrue();

        if ($account->user_type !== 'super_admin') {
            expect($account->company_id)->toBe($company->id);
        }
    }

    expect(Subscription::where('company_id', $company->id)
        ->where('status', 'active')
        ->where('starts_at', '<=', now())
        ->where('ends_at', '>=', now())
        ->exists())->toBeTrue();

    expect($company->activeSubscription->plan->features->pluck('slug')->all())
        ->toContain(
            'dashboard',
            'controllers_management',
            'employees_management',
            'customers_management',
            'workplaces_management',
            'attendances_management',
            'balance_sheet',
            'fonctions_management',
            'badges_management',
            'demandes_management',
            'attendancesBorne_management'
        );

    expect(Fonction::where('company_id', $company->id)->count())->toBe(5);
});

test('demo employee creation form lists the company functions', function () {
    $this->seed();
    $admin = User::where('email', 'admin.demo@example.test')->firstOrFail();

    $this->actingAs($admin)
        ->get(route('users.create', 'user'))
        ->assertOk()
        ->assertSee('Agent administratif')
        ->assertSee('Comptable')
        ->assertSee('Commercial')
        ->assertSee('Technicien')
        ->assertSee('Responsable RH');
});

test('function name validation rejects duplicates and allows keeping the current name when updating', function () {
    $this->seed();
    $admin = User::where('email', 'admin.demo@example.test')->firstOrFail();
    $fonction = Fonction::where('company_id', $admin->company_id)->firstOrFail();
    $otherFonction = Fonction::where('company_id', $admin->company_id)
        ->where('id', '!=', $fonction->id)
        ->firstOrFail();

    $this->actingAs($admin)
        ->post(route('fonctions.store'), [
            'nom_fonction' => $fonction->nom_fonction,
            'description_fonction' => 'Doublon de test',
        ])
        ->assertSessionHasErrors('nom_fonction');

    $this->put(route('fonction.update', $fonction->id), [
        'nom_fonction' => $fonction->nom_fonction,
        'description_fonction' => $fonction->description_fonction,
    ])->assertRedirect(route('fonctions.liste'));

    $this->put(route('fonction.update', $fonction->id), [
        'nom_fonction' => $otherFonction->nom_fonction,
        'description_fonction' => $fonction->description_fonction,
    ])->assertSessionHasErrors('nom_fonction');
});

test('demo admin menu lists existing company management features', function () {
    $this->seed();
    $admin = User::where('email', 'admin.demo@example.test')->firstOrFail();

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Contrôleurs')
        ->assertSee('Employé(e)s')
        ->assertSee('Clients')
        ->assertSee('Générer badges')
        ->assertSee('Lieux de travail')
        ->assertSee('Présences')
        ->assertSee('Bilan')
        ->assertSee('Fonctions')
        ->assertSee('Départs manquants')
        ->assertSee('Demandes');
});

test('super admin can deactivate and reactivate a company with matching request methods', function () {
    $this->seed();
    $superAdmin = User::where('user_type', 'super_admin')->firstOrFail();
    $company = Company::where('compagny_email', 'contact@sunuttech.example.test')->firstOrFail();
    $companyAdmin = User::where('company_id', $company->id)->where('user_type', 'admin')->firstOrFail();

    $this->actingAs($superAdmin)
        ->patch(route('companies.disabled', $company->id), ['reason' => 'Test de désactivation'])
        ->assertRedirect();

    expect($company->fresh()->status)->toBe('inactive')
        ->and($companyAdmin->fresh()->status)->toBe('inactive');

    $this->post(route('companies.activate', $company->id))
        ->assertRedirect();

    expect($company->fresh()->status)->toBe('active')
        ->and($companyAdmin->fresh()->status)->toBe('active');
});

test('demo manager menu shows team operations without admin-only sections', function () {
    $this->seed();
    $manager = User::where('email', 'manager.demo@example.test')->firstOrFail();

    $this->actingAs($manager)
        ->get(route('manager.dashboard'))
        ->assertOk()
        ->assertSee('Contrôleurs')
        ->assertSee('Employé(e)s')
        ->assertSee('Lieux de travail')
        ->assertSee('Présences')
        ->assertDontSee('Clients')
        ->assertDontSee('Générer badges')
        ->assertDontSee('Bilan')
        ->assertDontSee('Fonctions')
        ->assertDontSee('Départs manquants')
        ->assertDontSee('Demandes');
});

test('user creation rejects an email that is already in use', function () {
    $this->seed();
    $admin = User::where('email', 'admin.demo@example.test')->firstOrFail();

    $this->actingAs($admin)
        ->post(route('users.store'), [
            'user_type' => 'user',
            'company_id' => $admin->company_id,
            'email' => $admin->email,
        ])
        ->assertSessionHasErrors('email');
});

test('updating a user without a weekly schedule saves unchecked days as false', function () {
    $this->seed();
    $admin = User::where('email', 'admin.demo@example.test')->firstOrFail();
    $controller = User::where('email', 'controller.demo@example.test')->firstOrFail();

    $this->actingAs($admin)
        ->patch(route('users.update', ['user_type' => 'controller', 'id' => $controller->id]), [
            'user_type' => 'controller',
            'company_id' => $admin->company_id,
            'name' => $controller->name,
            'first_name' => $controller->first_name,
            'email' => $controller->email,
            'status' => 'active',
        ])
        ->assertRedirect(route('users.index', 'controller'));

    $controller->refresh();
    foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
        expect($controller->{$day . '_checked'})->toBeFalse();
    }
});

test('demo seeder can run repeatedly without creating duplicate accounts or companies', function () {
    $this->seed();
    $this->seed();

    expect(Company::where('compagny_email', 'contact@sunuttech.example.test')->count())->toBe(1)
        ->and(User::where('email', 'admin.demo@example.test')->count())->toBe(1);
});

test('demo admin can authenticate and open the dashboard', function () {
    $this->seed();

    $response = $this->post(route('login'), [
        'email' => 'admin.demo@example.test',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs(User::where('email', 'admin.demo@example.test')->firstOrFail());

    $this->get(route('dashboard'))->assertOk();
});

test('demo manager has a separate dashboard from admin', function () {
    $this->seed();
    $manager = User::where('email', 'manager.demo@example.test')->firstOrFail();

    $response = $this->withSession(['url.intended' => route('dashboard')])
        ->post(route('login'), [
            'email' => 'manager.demo@example.test',
            'password' => 'password',
        ]);

    $response->assertRedirect(route('manager.dashboard'));
    $this->assertAuthenticatedAs($manager);
    $this->get(route('manager.dashboard'))
        ->assertOk()
        ->assertSee('Espace manager');
    $this->get(route('dashboard'))->assertRedirect(route('manager.dashboard'));
});

test('demo customer can sign in to a restricted customer dashboard', function () {
    $this->seed();
    $customer = User::where('email', 'customer.demo@example.test')->firstOrFail();

    $response = $this->withSession(['url.intended' => route('dashboard')])
        ->post(route('login'), [
            'email' => 'customer.demo@example.test',
            'password' => 'password',
        ]);

    $response->assertRedirect(route('customer.dashboard'));
    $this->assertAuthenticatedAs($customer);
    $this->get(route('customer.dashboard'))
        ->assertOk()
        ->assertSee('Mon espace client')
        ->assertSee('Mon profil')
        ->assertSee('SunuTech Démo')
        ->assertSee('Déconnexion')
        ->assertDontSee('Mon abonnement');
    $this->get(route('dashboard'))->assertRedirect(route('customer.dashboard'));
});

test('demo employee and controller are sent to their own dashboards', function () {
    $this->seed();

    $response = $this->post(route('login'), [
        'email' => 'user.demo@example.test',
        'password' => 'password',
    ]);
    $response->assertRedirect(route('employee.dashboard'));
    $this->get(route('employee.dashboard'))
        ->assertOk()
        ->assertSee('Tableau de bord')
        ->assertSee('Mes présences')
        ->assertSee('Disponibilités et offres')
        ->assertSee('Mon profil')
        ->assertSee('Déconnexion')
        ->assertDontSee('Mon abonnement');
    $this->post(route('logout'));

    $response = $this->post(route('login'), [
        'email' => 'controller.demo@example.test',
        'password' => 'password',
    ]);
    $response->assertRedirect(route('dashboard.controller'));
    $this->get(route('dashboard.controller'))
        ->assertOk()
        ->assertSee('Tableau de bord Contrôleur')
        ->assertSee('Lieux de travail')
        ->assertSee('Présences')
        ->assertSee('Bilan')
        ->assertDontSee('Borne indisponible')
        ->assertDontSee('Mon abonnement')
        ->assertDontSee('Plan Actuel');
    $this->get(route('dashboard'))->assertRedirect(route('dashboard.controller'));
});

test('demo super admin is sent to the super admin dashboard', function () {
    $this->seed();

    $response = $this->post(route('login'), [
        'email' => 'superadmin@example.com',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('dashboard.superadmin'));
    $this->get(route('dashboard.superadmin'))
        ->assertOk()
        ->assertSee('Tableau de bord Super Admin');
    $this->get(route('dashboard'))->assertRedirect(route('dashboard.superadmin'));
});
