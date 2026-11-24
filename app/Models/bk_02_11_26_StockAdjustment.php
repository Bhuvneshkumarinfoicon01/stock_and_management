<?php

namespace App\Models;

use App\Models\Stock;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'product_id',
        'adjust_quantity',
        'reason',
        'adjust_date',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function stock()
    {
        return $this
            ->belongsTo(Stock::class, 'product_id', 'product_id')
            ->whereColumn('store_id', 'store_id');
    }
}
