<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;


class SendCreateUserEmailNotification extends Notification
{
    use Queueable;
    protected $userData;

    /**
     * Create a new notification instance.
     */
    public function __construct($userData)
    {
        $this->userData = $userData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // $userData = $this->userData;
        // return (new MailMessage)
        // ->subject('Notification d\'un nouvel utilisateur')
        // ->greeting('Salut,')
        // ->line('Voici vos données de connexion:')
        // ->line('Email: ' . $userData['email'])
        // ->line('Nom d\'utilisateur: ' . $userData['username'])
        // ->line('Nom: ' . $userData['name'])
        // ->line('Prénom: ' . $userData['first_name'])
        // ->line('Numéro de téléphone: ' . $userData['phone'])
        // ->line('Numéro social: ' . $userData['n_social'])
        // ->line('Votre mot de passe: ' . $userData['password'])
        // ->action('Se connecter', url('/'));

        $userData = $this->userData;

        // Vérifier le type d'utilisateur et rediriger correctement
        if (in_array($notifiable->user_type, ['admin', 'manager', 'controller'])) {
            $loginUrl = url('/dashboard'); // Redirection pour l'admin, manager et controller
        } else {
            $loginUrl = 'https://play.google.com/store/apps/details?id=com.konney.easypoint&hl=fr'; // Lien vers le store pour les employés
        }
        return (new MailMessage)
            ->subject('Notification d\'un nouvel utilisateur')
            ->greeting('Salut,')
            ->line('Votre compte a été créé avec succès. Voici vos informations de connexion :')
            ->line('**Email** : ' . ($userData['email'] ?? 'Non disponible'))
            ->line('**Nom d\'utilisateur** : ' . ($userData['email'] ?? 'Non disponible'))
            ->line('**Nom** : ' . ($userData['name'] ?? 'Non disponible'))
            ->line('**Prénom** : ' . ($userData['first_name'] ?? 'Non disponible'))
            ->line('**Numéro de téléphone** : ' . ($userData['phone'] ?? 'Non disponible'))
            ->line('**Numéro social** : ' . ($userData['n_social'] ?? 'Non disponible'))
            ->line('**Mot de passe temporaire** : ' . ($userData['password'] ?? 'Non disponible'))
            ->line('⚠️ **Veuillez modifier votre mot de passe dès votre première connexion pour sécuriser votre compte.**')
            ->action('Se connecter', $loginUrl)
            ->line('Merci d\'utiliser notre plateforme.');
    // Vérifier si l'utilisateur est un administrateur
    }
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'email' => $this->userData['email'] ?? "",
            'username' => $this->userData['username'] ?? $this->userData['email'],
            'first_name' => $this->userData['first_name']?? "",
            'name' => $this->userData['name']?? "",
            'phone' => $this->userData['phone']?? "",
            'n_social' => $this->userData['n_social']?? "",
            'password' => $this->userData['password']?? "",
        ];
    }
}
