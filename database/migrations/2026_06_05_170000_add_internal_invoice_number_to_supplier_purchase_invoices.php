<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('supplier_purchase_invoices')) {
            return;
        }

        Schema::table('supplier_purchase_invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('supplier_purchase_invoices', 'internal_invoice_number')) {
                $table->string('internal_invoice_number', 64)
                    ->nullable()
                    ->after('store_id');
            }
        });

        // Backfill: existing confirmed numbers become internal references.
        if (Schema::hasColumn('supplier_purchase_invoices', 'internal_invoice_number')) {
            DB::table('supplier_purchase_invoices')
                ->whereNull('internal_invoice_number')
                ->whereNotNull('invoice_number')
                ->where(function ($query) {
                    $query->whereNull('invoice_status')
                        ->orWhere('invoice_status', '!=', 'Draft');
                })
                ->update([
                    'internal_invoice_number' => DB::raw('invoice_number'),
                ]);
        }

        // Supplier invoice number can be anything — drop global uniqueness.
        $indexes = collect(DB::select('SHOW INDEX FROM supplier_purchase_invoices'))
            ->pluck('Key_name')
            ->unique();

        if ($indexes->contains('supplier_purchase_invoices_invoice_number_unique')) {
            Schema::table('supplier_purchase_invoices', function (Blueprint $table) {
                $table->dropUnique('supplier_purchase_invoices_invoice_number_unique');
            });
        }

        Schema::table('supplier_purchase_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('supplier_purchase_invoices', 'internal_invoice_number')) {
                $table->unique('internal_invoice_number', 'supplier_purchase_invoices_internal_invoice_number_unique');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('supplier_purchase_invoices')) {
            return;
        }

        Schema::table('supplier_purchase_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('supplier_purchase_invoices', 'internal_invoice_number')) {
                $table->dropUnique('supplier_purchase_invoices_internal_invoice_number_unique');
                $table->dropColumn('internal_invoice_number');
            }
        });
    }
};
