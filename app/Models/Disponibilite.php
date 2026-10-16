<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;


class Disponibilite extends Model
{
    use HasFactory;
    protected $table = 'disponibilites';

    // Définir les champs remplissables
    protected $fillable = [
        'user_id',
        'company_id',
        'date_disponibilite',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec la compagnie
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
