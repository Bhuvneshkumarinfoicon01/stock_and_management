<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'draft_reference')) {
                $table->string('draft_reference', 64)->nullable()->after('invoice_number');
            }
            if (! Schema::hasColumn('orders', 'credit_limit_blocked')) {
                $table->boolean('credit_limit_blocked')->default(false)->after('draft_reference');
            }
        });

        Schema::table('supplier_purchase_invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('supplier_purchase_invoices', 'draft_reference')) {
                $table->string('draft_reference', 64)->nullable()->after('invoice_number');
            }
            if (! Schema::hasColumn('supplier_purchase_invoices', 'credit_limit_blocked')) {
                $table->boolean('credit_limit_blocked')->default(false)->after('draft_reference');
            }
        });

        if (Schema::hasColumn('supplier_purchase_invoices', 'invoice_number')) {
            DB::statement('ALTER TABLE supplier_purchase_invoices MODIFY invoice_number VARCHAR(64) NULL');
        }

        if (Schema::hasTable('settings') && Schema::hasColumn('settings', 'value')) {
            DB::statement('ALTER TABLE settings MODIFY value TEXT NULL');
        }

        if (Schema::hasColumn('supplier_purchase_invoices', 'draft_reference')) {
            $purchaseDrafts = DB::table('supplier_purchase_invoices')
                ->where('invoice_status', 'Draft')
                ->where(function ($q) {
                    $q->whereNull('draft_reference')->orWhere('draft_reference', '');
                })
                ->orderBy('id')
                ->get(['id', 'invoice_number']);

            foreach ($purchaseDrafts as $row) {
                $ref = 'DRAFT-P' . str_pad((string) $row->id, 6, '0', STR_PAD_LEFT);
                DB::table('supplier_purchase_invoices')->where('id', $row->id)->update([
                    'draft_reference' => $ref,
                    'invoice_number' => str_starts_with((string) ($row->invoice_number ?? ''), 'HOLD-') ? null : $row->invoice_number,
                ]);
            }
        }

        if (Schema::hasColumn('orders', 'draft_reference')) {
            $saleDrafts = DB::table('orders')
                ->where('invoice_status', 'Draft')
                ->where(function ($q) {
                    $q->whereNull('draft_reference')->orWhere('draft_reference', '');
                })
                ->orderBy('id')
                ->get(['id', 'invoice_number']);

            foreach ($saleDrafts as $row) {
                $ref = 'DRAFT-S' . str_pad((string) $row->id, 6, '0', STR_PAD_LEFT);
                DB::table('orders')->where('id', $row->id)->update([
                    'draft_reference' => $ref,
                    'invoice_number' => null,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'credit_limit_blocked')) {
                $table->dropColumn('credit_limit_blocked');
            }
            if (Schema::hasColumn('orders', 'draft_reference')) {
                $table->dropColumn('draft_reference');
            }
        });

        Schema::table('supplier_purchase_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('supplier_purchase_invoices', 'credit_limit_blocked')) {
                $table->dropColumn('credit_limit_blocked');
            }
            if (Schema::hasColumn('supplier_purchase_invoices', 'draft_reference')) {
                $table->dropColumn('draft_reference');
            }
        });
    }
};
