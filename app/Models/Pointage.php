<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pointage extends Model
{
    //
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'equipe_id',
        'site_id',
        'company_id',
        'rotation_id',
        'periode_id',
        'date_pointage',
        'heure_pointage',
        'type',
        'statut',
        'latitude',
        'longitude'
    ];

    protected $casts = [
        'date_pointage' => 'date',
    ];


      public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔗 Compagnie
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // 🔗 Site de rotation
    public function site()
    {
        return $this->belongsTo(SiteRotation::class, 'site_id');
    }
        public function repartition()
        {
            return $this->belongsTo(Repartition::class);
        }
}
