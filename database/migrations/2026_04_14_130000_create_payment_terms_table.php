<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_terms', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->unsignedInteger('due_days')->nullable();
            $table->unsignedInteger('due_months')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        DB::table('payment_terms')->insert([
            ['id' => 1, 'label' => '0-30 days', 'due_days' => 30, 'due_months' => 0, 'is_active' => 1, 'sort_order' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'label' => '30-60 days', 'due_days' => 60, 'due_months' => 0, 'is_active' => 1, 'sort_order' => 20, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'label' => '60-90 days', 'due_days' => 90, 'due_months' => 0, 'is_active' => 1, 'sort_order' => 30, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'label' => '90+ days', 'due_days' => null, 'due_months' => 4, 'is_active' => 1, 'sort_order' => 40, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_terms');
    }
};

