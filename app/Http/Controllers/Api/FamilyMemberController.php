<?php

namespace App\Http\Controllers\Api;

use App\Enums\FamilyMemberPermission;
use App\Http\Requests\Api\StoreFamilyRequest;
use App\Http\Requests\Api\UpdateFamilyRequest;
use App\Http\Resources\FamilyMemberResource;
use App\Models\Child;
use App\Models\FamilyMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FamilyMemberController extends ApiController
{
    /**
     * List family members for a child.
     */
    public function index(Request $request, Child $child): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);

        $familyMembers = $child->familyMembers()
            ->orderBy('name', 'asc')
            ->paginate($request->input('per_page', 15));

        return $this->paginatedResponse($familyMembers, 'Daftar anggota keluarga berhasil diambil');
    }

    /**
     * Store a new family member.
     */
    public function store(StoreFamilyRequest $request, Child $child): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);

        $data = $request->validated();
        $data['child_id'] = $child->id;
        $data['user_id'] = $request->user()->id;

        if (! isset($data['permission_level'])) {
            $data['permission_level'] = in_array($data['relationship'], ['father', 'mother', 'guardian'])
                ? FamilyMemberPermission::Edit
                : FamilyMemberPermission::View;
        }

        $familyMember = FamilyMember::create($data);

        return $this->successResponse(
            new FamilyMemberResource($familyMember),
            'Anggota keluarga berhasil ditambahkan',
            201
        );
    }

    /**
     * Show a specific family member.
     */
    public function show(Request $request, Child $child, FamilyMember $familyMember): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($familyMember->child_id === $child->id, 404);

        return $this->successResponse(
            new FamilyMemberResource($familyMember),
            'Detail anggota keluarga berhasil diambil'
        );
    }

    /**
     * Update a specific family member.
     */
    public function update(UpdateFamilyRequest $request, Child $child, FamilyMember $familyMember): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($familyMember->child_id === $child->id, 404);

        $familyMember->update($request->validated());

        return $this->successResponse(
            new FamilyMemberResource($familyMember->fresh()),
            'Anggota keluarga berhasil diperbarui'
        );
    }

    /**
     * Delete a specific family member.
     */
    public function destroy(Request $request, Child $child, FamilyMember $familyMember): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($familyMember->child_id === $child->id, 404);

        $familyMember->delete();

        return $this->successResponse(null, 'Anggota keluarga berhasil dihapus');
    }
}
