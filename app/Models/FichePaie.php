<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FichePaie extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mois',
        'annee',
        'heures_travaillees',
        'taux_horaire',
        'salaire_brut',
        'primes',
        'retenues',
        'cotisations',
        'avancements',
        'salaire_net',
        'company_id',
        'horaire_type'
    ];

    protected $casts = [
        'cotisations_details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Dans FichePaie.php
    public function company()
    {
        return $this->belongsTo(Company::class);
    }



}
