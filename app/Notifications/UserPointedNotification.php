<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserPointedNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $time;

    /**
     * Create a new notification instance.
     */
    public function __construct($user, $time)
    {
        $this->user = $user;
        $this->time = $time;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the Database representation of the notification.
     */
    public function toDatabase(object $notifiable)
    {
        return [
            'message' => "{$this->user->name} a pointé l'arrivée à {$this->time->format('H:i')}.",
            'user_id' => $this->user->id,
            'avatar' => $this->user->avatar,
            'time' => $this->time,
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
