<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'supplier_id', 'store_id', 'po_number', 'order_date', 'status',
        'subtotal', 'tax_percent', 'tax_amount', 'tax_exempt', 'total', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'subtotal' => 'decimal:2',
            'tax_percent' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'tax_exempt' => 'boolean',
            'total' => 'decimal:2',
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

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function purchaseInvoices(): HasMany
    {
        return $this->hasMany(SupplierPurchaseInvoice::class);
    }

    public function goodsReceiptNotes(): HasMany
    {
        return $this->hasMany(GoodsReceiptNote::class);
    }

    public function hasConfirmedPurchaseInvoice(): bool
    {
        return $this->purchaseInvoices()
            ->where('invoice_status', 'Confirmed')
            ->exists();
    }

    public function latestPurchaseInvoice(): ?SupplierPurchaseInvoice
    {
        return $this->purchaseInvoices()->orderByDesc('id')->first();
    }

    public function latestReceivedGrn(): ?GoodsReceiptNote
    {
        return $this->goodsReceiptNotes()
            ->where('status', 'received')
            ->orderByDesc('id')
            ->first();
    }

    public function hasPurchaseInvoice(): bool
    {
        return $this->purchaseInvoices()->exists();
    }

    public function canCreateGrn(): bool
    {
        if ($this->latestReceivedGrn()) {
            return false;
        }

        return $this->hasConfirmedPurchaseInvoice();
    }

    public function syncStatusFromDocuments(): void
    {
        $newStatus = 'draft';

        if ($this->goodsReceiptNotes()->where('status', 'received')->exists()) {
            $newStatus = 'received';
        } elseif ($this->purchaseInvoices()->exists()) {
            $newStatus = 'invoiced';
        }

        if ($this->status !== $newStatus) {
            $this->forceFill(['status' => $newStatus])->save();
        }
    }

    public static function statusLabel(?string $status): string
    {
        return match ($status) {
            'invoiced' => 'Invoiced',
            'received' => 'Received',
            default => 'Draft',
        };
    }
}
