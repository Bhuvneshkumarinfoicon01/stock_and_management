<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionAccountMapping extends Model
{
    protected $fillable = [
        'source_type',
        'label',
        'debit_account_id',
        'credit_account_id',
    ];

    public function debitAccount(): BelongsTo
    {
        return $this->belongsTo(GlAccount::class, 'debit_account_id');
    }

    public function creditAccount(): BelongsTo
    {
        return $this->belongsTo(GlAccount::class, 'credit_account_id');
    }
}
