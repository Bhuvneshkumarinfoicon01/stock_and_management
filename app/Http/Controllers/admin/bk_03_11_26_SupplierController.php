<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Services\DatatablePaginationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        $service = app(DatatablePaginationService::class);
        $options = $service->lengthOptions();
        $raw = trim((string) $request->query('per_page', ''));
        if ($raw === 'all') {
            $perPage = 10000;
        } elseif ($raw !== '' && ctype_digit($raw) && (int) $raw > 0 && (int) $raw <= 10000) {
            $perPage = (int) $raw;
        } else {
            $default = $service->defaultLength();
            $perPage = in_array($default, $options, true) ? $default : ($options[0] ?? 25);
        }
        $suppliers = Supplier::query()->orderBy('company_name')->paginate($perPage)->withQueryString();

        return view('admin.suppliers.index', [
            'suppliers' => $suppliers,
            'datatableConfig' => $service->config('suppliers'),
        ]);
    }

    public function create(): View
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:64',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:120',
            'country' => 'nullable|string|max:120',
            'tax_number' => 'nullable|string|max:120',
            'notes' => 'nullable|string|max:2000',
            'code' => 'nullable|string|max:32|unique:suppliers,code',
            'credit_limit' => 'nullable|numeric|min:0',
        ]);

        $validated['code'] = isset($validated['code']) && trim((string) $validated['code']) !== '' ? trim((string) $validated['code']) : null;
        $validated['credit_limit'] = (float) ($validated['credit_limit'] ?? 0);

        $supplier = Supplier::create($validated + ['status' => 1]);
        if (empty($supplier->code)) {
            $supplier->code = 'SUP-' . str_pad((string) $supplier->id, 5, '0', STR_PAD_LEFT);
            $supplier->save();
        }

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier created.');
    }

    public function edit(Supplier $supplier): View
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:64',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:120',
            'country' => 'nullable|string|max:120',
            'tax_number' => 'nullable|string|max:120',
            'notes' => 'nullable|string|max:2000',
            'code' => 'nullable|string|max:32|unique:suppliers,code,' . $supplier->id,
            'status' => 'required|in:0,1',
            'credit_limit' => 'nullable|numeric|min:0',
        ]);

        $validated['code'] = isset($validated['code']) && trim((string) $validated['code']) !== '' ? trim((string) $validated['code']) : null;
        $validated['credit_limit'] = (float) ($validated['credit_limit'] ?? 0);

        $supplier->update($validated);

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier updated.');
    }
}
