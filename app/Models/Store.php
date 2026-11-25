<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city',
        'state',
        'zipcode',
        'phone',
        'email',
        'status',
        'invoice_prefix',
        'current_sale_invoice_sequence',
        'next_sale_invoice_number',
        'purchase_invoice_prefix',
        'next_purchase_invoice_number',
        'purchase_order_prefix',
        'next_purchase_order_number',
        'purchase_return_prefix',
        'next_purchase_return_number',
        'sale_return_prefix',
        'next_sale_return_number',
        'grn_prefix',
        'next_grn_number',
        'credit_note_prefix',
        'next_credit_note_number',
        'debit_note_prefix',
        'next_debit_note_number',
        'receipt_prefix',
        'next_receipt_number',
        'batch_purchase_prefix',
        'next_batch_purchase_number',
    ];

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function assignedUsers()
    {
        return $this->hasMany(User::class, 'assigned_store', 'id');
    }
}
