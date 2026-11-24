<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('stocks')) {
            Schema::table('stocks', function (Blueprint $table) {
                if (! Schema::hasColumn('stocks', 'is_taxable')) {
                    $table->boolean('is_taxable')->default(true)->after('tax');
                }
                if (! Schema::hasColumn('stocks', 'tax_rate')) {
                    $table->decimal('tax_rate', 8, 2)->nullable()->after('is_taxable');
                }
            });
        }

        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                if (! Schema::hasColumn('order_items', 'is_taxable')) {
                    $table->boolean('is_taxable')->default(true)->after('price');
                }
                if (! Schema::hasColumn('order_items', 'tax_rate')) {
                    $table->decimal('tax_rate', 8, 2)->default(0)->after('is_taxable');
                }
                if (! Schema::hasColumn('order_items', 'tax_amount')) {
                    $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_rate');
                }
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (! Schema::hasColumn('orders', 'tax_exempt')) {
                    $table->boolean('tax_exempt')->default(false)->after('tax');
                }
                if (! Schema::hasColumn('orders', 'tax_amount')) {
                    $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_exempt');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('stocks')) {
            Schema::table('stocks', function (Blueprint $table) {
                if (Schema::hasColumn('stocks', 'tax_rate')) {
                    $table->dropColumn('tax_rate');
                }
                if (Schema::hasColumn('stocks', 'is_taxable')) {
                    $table->dropColumn('is_taxable');
                }
            });
        }

        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                foreach (['tax_amount', 'tax_rate', 'is_taxable'] as $col) {
                    if (Schema::hasColumn('order_items', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                foreach (['tax_amount', 'tax_exempt'] as $col) {
                    if (Schema::hasColumn('orders', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
