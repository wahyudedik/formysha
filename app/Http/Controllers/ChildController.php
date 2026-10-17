<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChildRequest;
use App\Http\Requests\UpdateChildRequest;
use App\Models\Child;
use App\Services\AccountDeletionService;
use App\Services\TenantService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ChildController extends Controller
{
    /**
     * Display a listing of the user's children.
     */
    public function index(Request $request): View
    {
        $children = $request->user()
            ->children()
            ->withCount('familyMembers')
            ->orderBy('date_of_birth', 'asc')
            ->get();

        return view('children.index', [
            'children' => $children,
        ]);
    }

    /**
     * Show the form for creating a new child.
     */
    public function create(): View
    {
        return view('children.create');
    }

    /**
     * Store a newly created child in storage.
     */
    public function store(StoreChildRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('children', 'private');
        }

        // Set tenant_id from current tenant for multi-tenant mode
        $tenantService = app(TenantService::class);
        $tenant = $tenantService->getCurrentTenant();
        if ($tenant) {
            $data['tenant_id'] = $tenant->id;
        }

        $child = $request->user()->children()->create($data);

        return redirect()->route('children.show', $child)
            ->with('status', __('status.children_created'));
    }

    /**
     * Display the specified child.
     */
    public function show(Request $request, Child $child): View
    {
        $child->load('familyMembers');

        return view('children.show', [
            'child' => $child,
        ]);
    }

    /**
     * Show the form for editing the specified child.
     */
    public function edit(Request $request, Child $child): View
    {
        return view('children.edit', [
            'child' => $child,
        ]);
    }

    /**
     * Update the specified child in storage.
     */
    public function update(UpdateChildRequest $request, Child $child): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            // Delete old photo if exists (private disk + legacy public copy)
            if ($child->photo) {
                Storage::disk('private')->delete($child->photo);
                Storage::disk('public')->delete($child->photo);
            }

            $data['photo'] = $request->file('photo')->store('children', 'private');
        }

        $child->update($data);

        return redirect()->route('children.show', $child)
            ->with('status', __('status.children_updated'));
    }

    /**
     * Remove the specified child from storage.
     *
     * Aksi destruktif memerlukan verifikasi ulang kata sandi (konsisten dengan
     * ErasureController). Pengguna OAuth tanpa kata sandi (users.password null)
     * melewati verifikasi agar tidak terkunci.
     */
    public function destroy(Request $request, Child $child, AccountDeletionService $deletionService): RedirectResponse
    {
        $user = $request->user();

        if ($user->password !== null) {
            $password = $request->input('current_password');

            if (! is_string($password) || $password === '' || ! Hash::check($password, $user->password)) {
                return back()
                    ->withErrors(['current_password' => __('children.password_incorrect')])
                    ->with('error', __('children.password_incorrect'));
            }
        }

        $deletionService->deleteChildData($child);

        return redirect()->route('children.index')
            ->with('status', __('status.children_deleted'));
    }
}
