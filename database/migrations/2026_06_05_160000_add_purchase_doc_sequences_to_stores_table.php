<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            if (! Schema::hasColumn('stores', 'purchase_invoice_prefix')) {
                $table->string('purchase_invoice_prefix', 32)->default('PINV')->after('next_sale_invoice_number');
            }
            if (! Schema::hasColumn('stores', 'next_purchase_invoice_number')) {
                $table->unsignedInteger('next_purchase_invoice_number')->default(1)->after('purchase_invoice_prefix');
            }

            if (! Schema::hasColumn('stores', 'purchase_order_prefix')) {
                $table->string('purchase_order_prefix', 32)->default('PO')->after('next_purchase_invoice_number');
            }
            if (! Schema::hasColumn('stores', 'next_purchase_order_number')) {
                $table->unsignedInteger('next_purchase_order_number')->default(1)->after('purchase_order_prefix');
            }

            if (! Schema::hasColumn('stores', 'purchase_return_prefix')) {
                $table->string('purchase_return_prefix', 32)->default('PR')->after('next_purchase_order_number');
            }
            if (! Schema::hasColumn('stores', 'next_purchase_return_number')) {
                $table->unsignedInteger('next_purchase_return_number')->default(1)->after('purchase_return_prefix');
            }

            if (! Schema::hasColumn('stores', 'grn_prefix')) {
                $table->string('grn_prefix', 32)->default('GRN')->after('next_purchase_return_number');
            }
            if (! Schema::hasColumn('stores', 'next_grn_number')) {
                $table->unsignedInteger('next_grn_number')->default(1)->after('grn_prefix');
            }

            // Not fully implemented as source documents yet in DB, but requested in point 11 UI.
            if (! Schema::hasColumn('stores', 'credit_note_prefix')) {
                $table->string('credit_note_prefix', 32)->default('CN')->after('next_grn_number');
            }
            if (! Schema::hasColumn('stores', 'next_credit_note_number')) {
                $table->unsignedInteger('next_credit_note_number')->default(1)->after('credit_note_prefix');
            }

            if (! Schema::hasColumn('stores', 'debit_note_prefix')) {
                $table->string('debit_note_prefix', 32)->default('DN')->after('next_credit_note_number');
            }
            if (! Schema::hasColumn('stores', 'next_debit_note_number')) {
                $table->unsignedInteger('next_debit_note_number')->default(1)->after('debit_note_prefix');
            }

            // Receipt numbering is applied to payment reference_no when missing.
            if (! Schema::hasColumn('stores', 'receipt_prefix')) {
                $table->string('receipt_prefix', 32)->default('RCPT')->after('next_debit_note_number');
            }
            if (! Schema::hasColumn('stores', 'next_receipt_number')) {
                $table->unsignedInteger('next_receipt_number')->default(1)->after('receipt_prefix');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            foreach ([
                'purchase_invoice_prefix',
                'next_purchase_invoice_number',
                'purchase_order_prefix',
                'next_purchase_order_number',
                'purchase_return_prefix',
                'next_purchase_return_number',
                'grn_prefix',
                'next_grn_number',
                'credit_note_prefix',
                'next_credit_note_number',
                'debit_note_prefix',
                'next_debit_note_number',
                'receipt_prefix',
                'next_receipt_number',
            ] as $col) {
                if (Schema::hasColumn('stores', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

