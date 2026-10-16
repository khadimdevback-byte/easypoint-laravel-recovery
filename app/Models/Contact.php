<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'company',
        'employees',
        'subject',
        'message',
        'intent',
        'attachment',
        'attachment_name',
        'ip_address',
        'user_agent',
        'status',
        'is_read',
        'read_at',
        'responded_at',
        'responded_by'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'responded_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    // Accesseur pour le nom complet
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Accesseur pour l'intent en français
    public function getIntentLabelAttribute()
    {
        $labels = [
            'general' => 'Renseignement général',
            'demo' => 'Demande de démo',
            'devis' => 'Demande de devis',
            'support' => 'Support technique'
        ];
        return $labels[$this->intent] ?? $this->intent;
    }

    // Accesseur pour le statut en français
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'En attente',
            'processing' => 'En traitement',
            'responded' => 'Répondu',
            'closed' => 'Clôturé'
        ];
        return $labels[$this->status] ?? $this->status;
    }

    // Scope pour les non lus
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
