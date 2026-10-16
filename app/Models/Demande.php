<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Demande extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'user_id',
        'categorie',
        'date_debut',
        'date_fin',
        'motif',
        'statut',
        'valide_par',
        'commentaire_admin'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    public function entreprise()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function employe()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function administrateur()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function getDureeEnJoursAttribute()
    {
        return $this->date_debut->diffInDays($this->date_fin) + 1;
    }
}
