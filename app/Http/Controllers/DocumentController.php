<?php

namespace App\Http\Controllers;

use App\Enums\DocumentType;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Child;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentController extends Controller
{
    /**
     * Display a listing of documents for a child.
     */
    public function index(Request $request, Child $child): View
    {
        $query = $child->documents();

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Sort options
        $sort = $request->input('sort', 'default');
        match ($sort) {
            'newest' => $query->orderBy('created_at', 'desc'),
            'oldest' => $query->orderBy('created_at', 'asc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            default => $query->orderBy('type', 'asc')->orderBy('created_at', 'desc'),
        };

        $documents = $query->paginate(12)->withQueryString();

        $documentTypes = DocumentType::options();

        return view('documents.index', [
            'child' => $child,
            'documents' => $documents,
            'currentSort' => $sort,
            'documentTypes' => $documentTypes,
            'request' => $request,
        ]);
    }

    /**
     * Show the form for creating a new document.
     */
    public function create(Request $request, Child $child): View
    {
        $children = $request->user()->children()->get();

        return view('documents.create', [
            'child' => $child,
            'children' => $children,
        ]);
    }

    /**
     * Store a newly created document in storage.
     */
    public function store(StoreDocumentRequest $request, Child $child): RedirectResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $validated['file_path'] = $file->store('documents', 'public');
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_size'] = $file->getSize();
        }

        $child->documents()->create($validated);

        return redirect()->route('documents.index', $child)
            ->with('status', __('status.documents_created'));
    }

    /**
     * Display the specified document.
     */
    public function show(Request $request, Child $child, Document $document): View
    {
        abort_unless($document->child_id === $child->id, 403);

        return view('documents.show', [
            'child' => $child,
            'document' => $document,
        ]);
    }

    /**
     * Show the form for editing the specified document.
     */
    public function edit(Request $request, Child $child, Document $document): View
    {
        abort_unless($document->child_id === $child->id, 403);

        return view('documents.edit', [
            'child' => $child,
            'document' => $document,
        ]);
    }

    /**
     * Update the specified document in storage.
     */
    public function update(UpdateDocumentRequest $request, Child $child, Document $document): RedirectResponse
    {
        abort_unless($document->child_id === $child->id, 403);

        $validated = $request->validated();

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $validated['file_path'] = $file->store('documents', 'public');
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_size'] = $file->getSize();
        }

        $document->update($validated);

        return redirect()->route('documents.show', [$child, $document])
            ->with('status', __('status.documents_updated'));
    }

    /**
     * Remove the specified document from storage.
     */
    public function destroy(Request $request, Child $child, Document $document): RedirectResponse
    {
        abort_unless($document->child_id === $child->id, 403);

        $document->delete();

        return redirect()->route('documents.index', $child)
            ->with('status', __('status.documents_deleted'));
    }
}
