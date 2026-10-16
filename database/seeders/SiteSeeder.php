<?php

namespace Database\Seeders;

use App\Models\Site;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Site::create([
            'company_id' => 1, // Assurez-vous que l'ID correspond à une entreprise existante
            'customer_id' => 5, // Assurez-vous que l'ID correspond à un client existant
            'controller_id' => 4, // Assurez-vous que l'ID correspond à un contrôleur existant
            'user_id' => 6, // Assurez-vous que l'ID correspond à un utilisateur existant
            'name' => 'Main Office',
            'address' => '101 Main St',
            'description' => 'Main office site for operations.',
            'radius' => 50, // En mètres
            'latitude' => 6.1652327,
            'longitude' => 1.2660681,
            'start_time' => '09:00',
            'break_time' => '12:00',
            'resumption_time' => '13:00',
            'end_time' => '17:00',
            'late_mark_duration' => 15,
            'status' => 'active',
        ]);

        // Ajoutez d'autres sites si nécessaire
    }
}
