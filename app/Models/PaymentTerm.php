<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'due_days',
        'due_months',
        'is_active',
        'sort_order',
    ];

    /**
     * Default credit terms used by sales invoices. Restored when the table was emptied.
     */
    public static function ensureDefaults(): void
    {
        $defaults = [
            1 => ['label' => '0-30 days', 'due_days' => 30, 'due_months' => 0, 'sort_order' => 10],
            2 => ['label' => '30-60 days', 'due_days' => 60, 'due_months' => 0, 'sort_order' => 20],
            3 => ['label' => '60-90 days', 'due_days' => 90, 'due_months' => 0, 'sort_order' => 30],
            4 => ['label' => '90+ days', 'due_days' => null, 'due_months' => 4, 'sort_order' => 40],
        ];

        foreach ($defaults as $id => $term) {
            $exists = static::query()->where('id', $id)->orWhere('label', $term['label'])->exists();
            if ($exists) {
                continue;
            }

            static::query()->insert([
                'id' => $id,
                'label' => $term['label'],
                'due_days' => $term['due_days'],
                'due_months' => $term['due_months'],
                'is_active' => 1,
                'sort_order' => $term['sort_order'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

