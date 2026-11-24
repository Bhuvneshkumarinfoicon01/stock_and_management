<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentTerm;
use App\Services\DatatablePaginationService;
use Illuminate\Http\Request;

class PaymentTermController extends Controller
{
    public function index(Request $request)
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

        $paymentTerms = PaymentTerm::orderBy('sort_order')
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.payment-terms', [
            'paymentTerms' => $paymentTerms,
            'datatableConfig' => $service->config('payment-terms'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'due_days' => 'nullable|integer|min:0',
            'due_months' => 'nullable|integer|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        PaymentTerm::create([
            'label' => $validated['label'],
            'due_days' => $validated['due_days'] ?? null,
            'due_months' => $validated['due_months'] ?? 0,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => (int) ($validated['is_active'] ?? 0),
        ]);

        return redirect()->route('admin.payment-terms.index')->with('success', 'Payment term created successfully.');
    }

    public function update(Request $request, PaymentTerm $paymentTerm)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'due_days' => 'nullable|integer|min:0',
            'due_months' => 'nullable|integer|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $paymentTerm->update([
            'label' => $validated['label'],
            'due_days' => $validated['due_days'] ?? null,
            'due_months' => $validated['due_months'] ?? 0,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => (int) ($validated['is_active'] ?? 0),
        ]);

        return redirect()->route('admin.payment-terms.index')->with('success', 'Payment term updated successfully.');
    }

    public function destroy(PaymentTerm $paymentTerm)
    {
        $paymentTerm->delete();

        return redirect()->route('admin.payment-terms.index')->with('success', 'Payment term deleted successfully.');
    }

    public function toggleStatus(PaymentTerm $paymentTerm)
    {
        $paymentTerm->is_active = $paymentTerm->is_active ? 0 : 1;
        $paymentTerm->save();

        return redirect()->route('admin.payment-terms.index')->with('success', 'Payment term status updated successfully.');
    }
}

