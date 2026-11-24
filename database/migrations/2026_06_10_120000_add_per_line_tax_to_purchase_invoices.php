<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('supplier_purchase_invoices')) {
            Schema::table('supplier_purchase_invoices', function (Blueprint $table) {
                if (! Schema::hasColumn('supplier_purchase_invoices', 'tax_exempt')) {
                    $table->boolean('tax_exempt')->default(false)->after('tax_amount');
                }
            });
        }

        if (Schema::hasTable('supplier_purchase_invoice_items')) {
            Schema::table('supplier_purchase_invoice_items', function (Blueprint $table) {
                if (! Schema::hasColumn('supplier_purchase_invoice_items', 'is_taxable')) {
                    $table->boolean('is_taxable')->default(true)->after('line_total');
                }
                if (! Schema::hasColumn('supplier_purchase_invoice_items', 'tax_rate')) {
                    $table->decimal('tax_rate', 8, 2)->default(0)->after('is_taxable');
                }
                if (! Schema::hasColumn('supplier_purchase_invoice_items', 'tax_amount')) {
                    $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_rate');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('supplier_purchase_invoices') && Schema::hasColumn('supplier_purchase_invoices', 'tax_exempt')) {
            Schema::table('supplier_purchase_invoices', function (Blueprint $table) {
                $table->dropColumn('tax_exempt');
            });
        }

        if (Schema::hasTable('supplier_purchase_invoice_items')) {
            Schema::table('supplier_purchase_invoice_items', function (Blueprint $table) {
                foreach (['tax_amount', 'tax_rate', 'is_taxable'] as $col) {
                    if (Schema::hasColumn('supplier_purchase_invoice_items', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
