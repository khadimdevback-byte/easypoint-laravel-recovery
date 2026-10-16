<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentArchive extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'company_id',
        'company_name',
        'company_email',
        'company_phone',
        'subscription_id',
        'plan_name',
        'duration_months',
        'fedapay_transaction_id',
        'amount',
        'currency',
        'payment_method',
        'status',
        'full_payment_data',
        'full_company_data',
        'full_subscription_data',
        'payment_date',
        'archived_at',
        'deleted_at',
    ];
    protected $casts = [
        'full_payment_data' => 'array',
        'full_company_data' => 'array',
        'full_subscription_data' => 'array',
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'archived_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    // Relations (optionnelles, car les données peuvent avoir été supprimées)
    public function company()
    {
        return $this->belongsTo(Company::class)->withTrashed();
    }
    public function subscription()
    {
        return $this->belongsTo(Subscription::class)->withTrashed();
    }
    // Scope pour filtrer par période
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }
    // Scope pour filtrer par montant
    public function scopeMinAmount($query, $amount)
    {
        return $query->where('amount', '>=', $amount);
    }
    // Scope pour les paiements approuvés
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
