<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'subscription_id',
        'fedapay_transaction_id',
        'amount',
        'payment_method',
        'status',
    ];

    
    public function company()
    {
        return $this->belongsTo(Company::class);
    }


    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
