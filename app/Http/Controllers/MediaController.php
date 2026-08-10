<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Child;
use App\Models\Diary;
use App\Models\Media;
use App\Models\Timeline;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    /**
     * Store media for a timeline entry.
     */
    public function storeForTimeline(Request $request, Child $child, Timeline $timeline): RedirectResponse
    {
        abort_unless($timeline->child_id === $child->id, 403);

        $request->validate([
            'media' => ['required', 'array'],
            'media.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,gif,webp,mp4,mov,webm,mp3,wav,ogg'],
        ]);

        $mediaService = new MediaService;
        $mediaService->uploadMultiple($request->file('media'), $timeline);

        return redirect()->route('timeline.show', [$child, $timeline])
            ->with('status', __('status.media_created'));
    }

    /**
     * Store media for an album.
     */
    public function storeForAlbum(Request $request, Child $child, Album $album): RedirectResponse
    {
        abort_unless($album->child_id === $child->id, 403);

        $request->validate([
            'media' => ['required', 'array'],
            'media.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,gif,webp,mp4,mov,webm,mp3,wav,ogg'],
        ]);

        $mediaService = new MediaService;
        $mediaService->uploadMultiple($request->file('media'), $album);

        return redirect()->route('albums.show', [$child, $album])
            ->with('status', __('status.media_created'));
    }

    /**
     * Store media for a diary entry.
     */
    public function storeForDiary(Request $request, Child $child, Diary $diary): RedirectResponse
    {
        abort_unless($diary->child_id === $child->id, 403);

        $request->validate([
            'media' => ['required', 'array'],
            'media.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,gif,webp,mp4,mov,webm,mp3,wav,ogg'],
        ]);

        $mediaService = new MediaService;
        $mediaService->uploadMultiple($request->file('media'), $diary);

        return redirect()->route('diaries.show', [$child, $diary])
            ->with('status', __('status.media_created'));
    }

    /**
     * Delete a media record.
     */
    public function destroy(Request $request, Child $child, Media $media): RedirectResponse
    {
        // Verify the media belongs to this child
        $isValid = false;

        if ($media->mediable_type === Child::class && $media->mediable_id == $child->id) {
            $isValid = true;
        } elseif (in_array($media->mediable_type, [Timeline::class, Album::class, Diary::class])) {
            $parent = $media->mediable;
            if ($parent && $parent->child_id == $child->id) {
                $isValid = true;
            }
        }

        abort_unless($isValid, 403);

        $mediaService = new MediaService;
        $mediaService->delete($media);

        // Redirect back to the appropriate page
        $referrer = $request->header('referer');
        $redirectUrl = $referrer ?? route('children.show', $child);

        return redirect($redirectUrl)
            ->with('status', __('status.media_deleted'));
    }
}
