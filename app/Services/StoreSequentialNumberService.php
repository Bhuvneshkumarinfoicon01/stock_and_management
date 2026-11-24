<?php

namespace App\Services;

use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StoreSequentialNumberService
{
    private const NUMERIC_WIDTH = 6;

    /**
     * Preview next formatted sequential number WITHOUT incrementing.
     * Returns null if the required store columns are not present.
     */
    public function previewNextForStore(
        int $storeId,
        string $prefixColumn,
        string $nextColumn,
        string $defaultPrefix
    ): ?string {
        if ($storeId < 1) {
            return null;
        }

        if (! Schema::hasColumn('stores', $prefixColumn) || ! Schema::hasColumn('stores', $nextColumn)) {
            return null;
        }

        $store = Store::query()->find($storeId);
        if (! $store) {
            return null;
        }

        $prefix = trim((string) ($store->{$prefixColumn} ?? ''));
        if ($prefix === '') {
            $prefix = $defaultPrefix;
        }

        $sequenceNumber = max(1, (int) ($store->{$nextColumn} ?? 1));

        return $prefix . str_pad((string) $sequenceNumber, self::NUMERIC_WIDTH, '0', STR_PAD_LEFT);
    }

    /**
     * Issue the next formatted sequential number and increment the counter.
     * If uniqueTable+uniqueColumn are provided, it will keep incrementing until it finds an unused value.
     * Returns null if the required store columns are not present.
     */
    public function issueNextForStore(
        int $storeId,
        string $prefixColumn,
        string $nextColumn,
        string $defaultPrefix,
        ?string $uniqueTable = null,
        ?string $uniqueColumn = null
    ): ?string {
        if ($storeId < 1) {
            return null;
        }

        if (! Schema::hasColumn('stores', $prefixColumn) || ! Schema::hasColumn('stores', $nextColumn)) {
            return null;
        }

        return DB::transaction(function () use ($storeId, $prefixColumn, $nextColumn, $defaultPrefix, $uniqueTable, $uniqueColumn) {
            $store = Store::query()->lockForUpdate()->findOrFail($storeId);

            $prefix = trim((string) ($store->{$prefixColumn} ?? ''));
            if ($prefix === '') {
                $prefix = $defaultPrefix;
            }

            $sequenceNumber = max(1, (int) ($store->{$nextColumn} ?? 1));

            while (true) {
                $candidate = $prefix . str_pad((string) $sequenceNumber, self::NUMERIC_WIDTH, '0', STR_PAD_LEFT);

                if ($uniqueTable && $uniqueColumn) {
                    $exists = DB::table($uniqueTable)->where($uniqueColumn, $candidate)->exists();
                    if ($exists) {
                        $sequenceNumber++;
                        continue;
                    }
                }

                $store->{$nextColumn} = $sequenceNumber + 1;
                $store->save();

                return $candidate;
            }
        });
    }
}

