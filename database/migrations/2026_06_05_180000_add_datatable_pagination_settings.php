<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $now = now();

        $rows = [
            [
                'key_name' => 'datatable_page_length_options',
                'value' => '10,25,50,100,250,500',
                'placeholder' => '10,25,50,100,250,500',
                'label' => 'Table rows per page options (comma-separated)',
            ],
            [
                'key_name' => 'datatable_default_page_length',
                'value' => '25',
                'placeholder' => '25',
                'label' => 'Default table rows per page',
            ],
            [
                'key_name' => 'datatable_scroll_chunk_size',
                'value' => '50',
                'placeholder' => '50',
                'label' => 'Rows loaded per scroll chunk (All mode)',
            ],
        ];

        foreach ($rows as $row) {
            $exists = DB::table('settings')->where('key_name', $row['key_name'])->exists();
            if ($exists) {
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
            'datatable_page_length_options',
            'datatable_default_page_length',
            'datatable_scroll_chunk_size',
        ])->delete();
    }
};
