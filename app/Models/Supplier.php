<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'code', 'company_name', 'contact_name', 'email', 'phone',
        'address', 'city', 'country', 'tax_number', 'notes', 'status', 'credit_limit',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'integer',
            'credit_limit' => 'decimal:2',
        ];
    }

    public function purchaseInvoices(): HasMany
    {
        return $this->hasMany(SupplierPurchaseInvoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}
