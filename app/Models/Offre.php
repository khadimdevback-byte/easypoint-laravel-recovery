<?php

namespace App\Models;

use App\Models\Site;
use App\Models\Fonction;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offre extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'poste_id',
        'company_id',
        'site_id',
        'taux_horaire',
        'nombre_personne_recherche',
        'demarrage',
        'start_date',
        'description_offre'
    ];

    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function fonction()
    {
        return $this->belongsTo(Fonction::class, 'poste_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function candidatures()
    {
        return $this->hasMany(Candidature::class);
    }


}
