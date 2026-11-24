<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatchPurchaseItem extends Model
{
    protected $fillable = [
        'batch_purchase_id', 'product_id', 'product_name', 'sku', 'category_id', 'category_name',
        'qty', 'unit_price', 'amount', 'additional_charges', 'grand_total', 'final_unit',
        'is_taxable', 'tax_rate', 'tax_amount',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'unit_price' => 'decimal:2',
            'amount' => 'decimal:2',
            'additional_charges' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'final_unit' => 'decimal:4',
            'is_taxable' => 'boolean',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
        ];
    }

    public function batchPurchase(): BelongsTo
    {
        return $this->belongsTo(BatchPurchase::class);
    }
}
