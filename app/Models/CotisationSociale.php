<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CotisationSociale extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'company_id',
        'libelle',
        'taux_salarial',
        'taux_employeur',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
