<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeureMensuelle extends Model
{
    //
    protected $fillable = ['user_id', 'company_id', 'mois', 'annee', 'total_heures'];

}
