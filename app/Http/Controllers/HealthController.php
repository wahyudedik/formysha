<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHealthRecordRequest;
use App\Http\Requests\UpdateHealthRecordRequest;
use App\Models\Child;
use App\Models\HealthRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HealthController extends Controller
{
    /**
     * Display a listing of health records for a child.
     */
    public function index(Request $request, Child $child): View
    {
        $query = $child->healthRecords()->orderBy('date', 'desc');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $healthRecords = $query->paginate(12);

        $typeCounts = $child->healthRecords()
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');

        return view('health.index', [
            'child' => $child,
            'healthRecords' => $healthRecords,
            'typeCounts' => $typeCounts,
            'activeType' => $request->type,
        ]);
    }

    /**
     * Show the form for creating a new health record.
     */
    public function create(Request $request, Child $child): View
    {
        $children = $request->user()->children()->get();

        return view('health.create', [
            'child' => $child,
            'children' => $children,
        ]);
    }

    /**
     * Store a newly created health record in storage.
     */
    public function store(StoreHealthRecordRequest $request, Child $child): RedirectResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;

        $child->healthRecords()->create($validated);

        return redirect()->route('health.index', $child)
            ->with('status', __('status.health_created'));
    }

    /**
     * Display the specified health record.
     */
    public function show(Request $request, Child $child, HealthRecord $healthRecord): View
    {
        abort_unless($healthRecord->child_id === $child->id, 403);

        return view('health.show', [
            'child' => $child,
            'healthRecord' => $healthRecord,
        ]);
    }

    /**
     * Show the form for editing the specified health record.
     */
    public function edit(Request $request, Child $child, HealthRecord $healthRecord): View
    {
        abort_unless($healthRecord->child_id === $child->id, 403);

        return view('health.edit', [
            'child' => $child,
            'healthRecord' => $healthRecord,
        ]);
    }

    /**
     * Update the specified health record in storage.
     */
    public function update(UpdateHealthRecordRequest $request, Child $child, HealthRecord $healthRecord): RedirectResponse
    {
        abort_unless($healthRecord->child_id === $child->id, 403);

        $healthRecord->update($request->validated());

        return redirect()->route('health.index', $child)
            ->with('status', __('status.health_updated'));
    }

    /**
     * Remove the specified health record from storage.
     */
    public function destroy(Request $request, Child $child, HealthRecord $healthRecord): RedirectResponse
    {
        abort_unless($healthRecord->child_id === $child->id, 403);

        $healthRecord->delete();

        return redirect()->route('health.index', $child)
            ->with('status', __('status.health_deleted'));
    }
}
