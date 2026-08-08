<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreGrowthRequest;
use App\Http\Requests\Api\UpdateGrowthRequest;
use App\Http\Resources\GrowthResource;
use App\Models\Child;
use App\Models\Growth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GrowthController extends ApiController
{
    /**
     * List growth records for a child.
     */
    public function index(Request $request, Child $child): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);

        $query = $child->growths();

        if ($request->filled('date_from')) {
            $query->where('measured_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->where('measured_at', '<=', $request->input('date_to'));
        }

        $growths = $query->orderBy('measured_at', 'desc')
            ->paginate($request->input('per_page', 15));

        return $this->paginatedResponse($growths, 'Daftar data pertumbuhan berhasil diambil');
    }

    /**
     * Store a new growth record.
     */
    public function store(StoreGrowthRequest $request, Child $child): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);

        $data = $request->validated();
        $data['child_id'] = $child->id;
        $data['user_id'] = $request->user()->id;

        $growth = Growth::create($data);

        return $this->successResponse(
            new GrowthResource($growth),
            'Data pertumbuhan berhasil ditambahkan',
            201
        );
    }

    /**
     * Show a specific growth record.
     */
    public function show(Request $request, Child $child, Growth $growth): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($growth->child_id === $child->id, 404);

        return $this->successResponse(
            new GrowthResource($growth),
            'Detail data pertumbuhan berhasil diambil'
        );
    }

    /**
     * Update a specific growth record.
     */
    public function update(UpdateGrowthRequest $request, Child $child, Growth $growth): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($growth->child_id === $child->id, 404);

        $growth->update($request->validated());

        return $this->successResponse(
            new GrowthResource($growth->fresh()),
            'Data pertumbuhan berhasil diperbarui'
        );
    }

    /**
     * Delete a specific growth record.
     */
    public function destroy(Request $request, Child $child, Growth $growth): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($growth->child_id === $child->id, 404);

        $growth->delete();

        return $this->successResponse(null, 'Data pertumbuhan berhasil dihapus');
    }

    /**
     * Get chart data for weight & height over time.
     */
    public function chartData(Request $request, Child $child): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);

        $query = $child->growths()->orderBy('measured_at', 'asc');

        if ($request->filled('date_from')) {
            $query->where('measured_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->where('measured_at', '<=', $request->input('date_to'));
        }

        $growths = $query->get(['measured_at', 'weight_kg', 'height_cm', 'head_circumference_cm']);

        $chartData = [
            'labels' => $growths->pluck('measured_at')->map(fn ($date) => $date->format('Y-m-d'))->values()->all(),
            'weight' => $growths->pluck('weight_kg')->values()->all(),
            'height' => $growths->pluck('height_cm')->values()->all(),
            'head_circumference' => $growths->pluck('head_circumference_cm')->values()->all(),
        ];

        return $this->successResponse($chartData, 'Data grafik pertumbuhan berhasil diambil');
    }
}
