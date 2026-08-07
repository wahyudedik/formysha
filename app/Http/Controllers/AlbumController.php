<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAlbumRequest;
use App\Http\Requests\UpdateAlbumRequest;
use App\Models\Album;
use App\Models\Child;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlbumController extends Controller
{
    /**
     * Display a listing of albums for a child.
     */
    public function index(Request $request, Child $child): View
    {
        $albums = $child->albums()
            ->withCount('media')
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('albums.index', [
            'child' => $child,
            'albums' => $albums,
        ]);
    }

    /**
     * Show the form for creating a new album.
     */
    public function create(Request $request, Child $child): View
    {
        return view('albums.create', [
            'child' => $child,
        ]);
    }

    /**
     * Store a newly created album in storage.
     */
    public function store(StoreAlbumRequest $request, Child $child): RedirectResponse
    {
        $child->albums()->create($request->validated());

        return redirect()->route('albums.index', $child)
            ->with('status', 'Album berhasil dibuat!');
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
            ->with('status', 'Album berhasil diperbarui!');
    }

    /**
     * Remove the specified album from storage.
     */
    public function destroy(Request $request, Child $child, Album $album): RedirectResponse
    {
        abort_unless($album->child_id === $child->id, 403);

        $album->delete();

        return redirect()->route('albums.index', $child)
            ->with('status', 'Album berhasil dihapus.');
    }
}
