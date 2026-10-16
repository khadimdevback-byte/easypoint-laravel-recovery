<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    //
    protected $fillable=[
        'name',
        'group'
    ];
    public function compagnies()
    {
        return $this->belongsToMany(Company::class, 'compagnie_menu');
    }
}
