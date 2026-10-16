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
        return ['broadcast', 'database']; // Notifications en temps réel + base de données
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => "{$this->user->name} a postulé pour l'offre {$this->candidature->offre->title}.",
        ]);
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "{$this->user->name} a postulé pour l'offre {$this->candidature->offre->title}.",
        ];
    }
}
