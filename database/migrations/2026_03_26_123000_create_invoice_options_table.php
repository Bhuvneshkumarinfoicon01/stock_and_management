<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoice_options', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        DB::table('invoice_options')->insert([
            ['id' => 1, 'name' => 'Cash Sales', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Credit Sales', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Estimates', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_options');
    }
};
