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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // $table->bigInteger('store_id');
            $table->unsignedBigInteger('store_id')->nullable()->constrained()->onDelete('set null');

            // $table->bigInteger('product_id');
            $table->bigInteger('customer_id');
            // $table->string('product_name');
            // $table->string('product_no');
            $table->string('customer_name');
            // $table->integer('quantity');
            $table->float('total_price');
            $table->integer('status')->default(1);
            $table->float('subtotal_price');
            $table->float('tax')->nullable();
            $table->integer('discount')->nullable();
            $table->integer('shipping')->nullable();
            $table->string('payment_method');
            $table->string('type')->default('online');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
