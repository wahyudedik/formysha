<?php

namespace App\Http\Controllers;

use App\Enums\FamilyMemberPermission;
use App\Http\Requests\StoreFamilyMemberRequest;
use App\Http\Requests\UpdateFamilyMemberRequest;
use App\Models\Child;
use App\Models\FamilyMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            'permissions' => FamilyMemberPermission::options(),
        ]);
    }

    /**
     * Show the form for creating a new family member.
     */
    public function create(Request $request, Child $child): View
    {
        $children = $request->user()->children()->get();

        return view('family.create', [
            'child' => $child,
            'children' => $children,
            'permissions' => FamilyMemberPermission::options(),
        ]);
    }

    /**
     * Store a newly created family member in storage.
     */
    public function store(StoreFamilyMemberRequest $request, Child $child): RedirectResponse
    {
        $data = $request->validated();
        $data['tenant_id'] = $child->tenant_id;

        // Auto-set permission level based on relationship if not provided
        if (! isset($data['permission_level'])) {
            $data['permission_level'] = in_array($data['relationship'], ['father', 'mother', 'guardian'])
                ? FamilyMemberPermission::Edit
                : FamilyMemberPermission::View;
        }

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('family-photos', 'public');
        }

        $child->familyMembers()->create($data);

        return redirect()->route('family.index', $child)
            ->with('status', __('status.family_created'));
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
            'permissions' => FamilyMemberPermission::options(),
        ]);
    }

    /**
     * Update the specified family member in storage.
     */
    public function update(UpdateFamilyMemberRequest $request, Child $child, FamilyMember $familyMember): RedirectResponse
    {
        abort_unless($familyMember->child_id === $child->id, 403);

        $data = $request->validated();

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($familyMember->photo) {
                Storage::disk('public')->delete($familyMember->photo);
            }
            $data['photo'] = $request->file('photo')->store('family-photos', 'public');
        }

        $familyMember->update($data);

        return redirect()->route('family.index', $child)
            ->with('status', __('status.family_updated'));
    }

    /**
     * Remove the specified family member from storage.
     */
    public function destroy(Request $request, Child $child, FamilyMember $familyMember): RedirectResponse
    {
        abort_unless($familyMember->child_id === $child->id, 403);

        // Delete photo from storage if exists
        if ($familyMember->photo) {
            Storage::disk('public')->delete($familyMember->photo);
        }

        $familyMember->delete();

        return redirect()->route('family.index', $child)
            ->with('status', __('status.family_deleted'));
    }
}
