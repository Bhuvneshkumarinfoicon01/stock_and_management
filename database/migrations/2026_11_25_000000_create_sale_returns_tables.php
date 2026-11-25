<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            if (! Schema::hasColumn('stores', 'sale_return_prefix')) {
                $table->string('sale_return_prefix', 32)->default('SR');
            }
            if (! Schema::hasColumn('stores', 'next_sale_return_number')) {
                $table->unsignedInteger('next_sale_return_number')->default(1);
            }
        });

        Schema::create('sale_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->string('return_number', 64)->unique();
            $table->date('return_date');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->boolean('tax_exempt')->default(false);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('settlement_method', 32)->default('cash_refund');
            $table->string('status', 32)->default('completed');
            $table->text('reason')->nullable();
            $table->timestamps();
        });

        Schema::create('sale_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_return_id')->constrained('sale_returns')->cascadeOnDelete();
            $table->string('product_id', 64);
            $table->string('product_name');
            $table->unsignedInteger('qty');
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->boolean('is_taxable')->default(false);
            $table->decimal('tax_rate', 7, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_return_items');
        Schema::dropIfExists('sale_returns');

        Schema::table('stores', function (Blueprint $table) {
            foreach (['sale_return_prefix', 'next_sale_return_number'] as $column) {
                if (Schema::hasColumn('stores', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
