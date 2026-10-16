<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRotation extends Model
{
    protected $fillable = [
        'user_id',
        'equipe_id',
        'site_id',
        'rotation_id',
        'periode',
        'date',
        'heure_arrivee',
        'heure_depart',
        'retard',
        'absence',
        'motif_absence'
    ];


    public function equipe() {
        return $this->belongsTo(Equipe::class);
    }

    public function site() {
        return $this->belongsTo(Site::class);
    }



    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

