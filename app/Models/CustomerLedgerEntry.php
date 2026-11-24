<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerLedgerEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'order_id',
        'payment_id',
        'entry_type',
        'entry_date',
        'description',
        'debit',
        'credit',
        'balance_after',
        'meta',
    ];

    protected $casts = [
        'entry_date' => 'datetime',
        'meta' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function payment()
    {
        return $this->belongsTo(CustomerPayment::class, 'payment_id');
    }
}

