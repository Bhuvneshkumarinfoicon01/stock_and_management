<?php

namespace App\Services;

use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SaleInvoiceNumberService
{
    /** Fixed 6-digit padding stored in stores.current_sale_invoice_sequence */
    public const NUMERIC_SEQUENCE_FORMAT = '######';

    /**
     * Reserve and return the next sale invoice number for a store (row lock prevents duplicates).
     */
    public function issueNextForStore(int $storeId): string
    {
        if ($storeId < 1) {
            return 'INV' . time();
        }

        return DB::transaction(function () use ($storeId) {
            $store = Store::query()->lockForUpdate()->findOrFail($storeId);

            [$prefix, $width] = $this->resolvePrefixAndWidth($store);
            $sequenceNumber = max(1, (int) ($store->next_sale_invoice_number ?? 1));

            $invoiceNumber = $this->format($prefix, $width, $sequenceNumber);

            if (Schema::hasColumn('stores', 'next_sale_invoice_number')) {
                $store->next_sale_invoice_number = $sequenceNumber + 1;
                $store->save();
            }

            return $invoiceNumber;
        });
    }

    /**
     * @return array{0: string, 1: int}
     */
    public function resolvePrefixAndWidth(Store $store): array
    {
        $prefix = trim((string) ($store->invoice_prefix ?? ''));
        if ($prefix === '') {
            $prefix = 'INV';
        }

        $sequence = trim((string) ($store->current_sale_invoice_sequence ?? ''));
        if (preg_match('/^#+$/u', $sequence)) {
            return [$prefix, strlen($sequence)];
        }

        return [$prefix, strlen(self::NUMERIC_SEQUENCE_FORMAT)];
    }

    public function format(string $prefix, int $width, int $sequenceNumber): string
    {
        if ($width > 0) {
            return $prefix . str_pad((string) $sequenceNumber, $width, '0', STR_PAD_LEFT);
        }

        return $prefix . $sequenceNumber;
    }

    public function preview(Store $store): string
    {
        [$prefix, $width] = $this->resolvePrefixAndWidth($store);
        $sequenceNumber = max(1, (int) ($store->next_sale_invoice_number ?? 1));

        return $this->format($prefix, $width, $sequenceNumber);
    }
}
