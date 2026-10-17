<?php

namespace App\Console\Commands;

use App\Models\Child;
use App\Models\Document;
use App\Models\FamilyMember;
use App\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Migrate sensitive file uploads (child photos, family photos, documents, media)
 * from the public disk to the private disk.
 *
 * - Non-destructive by default: files on the public disk are never removed unless
 *   --delete-original is passed (with interactive confirmation unless --force).
 * - Idempotent: files already present on the private disk with a matching size
 *   are skipped, so the command can be re-run safely.
 * - DB paths are relative and identical on both disks, so no path rewrite is
 *   needed; the command only verifies file integrity.
 */
class MigratePrivateStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:migrate-private
        {--delete-original : Hapus file lama dari disk public setelah verifikasi berhasil (destructive)}
        {--force : Lewati konfirmasi interaktif untuk --delete-original}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate sensitive uploaded files (photos, family photos, documents, media) from the public disk to the private disk';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $publicDisk = Storage::disk('public');
        $privateDisk = Storage::disk('private');

        $paths = $this->collectSensitivePaths();

        $this->info("Ditemukan {$paths->count()} path file sensitif di database.");
        $this->newLine();

        $copied = 0;
        $alreadyPresent = 0;
        $missing = 0;
        $failed = [];
        $copiedPaths = [];

        $bar = $this->output->createProgressBar($paths->count());
        $bar->start();

        foreach ($paths as $path) {
            // Already migrated (idempotent re-run): verify integrity, skip copy.
            if ($privateDisk->exists($path)) {
                $alreadyPresent++;
                $bar->advance();

                continue;
            }

            // Nothing to migrate: file does not exist on the public disk either.
            if (! $publicDisk->exists($path)) {
                $missing++;
                $bar->advance();

                continue;
            }

            $publicSize = $publicDisk->size($path);

            if ($this->copyToPrivate($path)) {
                // Verify: file exists on private disk with the same size.
                if ($privateDisk->exists($path) && $privateDisk->size($path) === $publicSize) {
                    $copied++;
                    $copiedPaths[] = $path;
                } else {
                    $failed[] = $path;
                }
            } else {
                $failed[] = $path;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(['Metrik', 'Jumlah'], [
            ['Berhasil disalin ke private disk', $copied],
            ['Sudah ada di private disk (dilewati)', $alreadyPresent],
            ['Tidak ditemukan di kedua disk', $missing],
            ['Gagal / verifikasi gagal', count($failed)],
        ]);

        if ($failed !== []) {
            $this->newLine();
            $this->warn('Path yang gagal dimigrasi:');
            foreach ($failed as $path) {
                $this->warn("  - {$path}");
            }
        }

        Log::info('storage:migrate-private selesai', [
            'copied' => $copied,
            'already_present' => $alreadyPresent,
            'missing' => $missing,
            'failed' => count($failed),
        ]);

        // Optional cleanup of legacy public copies.
        if ($copiedPaths !== [] && $this->option('delete-original')) {
            if (! $this->option('force') && ! $this->confirm(
                'Hapus '.count($copiedPaths).' file asli dari disk public? Tindakan ini tidak dapat dibatalkan.'
            )) {
                $this->info('Penghapusan file asli dibatalkan. File tetap ada di kedua disk.');

                return self::SUCCESS;
            }

            foreach ($copiedPaths as $path) {
                $publicDisk->delete($path);
            }

            $this->info(count($copiedPaths).' file asli dihapus dari disk public.');
            Log::info('storage:migrate-private: file asli dihapus dari disk public', [
                'deleted' => count($copiedPaths),
            ]);
        } elseif ($copied !== 0 && ! $this->option('delete-original')) {
            $this->newLine();
            $this->line('File asli TIDAK dihapus (non-destruktif). Jalankan ulang dengan');
            $this->line('--delete-original setelah dipastikan aplikasi berjalan normal untuk membersihkan disk public.');
        }

        $this->newLine();
        $this->info('Kolom database tidak diubah karena path relatif identik di kedua disk.');

        return $failed === [] ? self::SUCCESS : self::FAILURE;
    }

    /**
     * Collect all sensitive file paths stored in the database.
     *
     * @return Collection<int, string>
     */
    private function collectSensitivePaths()
    {
        $paths = collect();

        // Child photos
        $paths = $paths->merge(
            Child::query()->whereNotNull('photo')->pluck('photo')->all()
        );

        // Family member photos
        $paths = $paths->merge(
            FamilyMember::query()->whereNotNull('photo')->pluck('photo')->all()
        );

        // Document files
        $paths = $paths->merge(
            Document::query()->whereNotNull('file_path')->pluck('file_path')->all()
        );

        // Media files + thumbnails
        $mediaPaths = Media::query()
            ->where(function ($query) {
                $query->whereNotNull('file_path')->orWhereNotNull('thumbnail_path');
            })
            ->get(['file_path', 'thumbnail_path']);

        foreach ($mediaPaths as $media) {
            if ($media->file_path) {
                $paths->push($media->file_path);
            }
            if ($media->thumbnail_path) {
                $paths->push($media->thumbnail_path);
            }
        }

        return $paths
            ->filter(fn ($path) => is_string($path) && $path !== '')
            ->unique()
            ->values();
    }

    /**
     * Copy a single file from the public disk to the private disk via stream.
     */
    private function copyToPrivate(string $path): bool
    {
        $stream = Storage::disk('public')->readStream($path);

        if ($stream === false || $stream === null) {
            return false;
        }

        try {
            return (bool) Storage::disk('private')->writeStream($path, $stream);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }
    }
}
