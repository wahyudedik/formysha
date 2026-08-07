<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGrowthRequest;
use App\Http\Requests\UpdateGrowthRequest;
use App\Models\Child;
use App\Models\Growth;
use App\Services\GrowthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GrowthController extends Controller
{
    public function __construct(
        private GrowthService $growthService,
    ) {}

    /**
     * Display a listing of growth records for a child.
     */
    public function index(Request $request, Child $child): View
    {
        $growths = $child->growths()
            ->orderBy('measured_at', 'desc')
            ->paginate(12);

        $latestGrowth = $child->growths()
            ->latest('measured_at')
            ->first();

        $growthHistory = $child->growths()
            ->orderBy('measured_at', 'asc')
            ->get();

        $chartData = $this->growthService->getGrowthChartData($child);
        $whoWeight = $this->growthService->getWhoWeightPercentiles($child->gender);
        $whoHeight = $this->growthService->getWhoHeightPercentiles($child->gender);

        $assessment = null;
        if ($latestGrowth) {
            $assessment = $this->growthService->assessGrowth($child, $latestGrowth);
        }

        return view('growth.index', [
            'child' => $child,
            'growths' => $growths,
            'latestGrowth' => $latestGrowth,
            'growthHistory' => $growthHistory,
            'chartData' => $chartData,
            'whoWeight' => $whoWeight,
            'whoHeight' => $whoHeight,
            'assessment' => $assessment,
        ]);
    }

    /**
     * Show the form for creating a new growth record.
     */
    public function create(Request $request, Child $child): View
    {
        $children = $request->user()->children()->get();

        return view('growth.create', [
            'child' => $child,
            'children' => $children,
        ]);
    }

    /**
     * Store a newly created growth record in storage.
     */
    public function store(StoreGrowthRequest $request, Child $child): RedirectResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;

        $child->growths()->create($validated);

        return redirect()->route('growth.index', $child)
            ->with('status', 'Data pertumbuhan berhasil disimpan!');
    }

    /**
     * Show the form for editing the specified growth record.
     */
    public function edit(Request $request, Child $child, Growth $growth): View
    {
        abort_unless($growth->child_id === $child->id, 403);

        return view('growth.edit', [
            'child' => $child,
            'growth' => $growth,
        ]);
    }

    /**
     * Update the specified growth record in storage.
     */
    public function update(UpdateGrowthRequest $request, Child $child, Growth $growth): RedirectResponse
    {
        abort_unless($growth->child_id === $child->id, 403);

        $growth->update($request->validated());

        return redirect()->route('growth.index', $child)
            ->with('status', 'Data pertumbuhan berhasil diperbarui!');
    }

    /**
     * Remove the specified growth record from storage.
     */
    public function destroy(Request $request, Child $child, Growth $growth): RedirectResponse
    {
        abort_unless($growth->child_id === $child->id, 403);

        $growth->delete();

        return redirect()->route('growth.index', $child)
            ->with('status', 'Data pertumbuhan berhasil dihapus.');
    }
}
