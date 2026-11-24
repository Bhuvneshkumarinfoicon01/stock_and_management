<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->enum('product_type', ['service', 'inventory_stock', 'non_inventory_stock'])
                ->default('inventory_stock')
                ->after('product_name');
        });
    }

    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn('product_type');
        });
    }
};
