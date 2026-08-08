<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreAlbumRequest;
use App\Http\Requests\Api\UpdateAlbumRequest;
use App\Http\Resources\AlbumResource;
use App\Models\Album;
use App\Models\Child;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlbumController extends ApiController
{
    /**
     * List albums for a child.
     */
    public function index(Request $request, Child $child): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);

        $albums = $child->albums()
            ->withCount('media')
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 15));

        return $this->paginatedResponse($albums, 'Daftar album berhasil diambil');
    }

    /**
     * Store a new album.
     */
    public function store(StoreAlbumRequest $request, Child $child): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);

        $data = $request->validated();
        $data['child_id'] = $child->id;

        $album = Album::create($data);

        return $this->successResponse(
            new AlbumResource($album->loadCount('media')),
            'Album berhasil ditambahkan',
            201
        );
    }

    /**
     * Show a specific album with media.
     */
    public function show(Request $request, Child $child, Album $album): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($album->child_id === $child->id, 404);

        $album->load('media');
        $album->loadCount('media');

        return $this->successResponse(
            new AlbumResource($album),
            'Detail album berhasil diambil'
        );
    }

    /**
     * Update a specific album.
     */
    public function update(UpdateAlbumRequest $request, Child $child, Album $album): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($album->child_id === $child->id, 404);

        $album->update($request->validated());

        return $this->successResponse(
            new AlbumResource($album->fresh()->loadCount('media')),
            'Album berhasil diperbarui'
        );
    }

    /**
     * Delete a specific album.
     */
    public function destroy(Request $request, Child $child, Album $album): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($album->child_id === $child->id, 404);

        $album->delete();

        return $this->successResponse(null, 'Album berhasil dihapus');
    }
}
