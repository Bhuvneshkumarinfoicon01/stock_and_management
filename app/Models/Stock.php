<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    protected $fillable = [
        'store_id', 'product_id', 'product_name', 'product_type', 'sku', 'category_id', 'unit_id', 'supplier_name',
        'purchase_date', 'description', 'quantity', 'price', 'cost', 'discount', 'tax', 'manufactured_date',
        'expiry_date', 'images', 'selling_price', 'average_cost', 'last_purchase_price', 'total_stock', 'status',
        'debit_account_id', 'credit_account_id',
        'is_taxable', 'tax_rate',
    ];

    protected function casts(): array
    {
        return [
            'is_taxable' => 'boolean',
            'tax_rate' => 'decimal:2',
        ];
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function debitAccount(): BelongsTo
    {
        return $this->belongsTo(GlAccount::class, 'debit_account_id');
    }

    public function creditAccount(): BelongsTo
    {
        return $this->belongsTo(GlAccount::class, 'credit_account_id');
    }

    public function adjustments()
    {
        return $this->hasMany(StockAdjustment::class, 'product_id');
    }

    public function orderItems()
    {
        return $this
            ->hasMany(OrderItem::class, 'product_id', 'product_id');
    }
}
