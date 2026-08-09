<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDiaryRequest;
use App\Http\Requests\UpdateDiaryRequest;
use App\Models\Child;
use App\Models\Diary;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DiaryController extends Controller
{
    /**
     * Display a listing of diary entries for a child.
     */
    public function index(Request $request, Child $child): View
    {
        $query = $child->diaries();

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('diary_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('diary_date', '<=', $request->date_to);
        }

        // Filter by mood
        if ($request->filled('mood')) {
            $query->where('mood', $request->mood);
        }

        // Sort options
        $sort = $request->input('sort', 'newest');
        match ($sort) {
            'oldest' => $query->orderBy('diary_date', 'asc'),
            'title_asc' => $query->orderBy('title', 'asc'),
            'title_desc' => $query->orderBy('title', 'desc'),
            default => $query->orderBy('diary_date', 'desc'),
        };

        $diaries = $query->paginate(12)->withQueryString();

        return view('diaries.index', [
            'child' => $child,
            'diaries' => $diaries,
            'currentSort' => $sort,
            'request' => $request,
        ]);
    }

    /**
     * Show the form for creating a new diary entry.
     */
    public function create(Request $request, Child $child): View
    {
        $children = $request->user()->children()->get();

        return view('diaries.create', [
            'child' => $child,
            'children' => $children,
        ]);
    }

    /**
     * Store a newly created diary entry in storage.
     */
    public function store(StoreDiaryRequest $request, Child $child): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        // Remove media from data before creating diary
        $mediaFiles = $request->file('media') ?? [];
        unset($data['media']);

        $diary = $child->diaries()->create($data);

        // Handle media upload
        if (! empty($mediaFiles)) {
            $mediaService = new MediaService;
            $mediaService->uploadMultiple($mediaFiles, $diary);
        }

        return redirect()->route('diaries.index', $child)
            ->with('status', 'Catatan harian berhasil dibuat!');
    }

    /**
     * Display the specified diary entry.
     */
    public function show(Request $request, Child $child, Diary $diary): View
    {
        abort_unless($diary->child_id === $child->id, 403);

        $diary->load('media');

        return view('diaries.show', [
            'child' => $child,
            'diary' => $diary,
        ]);
    }

    /**
     * Show the form for editing the specified diary entry.
     */
    public function edit(Request $request, Child $child, Diary $diary): View
    {
        abort_unless($diary->child_id === $child->id, 403);

        return view('diaries.edit', [
            'child' => $child,
            'diary' => $diary,
        ]);
    }

    /**
     * Update the specified diary entry in storage.
     */
    public function update(UpdateDiaryRequest $request, Child $child, Diary $diary): RedirectResponse
    {
        abort_unless($diary->child_id === $child->id, 403);

        $diary->update($request->validated());

        return redirect()->route('diaries.show', [$child, $diary])
            ->with('status', 'Catatan harian berhasil diperbarui!');
    }

    /**
     * Remove the specified diary entry from storage.
     */
    public function destroy(Request $request, Child $child, Diary $diary): RedirectResponse
    {
        abort_unless($diary->child_id === $child->id, 403);

        $diary->delete();

        return redirect()->route('diaries.index', $child)
            ->with('status', 'Catatan harian berhasil dihapus.');
    }
}
