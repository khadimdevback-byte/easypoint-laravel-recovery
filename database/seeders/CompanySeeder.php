<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Feature;
use App\Models\Fonction;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $this->call(FeaturesTableSeeder::class);

        $company = Company::updateOrCreate(
            ['compagny_email' => 'contact@sunuttech.example.test'],
            [
                'compagny_name' => 'SunuTech Démo',
                'phone' => '+221 77 000 00 00',
                'country' => 'Sénégal',
                'currency' => 'XOF',
                'website' => 'https://sunuttech.example.test',
                'timezone' => 'Africa/Dakar',
                'address' => 'Dakar, Sénégal',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );

        foreach (['Agent administratif', 'Comptable', 'Commercial', 'Technicien', 'Responsable RH'] as $name) {
            $fonction = Fonction::firstOrNew([
                'nom_fonction' => $name,
                'company_id' => $company->id,
            ]);
            $fonction->company_id = $company->id;
            $fonction->save();
        }

        $plan = Plan::updateOrCreate(
            ['slug' => 'demo-free'],
            [
                'name' => 'Démo gratuite',
                'price' => 0,
                'currency' => 'XOF',
                'max_employees' => -1,
                'dashboard_type' => 'Complet',
                'history_duration' => 'Illimitée',
                'support_type' => 'Email',
                'is_active' => true,
                'is_visible' => false,
            ]
        );

        $plan->features()->syncWithoutDetaching(
            Feature::whereIn('slug', [
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
                'attendancesBorne_management',
            ])
                ->pluck('id')
        );

        $subscription = Subscription::withTrashed()->updateOrCreate(
            ['company_id' => $company->id, 'plan_id' => $plan->id],
            [
                'duration_months' => 120,
                'total_amount' => 0,
                'starts_at' => now()->startOfDay(),
                'ends_at' => now()->addYears(10),
                'status' => 'active',
            ]
        );

        if ($subscription->trashed()) {
            $subscription->restore();
        }
    }
}
