<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'ledger_date',
    ];

    public function entries()
    {
        return $this->hasMany(LedgerEntry::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id')->withDefault();
    }
}
