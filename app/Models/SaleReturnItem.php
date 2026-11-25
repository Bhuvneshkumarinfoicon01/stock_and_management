<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleReturnItem extends Model
{
    protected $fillable = [
        'sale_return_id',
        'product_id',
        'product_name',
        'qty',
        'unit_price',
        'unit_cost',
        'line_total',
        'is_taxable',
        'tax_rate',
        'tax_amount',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'unit_price' => 'decimal:2',
            'unit_cost' => 'decimal:2',
            'line_total' => 'decimal:2',
            'is_taxable' => 'boolean',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
        ];
    }

    public function saleReturn(): BelongsTo
    {
        return $this->belongsTo(SaleReturn::class);
    }
}
