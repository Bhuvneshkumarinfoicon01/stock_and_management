<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\GlAccount;
use App\Models\JournalLine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GeneralLedgerController extends Controller
{
    public function index(Request $request): View
    {
        $accounts = GlAccount::query()
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $lines = collect();
        $selectedAccount = null;
        $openingBalance = 0.0;
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());

        if ($request->filled('gl_account_id')) {
            $selectedAccount = GlAccount::find((int) $request->input('gl_account_id'));
            if ($selectedAccount) {
                $from = Carbon::parse($dateFrom)->startOfDay();
                $to = Carbon::parse($dateTo)->endOfDay();

                $openingBalance = $this->balanceBefore($selectedAccount, $from);

                $lines = JournalLine::query()
                    ->with(['journalEntry'])
                    ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
                    ->where('journal_lines.gl_account_id', $selectedAccount->id)
                    ->where('journal_entries.status', 'posted')
                    ->whereBetween('journal_entries.entry_date', [$from->toDateString(), $to->toDateString()])
                    ->orderBy('journal_entries.entry_date')
                    ->orderBy('journal_entries.id')
                    ->orderBy('journal_lines.id')
                    ->select('journal_lines.*')
                    ->get();

                $running = $openingBalance;
                foreach ($lines as $line) {
                    $d = (float) $line->debit;
                    $c = (float) $line->credit;
                    if ($selectedAccount->isDebitNormal()) {
                        $running += $d - $c;
                    } else {
                        $running += $c - $d;
                    }
                    $line->running_balance = round($running, 2);
                }
            }
        }

        return view('admin.accounting.general-ledger', compact(
            'accounts',
            'lines',
            'selectedAccount',
            'openingBalance',
            'dateFrom',
            'dateTo'
        ));
    }

    public function trialBalance(Request $request): View
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());
        $from = Carbon::parse($dateFrom)->startOfDay();
        $to = Carbon::parse($dateTo)->endOfDay();

        $fromStr = $from->toDateString();
        $toStr = $to->toDateString();

        $sub = DB::table('journal_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->where('journal_entries.status', 'posted')
            ->whereBetween('journal_entries.entry_date', [$fromStr, $toStr])
            ->groupBy('journal_lines.gl_account_id')
            ->select(
                'journal_lines.gl_account_id',
                DB::raw('SUM(journal_lines.debit) as period_debit'),
                DB::raw('SUM(journal_lines.credit) as period_credit')
            );

        $rows = GlAccount::query()
            ->where('is_active', true)
            ->leftJoinSub($sub, 'sums', 'gl_accounts.id', '=', 'sums.gl_account_id')
            ->orderBy('gl_accounts.code')
            ->select(
                'gl_accounts.id',
                'gl_accounts.code',
                'gl_accounts.name',
                'gl_accounts.type',
                DB::raw('COALESCE(sums.period_debit, 0) as period_debit'),
                DB::raw('COALESCE(sums.period_credit, 0) as period_credit')
            )
            ->get();

        $totalDebit = round((float) $rows->sum('period_debit'), 2);
        $totalCredit = round((float) $rows->sum('period_credit'), 2);
        $isBalanced = $totalDebit === $totalCredit;

        return view('admin.accounting.trial-balance', compact('rows', 'dateFrom', 'dateTo', 'totalDebit', 'totalCredit', 'isBalanced'));
    }

    private function balanceBefore(GlAccount $account, Carbon $from): float
    {
        $row = DB::table('journal_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->where('journal_lines.gl_account_id', $account->id)
            ->where('journal_entries.status', 'posted')
            ->where('journal_entries.entry_date', '<', $from->toDateString())
            ->selectRaw('COALESCE(SUM(journal_lines.debit), 0) as td, COALESCE(SUM(journal_lines.credit), 0) as tc')
            ->first();

        $td = (float) ($row->td ?? 0);
        $tc = (float) ($row->tc ?? 0);

        if ($account->isDebitNormal()) {
            return round($td - $tc, 2);
        }

        return round($tc - $td, 2);
    }



    // public function index(Request $request): View
    // {
    //     //return "hello";
    //     //dd('working going on');
    //     $accounts = GlAccount::query()->where('is_active', true)->orderBy('code')->get();
    //     //echo "<pre>"; print_r($accounts); die();

    //     $lines = collect();
    //     $selectedAccount = NULL;
    //     $openingBalance = 0.0;
    //     $dateFrom = $request->input('date_from', now()->startOfMonth()->ToDateString());
    //     $dateTo = $request->input('date_to', now()->toDateString());

    //     // if($request->filled('gl_account_id')){
    //     //     $selectedAccount = GlAccount::find((int) $request->input('gl_account_id'));
    //     if(1==1){
    //         $selectedAccount = GlAccount::find((int) 1);

    //         if($selectedAccount){
    //             $from = Carbon::parse($dateFrom)->startOfDay();
    //             $to = Carbon::parse($dateTo)->endOfDay();

    //             $openingBalance = $this->balanceBefore($selectedAccount, $from);
               
    //             $lines = JournalLine::query()->with(['journalEntry'])->join('journal_entries', 'journal_entries.id' , '=', 'journal_lines.journal_entry_id')
    //                      ->where('journal_lines.gl_account_id', $selectedAccount->id)->where('journal_entries.status', 'posted')
    //                      ->whereBetween('journal_entries.entry_date', [$from->toDateString(), $to->toDateString()])->orderBy('journal_entries.entry_date')
    //                      ->orderBy('journal_entries.id')->orderBy('journal_lines.id')->select('journal_lines.*')->get();

    //             $running = $openingBalance;
    //             foreach($lines as $line){
    //                 $d = (float) $line->debit;
    //                 $c = (float) $line->credit;
    //                 if($selectedAccount->isDebitNormal()){
    //                     $running += $d - $c;
    //                 }else{
    //                     $running += $c - $d;
    //                 }

    //                 $line->running_balance = round($running, 2);
    //             }

    //         }

    //     }

    //     echo "<pre>";
    //     print_r($openingBalance);
    //     print_r($selectedAccount->toArray());
    //     //print_r($dateTo);
    //     die();

    //     return view('admin.accounting.general-ledger', compact('accounts', 'lines', 'selectedAccount', 'openingBalance', 'dateFrom', 'dateTo'));

    // }


    // private function balanceBefore(GlAccount $account, Carbon $from): float
    // {
    //     $row = DB::table('journal_lines')->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')->where('journal_lines.gl_account_id', $account->id)
    //                ->where('journal_entries.status', 'posted')->where('journal_entries.entry_date', '<', $from->toDateString())
    //                ->selectRaw('COALESCE(SUM(journal_lines.debit), 0) as td, COALESCE(SUM(journal_lines.credit), 0) as tc')->first();

    //     $td = (float) ($row->td ?? 0);
    //     $tc = (float) ($row->tc ?? 0);

    //     if($account->isDebitNormal()){
    //         return round($td - $tc, 2);
    //     }
    //     return round ($tc - $td, 2);


    // }


    // public function trailBalance(Request $request): View
    // {

    //     $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
    //     $dateTo = $request->input('date_to', now()->toDateString());
    //     $from = Carbon::parse($dateFrom)->startOfDay();
    //     $to = Carbon::parse($dateTo)->endOfDay();

    //     $fromStr = $from->toDateString();
    //     $toStr = $to->toDateString();

    //     $sub = DB::table('journal lies')->join('jounal_entries', 'journal_entries.id', '=', 'journal_lines.jounal_entry_id')
    //               ->where('journal_entries.status', 'posted')->whereBetween('journal_entries.entry_date', [$fromStr, $toStr])
    //               ->groupBy('journal_lines.gl_account_id')->select('journal_lines.gl_account_id', DB::raw('SUM(journal_lines.debit) as perid_debit'),
    //                 DB::raw('SUM(journal_lines.credit) as period_credit')
    //               );
    //     $rows = GlAccount::query()->where('is_active', true)->leftJoinSub($sub, 'sums', 'gi_accounts.id', '=', 'sums.gl_account_id')
    //                         ->orderBy('gl_accounts.code')->select('gl_accounts.id', 'gl_accounts.code', 'gl_accounts.name', 'gl_accounts.type',
    //                             DB::raw('COALESCE(sums.period_debit, 0) as period_debit'),
    //                             DB::raw('COALESCE(sums.period_credit, 0)  as period_credit')
    //                         )->get();

    //     //$totalDebit = round((float) $rows->sum('period_debit'), 2);
    //     //$totalCredit = round((float) $rows->sum('period_credit'), 2);
    //     $totalDebit = round((float) $rows->sum('period_debit'), 2);
    //     $totalCredit = round((float) $rows->sub('period_credit'), 2);
    //     $isBalanced = $totalDebit === $totalCredit;

    //     return view('admin.accounting.trail-balance', compact('rows', 'dataFrom', 'dateTo', 'totalCredit', 'isBalanced'));


    // }





}
