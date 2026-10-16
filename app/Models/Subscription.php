<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // ✅ Ajoutez ce use

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'plan_id',
        'duration_months',
        'total_amount',
        'starts_at',
        'ends_at',
        'status',
    ];

    /**
     * Formatage automatique des dates
     */
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Relation : Une souscription appartient à une compagnie
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relation : Une souscription est liée à un plan tarifaire
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * ✅ RELATION : Une souscription peut avoir plusieurs paiements
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Relation : Un paiement le plus récent
     */
    public function latestPayment(): HasMany
    {
        return $this->hasMany(Payment::class)->latest();
    }

    /**
     * Scope pour récupérer uniquement les souscriptions actuellement valides
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('starts_at', '<=', now())
                     ->where('ends_at', '>=', now());
    }
}
