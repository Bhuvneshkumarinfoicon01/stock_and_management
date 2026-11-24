<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Supplier;
use App\Models\SupplierPurchaseInvoice;

class CreditLimitService
{
    public const SETTING_OVERRIDE_USER_IDS = 'credit_limit_override_user_ids';

    public function canOverrideCreditLimit(?int $userId = null): bool
    {
        $userId = $userId ?? (int) auth()->id();
        if ($userId <= 0) {
            return false;
        }

        return in_array($userId, $this->overrideUserIds(), true);
    }

    /** @return int[] */
    public function overrideUserIds(): array
    {
        $raw = (string) (function_exists('setting')
            ? setting(self::SETTING_OVERRIDE_USER_IDS, '')
            : '');

        if ($raw === '') {
            return [];
        }

        return array_values(array_filter(array_map('intval', preg_split('/\s*,\s*/', $raw))));
    }

    public function saveOverrideUserIds(array $userIds): void
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $userIds))));
        \App\Models\Setting::query()->updateOrCreate(
            ['key_name' => self::SETTING_OVERRIDE_USER_IDS],
            [
                'value' => implode(',', $ids),
                'label' => 'Users Allowed To Override Credit Limits',
                'placeholder' => 'Select users who may override credit limits',
            ]
        );
    }

    public function customerOutstanding(int $customerId, ?int $excludeOrderId = null): float
    {
        $query = Order::query()
            ->where('customer_id', $customerId)
            ->where('type', 'offline')
            ->where('invoice_status', 'Confirmed');

        if ($excludeOrderId) {
            $query->where('id', '!=', $excludeOrderId);
        }

        return (float) $query->sum('due_amount');
    }

    public function supplierOutstanding(int $supplierId, ?int $excludeInvoiceId = null): float
    {
        $query = SupplierPurchaseInvoice::query()
            ->where('supplier_id', $supplierId)
            ->where('invoice_status', 'Confirmed');

        if ($excludeInvoiceId) {
            $query->where('id', '!=', $excludeInvoiceId);
        }

        return (float) $query->sum('due_amount');
    }

    /**
     * @return array{
     *     exceeded: bool,
     *     has_limit: bool,
     *     limit: float,
     *     outstanding: float,
     *     additional_due: float,
     *     projected_total: float,
     *     message: ?string
     * }
     */
    public function assessCustomerCredit(Customer $customer, float $additionalDueAmount, ?int $excludeOrderId = null): array
    {
        $limit = (float) ($customer->credit_limit ?? 0);
        $outstanding = $this->customerOutstanding((int) $customer->id, $excludeOrderId);
        $additional = max(0, $additionalDueAmount);
        $projected = $outstanding + $additional;
        $hasLimit = $limit > 0;
        $exceeded = $hasLimit && $projected > $limit + 0.0001;

        return [
            'exceeded' => $exceeded,
            'has_limit' => $hasLimit,
            'limit' => $limit,
            'outstanding' => $outstanding,
            'additional_due' => $additional,
            'projected_total' => $projected,
            'message' => $exceeded ? $this->formatExceededMessage('Customer', $limit, $outstanding, $additional, $projected) : null,
        ];
    }

    /**
     * @return array{
     *     exceeded: bool,
     *     has_limit: bool,
     *     limit: float,
     *     outstanding: float,
     *     additional_due: float,
     *     projected_total: float,
     *     message: ?string
     * }
     */
    public function assessSupplierCredit(Supplier $supplier, float $additionalDueAmount, ?int $excludeInvoiceId = null): array
    {
        $limit = (float) ($supplier->credit_limit ?? 0);
        $outstanding = $this->supplierOutstanding((int) $supplier->id, $excludeInvoiceId);
        $additional = max(0, $additionalDueAmount);
        $projected = $outstanding + $additional;
        $hasLimit = $limit > 0;
        $exceeded = $hasLimit && $projected > $limit + 0.0001;

        return [
            'exceeded' => $exceeded,
            'has_limit' => $hasLimit,
            'limit' => $limit,
            'outstanding' => $outstanding,
            'additional_due' => $additional,
            'projected_total' => $projected,
            'message' => $exceeded ? $this->formatExceededMessage('Supplier', $limit, $outstanding, $additional, $projected) : null,
        ];
    }

    public function customerWouldExceed(Customer $customer, float $additionalDueAmount, ?int $excludeOrderId = null): ?string
    {
        return $this->assessCustomerCredit($customer, $additionalDueAmount, $excludeOrderId)['message'];
    }

    public function supplierWouldExceed(Supplier $supplier, float $additionalDueAmount, ?int $excludeInvoiceId = null): ?string
    {
        return $this->assessSupplierCredit($supplier, $additionalDueAmount, $excludeInvoiceId)['message'];
    }

    private function formatExceededMessage(
        string $party,
        float $limit,
        float $outstanding,
        float $additional,
        float $projected
    ): string {
        return sprintf(
            '%s credit limit would be exceeded. Limit: %s. Outstanding amounts due: %s. This invoice due: %s. Combined total: %s. Use Place Invoice on Hold to save as draft only.',
            $party,
            number_format($limit, 2),
            number_format($outstanding, 2),
            number_format($additional, 2),
            number_format($projected, 2)
        );
    }
}
