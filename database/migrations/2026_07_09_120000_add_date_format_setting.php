<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $now = now();

        $exists = DB::table('settings')->where('key_name', 'date_format')->exists();
        if ($exists) {
            return;
        }

        DB::table('settings')->insert([
            'key_name' => 'date_format',
            'value' => 'd-M-Y',
            'placeholder' => 'Display format for dates across lists, documents, and date pickers',
            'label' => 'Date format',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->where('key_name', 'date_format')->delete();
    }
};
