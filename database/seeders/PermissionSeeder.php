<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the modules and actions
        $modules = ['stock', 'order', 'ledger'];
        $actions = ['create', 'update', 'delete'];

        // Loop through each module and action to create permissions
        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::create([
                    'module' => $module,
                    'action' => $action,
                ]);
            }
        }
    }
}
