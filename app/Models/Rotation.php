<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rotation extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'nom', 'systeme', 'duree_cycle',
        'nombre_jours_travail', 'nombre_jours_repos', 'nombre_repartitions','date_debut','statut','company_id', 'site_id'
    ];

    public function repartitions()
    {
        return $this->hasMany(Repartition::class);
    }

     public function equipes()
    {
        return $this->belongsToMany(Equipe::class, 'rotation_equipe', 'rotation_id', 'equipe_id');
    }

    public function sites()
    {
        return $this->belongsToMany(SiteRotation::class, 'rotation_site', 'rotation_id', 'site_id');
    }
}
