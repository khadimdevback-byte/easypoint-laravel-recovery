<?php
namespace App\Events;

use App\Models\Fonction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FonctionCreated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $fonction;

    /**
     * Crée une nouvelle instance de l'événement.
     *
     * @param  \App\Models\Fonction  $fonction
     * @return void
     */
    public function __construct(Fonction $fonction)
{
    // Vérifie que $fonction est une instance valide de la classe Fonction
    $this->fonction = $fonction;
}


    /**
     * Définir le canal de diffusion.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // Diffuser sur le canal privé de la compagnie
        return new PrivateChannel('fonctions.' . $this->fonction->company_id);
    }

    /**
     * Définir le nom de l'événement qui sera écouté côté client.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'fonction.created';
    }
}
