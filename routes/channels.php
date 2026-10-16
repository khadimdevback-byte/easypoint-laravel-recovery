<?php
use App\Events\FonctionCreated;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('fonctions.{company_id}', function ($user, $company_id) {
    // Vérifie si l'utilisateur appartient à la compagnie
    return $user->company_id == $company_id;
});


