<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
               // Création d'une entreprise avec un admin
               $company = Company::create([
                'admin_id' => 2, // L'ID de l'admin créé dans UserSeeder
                'name' => 'Tech Corp',
                'email' => 'contact@techcorp.com',
                'phone' => '0123456789',
                'website' => 'http://techcorp.com',
                'timezone' => 'UTC',
                'status' => 'active',
                'address' => '789 Tech St',
                'logo' => 'path/to/logo.png',
            ]);
    
            // Ajoutez d'autres entreprises si nécessaire
    }
}
