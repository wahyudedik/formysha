<?php

namespace App\Http\Controllers\FacilityAdmin;

use App\Enums\ClinicalNoteType;
use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\ClinicalNote;
use App\Models\Staff;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClinicalNoteController extends Controller
{
    public function __construct(
        private TenantService $tenantService,
    ) {}

    /**
     * Display a listing of clinical notes.
     */
    public function index(Request $request): View
    {
        $tenant = $this->tenantService->getCurrentTenant();
        $clinicalNotes = ClinicalNote::where('tenant_id', $tenant->id)
            ->with(['child', 'staffUser'])
            ->latest()
            ->paginate(15);

        return view('facility-admin.clinical-notes.index', compact('tenant', 'clinicalNotes'));
    }

    /**
     * Show the form for creating a new clinical note.
     */
    public function create(): View
    {
        $tenant = $this->tenantService->getCurrentTenant();
        $children = Child::where('tenant_id', $tenant->id)->get();
        $staffMembers = Staff::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->with('user')
            ->get();
        $types = ClinicalNoteType::cases();

        return view('facility-admin.clinical-notes.create', compact('tenant', 'children', 'staffMembers', 'types'));
    }

    /**
     * Store a newly created clinical note.
     */
    public function store(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant();

        $validated = $request->validate([
            'child_id' => 'required|exists:children,id',
            'staff_user_id' => 'required|exists:users,id',
            'type' => 'required|string|in:consultation,examination,treatment,follow_up',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'vitals' => 'nullable|array',
            'diagnosis' => 'nullable|string',
            'medications' => 'nullable|array',
        ]);

        $clinicalNote = ClinicalNote::create([
            'tenant_id' => $tenant->id,
            'child_id' => $validated['child_id'],
            'staff_user_id' => $validated['staff_user_id'],
            'type' => $validated['type'],
            'title' => $validated['title'],
            'content' => $validated['content'],
            'vitals' => $validated['vitals'] ?? null,
            'diagnosis' => $validated['diagnosis'] ?? null,
            'medications' => $validated['medications'] ?? null,
        ]);

        return redirect()->route('facility.clinical-notes.show', $clinicalNote)
            ->with('success', 'Catatan klinis berhasil ditambahkan.');
    }

    /**
     * Display the specified clinical note.
     */
    public function show(ClinicalNote $clinicalNote): View
    {
        $tenant = $this->tenantService->getCurrentTenant();
        abort_unless($clinicalNote->tenant_id === $tenant->id, 403);
        $clinicalNote->load(['child', 'staffUser']);

        return view('facility-admin.clinical-notes.show', compact('tenant', 'clinicalNote'));
    }

    /**
     * Show the form for editing the specified clinical note.
     */
    public function edit(ClinicalNote $clinicalNote): View
    {
        $tenant = $this->tenantService->getCurrentTenant();
        abort_unless($clinicalNote->tenant_id === $tenant->id, 403);
        $clinicalNote->load(['child', 'staffUser']);
        $children = Child::where('tenant_id', $tenant->id)->get();
        $staffMembers = Staff::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->with('user')
            ->get();
        $types = ClinicalNoteType::cases();

        return view('facility-admin.clinical-notes.edit', compact('tenant', 'clinicalNote', 'children', 'staffMembers', 'types'));
    }

    /**
     * Update the specified clinical note.
     */
    public function update(Request $request, ClinicalNote $clinicalNote)
    {
        $tenant = $this->tenantService->getCurrentTenant();
        abort_unless($clinicalNote->tenant_id === $tenant->id, 403);

        $validated = $request->validate([
            'type' => 'required|string|in:consultation,examination,treatment,follow_up',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'vitals' => 'nullable|array',
            'diagnosis' => 'nullable|string',
            'medications' => 'nullable|array',
        ]);

        $clinicalNote->update($validated);

        return redirect()->route('facility.clinical-notes.show', $clinicalNote)
            ->with('success', 'Catatan klinis berhasil diperbarui.');
    }

    /**
     * Remove the specified clinical note.
     */
    public function destroy(ClinicalNote $clinicalNote)
    {
        $tenant = $this->tenantService->getCurrentTenant();
        abort_unless($clinicalNote->tenant_id === $tenant->id, 403);

        $clinicalNote->delete();

        return redirect()->route('facility.clinical-notes.index')
            ->with('success', 'Catatan klinis berhasil dihapus.');
    }
}
