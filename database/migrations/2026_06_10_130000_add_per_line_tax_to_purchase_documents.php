<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('purchase_orders') && ! Schema::hasColumn('purchase_orders', 'tax_exempt')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->boolean('tax_exempt')->default(false)->after('tax_amount');
            });
        }

        if (Schema::hasTable('purchase_order_items')) {
            Schema::table('purchase_order_items', function (Blueprint $table) {
                if (! Schema::hasColumn('purchase_order_items', 'is_taxable')) {
                    $table->boolean('is_taxable')->default(true)->after('line_total');
                }
                if (! Schema::hasColumn('purchase_order_items', 'tax_rate')) {
                    $table->decimal('tax_rate', 8, 2)->default(0)->after('is_taxable');
                }
                if (! Schema::hasColumn('purchase_order_items', 'tax_amount')) {
                    $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_rate');
                }
            });
        }

        if (Schema::hasTable('purchase_returns')) {
            Schema::table('purchase_returns', function (Blueprint $table) {
                if (! Schema::hasColumn('purchase_returns', 'subtotal')) {
                    $table->decimal('subtotal', 15, 2)->default(0)->after('return_date');
                }
                if (! Schema::hasColumn('purchase_returns', 'tax_amount')) {
                    $table->decimal('tax_amount', 15, 2)->default(0)->after('subtotal');
                }
                if (! Schema::hasColumn('purchase_returns', 'tax_exempt')) {
                    $table->boolean('tax_exempt')->default(false)->after('tax_amount');
                }
            });
        }

        if (Schema::hasTable('purchase_return_items')) {
            Schema::table('purchase_return_items', function (Blueprint $table) {
                if (! Schema::hasColumn('purchase_return_items', 'is_taxable')) {
                    $table->boolean('is_taxable')->default(true)->after('line_total');
                }
                if (! Schema::hasColumn('purchase_return_items', 'tax_rate')) {
                    $table->decimal('tax_rate', 8, 2)->default(0)->after('is_taxable');
                }
                if (! Schema::hasColumn('purchase_return_items', 'tax_amount')) {
                    $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_rate');
                }
            });
        }

        if (Schema::hasTable('goods_receipt_notes')) {
            Schema::table('goods_receipt_notes', function (Blueprint $table) {
                if (! Schema::hasColumn('goods_receipt_notes', 'subtotal')) {
                    $table->decimal('subtotal', 15, 2)->default(0)->after('status');
                }
                if (! Schema::hasColumn('goods_receipt_notes', 'tax_amount')) {
                    $table->decimal('tax_amount', 15, 2)->default(0)->after('subtotal');
                }
                if (! Schema::hasColumn('goods_receipt_notes', 'total')) {
                    $table->decimal('total', 15, 2)->default(0)->after('tax_amount');
                }
                if (! Schema::hasColumn('goods_receipt_notes', 'tax_exempt')) {
                    $table->boolean('tax_exempt')->default(false)->after('total');
                }
            });
        }

        if (Schema::hasTable('goods_receipt_note_items')) {
            Schema::table('goods_receipt_note_items', function (Blueprint $table) {
                if (! Schema::hasColumn('goods_receipt_note_items', 'is_taxable')) {
                    $table->boolean('is_taxable')->default(true)->after('line_total');
                }
                if (! Schema::hasColumn('goods_receipt_note_items', 'tax_rate')) {
                    $table->decimal('tax_rate', 8, 2)->default(0)->after('is_taxable');
                }
                if (! Schema::hasColumn('goods_receipt_note_items', 'tax_amount')) {
                    $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_rate');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('purchase_orders') && Schema::hasColumn('purchase_orders', 'tax_exempt')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->dropColumn('tax_exempt');
            });
        }

        if (Schema::hasTable('purchase_order_items')) {
            Schema::table('purchase_order_items', function (Blueprint $table) {
                foreach (['tax_amount', 'tax_rate', 'is_taxable'] as $col) {
                    if (Schema::hasColumn('purchase_order_items', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('purchase_returns')) {
            Schema::table('purchase_returns', function (Blueprint $table) {
                foreach (['tax_exempt', 'tax_amount', 'subtotal'] as $col) {
                    if (Schema::hasColumn('purchase_returns', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('purchase_return_items')) {
            Schema::table('purchase_return_items', function (Blueprint $table) {
                foreach (['tax_amount', 'tax_rate', 'is_taxable'] as $col) {
                    if (Schema::hasColumn('purchase_return_items', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('goods_receipt_notes')) {
            Schema::table('goods_receipt_notes', function (Blueprint $table) {
                foreach (['tax_exempt', 'total', 'tax_amount', 'subtotal'] as $col) {
                    if (Schema::hasColumn('goods_receipt_notes', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('goods_receipt_note_items')) {
            Schema::table('goods_receipt_note_items', function (Blueprint $table) {
                foreach (['tax_amount', 'tax_rate', 'is_taxable'] as $col) {
                    if (Schema::hasColumn('goods_receipt_note_items', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
