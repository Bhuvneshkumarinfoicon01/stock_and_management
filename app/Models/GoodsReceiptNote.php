<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoodsReceiptNote extends Model
{
    protected $fillable = [
        'supplier_id', 'store_id', 'purchase_order_id', 'supplier_purchase_invoice_id',
        'grn_number', 'grn_date', 'status',
        'subtotal', 'tax_amount', 'total', 'tax_exempt', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'grn_date' => 'date',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'tax_exempt' => 'boolean',
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

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function supplierPurchaseInvoice(): BelongsTo
    {
        return $this->belongsTo(SupplierPurchaseInvoice::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(GoodsReceiptNoteItem::class);
    }
}
