<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->date('due_date')->nullable()->after('invoice_type');
            $table->unsignedInteger('payment_terms_days')->nullable()->after('due_date');
            $table->double('paid_amount')->default(0)->after('total_price');
            $table->double('due_amount')->default(0)->after('paid_amount');
            $table->timestamp('last_payment_at')->nullable()->after('due_amount');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'due_date',
                'payment_terms_days',
                'paid_amount',
                'due_amount',
                'last_payment_at',
            ]);
        });
    }
};


