<?php

namespace App\Http\Controllers\TenantAdmin;

use App\Models\Child;
use App\Models\Diary;
use App\Models\Document;
use App\Models\Media;
use App\Models\Timeline;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantAdminController extends Controller
{
    /**
     * Tampilkan dashboard tenant admin.
     */
    public function dashboard(Request $request): View
    {
        $user = $request->user();
        $tenant = $user->tenant;

        abort_unless($tenant, 404);

        // Statistik tenant
        $totalChildren = Child::where('tenant_id', $tenant->id)->count();
        $totalTimelines = Timeline::where('tenant_id', $tenant->id)->count();
        $totalPhotos = Media::where('tenant_id', $tenant->id)->where('file_type', 'photo')->count();
        $totalDocuments = Document::where('tenant_id', $tenant->id)->count();
        $totalDiaries = Diary::where('tenant_id', $tenant->id)->count();

        // Aktivitas terbaru (gabungan timeline dan diary)
        $recentTimelines = Timeline::where('tenant_id', $tenant->id)
            ->with('child')
            ->latest()
            ->take(5)
            ->get();

        $recentDiaries = Diary::where('tenant_id', $tenant->id)
            ->with('child')
            ->latest()
            ->take(5)
            ->get();

        // Gabungkan dan urutkan berdasarkan created_at
        $recentActivity = $recentTimelines->concat($recentDiaries)
            ->sortByDesc('created_at')
            ->take(10);

        // Subscription info
        $subscription = $tenant->activeSubscription()->with('plan')->first();
        $plan = $subscription?->plan;

        return view('admin.dashboard', compact(
            'tenant',
            'totalChildren',
            'totalTimelines',
            'totalPhotos',
            'totalDocuments',
            'totalDiaries',
            'recentActivity',
            'subscription',
            'plan',
        ));
    }
}
