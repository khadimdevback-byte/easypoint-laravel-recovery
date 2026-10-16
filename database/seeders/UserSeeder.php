<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Création d'un super admin
        $superAdmin = User::create([
            'user_type' => 'super_admin',
            'name' => 'Doe',
            'first_name' => 'John',
            'n_social' => '1 84 12 75 123 456 78',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('password'),
            'avatar' => 'https://st2.depositphotos.com/1491329/9010/i/450/depositphotos_90105542-stock-photo-beautiful-spa-model-girl.jpg',
            'status' => 'active',
        ]);

    }
}


