<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Ledger;
use App\Models\LedgerEntry;
use App\Models\Store;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    public function index()
    {
        $ledgers = Ledger::with(['store', 'entries'])->get();

        return view('admin.Ledger', compact('ledgers'));
    }

    public function storeLedger(Request $request)
    {
        $request->validate([
            'store_id' => 'required|integer',
            // 'ledger_date' => 'required|date_format:d-m-Y',
            'ledger_date' => 'required|app_date',
            'date' => 'required|array',
            'date.*' => 'required|date',
            'description' => 'required|array',
            'description.*' => 'required|string',
            'debit' => 'nullable|array',
            'debit.*' => 'nullable|numeric',
            'credit' => 'nullable|array',
            'credit.*' => 'nullable|numeric',
        ]);

        $ledger = Ledger::create([
            'store_id' => $request->store_id,
            'ledger_date' => parse_date_input_to_db($request->ledger_date),
        ]);
        foreach ($request->date as $index => $entryDate) {
            LedgerEntry::create([
                'ledger_id' => $ledger->id,
                'entry_date' => $entryDate,
                'description' => $request->description[$index],
                'debit' => $request->debit[$index] ?? null,
                'credit' => $request->credit[$index] ?? null,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Ledger added successfully!']);
    }

    public function viewLedgerEntries($id)
    {
        $ledgerEntries = LedgerEntry::where('ledger_id', $id)->get();
        $ledger = Ledger::with('store')->findOrFail($id);
        return view('admin.view-ledger', compact('ledgerEntries', 'ledger'));
    }

    public function getLedgerEntries($id)
    {
        $ledger = Ledger::with('entries', 'store')->findOrFail($id);

        return response()->json([
            'store_name' => $ledger->store->name,
            'ledger_date' => $ledger->ledger_date,
            'entries' => $ledger->entries,
        ]);
    }

    public function updateLedger(Request $request, $id)
    {
        $request->validate([
            // 'ledger_date' => 'required|date_format:d-m-Y',
            'ledger_date' => 'required|app_date',
            'date' => 'required|array',
            'date.*' => 'required|date',
            'description' => 'required|array',
            'description.*' => 'required|string',
            'debit' => 'nullable|array',
            'debit.*' => 'nullable|numeric',
            'credit' => 'nullable|array',
            'credit.*' => 'nullable|numeric',
        ]);
        $ledger = Ledger::findOrFail($id);
        $ledger->ledger_date = parse_date_input_to_db($request->ledger_date);
        $ledger->save();
        $ledger->entries()->delete();

        foreach ($request->date as $index => $entryDate) {
            LedgerEntry::create([
                'ledger_id' => $ledger->id,
                'entry_date' => $entryDate,
                'description' => $request->description[$index],
                'debit' => isset($request->debit[$index]) ? $request->debit[$index] : null,
                'credit' => isset($request->credit[$index]) ? $request->credit[$index] : null,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Ledger updated successfully.']);
    }

    public function destroy($id)
    {
        $ledger = Ledger::findOrFail($id);
        $ledger->delete();

        return response()->json(['message' => 'Ledger deleted successfully']);
    }
}
