<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CompanySeeder::class);
        $company = Company::where('compagny_email', 'contact@sunuttech.example.test')->firstOrFail();

        $accounts = [
            [
                'user_type' => 'super_admin',
                'name' => 'Doe',
                'first_name' => 'John',
                'email' => 'superadmin@example.com',
                'company_id' => null,
            ],

            [
                'user_type' => 'admin',
                'name' => 'Diop',
                'first_name' => 'Awa',
                'email' => 'admin.demo@example.test',
                'company_id' => $company->id,
            ],

            [
                'user_type' => 'manager',
                'name' => 'Ba',
                'first_name' => 'Mamadou',
                'email' => 'manager.demo@example.test',
                'company_id' => $company->id,
            ],

            [
                'user_type' => 'controller',
                'name' => 'Ndiaye',
                'first_name' => 'Fatou',
                'email' => 'controller.demo@example.test',
                'company_id' => $company->id,
            ],

            [
                'user_type' => 'user',
                'name' => 'Fall',
                'first_name' => 'Ibrahima',
                'email' => 'user.demo@example.test',
                'company_id' => $company->id,
            ],
            
            [
                'user_type' => 'customer',
                'name' => 'Sow',
                'first_name' => 'Mariama',
                'email' => 'customer.demo@example.test',
                'company_id' => $company->id,
            ],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                [
                    ...$account,
                    'phone' => '+221 77 000 00 00',
                    'password' => 'password',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
