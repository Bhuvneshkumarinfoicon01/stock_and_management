<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $entries = DB::table('customer_ledger_entries')
            ->select('id', 'customer_id', 'order_id', 'debit', 'credit')
            ->orderBy('customer_id')
            ->orderBy('order_id')
            ->orderBy('entry_date')
            ->orderBy('id')
            ->get();

        $runningBalances = [];
        foreach ($entries as $entry) {
            $orderKey = $entry->order_id === null ? 'null' : (string) $entry->order_id;
            $key = $entry->customer_id . '|' . $orderKey;
            $previous = $runningBalances[$key] ?? 0.0;
            $current = round($previous + (float) $entry->debit - (float) $entry->credit, 2);
            $runningBalances[$key] = $current;

            DB::table('customer_ledger_entries')
                ->where('id', $entry->id)
                ->update(['balance_after' => $current]);
        }
    }

    public function down(): void
    {
        // No rollback: this migration is a corrective backfill.
    }
};

