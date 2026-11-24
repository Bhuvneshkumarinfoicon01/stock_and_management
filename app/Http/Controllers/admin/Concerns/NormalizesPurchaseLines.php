<?php

namespace App\Http\Controllers\admin\Concerns;

use App\Models\Stock;
use Illuminate\Http\Request;

trait NormalizesPurchaseLines
{
    /**
     * @param  array<int, array<string, mixed>>  $lines
     * @return array<int, array{product_id: ?string, product_name: string, qty: int, unit_price: float, line_total: float}>
     */
    protected function normalizePurchaseLines(array $lines): array
    {
        $out = [];
        foreach ($lines as $line) {
            $name = trim((string) ($line['product_name'] ?? ''));
            if ($name === '') {
                continue;
            }
            $qty = max(1, (int) ($line['qty'] ?? 1));
            $unit = round((float) ($line['unit_price'] ?? 0), 2);
            $lineTotal = round($qty * $unit, 2);
            $out[] = [
                'product_id' => isset($line['product_id']) && $line['product_id'] !== '' ? (string) $line['product_id'] : null,
                'product_name' => $name,
                'qty' => $qty,
                'unit_price' => $unit,
                'line_total' => $lineTotal,
            ];
        }

        return $out;
    }

    /**
     * @param  array<int, array{line_total: float}>  $normalized
     * @return array{0: float, 1: float, 2: float} subtotal, tax_amount, total
     */
    protected function totalsFromLines(array $normalized, float $taxPercent): array
    {
        $subtotal = round(array_sum(array_column($normalized, 'line_total')), 2);
        $taxAmount = round($subtotal * ($taxPercent / 100), 2);

        return [$subtotal, $taxAmount, round($subtotal + $taxAmount, 2)];
    }

    /** Per-line tax totals (purchase/sale invoice style). */
    protected function totalsFromPerLineTax(array $lines): array
    {
        $subtotal = 0.0;
        $taxAmount = 0.0;

        foreach ($lines as $line) {
            $lineSubtotal = round((float) ($line['qty'] ?? 0) * (float) ($line['unit_price'] ?? 0), 2);
            $subtotal += $lineSubtotal;
            $taxAmount += (float) ($line['tax_amount'] ?? 0);
        }

        $subtotal = round($subtotal, 2);
        $taxAmount = round($taxAmount, 2);

        return [$subtotal, $taxAmount, round($subtotal + $taxAmount, 2)];
    }

    protected function parseLineTaxableFlag(mixed $value): bool
    {
        if (is_array($value)) {
            $value = end($value);
        }

        return in_array((string) $value, ['1', 'true', 'on'], true) || $value === 1 || $value === true;
    }

    /**
     * Build line rows from the same parallel arrays used on the invoice screen (product_id[], product_name[], qty[], price[]).
     *
     * @return array<int, array{product_id: ?string, product_name: string, qty: int, unit_price: float, line_total: float}>
     */
    protected function linesFromInvoiceStyleArrays(Request $request): array
    {
        $names = $request->input('product_name', []);
        $qtys = $request->input('qty', []);
        $prices = $request->input('price', []);
        $ids = $request->input('product_id', []);
        $usePerLineTax = $request->has('tax_exempt')
            || $request->has('line_is_taxable')
            || $request->has('line_tax_rate');
        $taxExempt = $request->boolean('tax_exempt');
        $lineIsTaxable = $request->input('line_is_taxable', []);
        $lineTaxRates = $request->input('line_tax_rate', []);
        $storeId = (int) $request->input('store_id', 0);

        $out = [];
        foreach ($names as $i => $name) {
            $name = trim((string) $name);
            if ($name === '') {
                continue;
            }
            $qty = max(1, (int) ($qtys[$i] ?? 1));
            $unit = round((float) ($prices[$i] ?? 0), 2);
            $lineSubtotal = round($qty * $unit, 2);

            $line = [
                'product_id' => isset($ids[$i]) && $ids[$i] !== '' && $ids[$i] !== null ? (string) $ids[$i] : null,
                'product_name' => $name,
                'qty' => $qty,
                'unit_price' => $unit,
                'line_total' => $lineSubtotal,
            ];

            if ($usePerLineTax) {
                $isTaxable = false;
                $rate = 0.0;
                $stock = null;
                if (! $taxExempt) {
                    if ($storeId > 0 && ! empty($ids[$i])) {
                        $stock = Stock::query()
                            ->where('store_id', $storeId)
                            ->where('product_id', (string) $ids[$i])
                            ->first();
                    }
                    if (array_key_exists($i, $lineIsTaxable)) {
                        $isTaxable = $this->parseLineTaxableFlag($lineIsTaxable[$i]);
                    } else {
                        $isTaxable = (bool) ($stock?->is_taxable ?? true);
                    }
                    $rate = $isTaxable
                        ? (float) ($lineTaxRates[$i] ?? ($stock?->tax_rate ?? 0))
                        : 0.0;
                }

                $line['is_taxable'] = $isTaxable;
                $line['tax_rate'] = $rate;
                $line['tax_amount'] = $isTaxable ? round($lineSubtotal * $rate / 100, 2) : 0.0;
            }

            $out[] = $line;
        }

        return $out;
    }

    /**
     * @return array<int, array{product_id: ?string, product_name: string, qty: int, unit_price: float, line_total: float}>
     */
    protected function resolvePurchaseLines(Request $request, ?array $linesInput = null): array
    {
        if ($request->has('product_name') && is_array($request->input('product_name'))) {
            return $this->linesFromInvoiceStyleArrays($request);
        }

        return $this->normalizePurchaseLines($linesInput ?? $request->input('lines', []));
    }

    /**
     * @param  array<int, array{product_id: ?string, product_name: string, qty: int, unit_price: float, line_total: float}>  $lines
     * @return array<int, array{product_id: ?string, product_name: string, qty_received: int, unit_cost: float, line_total: float}>
     */
    protected function mapLinesForGrn(array $lines): array
    {
        return array_map(fn (array $line) => [
            'product_id' => $line['product_id'],
            'product_name' => $line['product_name'],
            'qty_received' => $line['qty'],
            'unit_cost' => $line['unit_price'],
            'line_total' => $line['line_total'],
            'is_taxable' => (bool) ($line['is_taxable'] ?? false),
            'tax_rate' => (float) ($line['tax_rate'] ?? 0),
            'tax_amount' => (float) ($line['tax_amount'] ?? 0),
        ], $lines);
    }

    protected function invoiceStyleLineValidationRules(): array
    {
        return [
            'lines' => 'sometimes|array',
            'product_name' => 'sometimes|array',
            'product_name.*' => 'nullable|string|max:500',
            'product_id' => 'sometimes|array',
            'product_id.*' => 'nullable|string|max:64',
            'qty' => 'sometimes|array',
            'qty.*' => 'nullable|integer|min:1',
            'price' => 'sometimes|array',
            'price.*' => 'nullable|numeric|min:0',
            'tax_exempt' => 'sometimes|boolean',
            'line_is_taxable' => 'sometimes|array',
            'line_is_taxable.*' => 'nullable',
            'line_tax_rate' => 'sometimes|array',
            'line_tax_rate.*' => 'nullable|numeric|min:0|max:100',
        ];
    }
}
