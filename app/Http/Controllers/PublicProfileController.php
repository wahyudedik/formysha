<?php

namespace App\Http\Controllers;

use App\Models\Child;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicProfileController extends Controller
{
    /**
     * Show the public profile for a child by slug.
     */
    public function __invoke(Request $request, string $slug): View
    {
        $child = Child::where('slug', $slug)
            ->where('is_public', true)
            ->with([
                'timelines' => function ($query) {
                    $query->latest()->take(5);
                },
                'albums' => function ($query) {
                    $query->take(6)->with('media');
                },
                'achievements' => function ($query) {
                    $query->whereNotNull('earned_at')->latest('earned_at');
                },
            ])
            ->firstOrFail();

        $publicData = $child->public_profile_data ?? [];

        return view('public.profile', [
            'child' => $child,
            'publicData' => $publicData,
            'showTimeline' => in_array('timeline', $publicData),
            'showGallery' => in_array('gallery', $publicData),
            'showAwards' => in_array('awards', $publicData),
        ]);
    }
}
