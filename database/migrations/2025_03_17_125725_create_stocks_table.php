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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('store_id');
            $table->string('product_id');
            $table->string('product_name');
            $table->string('sku')->nullable();
            $table->bigInteger('category_id');
            $table->bigInteger('unit_id')->nullable();
            $table->string('supplier_name')->nullable();
            $table->date('purchase_date')->nullable();
            $table->text('description')->nullable();
            $table->integer('quantity')->nullable();
            $table->float('price')->nullable();
            $table->float('discount')->nullable();
            $table->float('tax')->nullable();
            $table->date('manufactured_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('images')->nullable();

            $table->tinyInteger('status')->default(1)->unsigned();
            $table->timestamps();
            $table->unique(['product_id', 'store_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
