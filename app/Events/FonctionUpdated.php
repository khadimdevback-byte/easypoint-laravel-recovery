<?php

namespace App\Events;

use App\Models\Fonction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FonctionUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $fonction;

    /**
     * Create a new event instance.
     */
    public function __construct(Fonction $fonction)
    {
        //
        $this->fonction = $fonction;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('fonctions ' .$this->fonction->company_id),
        ];
    }

    public function broadcastAs()
    {
        return 'fonction.updated';
    }
}
