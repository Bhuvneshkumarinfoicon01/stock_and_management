<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('stocks') && ! Schema::hasColumn('stocks', 'cost')) {
            Schema::table('stocks', function (Blueprint $table) {
                $table->decimal('cost', 10, 2)->default(0)->after('price');
            });
        }

        if (! Schema::hasTable('product_store_markups')) {
            Schema::create('product_store_markups', function (Blueprint $table) {
                $table->id();
                $table->string('product_id');
                $table->unsignedBigInteger('store_id');
                $table->decimal('markup_percentage', 8, 2)->default(0);
                $table->timestamps();

                $table->unique(['product_id', 'store_id'], 'product_store_markups_unique');
                $table->foreign('store_id')->references('id')->on('stores')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_store_markups');

        if (Schema::hasTable('stocks') && Schema::hasColumn('stocks', 'cost')) {
            Schema::table('stocks', function (Blueprint $table) {
                $table->dropColumn('cost');
            });
        }
    }
};
