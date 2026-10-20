<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

test('super admin creation stores the super admin user type', function () {
    Notification::fake();

    $admin = User::create([
        'user_type' => 'super_admin',
        'name' => 'Existing Admin',
        'email' => 'existing-admin@example.com',
        'password' => Hash::make('password'),
    ]);

    $response = $this
        ->actingAs($admin)
        ->post(route('super-admin.store'), [
            'name' => 'New Admin',
            'first_name' => 'Test',
            'email' => 'new-admin@example.com',
            'password' => 'password123',
            'status' => 'active',
            'user_type' => 'super_admin',
        ]);

    $response->assertRedirect(route('users.index', 'super_admin'));

    $this->assertDatabaseHas('users', [
        'email' => 'new-admin@example.com',
        'user_type' => 'super_admin',
    ]);
});
