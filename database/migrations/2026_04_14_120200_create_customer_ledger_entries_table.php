<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->enum('entry_type', ['invoice', 'payment', 'adjustment']);
            $table->timestamp('entry_date')->nullable();
            $table->string('description')->nullable();
            $table->double('debit')->default(0);
            $table->double('credit')->default(0);
            $table->double('balance_after')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->foreign('payment_id')->references('id')->on('customer_payments')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_ledger_entries');
    }
};

