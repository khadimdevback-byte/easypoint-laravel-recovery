<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Feature;

class FeaturesTableSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            [
                'name' => 'Dashboard',
                'slug' => 'dashboard',
                'icon' => 'fa-chart-simple',
                'route' => 'dashboard',
                'is_active' => 1,
            ],
            [
                'name' => 'Controllers',
                'slug' => 'controllers_management',
                'icon' => 'fa-user-group',
                'route' => 'users.index',
                'is_active' => 1,
            ],
            [
                'name' => 'Employé(e)',
                'slug' => 'employees_management',
                'icon' => 'fa-users',
                'route' => 'users.index',
                'is_active' => 1,
            ],
            [
                'name' => 'Customers',
                'slug' => 'customers_management',
                'icon' => 'fa-circle-user',
                'route' => 'users.index',
                'is_active' => 1,
            ],
            [
                'name' => 'Workplaces (Lieux de travail)',
                'slug' => 'workplaces_management',
                'icon' => 'fa-map-location-dot',
                'route' => 'sites.index',
                'is_active' => 1,
            ],
            [
                'name' => 'Présences',
                'slug' => 'attendances_management',
                'icon' => 'fa-calendar-check',
                'route' => 'attendances.index',
                'is_active' => 1,
            ],
            [
                'name' => 'Balance sheet (Bilan)',
                'slug' => 'balance_sheet',
                'icon' => 'fa-file-invoice-dollar',
                'route' => 'balance.sheet.index',
                'is_active' => 1,
            ],
            [
                'name' => 'Fonctions',
                'slug' => 'fonctions_management',
                'icon' => 'fa-user-tie',
                'route' => 'fonctions.liste',
                'is_active' => 1,
            ],
            [
                'name' => 'Générer badges',
                'slug' => 'badges_management',
                'icon' => 'fa-id-badge',
                'route' => 'badges.select',
                'is_active' => 1,
            ],
            [
                'name' => 'Demandes (Congés/Absences)',
                'slug' => 'demandes_management',
                'icon' => 'fa-calendar',
                'route' => 'admin.demandes.index',
                'is_active' => 1,
            ],
            [
                'name' => 'Scanner Badge',
                'slug' => 'attendancesBorne_management',
                'icon' => null, // NULL selon votre dump SQL
                'route' => 'borne.index',
                'is_active' => 1,
            ],
        ];

        foreach ($features as $feature) {
            Feature::updateOrCreate(['slug' => $feature['slug']], $feature);
        }
    }
}
