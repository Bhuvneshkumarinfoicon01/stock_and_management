<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\GlAccount;
use Illuminate\Contracts\View\View;

class AccountModuleController extends Controller
{
    /**
     * Account module hub: entry point for receivables, invoicing, and ledger tools.
     */
    public function index(): View
    {
        $glAccounts = GlAccount::query()
            ->orderBy('code')
            ->get();

        return view('admin.account-module', compact('glAccounts'));
    }
}
