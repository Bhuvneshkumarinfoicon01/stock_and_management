<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->foreignId('debit_account_id')
                ->nullable()
                ->after('unit_id')
                ->constrained('gl_accounts')
                ->nullOnDelete();

            $table->foreignId('credit_account_id')
                ->nullable()
                ->after('debit_account_id')
                ->constrained('gl_accounts')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('credit_account_id');
            $table->dropConstrainedForeignId('debit_account_id');
        });
    }
};
