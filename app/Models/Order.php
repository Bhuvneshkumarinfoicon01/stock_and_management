<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        // 'product_id',
        'customer_id',
        // 'product_name',
        'customer_name',
        // 'quantity',
        'total_price',
        'paid_amount',
        'due_amount',
        'subtotal_price',
        'tax',
        'tax_exempt',
        'tax_amount',
        'shipping',
        'discount',
        'payment_method',
        'invoice_type',
        'due_date',
        'payment_terms_days',
        'last_payment_at',
        'notes',
        'type',
        'status',
        'invoice_status',
        'invoice_number',
        'draft_reference',
        'credit_limit_blocked',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'due_date' => 'date',
        'last_payment_at' => 'datetime',
        'credit_limit_blocked' => 'boolean',
        'tax_exempt' => 'boolean',
        'tax_amount' => 'decimal:2',
    ];

    public function orderItems()
    {
        return $this
            ->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id')->withDefault();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id')->withDefault();
    }

    public function payments()
    {
        return $this->hasMany(CustomerPayment::class);
    }

    public function getQuantityAttribute()
    {
        return $this->orderItems()->sum('qty');
    }
}
