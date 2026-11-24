<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierPurchaseInvoice extends Model
{
    protected $fillable = [
        'supplier_id', 'store_id', 'purchase_order_id', 'internal_invoice_number', 'invoice_number', 'invoice_date', 'due_date',
        'subtotal', 'tax_percent', 'tax_amount', 'total', 'paid_amount', 'due_amount',
        'status', 'invoice_status', 'notes', 'draft_reference', 'credit_limit_blocked', 'tax_exempt',
    ];

    public function isOnHold(): bool
    {
        return $this->invoice_status === 'Draft';
    }

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'subtotal' => 'decimal:2',
            'tax_percent' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'due_amount' => 'decimal:2',
            'credit_limit_blocked' => 'boolean',
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

    public function items(): HasMany
    {
        return $this->hasMany(SupplierPurchaseInvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function batchPurchasesAsProduct(): BelongsToMany
    {
        return $this->belongsToMany(
            BatchPurchase::class,
            'batch_purchase_product_invoice',
            'supplier_purchase_invoice_id',
            'batch_purchase_id'
        );
    }

    public function batchPurchasesAsCharge(): BelongsToMany
    {
        return $this->belongsToMany(
            BatchPurchase::class,
            'batch_purchase_charge_invoice',
            'supplier_purchase_invoice_id',
            'batch_purchase_id'
        );
    }

    public function isAssignedToConfirmedBatchPurchase(?int $excludeBatchId = null): bool
    {
        $productQuery = $this->batchPurchasesAsProduct()->where('status', 'confirmed');
        $chargeQuery = $this->batchPurchasesAsCharge()->where('status', 'confirmed');

        if ($excludeBatchId) {
            $productQuery->where('batch_purchases.id', '!=', $excludeBatchId);
            $chargeQuery->where('batch_purchases.id', '!=', $excludeBatchId);
        }

        return $productQuery->exists() || $chargeQuery->exists();
    }

    public static function idsLockedByConfirmedBatchPurchase(array $invoiceIds, ?int $excludeBatchId = null): array
    {
        $invoiceIds = array_values(array_filter(array_map('intval', $invoiceIds)));
        if ($invoiceIds === []) {
            return [];
        }

        $locked = [];
        foreach ($invoiceIds as $invoiceId) {
            $invoice = self::query()->find($invoiceId);
            if ($invoice && $invoice->isAssignedToConfirmedBatchPurchase($excludeBatchId)) {
                $locked[] = $invoiceId;
            }
        }

        return $locked;
    }
}
