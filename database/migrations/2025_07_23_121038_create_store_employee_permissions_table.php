<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('store_employee_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_employee_id');
            $table->unsignedBigInteger('permission_id');
            $table->foreign('store_employee_id')->references('id')->on('store_employees')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_employee_permissions');
    }
};
