<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransferItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_no',
        'product_id',
        'product_name',
        'sku',
        'qty',
        'category_id',
        'status'
    ];

    public function transfer()
    {
        return $this->belongsTo(StockTransfer::class, 'reference_no', 'reference_no');
    }

    public function stock()
    {
        return $this
            ->belongsTo(Stock::class, 'product_id', 'product_id')
            ->where('store_id', session('selected_store_id'));  // Filter by selected store_id
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');  // Add category relationship
    }
}