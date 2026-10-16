<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer les utilisateurs existants de l'entreprise 1
        $userIds = User::where('company_id', 1)
            ->pluck('id')
            ->all();

        // Vérifier qu'il existe des utilisateurs
        if (empty($userIds)) {
            $this->command->warn(
                'Aucun utilisateur trouvé pour l’entreprise 1.'
            );

            return;
        }

        // Mois pour lesquels générer les pointages
        $months = [1, 3, 4, 5, 10, 12];

        foreach ($months as $month) {
            // Utiliser le nombre réel de jours du mois
            $daysInMonth = Carbon::create(2024, $month, 1)
                ->daysInMonth;

            foreach ($userIds as $userId) {
                for ($day = 1; $day <= $daysInMonth; $day++) {

                    $arriveeTimestamp = Carbon::create(
                        2024, $month, $day, rand(8, 9), rand(0, 59)
                    );

                    $departTimestamp = Carbon::create(
                        2024, $month, $day, rand(17, 18), rand(0, 59)
                    );

                    // Pointage d'arrivée
                    DB::table('attendances')->insert([
                        'user_id' => $userId,
                        'company_id' => 1,
                        'site_id' => 4,
                        'timestamp' => $arriveeTimestamp,
                        'type' => 'arrivee',
                        'created_at' => $arriveeTimestamp,
                    ]);

                    // Pointage de départ
                    DB::table('attendances')->insert([
                        'user_id' => $userId,
                        'company_id' => 1,
                        'site_id' => 4,
                        'timestamp' => $departTimestamp,
                        'type' => 'depart',
                        'created_at' => $departTimestamp,
                    ]);
                }
            }
        }

        $this->command->info(
            'Les pointages ont été générés avec succès.'
        );
    }
}
