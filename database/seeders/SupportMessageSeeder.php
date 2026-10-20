<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\SupportMessage;
use App\Models\User;
use Illuminate\Database\Seeder;

class SupportMessageSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('compagny_email', 'contact@sunuttech.example.test')->firstOrFail();
        $admin = User::where('email', 'admin.demo@example.test')->firstOrFail();

        $requests = [
            [
                'subject' => 'DEMO - Question sur les horaires de travail',
                'category' => 'question',
                'message' => 'Comment configurer des horaires différents selon les jours de la semaine ?',
                'status' => 'en_attente',
            ],
            [
                'subject' => 'DEMO - Assistance sur les pointages',
                'category' => 'bug',
                'message' => 'Nous souhaitons vérifier les pointages enregistrés pour un employé sur un site.',
                'status' => 'en_cours',
            ],
            [
                'subject' => 'DEMO - Question de facturation',
                'category' => 'facturation',
                'message' => 'Où pouvons-nous consulter les informations de facturation de notre abonnement ?',
                'status' => 'resolu',
            ],
        ];

        foreach ($requests as $request) {
            $message = SupportMessage::firstOrCreate(
                [
                    'company_id' => $company->id,
                    'user_id' => $admin->id,
                    'subject' => $request['subject'],
                    'parent_id' => null,
                ],
                [
                    'category' => $request['category'],
                    'message' => $request['message'],
                    'status' => $request['status'],
                    'sender_type' => 'company',
                ]
            );

            if ($request['status'] === 'en_cours') {
                SupportMessage::firstOrCreate(
                    [
                        'company_id' => $company->id,
                        'subject' => 'Re: ' . $request['subject'],
                        'parent_id' => $message->id,
                        'sender_type' => 'superadmin',
                    ],
                    [
                        'user_id' => null,
                        'category' => $request['category'],
                        'message' => 'Nous analysons cette demande. Merci de nous transmettre la période concernée si nécessaire.',
                        'status' => 'en_cours',
                    ]
                );
            }
        }
    }
}
