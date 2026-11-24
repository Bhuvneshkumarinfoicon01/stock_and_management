<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('supplier_purchase_invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('supplier_purchase_invoices', 'purchase_order_id')) {
                $table->foreignId('purchase_order_id')
                    ->nullable()
                    ->after('store_id')
                    ->constrained('purchase_orders')
                    ->nullOnDelete();
            }
        });

        Schema::table('goods_receipt_notes', function (Blueprint $table) {
            if (! Schema::hasColumn('goods_receipt_notes', 'supplier_purchase_invoice_id')) {
                $table->foreignId('supplier_purchase_invoice_id')
                    ->nullable()
                    ->after('purchase_order_id')
                    ->constrained('supplier_purchase_invoices')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('goods_receipt_notes', function (Blueprint $table) {
            if (Schema::hasColumn('goods_receipt_notes', 'supplier_purchase_invoice_id')) {
                $table->dropForeign(['supplier_purchase_invoice_id']);
                $table->dropColumn('supplier_purchase_invoice_id');
            }
        });

        Schema::table('supplier_purchase_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('supplier_purchase_invoices', 'purchase_order_id')) {
                $table->dropForeign(['purchase_order_id']);
                $table->dropColumn('purchase_order_id');
            }
        });
    }
};
