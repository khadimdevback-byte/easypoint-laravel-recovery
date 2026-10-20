<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NouvelleCandidatureNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $candidature;

    public function __construct($user, $candidature)
    {
        $this->user = $user;
        $this->candidature = $candidature;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => $this->message(),
        ]);
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->message(),
        ];
    }

    private function message(): string
    {
        $offer = $this->candidature->offre;
        $offerName = $offer?->fonction?->nom_fonction
            ?? $offer?->description_offre
            ?? 'une offre';

        return "{$this->user->name} a postulé pour l'offre {$offerName}.";
    }
}
