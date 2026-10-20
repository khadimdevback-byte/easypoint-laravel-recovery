<?php

use App\Notifications\CandidatureAcceptedNotification;
use App\Notifications\NouvelleCandidatureNotification;
use App\Notifications\SendCreateCompanyEmailNotification;

test('company notifications use a safe database payload alongside email', function () {
    $notification = new SendCreateCompanyEmailNotification([
        'compagny_name' => 'SunuTech Démo',
        'iban' => 'SENSITIVE-IBAN',
    ]);
    $notifiable = new stdClass();
    $payload = $notification->toDatabase($notifiable);

    expect($notification->via($notifiable))->toBe(['database', 'mail'])
        ->and($payload['message'])->toBe('Nouvelle entreprise ajoutée : SunuTech Démo.')
        ->and(json_encode($payload))->not->toContain('SENSITIVE-IBAN');
});

test('candidature notifications provide readable database messages from the offer function', function () {
    $notifiable = new stdClass();
    $candidature = (object) [
        'offre' => (object) [
            'fonction' => (object) ['nom_fonction' => 'Agent administratif'],
        ],
    ];

    $newApplication = new NouvelleCandidatureNotification((object) ['name' => 'Fall'], $candidature);
    $acceptedApplication = new CandidatureAcceptedNotification($candidature);

    expect($newApplication->toArray($notifiable)['message'])
        ->toBe("Fall a postulé pour l'offre Agent administratif.")
        ->and($acceptedApplication->via($notifiable))->toBe(['database', 'mail'])
        ->and($acceptedApplication->toArray($notifiable)['message'])
        ->toBe("Votre candidature pour l'offre Agent administratif a été acceptée.");
});
