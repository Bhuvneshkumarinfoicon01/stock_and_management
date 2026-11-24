<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodsReceiptNoteItem extends Model
{
    protected $fillable = [
        'goods_receipt_note_id', 'product_id', 'product_name', 'qty_received', 'unit_cost', 'line_total',
        'is_taxable', 'tax_rate', 'tax_amount',
    ];

    protected function casts(): array
    {
        return [
            'qty_received' => 'integer',
            'unit_cost' => 'decimal:2',
            'line_total' => 'decimal:2',
            'is_taxable' => 'boolean',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
        ];
    }

    public function goodsReceiptNote(): BelongsTo
    {
        return $this->belongsTo(GoodsReceiptNote::class);
    }
}
