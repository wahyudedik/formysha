<?php

namespace App\Console\Commands;

use App\Models\Child;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Artisan command untuk backup data anak ke JSON.
 *
 * Usage:
 *   php artisan backup:child-data              — Backup semua anak
 *   php artisan backup:child-data --child=slug  — Backup anak tertentu
 *   php artisan backup:child-data --user=1      — Backup anak milik user tertentu
 *   php artisan backup:child-data --cleanup=30  — Hapus backup lebih dari 30 hari
 */
class BackupChildData extends Command
{
    protected $signature = 'backup:child-data
        {--child= : Slug anak yang ingin di-backup}
        {--user= : ID user yang memiliki anak}
        {--cleanup= : Hapus backup lebih dari N hari}';

    protected $description = 'Backup data anak (profil, timeline, album, dokumen) ke JSON file';

    public function handle(): int
    {
        // Cleanup old backups if requested
        if ($this->option('cleanup')) {
            return $this->cleanupOldBackups((int) $this->option('cleanup'));
        }

        $query = Child::with([
            'timelines',
            'albums',
            'albums.media',
            'diaries',
            'documents',
            'events',
            'growths',
            'healthRecords',
            'familyMembers',
            'media',
        ]);

        if ($this->option('child')) {
            $query->where('slug', $this->option('child'));
        }

        if ($this->option('user')) {
            $query->where('user_id', $this->option('user'));
        }

        $children = $query->get();

        if ($children->isEmpty()) {
            $this->warn('Tidak ada data anak yang ditemukan untuk di-backup.');

            return self::SUCCESS;
        }

        $backupCount = 0;

        foreach ($children as $child) {
            $this->backupChild($child);
            $backupCount++;
        }

        $this->info("✅ Backup selesai! {$backupCount} data anak berhasil di-backup.");

        return self::SUCCESS;
    }

    /**
     * Backup data satu anak ke JSON file.
     */
    private function backupChild(Child $child): void
    {
        $timestamp = now()->format('Y-m-d_His');
        $filename = "backups/{$child->slug}_{$timestamp}.json";

        $data = [
            'backup_info' => [
                'created_at' => now()->toIso8601String(),
                'child_slug' => $child->slug,
                'child_name' => $child->name,
                'version' => '1.0',
            ],
            'profile' => [
                'name' => $child->name,
                'nickname' => $child->nickname,
                'gender' => $child->gender,
                'date_of_birth' => $child->date_of_birth,
                'blood_type' => $child->blood_type,
                'bio' => $child->bio,
                'is_public' => $child->is_public,
                'slug' => $child->slug,
            ],
            'timelines' => $child->timelines->map(fn ($t) => [
                'title' => $t->title,
                'description' => $t->description,
                'event_date' => $t->event_date,
                'tags' => $t->tags,
                'location' => $t->location,
                'created_at' => $t->created_at,
            ])->toArray(),
            'albums' => $child->albums->map(fn ($a) => [
                'name' => $a->name,
                'description' => $a->description,
                'media_count' => $a->media->count(),
            ])->toArray(),
            'diaries' => $child->diaries->map(fn ($d) => [
                'title' => $d->title,
                'content' => $d->content,
                'diary_date' => $d->diary_date,
                'mood' => $d->mood,
                'created_at' => $d->created_at,
            ])->toArray(),
            'documents' => $child->documents->map(fn ($d) => [
                'name' => $d->name,
                'type' => $d->type,
                'file_path' => $d->file_path,
                'notes' => $d->notes,
                'created_at' => $d->created_at,
            ])->toArray(),
            'events' => $child->events->map(fn ($e) => [
                'title' => $e->title,
                'description' => $e->description,
                'event_date' => $e->event_date,
                'event_type' => $e->event_type,
                'created_at' => $e->created_at,
            ])->toArray(),
            'growths' => $child->growths->map(fn ($g) => [
                'weight_kg' => $g->weight_kg,
                'height_cm' => $g->height_cm,
                'head_circumference_cm' => $g->head_circumference_cm,
                'recorded_at' => $g->recorded_at,
                'notes' => $g->notes,
            ])->toArray(),
            'health_records' => $child->healthRecords->map(fn ($h) => [
                'type' => $h->type,
                'title' => $h->title,
                'description' => $h->description,
                'recorded_at' => $h->recorded_at,
                'doctor_name' => $h->doctor_name,
                'notes' => $h->notes,
            ])->toArray(),
            'family_members' => $child->familyMembers->map(fn ($f) => [
                'name' => $f->name,
                'relationship' => $f->relationship,
                'phone' => $f->phone,
                'email' => $f->email,
            ])->toArray(),
        ];

        Storage::disk('local')->put($filename, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("  📦 Backup: {$child->name} ({$child->slug}) → {$filename}");
    }

    /**
     * Hapus backup yang lebih tua dari N hari.
     */
    private function cleanupOldBackups(int $days): int
    {
        $cutoff = now()->subDays($days);
        $deleted = 0;

        $files = Storage::disk('local')->files('backups');

        foreach ($files as $file) {
            $lastModified = Storage::disk('local')->lastModified($file);

            if ($lastModified < $cutoff->timestamp) {
                Storage::disk('local')->delete($file);
                $deleted++;
            }
        }

        $this->info("🗑️ Cleanup: {$deleted} backup lama (> {$days} hari) berhasil dihapus.");

        return self::SUCCESS;
    }
}
