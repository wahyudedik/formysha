<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAlbumRequest;
use App\Http\Requests\UpdateAlbumRequest;
use App\Models\Album;
use App\Models\Child;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlbumController extends Controller
{
    public function __construct(
        private MediaService $mediaService,
    ) {}

    /**
     * Display a listing of albums for a child.
     */
    public function index(Request $request, Child $child): View
    {
        $query = $child->albums()->withCount('media');

        // Sort options
        $sort = $request->input('sort', 'default');
        match ($sort) {
            'newest' => $query->orderBy('created_at', 'desc'),
            'oldest' => $query->orderBy('created_at', 'asc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            'most_media' => $query->orderBy('media_count', 'desc'),
            default => $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc'),
        };

        $albums = $query->paginate(12)->withQueryString();

        return view('albums.index', [
            'child' => $child,
            'albums' => $albums,
            'currentSort' => $sort,
        ]);
    }

    /**
     * Show the form for creating a new album.
     */
    public function create(Request $request, Child $child): View
    {
        $children = $request->user()->children()->get();

        return view('albums.create', [
            'child' => $child,
            'children' => $children,
        ]);
    }

    /**
     * Store a newly created album in storage.
     */
    public function store(StoreAlbumRequest $request, Child $child): RedirectResponse
    {
        $data = $request->validated();

        // Remove media from data before creating album
        $mediaFiles = $request->file('media') ?? [];
        unset($data['media']);

        $album = $child->albums()->create($data);

        // Handle media upload
        if (! empty($mediaFiles)) {
            $this->mediaService->uploadMultiple($mediaFiles, $album);
        }

        return redirect()->route('albums.index', $child)
            ->with('status', __('status.albums_created'));
    }

    /**
     * Display the specified album.
     */
    public function show(Request $request, Child $child, Album $album): View
    {
        abort_unless($album->child_id === $child->id, 403);

        $album->load('media');

        return view('albums.show', [
            'child' => $child,
            'album' => $album,
        ]);
    }

    /**
     * Show the form for editing the specified album.
     */
    public function edit(Request $request, Child $child, Album $album): View
    {
        abort_unless($album->child_id === $child->id, 403);

        return view('albums.edit', [
            'child' => $child,
            'album' => $album,
        ]);
    }

    /**
     * Update the specified album in storage.
     */
    public function update(UpdateAlbumRequest $request, Child $child, Album $album): RedirectResponse
    {
        abort_unless($album->child_id === $child->id, 403);

        $album->update($request->validated());

        return redirect()->route('albums.show', [$child, $album])
            ->with('status', __('status.albums_updated'));
    }

    /**
     * Remove the specified album from storage.
     */
    public function destroy(Request $request, Child $child, Album $album): RedirectResponse
    {
        abort_unless($album->child_id === $child->id, 403);

        $album->delete();

        return redirect()->route('albums.index', $child)
            ->with('status', __('status.albums_deleted'));
    }
}
