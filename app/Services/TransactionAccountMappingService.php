<?php

namespace App\Services;

use App\Models\GlAccount;
use App\Models\TransactionAccountMapping;
use Illuminate\Support\Facades\Schema;

class TransactionAccountMappingService
{
    /** @var array<string, ?TransactionAccountMapping> */
    private array $cache = [];

    public function mapping(string $sourceType): ?TransactionAccountMapping
    {
        if (! Schema::hasTable('transaction_account_mappings')) {
            return null;
        }

        if (! array_key_exists($sourceType, $this->cache)) {
            $this->cache[$sourceType] = TransactionAccountMapping::query()
                ->where('source_type', $sourceType)
                ->first();
        }

        return $this->cache[$sourceType];
    }

    public function debitAccountId(string $sourceType, ?string $fallbackCode = null): int
    {
        $mapping = $this->mapping($sourceType);
        if ($mapping?->debit_account_id) {
            return (int) $mapping->debit_account_id;
        }

        return $this->accountIdFromCode($fallbackCode ?? $this->defaultCodeForDebit($sourceType));
    }

    public function creditAccountId(string $sourceType, ?string $fallbackCode = null): int
    {
        $mapping = $this->mapping($sourceType);
        if ($mapping?->credit_account_id) {
            return (int) $mapping->credit_account_id;
        }

        return $this->accountIdFromCode($fallbackCode ?? $this->defaultCodeForCredit($sourceType));
    }

    private function defaultCodeForDebit(string $sourceType): string
    {
        return match ($sourceType) {
            'sale_invoice' => (string) config('accounting.accounts.accounts_receivable', '1100'),
            'purchase_invoice' => (string) config('accounting.accounts.inventory', '1200'),
            'customer_payment' => (string) config('accounting.accounts.cash', '1000'),
            'supplier_payment' => (string) config('accounting.accounts.accounts_payable', '2000'),
            default => (string) config('accounting.accounts.accounts_receivable', '1100'),
        };
    }

    private function defaultCodeForCredit(string $sourceType): string
    {
        return match ($sourceType) {
            'sale_invoice' => (string) config('accounting.accounts.sales_revenue', '4000'),
            'purchase_invoice' => (string) config('accounting.accounts.accounts_payable', '2000'),
            'customer_payment' => (string) config('accounting.accounts.accounts_receivable', '1100'),
            'supplier_payment' => (string) config('accounting.accounts.cash', '1000'),
            default => (string) config('accounting.accounts.sales_revenue', '4000'),
        };
    }

    private function accountIdFromCode(string $code): int
    {
        $id = GlAccount::query()
            ->where('code', $code)
            ->where('is_active', true)
            ->value('id');

        if (! $id) {
            throw new \RuntimeException("GL account code [{$code}] not found or inactive.");
        }

        return (int) $id;
    }
}
