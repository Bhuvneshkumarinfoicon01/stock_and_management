<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Role::query()->truncate();
        Schema::enableForeignKeyConstraints();

        $roles = [
            [
                'name' => 'SuperAdmin',
                'slug' => 'superadmin',
                'description' => 'manage administration privileges',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'To create and manage store ',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'StoreManager',
                'slug' => 'storemanager',
                'description' => 'To create and manage store ',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Anonymous',
                'slug' => 'anonymous',
                'description' => 'The users who are not allowed for any access',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Staff',
                'slug' => 'staff',
                'description' => 'The employees working at a store',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ];
        Role::insert($roles);
        echo "\nAll roles updated in the roles table\n";
    }
}
