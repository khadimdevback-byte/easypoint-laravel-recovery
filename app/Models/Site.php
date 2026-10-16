<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Site extends Model
{
    use HasFactory;
    protected $table = 'sites';

    protected $fillable = [
        'company_id',
        'customer_id',
        'controller_id',
        'name',
        'address',
        'description',
        'radius',
        'latitude',
        'longitude',
        'late_mark_duration',
        'qr_code',
        'status',
        'reason',
        'panier',
        'tuesday_checked',
        'start_time_tuesday',
        'end_time_tuesday',

        'wednesday_checked',
        'start_time_wednesday',
        'end_time_wednesday',

        'thursday_checked',
        'start_time_thursday',
        'end_time_thursday',

        'friday_checked',
        'start_time_friday',
        'end_time_friday',

        'saturday_checked',
        'start_time_saturday',
        'end_time_saturday',

        'sunday_checked',
        'start_time_sunday',
        'end_time_sunday',
    ];


    protected static function boot()
    {
        parent::boot();

        // Génération automatique du qr_code lors de la création d'un site
        static::creating(function ($site) {
            $site->qr_code = (string) Str::uuid(); // Génère un UUID unique
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function controller()
    {
        return $this->belongsToMany(User::class, 'site_controller');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'site_user');
    }


    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }





    // Relation avec les équipes via la table pivot 'rotation_equipe'


}
