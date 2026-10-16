<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repartition extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'rotation_id', 'index_repartition', 'periode', 'heure_debut', 'heure_fin','nombre_jours'
    ];

    public function rotation()
    {
        return $this->belongsTo(Rotation::class);
    }
}
