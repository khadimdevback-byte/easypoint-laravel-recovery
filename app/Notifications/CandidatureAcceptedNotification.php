<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class CandidatureAcceptedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $candidature;

    public function __construct($candidature)
    {
        $this->candidature = $candidature;
    }
    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Candidature acceptée')
                    ->greeting('Bonjour ' . ($notifiable->first_name ?? $notifiable->name) . ',')
                    ->line($this->message())
                    ->action('Accéder à mon compte', url('/'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->message(),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => $this->message(),
        ]);
    }

    private function message(): string
    {
        $offer = $this->candidature->offre;
        $offerName = $offer?->fonction?->nom_fonction
            ?? $offer?->description_offre
            ?? 'votre offre';

        return "Votre candidature pour l'offre {$offerName} a été acceptée.";
    }
}
