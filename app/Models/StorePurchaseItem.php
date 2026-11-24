<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StorePurchaseItem extends Model
{
    protected $fillable = [
        'store_purchase_id', 'product_id', 'product_name', 'qty', 'unit_price', 'line_total',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'unit_price' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    public function storePurchase(): BelongsTo
    {
        return $this->belongsTo(StorePurchase::class);
    }
}
