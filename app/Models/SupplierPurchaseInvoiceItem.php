<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierPurchaseInvoiceItem extends Model
{
    protected $fillable = [
        'supplier_purchase_invoice_id', 'product_id', 'product_name',
        'qty', 'unit_price', 'line_total', 'is_taxable', 'tax_rate', 'tax_amount',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'unit_price' => 'decimal:2',
            'line_total' => 'decimal:2',
            'is_taxable' => 'boolean',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(SupplierPurchaseInvoice::class, 'supplier_purchase_invoice_id');
    }
}
