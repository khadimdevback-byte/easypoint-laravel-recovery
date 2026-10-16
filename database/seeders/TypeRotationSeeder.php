<?php

namespace Database\Seeders;

use App\Models\TypeRotation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeRotationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Définir les types de rotation avec horaires
        $typesRotations = [
            [
                'nom' => '3x8',
                'nb_repartitions' => 3,
                'systeme_rotation' => json_encode([
                    'jour1' => [
                        'type' => 'Matin',
                        'heure_debut' => '06:00:00',
                        'heure_fin' => '14:00:00'
                    ],
                    'jour2' => [
                        'type' => 'Après-midi',
                        'heure_debut' => '14:00:00',
                        'heure_fin' => '22:00:00'
                    ],
                    'jour3' => [
                        'type' => 'Nuit',
                        'heure_debut' => '22:00:00',
                        'heure_fin' => '06:00:00'
                    ],
                ])
            ],
            [
                'nom' => '2x12',
                'nb_repartitions' => 2,
                'systeme_rotation' => json_encode([
                    'jour1' => [
                        'type' => 'Jour',
                        'heure_debut' => '08:00:00',
                        'heure_fin' => '20:00:00'
                    ],
                    'jour2' => [
                        'type' => 'Nuit',
                        'heure_debut' => '20:00:00',
                        'heure_fin' => '08:00:00'
                    ],
                ])
            ],
            [
                'nom' => '6x2',
                'nb_repartitions' => 6,
                'systeme_rotation' => json_encode([
                    'jour1' => [
                        'type' => 'Matin',
                        'heure_debut' => '06:00:00',
                        'heure_fin' => '14:00:00'
                    ],
                    'jour2' => [
                        'type' => 'Matin',
                        'heure_debut' => '06:00:00',
                        'heure_fin' => '14:00:00'
                    ],
                    'jour3' => [
                        'type' => 'Matin',
                        'heure_debut' => '06:00:00',
                        'heure_fin' => '14:00:00'
                    ],
                    'jour4' => [
                        'type' => 'Matin',
                        'heure_debut' => '06:00:00',
                        'heure_fin' => '14:00:00'
                    ],
                    'jour5' => [
                        'type' => 'Matin',
                        'heure_debut' => '06:00:00',
                        'heure_fin' => '14:00:00'
                    ],
                    'jour6' => [
                        'type' => 'Matin',
                        'heure_debut' => '06:00:00',
                        'heure_fin' => '14:00:00'
                    ],
                    'jour7' => [
                        'type' => 'Repos',
                        'heure_debut' => null,
                        'heure_fin' => null
                    ],
                    'jour8' => [
                        'type' => 'Repos',
                        'heure_debut' => null,
                        'heure_fin' => null
                    ],
                    'jour9' => [
                        'type' => 'Matin',
                        'heure_debut' => '06:00:00',
                        'heure_fin' => '14:00:00'
                    ],
                    'jour10' => [
                        'type' => 'Matin',
                        'heure_debut' => '06:00:00',
                        'heure_fin' => '14:00:00'
                    ],
                    'jour11' => [
                        'type' => 'Matin',
                        'heure_debut' => '06:00:00',
                        'heure_fin' => '14:00:00'
                    ],
                    'jour12' => [
                        'type' => 'Matin',
                        'heure_debut' => '06:00:00',
                        'heure_fin' => '14:00:00'
                    ],
                    'jour13' => [
                        'type' => 'Repos',
                        'heure_debut' => null,
                        'heure_fin' => null
                    ],
                    'jour14' => [
                        'type' => 'Repos',
                        'heure_debut' => null,
                        'heure_fin' => null
                    ]
                ])
            ]
        ];

        // Insérer ou mettre à jour les types de rotation dans la base de données
        foreach ($typesRotations as $type) {
            TypeRotation::updateOrCreate(['nom' => $type['nom']], $type);
        }

        // Message de confirmation
        $this->command->info('Types de rotation insérés avec succès!');
    }
}

