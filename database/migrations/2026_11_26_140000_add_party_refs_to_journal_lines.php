<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journal_lines', function (Blueprint $table) {
            $table->foreignId('supplier_id')
                ->nullable()
                ->after('gl_account_id')
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('customer_id')
                ->nullable()
                ->after('supplier_id')
                ->constrained()
                ->nullOnDelete();
            $table->string('party_name')->nullable()->after('customer_id');
        });

        $sources = [
            ['order_sale', 'orders', 'customer_id', 'customers', 'customer_name', 'Customer receivable'],
            ['order', 'orders', 'customer_id', 'customers', 'customer_name', 'Customer receivable'],
            ['order_sale', 'orders', 'customer_id', 'customers', 'customer_name', 'Cash received'],
            ['order', 'orders', 'customer_id', 'customers', 'customer_name', 'Cash received'],
            ['customer_payment', 'customer_payments', 'customer_id', 'customers', 'customer_name', 'Reduce receivable'],
            ['customer_payment', 'customer_payments', 'customer_id', 'customers', 'customer_name', 'Payment received'],
            ['sale_return', 'sale_returns', 'customer_id', 'customers', 'customer_name', 'Customer account credit'],
            ['sale_return', 'sale_returns', 'customer_id', 'customers', 'customer_name', 'Cash refunded to customer'],
            ['purchase_order', 'purchase_orders', 'supplier_id', 'suppliers', 'company_name', 'Supplier payable'],
            ['store_purchase', 'store_purchases', 'supplier_id', 'suppliers', 'company_name', 'Supplier payable'],
            ['goods_receipt_note', 'goods_receipt_notes', 'supplier_id', 'suppliers', 'company_name', 'Supplier payable'],
            ['supplier_purchase_invoice', 'supplier_purchase_invoices', 'supplier_id', 'suppliers', 'company_name', 'Supplier payable'],
            ['purchase_return', 'purchase_returns', 'supplier_id', 'suppliers', 'company_name', 'Reduce supplier payable'],
            ['purchase_return', 'purchase_returns', 'supplier_id', 'suppliers', 'company_name', 'Cash received for purchase return'],
            ['supplier_payment', 'supplier_payments', 'supplier_id', 'suppliers', 'company_name', 'Pay supplier'],
            ['supplier_payment', 'supplier_payments', 'supplier_id', 'suppliers', 'company_name', 'Cash paid'],
        ];

        foreach ($sources as [$sourceType, $sourceTable, $partyColumn, $partyTable, $partyNameColumn, $lineDescription]) {
            $partyName = $sourceTable === 'orders'
                ? DB::raw('COALESCE(customers.customer_name, orders.customer_name) as party_name')
                : "{$partyTable}.{$partyNameColumn} as party_name";

            DB::table('journal_lines')
                ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
                ->join($sourceTable, "{$sourceTable}.id", '=', 'journal_entries.source_id')
                ->leftJoin($partyTable, "{$partyTable}.id", '=', "{$sourceTable}.{$partyColumn}")
                ->where('journal_entries.source_type', $sourceType)
                ->where('journal_lines.line_description', $lineDescription)
                ->select(
                    'journal_lines.id as line_id',
                    "{$sourceTable}.{$partyColumn} as party_id",
                    $partyName,
                )
                ->orderBy('journal_lines.id')
                ->chunk(500, function ($lines) use ($partyColumn, $partyNameColumn) {
                    $partyType = $partyColumn === 'supplier_id' ? 'supplier_id' : 'customer_id';
                    foreach ($lines as $line) {
                        DB::table('journal_lines')
                            ->where('id', $line->line_id)
                            ->update([
                                $partyType => $line->party_id,
                                'party_name' => $line->party_name,
                            ]);
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::table('journal_lines', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_id');
            $table->dropConstrainedForeignId('supplier_id');
            $table->dropColumn('party_name');
        });
    }
};
