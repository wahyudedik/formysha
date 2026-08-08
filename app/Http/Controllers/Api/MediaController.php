<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\MediaResource;
use App\Models\Album;
use App\Models\Child;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends ApiController
{
    /**
     * List media for a child.
     */
    public function index(Request $request, Child $child): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);

        $query = Media::where('mediable_type', Child::class)
            ->where('mediable_id', $child->id);

        if ($request->filled('album_id')) {
            $query->where('mediable_type', Album::class)
                ->where('mediable_id', $request->input('album_id'));
        }

        $media = $query->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 15));

        return $this->paginatedResponse($media, 'Daftar media berhasil diambil');
    }

    /**
     * Store a new media file.
     */
    public function store(Request $request, Child $child): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);

        $request->validate([
            'file' => ['required', 'file', 'max:10240'],
            'album_id' => ['nullable', 'exists:albums,id'],
            'alt_text' => ['nullable', 'max:255'],
        ]);

        $file = $request->file('file');
        $filePath = $file->store('media', 'public');
        $fileType = str_starts_with($file->getMimeType(), 'image') ? 'photo' : 'video';

        // Determine the mediable type and ID
        if ($request->filled('album_id')) {
            $album = Album::findOrFail($request->input('album_id'));
            abort_unless($album->child_id === $child->id, 403);

            $mediableType = Album::class;
            $mediableId = $album->id;
        } else {
            $mediableType = Child::class;
            $mediableId = $child->id;
        }

        $media = Media::create([
            'mediable_type' => $mediableType,
            'mediable_id' => $mediableId,
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $fileType,
            'file_size' => $file->getSize(),
            'alt_text' => $request->input('alt_text'),
        ]);

        return $this->successResponse(
            new MediaResource($media),
            'Media berhasil diunggah',
            201
        );
    }

    /**
     * Delete a specific media file.
     */
    public function destroy(Request $request, Child $child, Media $media): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);

        // Verify the media belongs to this child
        if ($media->mediable_type === Child::class && $media->mediable_id != $child->id) {
            abort(404);
        }

        if ($media->mediable_type === Album::class) {
            $album = Album::find($media->mediable_id);
            if (! $album || $album->child_id != $child->id) {
                abort(404);
            }
        }

        // Delete the file from storage
        if ($media->file_path && Storage::disk('public')->exists($media->file_path)) {
            Storage::disk('public')->delete($media->file_path);
        }

        $media->delete();

        return $this->successResponse(null, 'Media berhasil dihapus');
    }
}
