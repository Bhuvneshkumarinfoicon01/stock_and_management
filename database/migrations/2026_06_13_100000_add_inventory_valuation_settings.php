<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $now = now();

        $rows = [
            [
                'key_name' => 'inventory_valuation_method',
                'value' => 'avco',
                'placeholder' => 'avco, fifo, or lifo',
                'label' => 'Inventory Valuation Method',
            ],
            [
                'key_name' => 'accounting_year_start_month',
                'value' => '1',
                'placeholder' => '1-12 (January = 1). Also used to determine when the valuation method may be changed.',
                'label' => 'Accounting year start month',
            ],
            [
                'key_name' => 'inventory_valuation_method_set_at',
                'value' => '',
                'placeholder' => 'Set automatically when valuation method is saved',
                'label' => 'Inventory valuation method last set at',
            ],
        ];

        foreach ($rows as $row) {
            if (DB::table('settings')->where('key_name', $row['key_name'])->exists()) {
                continue;
            }

            DB::table('settings')->insert(array_merge($row, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key_name', [
            'inventory_valuation_method',
            'accounting_year_start_month',
            'inventory_valuation_method_set_at',
        ])->delete();
    }
};
