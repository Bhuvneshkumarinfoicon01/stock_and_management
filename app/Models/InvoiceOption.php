<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class InvoiceOption extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * Cash Sales, Credit Sales, and Estimates. Restored when the table was emptied.
     *
     * @return Collection<int|string, string>
     */
    public static function ensureDefaults(): Collection
    {
        $defaults = [
            1 => 'Cash Sales',
            2 => 'Credit Sales',
            3 => 'Estimates',
        ];

        foreach ($defaults as $id => $name) {
            $exists = static::query()->where('id', $id)->orWhere('name', $name)->exists();
            if ($exists) {
                continue;
            }

            static::query()->insert([
                'id' => $id,
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return static::query()->orderBy('id')->pluck('name', 'id');
    }
}
