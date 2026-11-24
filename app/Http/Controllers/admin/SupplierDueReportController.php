<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SupplierDueReportController extends Controller
{
    public function index(): View
    {
        $rows = DB::table('suppliers')
            ->joinSub(
                DB::table('supplier_purchase_invoices')
                    ->select('supplier_id', DB::raw('SUM(due_amount) as balance_due'))
                    ->where('due_amount', '>', 0)
                    ->groupBy('supplier_id'),
                'open',
                'open.supplier_id',
                '=',
                'suppliers.id'
            )
            ->where('suppliers.status', 1)
            ->orderBy('suppliers.company_name')
            ->select('suppliers.id', 'suppliers.code', 'suppliers.company_name', 'open.balance_due')
            ->get();

        return view('admin.suppliers.due-report', compact('rows'));
    }
}
