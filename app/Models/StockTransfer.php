<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_from',
        'store_to',
        'rperson_from',
        'rperson_to',
        'reference_no',
        'notes',
        'arrival_date'
    ];

    // Relationship with StockTransferItem
    public function items()
    {
        return $this->hasMany(StockTransferItem::class, 'reference_no', 'reference_no');
    }

    public function storeFrom()
    {
        return $this->belongsTo(Store::class, 'store_from');
    }

    public function storeTo()
    {
        return $this->belongsTo(Store::class, 'store_to');
    }
}
