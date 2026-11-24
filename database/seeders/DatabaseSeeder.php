<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        
        // User::factory()->create([
        //     'name' => 'Super Admin',
        //     'email' => 'superadmin@gmail.com',
        //     'email_verified_at' => now(),
        //     'role_id' => 1,
        //     'password' => Hash::make('password'),
        //     'remember_token' => Str::random(10),
        // ]);
    }
}
