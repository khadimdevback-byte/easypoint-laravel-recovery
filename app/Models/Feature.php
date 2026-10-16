<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'route',
        'is_active'
    ];

    /**
     * Relation Many-to-Many avec le modèle Plan.
     * (Nous la paramétrons déjà, elle servira dès qu'on créera la table des plans).
     */
    // public function plans()
    // {
    //     return $this->belongsToMany(Plan::class);
    // }
}
