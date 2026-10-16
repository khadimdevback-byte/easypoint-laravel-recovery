<?php
// app/Models/MissingDeparture.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissingDeparture extends Model
{
    protected $table = 'missing_departures';

    protected $fillable = [
        'company_id',
        'user_id',
        'site_id',
        'date',
        'expected_depart_time',
        'status',
        'admin_comment',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'date' => 'date',
        'approved_at' => 'datetime'
    ];

    // Relation avec la compagnie
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Relation avec l'utilisateur
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec le site
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    // Relation avec l'approbateur
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
