<?php

namespace App\Http\Controllers\FacilityAdmin;

use App\Enums\StaffRole;
use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\User;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function __construct(
        private TenantService $tenantService,
    ) {}

    /**
     * Display a listing of staff members.
     */
    public function index(Request $request): View
    {
        $tenant = $this->tenantService->getCurrentTenant();
        $staff = Staff::where('tenant_id', $tenant->id)
            ->with('user')
            ->latest()
            ->paginate(15);

        return view('facility-admin.staff.index', compact('tenant', 'staff'));
    }

    /**
     * Show the form for creating a new staff member.
     */
    public function create(): View
    {
        $tenant = $this->tenantService->getCurrentTenant();
        $roles = StaffRole::cases();

        return view('facility-admin.staff.create', compact('tenant', 'roles'));
    }

    /**
     * Store a newly created staff member.
     */
    public function store(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'staff_role' => 'required|string|in:doctor,midwife,nurse,staff_admin,staff',
            'specialization' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:100',
        ]);

        // Create user — staff uses 'parent' role (not tenant_admin) to prevent admin panel access
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make(Str::random(12)),
            'role' => 'parent',
            'tenant_id' => $tenant->id,
        ]);

        // Create staff record
        Staff::create([
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
            'staff_role' => $validated['staff_role'],
            'specialization' => $validated['specialization'] ?? null,
            'license_number' => $validated['license_number'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('facility.staff.index')
            ->with('success', 'Staf berhasil ditambahkan.');
    }

    /**
     * Display the specified staff member.
     */
    public function show(Staff $staff): View
    {
        $tenant = $this->tenantService->getCurrentTenant();
        abort_unless($staff->tenant_id === $tenant->id, 403);
        $staff->load('user');

        return view('facility-admin.staff.show', compact('tenant', 'staff'));
    }

    /**
     * Show the form for editing the specified staff member.
     */
    public function edit(Staff $staff): View
    {
        $tenant = $this->tenantService->getCurrentTenant();
        abort_unless($staff->tenant_id === $tenant->id, 403);
        $staff->load('user');
        $roles = StaffRole::cases();

        return view('facility-admin.staff.edit', compact('tenant', 'staff', 'roles'));
    }

    /**
     * Update the specified staff member.
     */
    public function update(Request $request, Staff $staff)
    {
        $tenant = $this->tenantService->getCurrentTenant();
        abort_unless($staff->tenant_id === $tenant->id, 403);

        $validated = $request->validate([
            'staff_role' => 'required|string|in:doctor,midwife,nurse,staff_admin,staff',
            'specialization' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $staff->update($validated);

        return redirect()->route('facility.staff.show', $staff)
            ->with('success', 'Data staf berhasil diperbarui.');
    }

    /**
     * Remove the specified staff member.
     */
    public function destroy(Staff $staff)
    {
        $tenant = $this->tenantService->getCurrentTenant();
        abort_unless($staff->tenant_id === $tenant->id, 403);

        $staff->update(['is_active' => false]);

        return redirect()->route('facility.staff.index')
            ->with('success', 'Staf berhasil dinonaktifkan.');
    }
}
