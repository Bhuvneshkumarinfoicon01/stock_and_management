<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseReturn extends Model
{
    protected $fillable = [
        'supplier_id', 'store_id', 'supplier_purchase_invoice_id', 'return_number',
        'return_date', 'subtotal', 'tax_amount', 'tax_exempt', 'total_amount', 'status', 'reason',
    ];

    protected function casts(): array
    {
        return [
            'return_date' => 'date',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'tax_exempt' => 'boolean',
            'total_amount' => 'decimal:2',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function purchaseInvoice(): BelongsTo
    {
        return $this->belongsTo(SupplierPurchaseInvoice::class, 'supplier_purchase_invoice_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseReturnItem::class);
    }
}
