<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteRotation extends Model
{
    use HasFactory;

    protected $table = 'site_rotation';

    protected $fillable = [
        'company_id','user_id',
        'name', 'address', 'description', 'timezone', 'panier',
        'radius', 'latitude', 'longitude',
        'late_mark_duration', 'qr_code', 'status',
        'heure_debut_matin', 'heure_fin_matin',
        'heure_debut_apres_midi', 'heure_fin_apres_midi',
        'heure_debut_nuit', 'heure_fin_nuit',
        'jours_repos'
    ];

    protected $casts = [
        'jours_repos' => 'array',
    ];


    public function company()
    {
        return $this->belongsTo(Company::class);
    }


    public function equipes()
    {
        return $this->belongsToMany(Equipe::class, 'site_equipe', 'site_id', 'equipe_id');
    }

     public function rotations()
    {
        return $this->belongsToMany(Rotation::class, 'rotation_site', 'site_id', 'rotation_id');
    }

    public function rotation()
    {
        return $this->belongsTo(Rotation::class, 'rotation_id');
    }



}

