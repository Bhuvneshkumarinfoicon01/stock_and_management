<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'sku',
        'qty',
        'category_id',
        'price',
        'is_taxable',
        'tax_rate',
        'tax_amount',
    ];

    protected function casts(): array
    {
        return [
            'is_taxable' => 'boolean',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
        ];
    }

    // Define relationship with Order
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    // Define relationship with Product
    public function stock()
    {
        return $this
            ->hasOne(Stock::class, 'product_id', 'product_id')
            ->where('store_id', session('selected_store_id'));  // Ensure the store_id is considered
    }
}
