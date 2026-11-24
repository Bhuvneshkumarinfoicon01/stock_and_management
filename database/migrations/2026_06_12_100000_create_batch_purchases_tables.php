<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            if (! Schema::hasColumn('stores', 'batch_purchase_prefix')) {
                $table->string('batch_purchase_prefix', 32)->default('BP')->after('next_receipt_number');
            }
            if (! Schema::hasColumn('stores', 'next_batch_purchase_number')) {
                $table->unsignedInteger('next_batch_purchase_number')->default(1)->after('batch_purchase_prefix');
            }
        });

        if (! Schema::hasTable('batch_purchases')) {
            Schema::create('batch_purchases', function (Blueprint $table) {
                $table->id();
                $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
                $table->string('batch_number')->unique();
                $table->date('batch_date');
                $table->string('status')->default('draft');
                $table->boolean('tax_exempt')->default(false);
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->decimal('total', 15, 2)->default(0);
                $table->decimal('charge_invoices_subtotal', 15, 2)->default(0);
                $table->decimal('allocated_additional_charges', 15, 2)->default(0);
                $table->string('draft_reference')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('batch_purchase_items')) {
            Schema::create('batch_purchase_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('batch_purchase_id')->constrained('batch_purchases')->cascadeOnDelete();
                $table->string('product_id')->nullable();
                $table->string('product_name');
                $table->string('sku')->nullable();
                $table->unsignedBigInteger('category_id')->nullable();
                $table->string('category_name')->nullable();
                $table->unsignedInteger('qty');
                $table->decimal('unit_price', 15, 2);
                $table->decimal('amount', 15, 2);
                $table->decimal('additional_charges', 15, 2)->default(0);
                $table->decimal('grand_total', 15, 2);
                $table->decimal('final_unit', 15, 4);
                $table->boolean('is_taxable')->default(true);
                $table->decimal('tax_rate', 8, 2)->default(0);
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('batch_purchase_product_invoice')) {
            Schema::create('batch_purchase_product_invoice', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('batch_purchase_id');
                $table->unsignedBigInteger('supplier_purchase_invoice_id');
                $table->unique(['batch_purchase_id', 'supplier_purchase_invoice_id'], 'bp_prod_inv_unique');
                $table->foreign('batch_purchase_id', 'bp_prod_inv_batch_fk')
                    ->references('id')->on('batch_purchases')->cascadeOnDelete();
                $table->foreign('supplier_purchase_invoice_id', 'bp_prod_inv_spi_fk')
                    ->references('id')->on('supplier_purchase_invoices')->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('batch_purchase_charge_invoice')) {
            Schema::create('batch_purchase_charge_invoice', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('batch_purchase_id');
                $table->unsignedBigInteger('supplier_purchase_invoice_id');
                $table->unique(['batch_purchase_id', 'supplier_purchase_invoice_id'], 'bp_chg_inv_unique');
                $table->foreign('batch_purchase_id', 'bp_chg_inv_batch_fk')
                    ->references('id')->on('batch_purchases')->cascadeOnDelete();
                $table->foreign('supplier_purchase_invoice_id', 'bp_chg_inv_spi_fk')
                    ->references('id')->on('supplier_purchase_invoices')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_purchase_charge_invoice');
        Schema::dropIfExists('batch_purchase_product_invoice');
        Schema::dropIfExists('batch_purchase_items');
        Schema::dropIfExists('batch_purchases');

        Schema::table('stores', function (Blueprint $table) {
            foreach (['batch_purchase_prefix', 'next_batch_purchase_number'] as $col) {
                if (Schema::hasColumn('stores', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
