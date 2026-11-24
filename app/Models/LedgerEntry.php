<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LedgerEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'ledger_id',
        'entry_date',
        'description',
        'debit',
        'credit',
    ];

    public function ledger()
    {
        return $this->belongsTo(Ledger::class);
    }
}