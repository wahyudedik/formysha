<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDiaryRequest;
use App\Http\Requests\UpdateDiaryRequest;
use App\Models\Child;
use App\Models\Diary;
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
        $diaries = $child->diaries()
            ->orderBy('diary_date', 'desc')
            ->paginate(12);

        return view('diaries.index', [
            'child' => $child,
            'diaries' => $diaries,
        ]);
    }

    /**
     * Show the form for creating a new diary entry.
     */
    public function create(Request $request, Child $child): View
    {
        return view('diaries.create', [
            'child' => $child,
        ]);
    }

    /**
     * Store a newly created diary entry in storage.
     */
    public function store(StoreDiaryRequest $request, Child $child): RedirectResponse
    {
        $child->diaries()->create(array_merge(
            $request->validated(),
            ['user_id' => $request->user()->id],
        ));

        return redirect()->route('diaries.index', $child)
            ->with('status', 'Catatan harian berhasil dibuat!');
    }

    /**
     * Display the specified diary entry.
     */
    public function show(Request $request, Child $child, Diary $diary): View
    {
        abort_unless($diary->child_id === $child->id, 403);

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
