<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\Child;
use App\Models\Consent;
use App\Models\FamilyMember;
use App\Models\MilestoneAlert;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Service untuk mengelola penghapusan data pengguna (Right to Erasure / Hak Penghapusan Data).
 *
 * Mengikuti prinsip UU PDP (Undang-Undang Pelindungan Data Pribadi).
 */
class AccountDeletionService
{
    /**
     * Dapatkan ringkasan data child yang akan dihapus.
     *
     * @return array{timelines: int, albums: int, media: int, diaries: int, documents: int, health_records: int, growths: int, events: int, family_members: int, achievements: int, milestone_alerts: int, consents: int}
     */
    public function getChildDataSummary(Child $child): array
    {
        return [
            'timelines' => $child->timelines()->count(),
            'albums' => $child->albums()->count(),
            'media' => $child->media()->count(),
            'diaries' => $child->diaries()->count(),
            'documents' => $child->documents()->count(),
            'health_records' => $child->healthRecords()->count(),
            'growths' => $child->growths()->count(),
            'events' => $child->events()->count(),
            'family_members' => $child->familyMembers()->count(),
            'achievements' => Achievement::where('child_id', $child->id)->count(),
            'milestone_alerts' => MilestoneAlert::where('child_id', $child->id)->count(),
            'consents' => Consent::where('child_id', $child->id)->count(),
        ];
    }

    /**
     * Dapatkan ringkasan data user yang akan dihapus.
     *
     * @return array{children: int, family_members: int, consents: int, notifications: int, audit_logs: int}
     */
    public function getUserDataSummary(User $user): array
    {
        $childIds = $user->children()->pluck('id');

        return [
            'children' => $user->children()->count(),
            'family_members' => FamilyMember::whereIn('child_id', $childIds)->count(),
            'consents' => Consent::where('user_id', $user->id)->count(),
            'notifications' => $user->notifications()->count(),
            'audit_logs' => $user->auditLogs()->count(),
        ];
    }

    /**
     * Hapus semua data milik child (media files + database records).
     *
     * @return array{deleted: int, media_freed: int}
     */
    public function deleteChildData(Child $child): array
    {
        $mediaFreed = 0;

        // Hapus file media dari storage
        $media = $child->media()->get();
        foreach ($media as $m) {
            if ($m->file_path && Storage::disk('public')->exists($m->file_path)) {
                $mediaFreed += Storage::disk('public')->size($m->file_path);
                Storage::disk('public')->delete($m->file_path);
            }
        }

        // Hapus semua data terkait
        $child->media()->delete();
        $child->timelines()->delete();
        $child->albums()->delete();
        $child->diaries()->delete();
        $child->documents()->delete();
        $child->healthRecords()->delete();
        $child->growths()->delete();
        $child->events()->delete();
        $child->familyMembers()->delete();
        Achievement::where('child_id', $child->id)->delete();
        MilestoneAlert::where('child_id', $child->id)->delete();
        Consent::where('child_id', $child->id)->delete();

        // Gunakan Query Builder untuk delete (bukan model instance delete)
        // karena Eloquent model delete() bisa auto-commit di SQLite
        DB::table('children')->where('id', $child->id)->delete();

        return [
            'deleted' => 1,
            'media_freed' => $mediaFreed,
        ];
    }

    /**
     * Hapus semua data milik user (termasuk semua child dan data terkait).
     *
     * @return array{children_deleted: int, media_freed: int}
     */
    public function deleteUserData(User $user): array
    {
        $totalMediaFreed = 0;
        $childrenDeleted = 0;

        // Hapus semua data child
        $children = $user->children()->get();
        foreach ($children as $child) {
            // Hapus file media dari storage
            $media = $child->media()->get();
            foreach ($media as $m) {
                if ($m->file_path && Storage::disk('public')->exists($m->file_path)) {
                    $totalMediaFreed += Storage::disk('public')->size($m->file_path);
                    Storage::disk('public')->delete($m->file_path);
                }
            }

            $child->media()->delete();
            $child->timelines()->delete();
            $child->albums()->delete();
            $child->diaries()->delete();
            $child->documents()->delete();
            $child->healthRecords()->delete();
            $child->growths()->delete();
            $child->events()->delete();
            $child->familyMembers()->delete();
            Achievement::where('child_id', $child->id)->delete();
            MilestoneAlert::where('child_id', $child->id)->delete();
            Consent::where('child_id', $child->id)->delete();

            // Gunakan Query Builder untuk delete
            DB::table('children')->where('id', $child->id)->delete();
            $childrenDeleted++;
        }

        // Hapus data user
        Consent::where('user_id', $user->id)->delete();
        $user->notifications()->delete();
        $user->auditLogs()->delete();
        $user->tokens()->delete();

        // Gunakan Query Builder untuk delete user
        DB::table('users')->where('id', $user->id)->delete();

        return [
            'children_deleted' => $childrenDeleted,
            'media_freed' => $totalMediaFreed,
        ];
    }
}
