<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendCreateCompanyEmailNotification extends Notification
{
    use Queueable;
    protected $companyData;
    /**
     * Create a new notification instance.
     */
    public function __construct($companyData)
    {
        $this->companyData = $companyData;
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
        $companyData = $this->companyData;

        return (new MailMessage)
            ->subject('Nouvelle entreprise ajoutée')
            ->greeting('Bonjour Super Admin,')
            ->line('Une nouvelle entreprise a été ajoutée :')
            ->line('Nom de l\'entreprise : ' . $companyData['compagny_name'])
            ->line('Numéro SIRET : ' . $companyData['n_siret'])
            ->line('Numéro SIREN : ' . $companyData['n_siren'])
            ->line('IBAN : ' . $companyData['iban'])
            ->line('Email de l\'entreprise : ' . $companyData['compagny_email'])
            ->line('Téléphone : ' . $companyData['phone'])
            ->line('Site Web : ' . $companyData['website'])
            ->line('Adresse : ' . $companyData['address'])
            ->line('Statut : ' . $companyData['status'])
            ->line('Timezone : ' . $companyData['timezone'])
            ->action('Voir l\'entreprise', url('/companies')); 
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'compagny_name' => $this->companyData['compagny_name'] ?? "",
            'n_siret' => $this->companyData['n_siret'] ?? "",
            'n_siren' => $this->companyData['n_siren'] ?? "",
            'iban' => $this->companyData['iban'] ?? "",
            'compagny_email' => $this->companyData['compagny_email'] ?? "",
            'phone' => $this->companyData['phone'] ?? "",
            'website' => $this->companyData['website'] ?? "",
            'address' => $this->companyData['address'] ?? "",
            'status' => $this->companyData['status'] ?? "",
            'timezone' => $this->companyData['timezone'] ?? "",
        ];
    }

    public function toDatabase(object $notifiable): array
    {
        $companyName = $this->companyData['compagny_name'] ?? 'Entreprise';

        return [
            'message' => "Nouvelle entreprise ajoutée : {$companyName}.",
            'company_name' => $companyName,
            'url' => url('/companies'),
        ];
    }
}
