<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\OrganizationInfo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OrganizationInfoController extends Controller
{
    protected function ensureSuperAdmin(): void
    {
        $user = auth()->user();
        if (!$user || (int) $user->role_id !== 1) {
            abort(403, 'Only super administrators can manage organization info.');
        }
    }

    public function index(): View
    {
        $this->ensureSuperAdmin();
        $organizations = OrganizationInfo::orderByDesc('id')->get();
        $hasOrganization = OrganizationInfo::exists();

        return view('SuperAdmin.organization-info.index', compact('organizations', 'hasOrganization'));
    }

    public function create(): View|RedirectResponse
    {
        $this->ensureSuperAdmin();
        $organization = OrganizationInfo::first();
        if ($organization) {
            return redirect()
                ->route('admin.organization-info.edit', $organization)
                ->with('error', 'Organization already exists. Only one record is allowed.');
        }

        return view('SuperAdmin.organization-info.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureSuperAdmin();

        if (OrganizationInfo::exists()) {
            return redirect()
                ->route('admin.organization-info.edit', OrganizationInfo::first())
                ->with('error', 'Organization already exists. Only one record is allowed.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:2000',
            'website' => 'nullable|string|max:500',
            'invoice_footer_text' => 'required|string|max:2000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('organization_logos', 'public');
        }

        OrganizationInfo::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'website' => $validated['website'] ?? null,
            'logo_path' => $logoPath,
            'invoice_footer_text' => $validated['invoice_footer_text'],
        ]);

        return redirect()
            ->route('admin.organization-info.index')
            ->with('success', 'Organization added successfully.');
    }

    public function edit(OrganizationInfo $organization_info): View
    {
        $this->ensureSuperAdmin();

        return view('SuperAdmin.organization-info.edit', ['organization' => $organization_info]);
    }

    public function update(Request $request, OrganizationInfo $organization_info): RedirectResponse
    {
        $this->ensureSuperAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:2000',
            'website' => 'nullable|string|max:500',
            'invoice_footer_text' => 'required|string|max:2000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'remove_logo' => 'nullable|in:0,1',
        ]);

        if (!empty($validated['remove_logo']) && $organization_info->logo_path) {
            Storage::disk('public')->delete($organization_info->logo_path);
            $organization_info->logo_path = null;
        }

        if ($request->hasFile('logo')) {
            if ($organization_info->logo_path) {
                Storage::disk('public')->delete($organization_info->logo_path);
            }
            $organization_info->logo_path = $request->file('logo')->store('organization_logos', 'public');
        }

        $organization_info->fill([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'website' => $validated['website'] ?? null,
            'invoice_footer_text' => $validated['invoice_footer_text'],
        ]);
        $organization_info->save();

        return redirect()
            ->route('admin.organization-info.index')
            ->with('success', 'Organization updated successfully.');
    }

    public function destroy(OrganizationInfo $organization_info): RedirectResponse
    {
        $this->ensureSuperAdmin();

        if ($organization_info->logo_path) {
            Storage::disk('public')->delete($organization_info->logo_path);
        }
        $organization_info->delete();

        return redirect()
            ->route('admin.organization-info.index')
            ->with('success', 'Organization removed successfully.');
    }
}
