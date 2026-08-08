<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreHealthRequest;
use App\Http\Requests\Api\UpdateHealthRequest;
use App\Http\Resources\HealthRecordResource;
use App\Models\Child;
use App\Models\HealthRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HealthRecordController extends ApiController
{
    /**
     * List health records for a child.
     */
    public function index(Request $request, Child $child): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);

        $query = $child->healthRecords();

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $healthRecords = $query->orderBy('date', 'desc')
            ->paginate($request->input('per_page', 15));

        return $this->paginatedResponse($healthRecords, 'Daftar catatan kesehatan berhasil diambil');
    }

    /**
     * Store a new health record.
     */
    public function store(StoreHealthRequest $request, Child $child): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);

        $data = $request->validated();
        $data['child_id'] = $child->id;
        $data['user_id'] = $request->user()->id;

        $healthRecord = HealthRecord::create($data);

        return $this->successResponse(
            new HealthRecordResource($healthRecord),
            'Catatan kesehatan berhasil ditambahkan',
            201
        );
    }

    /**
     * Show a specific health record.
     */
    public function show(Request $request, Child $child, HealthRecord $healthRecord): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($healthRecord->child_id === $child->id, 404);

        return $this->successResponse(
            new HealthRecordResource($healthRecord),
            'Detail catatan kesehatan berhasil diambil'
        );
    }

    /**
     * Update a specific health record.
     */
    public function update(UpdateHealthRequest $request, Child $child, HealthRecord $healthRecord): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($healthRecord->child_id === $child->id, 404);

        $healthRecord->update($request->validated());

        return $this->successResponse(
            new HealthRecordResource($healthRecord->fresh()),
            'Catatan kesehatan berhasil diperbarui'
        );
    }

    /**
     * Delete a specific health record.
     */
    public function destroy(Request $request, Child $child, HealthRecord $healthRecord): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($healthRecord->child_id === $child->id, 404);

        $healthRecord->delete();

        return $this->successResponse(null, 'Catatan kesehatan berhasil dihapus');
    }
}
