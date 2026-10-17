<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Child;
use App\Models\Diary;
use App\Models\Document;
use App\Models\FamilyMember;
use App\Models\Media;
use App\Models\Timeline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Menyajikan file sensitif (foto anak, foto anggota keluarga, media, dokumen)
 * dari disk PRIVATE melalui route terautentikasi.
 *
 * File tidak pernah dilayani langsung dari URL publik; path file asli tidak
 * pernah diekspos ke client.
 */
class FileDownloadController extends Controller
{
    /**
     * Serve a child profile photo (authenticated, owner only).
     */
    public function childPhoto(Request $request, Child $child): StreamedResponse
    {
        return $this->streamFromPrivate($child->photo);
    }

    /**
     * Serve a family member photo (authenticated, owner of the child only).
     */
    public function familyMemberPhoto(Request $request, Child $child, FamilyMember $familyMember): StreamedResponse
    {
        abort_unless($familyMember->child_id === $child->id, 403);

        return $this->streamFromPrivate($familyMember->photo);
    }

    /**
     * Serve a media file (photo/video/audio) inline.
     */
    public function mediaFile(Request $request, Child $child, Media $media): StreamedResponse
    {
        $this->authorizeMediaBelongsToChild($child, $media);

        return $this->streamFromPrivate($media->file_path);
    }

    /**
     * Serve a media thumbnail inline (falls back to the full file).
     */
    public function mediaThumbnail(Request $request, Child $child, Media $media): StreamedResponse
    {
        $this->authorizeMediaBelongsToChild($child, $media);

        return $this->streamFromPrivate($media->thumbnail_path ?? $media->file_path);
    }

    /**
     * Serve a document file inline (for image/PDF preview).
     */
    public function documentFile(Request $request, Child $child, Document $document): StreamedResponse
    {
        abort_unless($document->child_id === $child->id, 403);

        return $this->streamFromPrivate($document->file_path);
    }

    /**
     * Download a document as an attachment (Content-Disposition: attachment).
     */
    public function documentDownload(Request $request, Child $child, Document $document): StreamedResponse
    {
        abort_unless($document->child_id === $child->id, 403);

        return $this->streamFromPrivate($document->file_path, 'attachment', $document->file_name);
    }

    /**
     * Serve an album cover photo inline (authenticated, owner only).
     */
    public function albumCover(Request $request, Child $child, Album $album): StreamedResponse
    {
        abort_unless($album->child_id === $child->id, 403);

        return $this->streamFromPrivate($album->cover_photo);
    }

    /**
     * Serve the child profile photo on the PUBLIC profile page.
     *
     * Only children explicitly marked as public can be served; the file path
     * itself is never exposed to the client.
     */
    public function publicPhoto(Request $request, string $slug): StreamedResponse
    {
        $child = Child::where('slug', $slug)
            ->where('is_public', true)
            ->firstOrFail();

        return $this->streamFromPrivate($child->photo);
    }

    /**
     * Serve an album cover photo on the PUBLIC profile page.
     *
     * Only covers of albums that belong to an explicitly public child are served.
     */
    public function publicAlbumCover(Request $request, string $slug, Album $album): StreamedResponse
    {
        $child = Child::where('slug', $slug)
            ->where('is_public', true)
            ->firstOrFail();

        abort_unless($album->child_id === $child->id, 404);

        return $this->streamFromPrivate($album->cover_photo);
    }

    /**
     * Verify the media belongs to the child via its mediable chain
     * (media -> mediable -> child), same pattern as MediaController::destroy().
     */
    private function authorizeMediaBelongsToChild(Child $child, Media $media): void
    {
        $isValid = false;

        if ($media->mediable_type === Child::class && $media->mediable_id == $child->id) {
            $isValid = true;
        } elseif (in_array($media->mediable_type, [Timeline::class, Album::class, Diary::class], true)) {
            $parent = $media->mediable;
            if ($parent && $parent->child_id == $child->id) {
                $isValid = true;
            }
        }

        abort_unless($isValid, 403);
    }

    /**
     * Stream a file from the private disk with the given disposition.
     */
    private function streamFromPrivate(?string $path, string $disposition = 'inline', ?string $downloadName = null): StreamedResponse
    {
        abort_unless($path !== null && $path !== '', 404);
        abort_unless(Storage::disk('private')->exists($path), 404);

        return Storage::disk('private')->response($path, $downloadName, [], $disposition);
    }
}
