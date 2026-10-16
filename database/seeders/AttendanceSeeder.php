<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        // 2. User IDs for company_id = 1
        $userIds = [17, 24]; // Exemple de IDs d'utilisateurs pour l'entreprise 1

        // 3. Seed Attendances for multiple months
        $months = [1, 3, 4, 5,10,12]; // Janvier à Mai
        foreach ($months as $month) {
            foreach ($userIds as $userId) {
                // Pour chaque mois, générez des pointages pour chaque utilisateur
                // Générer des pointages pour différents jours du mois
                for ($day = 1; $day <= 30; $day++) { // Simuler des pointages pour 28 jours
                    // Générer un temps aléatoire pour l'arrivée et le départ
                    $arriveeTimestamp = Carbon::create(2024, $month, $day, rand(8, 9), rand(0, 59));
                    $departTimestamp = Carbon::create(2024, $month, $day, rand(17, 18), rand(0, 59));

                    // Insérer le pointage "arrivee"
                    DB::table('attendances')->insert([
                        'user_id' => $userId,
                        'company_id' => 1,
                        'site_id' => 4, // Utiliser l'ID du site créé ci-dessus
                        'timestamp' => $arriveeTimestamp,
                        'type' => 'arrivee',
                        'created_at' => $arriveeTimestamp,
                    ]);

                    // Insérer le pointage "depart"
                    DB::table('attendances')->insert([
                        'user_id' => $userId,
                        'company_id' => 1,
                        'site_id' => 4, // Utiliser l'ID du site créé ci-dessus
                        'timestamp' => $departTimestamp,
                        'type' => 'depart',
                        'created_at' => $departTimestamp,
                    ]);
                }
            }
        }
    }
}
