<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreChildRequest;
use App\Http\Requests\Api\UpdateChildRequest;
use App\Http\Resources\ChildResource;
use App\Models\Child;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChildController extends ApiController
{
    /**
     * List all children for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Child::where('user_id', $request->user()->id)
            ->withCount(['timelines', 'albums', 'diaries', 'documents', 'events', 'growths', 'healthRecords']);

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $children = $query->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 15));

        return $this->paginatedResponse($children, 'Daftar anak berhasil diambil');
    }

    /**
     * Store a new child.
     */
    public function store(StoreChildRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('children', 'public');
        }

        $child = Child::create($data);

        return $this->successResponse(
            new ChildResource($child->load('user')),
            'Anak berhasil ditambahkan',
            201
        );
    }

    /**
     * Show a specific child.
     */
    public function show(Request $request, Child $child): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);

        return $this->successResponse(
            new ChildResource($child->load(['user', 'familyMembers'])),
            'Detail anak berhasil diambil'
        );
    }

    /**
     * Update a specific child.
     */
    public function update(UpdateChildRequest $request, Child $child): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);

        $data = $request->validated();

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($child->photo && Storage::disk('public')->exists($child->photo)) {
                Storage::disk('public')->delete($child->photo);
            }
            $data['photo'] = $request->file('photo')->store('children', 'public');
        }

        $child->update($data);

        return $this->successResponse(
            new ChildResource($child->fresh()),
            'Data anak berhasil diperbarui'
        );
    }

    /**
     * Delete a specific child.
     */
    public function destroy(Request $request, Child $child): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);

        // Delete photo if exists
        if ($child->photo && Storage::disk('public')->exists($child->photo)) {
            Storage::disk('public')->delete($child->photo);
        }

        $child->delete();

        return $this->successResponse(null, 'Anak berhasil dihapus');
    }
}
