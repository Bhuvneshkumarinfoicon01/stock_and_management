<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transaction_account_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('source_type', 64)->unique();
            $table->foreignId('debit_account_id')->constrained('gl_accounts')->restrictOnDelete();
            $table->foreignId('credit_account_id')->constrained('gl_accounts')->restrictOnDelete();
            $table->string('label')->nullable();
            $table->timestamps();
        });

        $accountIds = DB::table('gl_accounts')
            ->whereIn('code', ['1100', '4000', '1200', '2000', '1000', '5000'])
            ->pluck('id', 'code');

        if ($accountIds->count() < 4) {
            return;
        }

        $now = now();
        $defaults = [
            [
                'source_type' => 'sale_invoice',
                'label' => 'Sale invoice (revenue)',
                'debit_account_id' => $accountIds['1100'],
                'credit_account_id' => $accountIds['4000'],
            ],
            [
                'source_type' => 'purchase_invoice',
                'label' => 'Purchase invoice',
                'debit_account_id' => $accountIds['1200'],
                'credit_account_id' => $accountIds['2000'],
            ],
            [
                'source_type' => 'credit_note',
                'label' => 'Credit note',
                'debit_account_id' => $accountIds['4000'],
                'credit_account_id' => $accountIds['1100'],
            ],
            [
                'source_type' => 'debit_note',
                'label' => 'Debit note',
                'debit_account_id' => $accountIds['2000'],
                'credit_account_id' => $accountIds['1200'],
            ],
            [
                'source_type' => 'customer_payment',
                'label' => 'Customer receipt',
                'debit_account_id' => $accountIds['1000'],
                'credit_account_id' => $accountIds['1100'],
            ],
            [
                'source_type' => 'supplier_payment',
                'label' => 'Supplier payment',
                'debit_account_id' => $accountIds['2000'],
                'credit_account_id' => $accountIds['1000'],
            ],
        ];

        foreach ($defaults as $row) {
            DB::table('transaction_account_mappings')->insert(array_merge($row, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_account_mappings');
    }
};
