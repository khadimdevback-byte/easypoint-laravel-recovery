<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;


    protected $fillable = [
        'compagny_name',
        'n_siret',
        'n_siren',
        'iban',
        'compagny_email',
        'phone',
        'country',
        'currency',
        'website',
        'timezone',
        'address',
        'password',
        'status',
        'logo',
        'avatar',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    public function offres()
    {
        return $this->hasMany(Offre::class);
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'compagnie_menu');
    }

    public function cotisations()
    {
        return $this->hasMany(CotisationSociale::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }


    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
                    ->where('status', 'active')
                    ->where('starts_at', '<=', now())
                    ->where('ends_at', '>=', now())
                    ->latestOfMany(); // Récupère la plus récente si doublon
    }

    public function subscription()
    {
        // Si une entreprise n'a qu'un seul abonnement actif/créé à la fois :
        return $this->hasOne(Subscription::class);
    }

}
