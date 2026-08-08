<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreDocumentRequest;
use App\Http\Requests\Api\UpdateDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Models\Child;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends ApiController
{
    /**
     * List documents for a child.
     */
    public function index(Request $request, Child $child): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);

        $query = $child->documents();

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $documents = $query->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 15));

        return $this->paginatedResponse($documents, 'Daftar dokumen berhasil diambil');
    }

    /**
     * Store a new document.
     */
    public function store(StoreDocumentRequest $request, Child $child): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);

        $data = $request->validated();
        $data['child_id'] = $child->id;
        $data['user_id'] = $request->user()->id;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $data['file_path'] = $file->store('documents', 'public');
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
        }

        unset($data['file']);

        $document = Document::create($data);

        return $this->successResponse(
            new DocumentResource($document),
            'Dokumen berhasil ditambahkan',
            201
        );
    }

    /**
     * Show a specific document.
     */
    public function show(Request $request, Child $child, Document $document): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($document->child_id === $child->id, 404);

        return $this->successResponse(
            new DocumentResource($document),
            'Detail dokumen berhasil diambil'
        );
    }

    /**
     * Update a specific document.
     */
    public function update(UpdateDocumentRequest $request, Child $child, Document $document): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($document->child_id === $child->id, 404);

        $data = $request->validated();

        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $request->file('file');
            $data['file_path'] = $file->store('documents', 'public');
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
        }

        unset($data['file']);

        $document->update($data);

        return $this->successResponse(
            new DocumentResource($document->fresh()),
            'Dokumen berhasil diperbarui'
        );
    }

    /**
     * Delete a specific document.
     */
    public function destroy(Request $request, Child $child, Document $document): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($document->child_id === $child->id, 404);

        // Delete file if exists
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return $this->successResponse(null, 'Dokumen berhasil dihapus');
    }
}
