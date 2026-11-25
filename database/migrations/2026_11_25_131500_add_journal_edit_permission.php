<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('permissions')
            ->where('module', 'journal')
            ->where('action', 'update')
            ->exists();
        if ($exists) {
            return;
        }

        DB::table('permissions')->insert([
            'module' => 'journal',
            'action' => 'update',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        $permissionId = DB::table('permissions')
            ->where('module', 'journal')
            ->where('action', 'update')
            ->value('id');
        if (! $permissionId) {
            return;
        }

        $assignedToUser = DB::table('user_permissions')->where('permission_id', $permissionId)->exists();
        $assignedToStoreEmployee = DB::table('store_employee_permissions')->where('permission_id', $permissionId)->exists();
        if (! $assignedToUser && ! $assignedToStoreEmployee) {
            DB::table('permissions')->where('id', $permissionId)->delete();
        }
    }
};
