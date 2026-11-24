<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->nullable()->unique();
            $table->string('company_name');
            $table->string('contact_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('tax_number')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedTinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create('supplier_purchase_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignId('store_id')->nullable()->constrained('stores')->nullOnDelete();
            $table->string('invoice_number', 64)->unique();
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_percent', 8, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('due_amount', 15, 2)->default(0);
            $table->string('status', 32)->default('unpaid');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('supplier_purchase_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_purchase_invoice_id');
            $table->string('product_id', 64)->nullable();
            $table->string('product_name');
            $table->unsignedInteger('qty')->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('supplier_purchase_invoice_id', 'fk_spi_items_invoice_id')
                ->references('id')
                ->on('supplier_purchase_invoices')
                ->cascadeOnDelete();
        });

        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignId('supplier_purchase_invoice_id')->nullable()->constrained('supplier_purchase_invoices')->nullOnDelete();
            $table->date('payment_date');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method', 64)->nullable();
            $table->string('reference_no', 128)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignId('store_id')->nullable()->constrained('stores')->nullOnDelete();
            $table->string('po_number', 64)->unique();
            $table->date('order_date');
            $table->string('status', 32)->default('draft');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_percent', 8, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->string('product_id', 64)->nullable();
            $table->string('product_name');
            $table->unsignedInteger('qty')->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('store_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignId('store_id')->nullable()->constrained('stores')->nullOnDelete();
            $table->string('purchase_number', 64)->unique();
            $table->date('purchase_date');
            $table->string('status', 32)->default('completed');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_percent', 8, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('store_purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_purchase_id')->constrained('store_purchases')->cascadeOnDelete();
            $table->string('product_id', 64)->nullable();
            $table->string('product_name');
            $table->unsignedInteger('qty')->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignId('store_id')->nullable()->constrained('stores')->nullOnDelete();
            $table->foreignId('supplier_purchase_invoice_id')->nullable()->constrained('supplier_purchase_invoices')->nullOnDelete();
            $table->string('return_number', 64)->unique();
            $table->date('return_date');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('status', 32)->default('draft');
            $table->text('reason')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_return_id')->constrained('purchase_returns')->cascadeOnDelete();
            $table->string('product_id', 64)->nullable();
            $table->string('product_name');
            $table->unsignedInteger('qty')->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('goods_receipt_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignId('store_id')->nullable()->constrained('stores')->nullOnDelete();
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->string('grn_number', 64)->unique();
            $table->date('grn_date');
            $table->string('status', 32)->default('received');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('goods_receipt_note_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goods_receipt_note_id')->constrained('goods_receipt_notes')->cascadeOnDelete();
            $table->string('product_id', 64)->nullable();
            $table->string('product_name');
            $table->unsignedInteger('qty_received')->default(1);
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_receipt_note_items');
        Schema::dropIfExists('goods_receipt_notes');
        Schema::dropIfExists('purchase_return_items');
        Schema::dropIfExists('purchase_returns');
        Schema::dropIfExists('store_purchase_items');
        Schema::dropIfExists('store_purchases');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('supplier_payments');
        Schema::dropIfExists('supplier_purchase_invoice_items');
        Schema::dropIfExists('supplier_purchase_invoices');
        Schema::dropIfExists('suppliers');
    }
};
