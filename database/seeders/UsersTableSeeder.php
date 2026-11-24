<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        User::query()->truncate();
        Schema::enableForeignKeyConstraints();


        User::create([
            'name' => 'Super Admin',
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'username' => 'SuperAdmin',
            'email' => 'superadmin@gmail.com',
            'email_verified_at' => now(),
            'role_id' => 1,
            'password' => Hash::make('Admin@123'),
            'remember_token' => Str::random(10),
        ]);

        // Create Admin
        User::create([
            'name' => 'Admin',
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'username' => 'Admin',
            'email' => 'admin@gmail.com',
            'email_verified_at' => now(),
            'role_id' => 2,
            'password' => Hash::make('Admin@123'),
            'remember_token' => Str::random(10),
        ]);

        // Create Store Manager
        User::create([
            'name' => 'Store Manager',
            'first_name' => 'Store',
            'last_name' => 'Manager',
            'username' => 'StoreManager',
            'email' => 'storemanager@gmail.com',
            'email_verified_at' => now(),
            'role_id' => 3,
            'password' => Hash::make('Admin@123'),
            'remember_token' => Str::random(10),
        ]);
        
    }
}
