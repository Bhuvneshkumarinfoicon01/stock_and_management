<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BatchPurchase extends Model
{
    protected $fillable = [
        'store_id', 'batch_number', 'batch_date', 'status', 'tax_exempt',
        'subtotal', 'tax_amount', 'total', 'charge_invoices_subtotal',
        'allocated_additional_charges', 'draft_reference', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'batch_date' => 'date',
            'tax_exempt' => 'boolean',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'charge_invoices_subtotal' => 'decimal:2',
            'allocated_additional_charges' => 'decimal:2',
        ];
    }

    public function isOnHold(): bool
    {
        return $this->status === 'draft';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BatchPurchaseItem::class);
    }

    public function productInvoices(): BelongsToMany
    {
        return $this->belongsToMany(
            SupplierPurchaseInvoice::class,
            'batch_purchase_product_invoice',
            'batch_purchase_id',
            'supplier_purchase_invoice_id'
        );
    }

    public function chargeInvoices(): BelongsToMany
    {
        return $this->belongsToMany(
            SupplierPurchaseInvoice::class,
            'batch_purchase_charge_invoice',
            'batch_purchase_id',
            'supplier_purchase_invoice_id'
        );
    }
}
