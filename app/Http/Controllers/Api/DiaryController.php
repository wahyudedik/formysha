<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreDiaryRequest;
use App\Http\Requests\Api\UpdateDiaryRequest;
use App\Http\Resources\DiaryResource;
use App\Models\Child;
use App\Models\Diary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiaryController extends ApiController
{
    /**
     * List diaries for a child.
     */
    public function index(Request $request, Child $child): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);

        $query = $child->diaries();

        if ($request->filled('date')) {
            $query->whereDate('diary_date', $request->input('date'));
        }

        $diaries = $query->orderBy('diary_date', 'desc')
            ->paginate($request->input('per_page', 15));

        return $this->paginatedResponse($diaries, 'Daftar diary berhasil diambil');
    }

    /**
     * Store a new diary entry.
     */
    public function store(StoreDiaryRequest $request, Child $child): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);

        $data = $request->validated();
        $data['child_id'] = $child->id;
        $data['user_id'] = $request->user()->id;

        $diary = Diary::create($data);

        return $this->successResponse(
            new DiaryResource($diary),
            'Diary berhasil ditambahkan',
            201
        );
    }

    /**
     * Show a specific diary entry.
     */
    public function show(Request $request, Child $child, Diary $diary): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($diary->child_id === $child->id, 404);

        return $this->successResponse(
            new DiaryResource($diary),
            'Detail diary berhasil diambil'
        );
    }

    /**
     * Update a specific diary entry.
     */
    public function update(UpdateDiaryRequest $request, Child $child, Diary $diary): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($diary->child_id === $child->id, 404);

        $diary->update($request->validated());

        return $this->successResponse(
            new DiaryResource($diary->fresh()),
            'Diary berhasil diperbarui'
        );
    }

    /**
     * Delete a specific diary entry.
     */
    public function destroy(Request $request, Child $child, Diary $diary): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($diary->child_id === $child->id, 404);

        $diary->delete();

        return $this->successResponse(null, 'Diary berhasil dihapus');
    }
}
