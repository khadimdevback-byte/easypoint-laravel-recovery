<?php

use App\Models\Contact;
use App\Models\Company;
use App\Models\SupportMessage;
use App\Models\User;

test('demo support messages and contact enquiries appear in the super admin pages', function () {
    $this->seed();

    $superAdmin = User::where('email', 'superadmin@example.com')->firstOrFail();
    $company = Company::where('compagny_email', 'contact@sunuttech.example.test')->firstOrFail();

    expect(SupportMessage::where('company_id', $company->id)->count())->toBe(4)
        ->and(Contact::where('email', 'like', '%@example.test')->count())->toBe(4);

    $this->actingAs($superAdmin)
        ->get(route('superadmin.support.index'))
        ->assertOk()
        ->assertSee('DEMO - Question sur les horaires de travail')
        ->assertSee('DEMO - Assistance sur les pointages')
        ->assertSee('DEMO - Question de facturation');

    $this->get(route('admin.contacts.index'))
        ->assertOk()
        ->assertSee('DEMO - Demande de présentation')
        ->assertSee('DEMO - Demande de devis')
        ->assertSee('aminata.contact@example.test');

    $this->get(route('admin.contacts.index', ['status' => 'pending']))
        ->assertOk()
        ->assertSee('DEMO - Demande de présentation')
        ->assertDontSee('DEMO - Demande de devis');
});

test('support and contact demo seeders are idempotent', function () {
    $this->seed();
    $this->seed();

    $company = Company::where('compagny_email', 'contact@sunuttech.example.test')->firstOrFail();

    expect(SupportMessage::where('company_id', $company->id)->count())->toBe(4)
        ->and(Contact::where('email', 'like', '%@example.test')->count())->toBe(4);
});

test('notification demo seeder adds idempotent attendance notifications and the page marks them read', function () {
    $this->seed();
    $this->seed();

    $admin = User::where('email', 'admin.demo@example.test')->firstOrFail();
    $manager = User::where('email', 'manager.demo@example.test')->firstOrFail();
    $employee = User::where('email', 'user.demo@example.test')->firstOrFail();
    $superAdmin = User::where('email', 'superadmin@example.com')->firstOrFail();
    $message = "Fall a pointé l'arrivée à 08:05.";

    expect($admin->notifications()->count())->toBe(1)
        ->and($manager->notifications()->count())->toBe(1)
        ->and($employee->notifications()->count())->toBe(0)
        ->and($superAdmin->notifications()->count())->toBe(1);

    $this->actingAs($superAdmin)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertSee('Nouvelle entreprise ajoutée : SunuTech Démo.')
        ->assertDontSee('IBAN');

    $this->actingAs($admin)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertSee($message)
        ->assertSee('Non lue');

    $this->post('/notifications/mark-all-as-read')->assertOk();

    expect($admin->fresh()->unreadNotifications()->count())->toBe(0);

    $this->get(route('notifications.index'))
        ->assertOk()
        ->assertSee($message)
        ->assertSee('Lue');
});
