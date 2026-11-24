<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('settings')) {
            return;
        }

        $exists = DB::table('settings')->where('key_name', 'credit_limit_override_user_ids')->exists();
        if ($exists) {
            return;
        }

        DB::table('settings')->insert([
            'key_name' => 'credit_limit_override_user_ids',
            'value' => '',
            'placeholder' => 'Select users who may override credit limits',
            'label' => 'Users Allowed To Override Credit Limits',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->where('key_name', 'credit_limit_override_user_ids')->delete();
    }
};
