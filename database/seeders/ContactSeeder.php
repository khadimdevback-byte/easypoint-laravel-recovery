<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\ContactNotification;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $contacts = [
            [
                'first_name' => 'Aminata',
                'last_name' => 'Exemple',
                'email' => 'aminata.contact@example.test',
                'phone' => '+221 77 111 11 11',
                'company' => 'Entreprise Démo A',
                'employees' => '10-50',
                'subject' => 'DEMO - Demande de présentation',
                'message' => 'Nous aimerions découvrir les fonctionnalités de gestion des équipes et des présences.',
                'intent' => 'general',
                'status' => 'pending',
                'is_read' => false,
            ],
            [
                'first_name' => 'Moussa',
                'last_name' => 'Exemple',
                'email' => 'moussa.contact@example.test',
                'phone' => '+221 76 222 22 22',
                'company' => 'Entreprise Démo B',
                'employees' => '50-100',
                'subject' => 'DEMO - Demande de devis',
                'message' => 'Merci de nous communiquer un devis adapté à une équipe de démonstration.',
                'intent' => 'devis',
                'status' => 'processing',
                'is_read' => true,
            ],
            [
                'first_name' => 'Fatou',
                'last_name' => 'Exemple',
                'email' => 'fatou.contact@example.test',
                'phone' => '+221 70 333 33 33',
                'company' => 'Entreprise Démo C',
                'employees' => '1-10',
                'subject' => 'DEMO - Question sur la borne',
                'message' => 'Nous souhaitons obtenir des renseignements sur le pointage par borne.',
                'intent' => 'support',
                'status' => 'responded',
                'is_read' => true,
            ],
            [
                'first_name' => 'Ibrahima',
                'last_name' => 'Exemple',
                'email' => 'ibrahima.contact@example.test',
                'phone' => '+221 78 444 44 44',
                'company' => 'Entreprise Démo D',
                'employees' => '100+',
                'subject' => 'DEMO - Demande clôturée',
                'message' => 'Nous avons reçu les informations demandées. Merci pour votre retour.',
                'intent' => 'general',
                'status' => 'closed',
                'is_read' => true,
            ],
        ];

        foreach ($contacts as $data) {
            $isRead = $data['is_read'];
            unset($data['is_read']);

            $contact = Contact::firstOrCreate(
                ['email' => $data['email'], 'subject' => $data['subject']],
                [
                    ...$data,
                    'is_read' => $isRead,
                    'read_at' => $isRead ? now() : null,
                    'responded_at' => $data['status'] === 'responded' ? now() : null,
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'EasyPoint demo seeder',
                ]
            );

            ContactNotification::firstOrCreate(
                ['contact_id' => $contact->id, 'type' => 'new_contact'],
                [
                    'user_id' => null,
                    'message' => 'Message de démonstration de ' . $contact->first_name . ' ' . $contact->last_name,
                    'data' => [
                        'contact_id' => $contact->id,
                        'name' => $contact->first_name . ' ' . $contact->last_name,
                        'email' => $contact->email,
                        'company' => $contact->company,
                        'intent' => $contact->intent,
                        'subject' => $contact->subject,
                    ],
                    'is_read' => $isRead,
                    'read_at' => $isRead ? now() : null,
                ]
            );
        }
    }
}
