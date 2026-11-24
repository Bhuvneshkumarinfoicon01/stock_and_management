<?php

namespace App\Models;

use App\Models\Stock;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    public const TYPE_ADD = 'add';

    public const TYPE_REMOVE = 'remove';

    use HasFactory;

    protected $fillable = [
        'store_id',
        'product_id',
        'adjust_quantity',
        'unit_cost',
        'adjustment_account_id',
        'adjustment_type',
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

    public function scopeWhereType($query, string $type)
    {
        return $query->where('adjustment_type', $type);
    }

    public function getAdjustmentTypeLabelAttribute(): string
    {
        return $this->adjustment_type === self::TYPE_ADD ? 'Add to Stock' : 'Reduce Stock';
    }
}
