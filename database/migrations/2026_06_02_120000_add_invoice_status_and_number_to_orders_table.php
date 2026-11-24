<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('invoice_status', ['Draft', 'Confirmed', 'Cancelled'])
                ->default('Draft')
                ->after('invoice_type');

            $table->string('invoice_number', 64)->nullable()->after('invoice_status');
            $table->index(['store_id', 'invoice_number']);
        });

        // Backfill existing orders (prefix may be applied later when stores.invoice_prefix exists).
        DB::statement("
            UPDATE orders o
            SET
                o.invoice_status = CASE WHEN o.status = 3 THEN 'Draft' ELSE 'Confirmed' END,
                o.invoice_number = COALESCE(o.invoice_number, CONCAT('INV/', o.id))
            WHERE o.invoice_number IS NULL OR o.invoice_status IS NULL
        ");

        if (Schema::hasColumn('stores', 'invoice_prefix')) {
            DB::statement("
                UPDATE orders o
                INNER JOIN stores s ON o.store_id = s.id
                SET o.invoice_number = CONCAT(COALESCE(NULLIF(s.invoice_prefix, ''), 'INV/'), o.id)
                WHERE o.invoice_number IS NOT NULL
            ");
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['store_id', 'invoice_number']);
            $table->dropColumn('invoice_number');
            $table->dropColumn('invoice_status');
        });
    }
};

