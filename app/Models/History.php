<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $fillable = [
        'user_id', 'key', 'event', 'auditable_type', 'auditable_id',
        'requested_data', 'new_values', 'old_values',
        'ip_address', 'user_agent', 'url'
    ];

    protected $casts = [
        'requested_data' => 'array',
        'new_values' => 'array',
        'old_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
