<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFamilyMemberRequest;
use App\Http\Requests\UpdateFamilyMemberRequest;
use App\Models\Child;
use App\Models\FamilyMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FamilyMemberController extends Controller
{
    /**
     * Display a listing of family members for a child.
     */
    public function index(Request $request, Child $child): View
    {
        $familyMembers = $child->familyMembers()
            ->orderBy('is_primary', 'desc')
            ->orderBy('name')
            ->get();

        return view('family.index', [
            'child' => $child,
            'familyMembers' => $familyMembers,
        ]);
    }

    /**
     * Show the form for creating a new family member.
     */
    public function create(Request $request, Child $child): View
    {
        return view('family.create', [
            'child' => $child,
        ]);
    }

    /**
     * Store a newly created family member in storage.
     */
    public function store(StoreFamilyMemberRequest $request, Child $child): RedirectResponse
    {
        $child->familyMembers()->create($request->validated());

        return redirect()->route('family.index', $child)
            ->with('status', 'Anggota keluarga berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified family member.
     */
    public function edit(Request $request, Child $child, FamilyMember $familyMember): View
    {
        abort_unless($familyMember->child_id === $child->id, 403);

        return view('family.edit', [
            'child' => $child,
            'familyMember' => $familyMember,
        ]);
    }

    /**
     * Update the specified family member in storage.
     */
    public function update(UpdateFamilyMemberRequest $request, Child $child, FamilyMember $familyMember): RedirectResponse
    {
        abort_unless($familyMember->child_id === $child->id, 403);

        $familyMember->update($request->validated());

        return redirect()->route('family.index', $child)
            ->with('status', 'Anggota keluarga berhasil diperbarui!');
    }

    /**
     * Remove the specified family member from storage.
     */
    public function destroy(Request $request, Child $child, FamilyMember $familyMember): RedirectResponse
    {
        abort_unless($familyMember->child_id === $child->id, 403);

        $familyMember->delete();

        return redirect()->route('family.index', $child)
            ->with('status', 'Anggota keluarga berhasil dihapus.');
    }
}
