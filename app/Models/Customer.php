<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name', 'email', 'phone', 'country', 'city', 'address', 'description', 'status', 'credit_limit'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function payments()
    {
        return $this->hasMany(CustomerPayment::class);
    }

    public function ledgerEntries()
    {
        return $this->hasMany(CustomerLedgerEntry::class);
    }
}
