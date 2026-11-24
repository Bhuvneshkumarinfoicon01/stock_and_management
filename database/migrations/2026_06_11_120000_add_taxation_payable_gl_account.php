<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('gl_accounts')) {
            return;
        }

        $exists = DB::table('gl_accounts')->where('code', '2200')->exists();
        if ($exists) {
            return;
        }

        $now = now();
        DB::table('gl_accounts')->insert([
            'code' => '2200',
            'name' => 'Taxation Payable',
            'type' => 'liability',
            'parent_id' => null,
            'is_active' => true,
            'description' => 'Output tax on sales and input tax on purchases (point 17).',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        if (Schema::hasTable('gl_accounts')) {
            DB::table('gl_accounts')->where('code', '2200')->delete();
        }
    }
};
