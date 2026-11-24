<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProfitLossController extends Controller
{
    public function showProfitLossStatement()
    {
        abort_unless(hasSidebarLink('profit_and_loss'), 403);

        return view('admin.profit-loss-statement');
    }

    public function getMonthlySales(Request $request)
    {
        abort_unless(hasSidebarLink('profit_and_loss'), 403);

        $request->validate([
            'start_date' => 'required|string',
            'end_date' => 'required|string',
            'store_id' => 'nullable|integer|exists:stores,id',
        ]);

        $startDate = parse_date_input($request->start_date)?->startOfMonth();
        $endDate = parse_date_input($request->end_date)?->endOfMonth();
        $storeId = $request->filled('store_id') ? (int) $request->store_id : null;

        return response()->json($this->buildGlProfitLoss($startDate, $endDate, $storeId));
    }

    public function exportMonthlyProfitLoss(Request $request)
    {
        abort_unless(hasSidebarLink('profit_and_loss'), 403);

        $request->validate([
            'start_date' => 'required|string',
            'end_date' => 'required|string',
            'store_id' => 'nullable|integer|exists:stores,id',
        ]);

        $startDate = parse_date_input($request->start_date)?->startOfMonth();
        $endDate = parse_date_input($request->end_date)?->endOfMonth();
        $storeId = $request->filled('store_id') ? (int) $request->store_id : null;

        $data = $this->buildGlProfitLoss($startDate, $endDate, $storeId);

        $storeName = 'All Stores';
        if ($storeId) {
            $storeName = Store::query()->where('id', $storeId)->value('name') ?? 'Unknown Store';
        }

        $filename = 'profit_loss_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data, $storeName, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Profit & Loss (General Ledger)']);
            fputcsv($file, ['Store', $storeName]);
            fputcsv($file, ['From', $startDate->format('Y-m-d')]);
            fputcsv($file, ['To', $endDate->format('Y-m-d')]);
            fputcsv($file, []);

            $header = array_merge(['Category'], $data['months']);
            fputcsv($file, $header);

            $mapRow = function (string $label, array $series) use ($data) {
                $byMonth = collect($series)->keyBy('month');
                $row = [$label];
                foreach ($data['months'] as $month) {
                    $row[] = $byMonth->get($month)['amount'] ?? 0;
                }

                return $row;
            };

            fputcsv($file, $mapRow('Sales Revenue', $data['revenue']));
            fputcsv($file, $mapRow('Cost of Goods Sold', $data['cogs']));
            fputcsv($file, $mapRow('Gross Profit', $data['gross_profit']));
            fputcsv($file, $mapRow('Operating Expenses', $data['operating_expenses']));
            fputcsv($file, $mapRow('Net Income', $data['net_income']));

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildGlProfitLoss(Carbon $startDate, Carbon $endDate, ?int $storeId): array
    {
        $cogsCode = config('accounting.accounts.cogs', '5000');
        $raw = $this->fetchMonthlyGlActivity($startDate, $endDate, $storeId);

        $months = $this->monthLabelsBetween($startDate, $endDate);

        $revenueByMonth = [];
        $cogsByMonth = [];
        $opexByMonth = [];

        foreach ($months as $month) {
            $revenueByMonth[$month] = 0.0;
            $cogsByMonth[$month] = 0.0;
            $opexByMonth[$month] = 0.0;
        }

        foreach ($raw as $row) {
            $month = $row->month;
            if (!isset($revenueByMonth[$month])) {
                continue;
            }

            $td = (float) $row->total_debit;
            $tc = (float) $row->total_credit;

            if ($row->type === 'revenue') {
                $revenueByMonth[$month] += round($tc - $td, 2);
            } elseif ($row->type === 'expense') {
                $amount = round($td - $tc, 2);
                if ($row->code === $cogsCode) {
                    $cogsByMonth[$month] += $amount;
                } else {
                    $opexByMonth[$month] += $amount;
                }
            }
        }

        $revenue = [];
        $cogs = [];
        $grossProfit = [];
        $operatingExpenses = [];
        $netIncome = [];

        foreach ($months as $month) {
            $rev = round($revenueByMonth[$month], 2);
            $cogsAmt = round($cogsByMonth[$month], 2);
            $opex = round($opexByMonth[$month], 2);
            $gross = round($rev - $cogsAmt, 2);
            $net = round($gross - $opex, 2);

            $revenue[] = ['month' => $month, 'amount' => $rev];
            $cogs[] = ['month' => $month, 'amount' => $cogsAmt];
            $grossProfit[] = ['month' => $month, 'amount' => $gross];
            $operatingExpenses[] = ['month' => $month, 'amount' => $opex];
            $netIncome[] = ['month' => $month, 'amount' => $net];
        }

        return [
            'data_source' => 'gl',
            'has_gl_data' => DB::table('journal_entries')->where('status', 'posted')->exists(),
            'months' => $months,
            'revenue' => $revenue,
            'cogs' => $cogs,
            'gross_profit' => $grossProfit,
            'operating_expenses' => $operatingExpenses,
            'net_income' => $netIncome,
        ];
    }

  /**
     * @return Collection<int, object{month: string, code: string, type: string, total_debit: float, total_credit: float}>
     */
    private function fetchMonthlyGlActivity(Carbon $startDate, Carbon $endDate, ?int $storeId): Collection
    {
        $query = DB::table('journal_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->join('gl_accounts', 'gl_accounts.id', '=', 'journal_lines.gl_account_id')
            ->where('journal_entries.status', 'posted')
            ->whereIn('gl_accounts.type', ['revenue', 'expense'])
            ->whereBetween('journal_entries.entry_date', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ]);

        if ($storeId !== null) {
            $query->where('journal_entries.store_id', $storeId);
        }

        return $query
            ->groupBy('month', 'gl_accounts.code', 'gl_accounts.type')
            ->orderBy(DB::raw('MIN(journal_entries.entry_date)'))
            ->selectRaw("DATE_FORMAT(journal_entries.entry_date, '%b %Y') as month")
            ->selectRaw('gl_accounts.code')
            ->selectRaw('gl_accounts.type')
            ->selectRaw('COALESCE(SUM(journal_lines.debit), 0) as total_debit')
            ->selectRaw('COALESCE(SUM(journal_lines.credit), 0) as total_credit')
            ->get();
    }

    /**
     * @return array<int, string>
     */
    private function monthLabelsBetween(Carbon $startDate, Carbon $endDate): array
    {
        $labels = [];
        $cursor = $startDate->copy()->startOfMonth();
        $end = $endDate->copy()->endOfMonth();

        while ($cursor <= $end) {
            $labels[] = $cursor->format('M Y');
            $cursor->addMonth();
        }

        return $labels;
    }

    /*
    |--------------------------------------------------------------------------
    | Legacy operational P&L (orders, stock adjustments, transfers) — replaced
    | by GL-based buildGlProfitLoss(). Kept for reference only.
    |--------------------------------------------------------------------------
    |
    | public function getMonthlySalesLegacy(Request $request)
    | {
    |     $startDate = Carbon::parse($request->start_date)->startOfMonth();
    |     ...
    |     return response()->json([
    |         'sales' => $salesData,
    |         'adjustments' => $adjustmentData,
    |         'incoming' => $incomingTransfers,
    |         'outgoing' => $outgoingTransfers,
    |         'stocks' => $stocks,
    |         'net_profit' => $netProfit,
    |     ]);
    | }
    */
}
