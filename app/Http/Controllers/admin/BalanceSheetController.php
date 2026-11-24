<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\GlAccount;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BalanceSheetController extends Controller
{
    public function showBalanceSheet()
    {
        abort_unless(hasSidebarLink('balance_sheet'), 403);

        return view('admin.balance-sheet');
    }

    public function getBalanceSheetData(Request $request)
    {
        abort_unless(hasSidebarLink('balance_sheet'), 403);

        $request->validate([
            'start_date' => 'required|string',
            'end_date' => 'required|string',
            'store_id' => 'nullable|integer|exists:stores,id',
        ]);

        $asOf = parse_date_input($request->end_date)?->endOfDay();
        $storeId = $request->filled('store_id') ? (int) $request->store_id : null;

        $payload = $this->buildGlBalanceSheet($asOf, $storeId);
        $payload['start_date'] = $request->start_date;
        $payload['end_date'] = $request->end_date;

        return response()->json($payload);
    }

    public function exportBalanceSheet(Request $request)
    {
        abort_unless(hasSidebarLink('balance_sheet'), 403);

        $request->validate([
            'start_date' => 'required|string',
            'end_date' => 'required|string',
            'store_id' => 'nullable|integer|exists:stores,id',
        ]);

        $asOf = parse_date_input($request->end_date)?->endOfDay();
        $storeId = $request->filled('store_id') ? (int) $request->store_id : null;
        $data = $this->buildGlBalanceSheet($asOf, $storeId);

        $storeName = 'All Stores';
        if ($storeId) {
            $storeName = Store::query()->where('id', $storeId)->value('name') ?? 'Unknown Store';
        }

        $filename = 'balance_sheet_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data, $storeName, $request, $asOf) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Balance Sheet (General Ledger)']);
            fputcsv($file, ['Store', $storeName]);
            fputcsv($file, ['As of date', $asOf->format('Y-m-d')]);
            fputcsv($file, ['Filter from', $request->start_date]);
            fputcsv($file, ['Filter to', $request->end_date]);
            fputcsv($file, []);

            fputcsv($file, ['Section', 'Code', 'Account', 'Amount']);
            foreach ($data['assets'] as $row) {
                fputcsv($file, ['Asset', $row['code'], $row['name'], $row['balance']]);
            }
            fputcsv($file, ['', '', 'Total Assets', $data['total_assets']]);
            fputcsv($file, []);

            foreach ($data['liabilities'] as $row) {
                fputcsv($file, ['Liability', $row['code'], $row['name'], $row['balance']]);
            }
            fputcsv($file, ['', '', 'Total Liabilities', $data['total_liabilities']]);
            fputcsv($file, []);

            foreach ($data['equity'] as $row) {
                fputcsv($file, ['Equity', $row['code'], $row['name'], $row['balance']]);
            }
            fputcsv($file, ['', '', 'Net income (unclosed)', $data['net_income']]);
            fputcsv($file, ['', '', 'Total Equity', $data['total_equity']]);
            fputcsv($file, ['', '', 'Total Liabilities + Equity', $data['total_liabilities_and_equity']]);
            fputcsv($file, ['', '', 'Balanced', $data['is_balanced'] ? 'Yes' : 'No']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Balance sheet as of a date from posted journal entries (double-entry GL).
     *
     * @return array<string, mixed>
     */
    private function buildGlBalanceSheet(Carbon $asOf, ?int $storeId): array
    {
        $asOfStr = $asOf->toDateString();
        $accounts = GlAccount::query()->where('is_active', true)->orderBy('code')->get();

        $assets = [];
        $liabilities = [];
        $equity = [];
        $netIncome = 0.0;

        foreach ($accounts as $account) {
            $balance = $this->glBalanceAsOf($account, $asOfStr, $storeId);

            if (abs($balance) < 0.005) {
                continue;
            }

            $row = [
                'code' => $account->code,
                'name' => $account->name,
                'balance' => round($balance, 2),
            ];

            match ($account->type) {
                'asset' => $assets[] = $row,
                'liability' => $liabilities[] = $row,
                'equity' => $equity[] = $row,
                'revenue' => $netIncome += $balance,
                'expense' => $netIncome -= $balance,
                default => null,
            };
        }

        $totalAssets = round(array_sum(array_column($assets, 'balance')), 2);
        $totalLiabilities = round(array_sum(array_column($liabilities, 'balance')), 2);
        $equityAccountsTotal = round(array_sum(array_column($equity, 'balance')), 2);
        $netIncome = round($netIncome, 2);
        $totalEquity = round($equityAccountsTotal + $netIncome, 2);
        $totalLiabilitiesAndEquity = round($totalLiabilities + $totalEquity, 2);

        $hasGlData = DB::table('journal_entries')->where('status', 'posted')->exists();

        return [
            'data_source' => 'gl',
            'as_of_date' => $asOfStr,
            'has_gl_data' => $hasGlData,
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
            'net_income' => $netIncome,
            'total_assets' => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'total_equity' => $totalEquity,
            'total_liabilities_and_equity' => $totalLiabilitiesAndEquity,
            'is_balanced' => abs($totalAssets - $totalLiabilitiesAndEquity) < 0.02,
        ];
    }

    private function glBalanceAsOf(GlAccount $account, string $asOfDate, ?int $storeId): float
    {
        $query = DB::table('journal_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->where('journal_lines.gl_account_id', $account->id)
            ->where('journal_entries.status', 'posted')
            ->where('journal_entries.entry_date', '<=', $asOfDate);

        if ($storeId !== null) {
            $query->where('journal_entries.store_id', $storeId);
        }

        $row = $query->selectRaw('COALESCE(SUM(journal_lines.debit), 0) as td, COALESCE(SUM(journal_lines.credit), 0) as tc')
            ->first();

        $td = (float) ($row->td ?? 0);
        $tc = (float) ($row->tc ?? 0);

        if ($account->isDebitNormal()) {
            return round($td - $tc, 2);
        }

        return round($tc - $td, 2);
    }

    /*
    |--------------------------------------------------------------------------
    | Legacy operational balance sheet (orders / stocks) — incorrect for formal BS.
    | Kept for reference; replaced by GL-based buildGlBalanceSheet() above.
    |--------------------------------------------------------------------------
    |
    | public function getBalanceSheetDataLegacy(Request $request)
    | {
    |     $storeId = $request->store_id;
    |     $startDate = Carbon::createFromFormat('d-m-Y', $request->start_date)->startOfDay();
    |     $endDate = Carbon::createFromFormat('d-m-Y', $request->end_date)->endOfDay();
    |
    |     $cashQuery = DB::table('orders')
    |         ->where('payment_method', 'cash')
    |         ->whereBetween('created_at', [$startDate, $endDate]);
    |     ...
    |     return response()->json([
    |         'cash' => $cash,
    |         'digital' => $digital,
    |         'stock' => $stock,
    |         'total_sales' => $totalSales,
    |     ]);
    | }
    */
}
