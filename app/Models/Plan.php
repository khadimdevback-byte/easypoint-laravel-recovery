<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'currency',
        'max_employees',
        'dashboard_type',
        'history_duration',
        'support_type',
        'is_active',
        'is_visible',
    ];

    /**
     * Relation Many-to-Many avec le modèle Feature.
     */
    public function features()
    {
        return $this->belongsToMany(Feature::class);
    }
}
