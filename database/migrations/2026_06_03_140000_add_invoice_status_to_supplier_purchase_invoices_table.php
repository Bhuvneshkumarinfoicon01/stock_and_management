<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('supplier_purchase_invoices', function (Blueprint $table) {
            $table->enum('invoice_status', ['Draft', 'Confirmed', 'Cancelled'])
                ->default('Confirmed')
                ->after('status');
        });

        DB::table('supplier_purchase_invoices')->update(['invoice_status' => 'Confirmed']);
    }

    public function down(): void
    {
        Schema::table('supplier_purchase_invoices', function (Blueprint $table) {
            $table->dropColumn('invoice_status');
        });
    }
};
