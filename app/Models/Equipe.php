<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipe extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'nom',
        'company_id'
    ];

     public function users()
    {
        return $this->belongsToMany(User::class, 'equipe_user', 'equipe_id', 'user_id')
                    ->withTimestamps();
    }

    public function sites()
    {
        return $this->belongsToMany(SiteRotation::class, 'site_equipe', 'equipe_id', 'site_id');
    }

    public function rotations()
    {
        return $this->belongsToMany(Rotation::class, 'rotation_equipe', 'equipe_id', 'rotation_id');
    }

    public function compagnie()
    {
        return $this->belongsTo(Company::class);
    }








}
