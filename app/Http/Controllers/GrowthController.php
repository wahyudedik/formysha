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
        $query = $child->growths();

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('measured_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('measured_at', '<=', $request->date_to);
        }

        $growths = $query->orderBy('measured_at', 'desc')->paginate(12)->withQueryString();

        $latestGrowth = $child->growths()
            ->latest('measured_at')
            ->first();

        $growthHistory = $child->growths()
            ->orderBy('measured_at', 'asc')
            ->get();

        $chartData = $this->growthService->getGrowthChartData($child);
        $whoWeight = $this->growthService->getWhoWeightPercentiles($child->gender);
        $whoHeight = $this->growthService->getWhoHeightPercentiles($child->gender);
        $whoHead = $this->growthService->getWhoHeadPercentiles($child->gender);

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
            'whoHead' => $whoHead,
            'assessment' => $assessment,
            'request' => $request,
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
            ->with('status', __('status.growth_created'));
    }

    /**
     * Display the specified growth record.
     */
    public function show(Request $request, Child $child, Growth $growth): View
    {
        abort_unless($growth->child_id === $child->id, 403);

        $assessment = $this->growthService->assessGrowth($child, $growth);

        return view('growth.show', [
            'child' => $child,
            'growth' => $growth,
            'assessment' => $assessment,
        ]);
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
            ->with('status', __('status.growth_updated'));
    }

    /**
     * Remove the specified growth record from storage.
     */
    public function destroy(Request $request, Child $child, Growth $growth): RedirectResponse
    {
        abort_unless($growth->child_id === $child->id, 403);

        $growth->delete();

        return redirect()->route('growth.index', $child)
            ->with('status', __('status.growth_deleted'));
    }
}
