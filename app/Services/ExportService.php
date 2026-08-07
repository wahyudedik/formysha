<?php

namespace App\Services;

use App\Models\Child;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class ExportService
{
    /**
     * Generate PDF for child profile.
     */
    public function exportChildProfile(Child $child): Response
    {
        $child->load(['familyMembers', 'healthRecords' => fn ($q) => $q->latest('date'), 'growths' => fn ($q) => $q->latest('measured_at')]);

        $pdf = Pdf::loadView('exports.child-profile', [
            'child' => $child,
            'title' => 'Profil Anak — '.($child->nickname ?? $child->name),
        ]);

        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('isFontSubsettingEnabled', true);
        $pdf->setOption('isRemoteEnabled', false);

        $filename = 'profil-'.$child->slug.'-'.now()->format('Y-m-d').'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate PDF for child health records.
     */
    public function exportHealthRecords(Child $child): Response
    {
        $child->load(['healthRecords' => fn ($q) => $q->latest('date')]);

        $pdf = Pdf::loadView('exports.health-records', [
            'child' => $child,
            'healthRecords' => $child->healthRecords,
            'title' => 'Riwayat Kesehatan — '.($child->nickname ?? $child->name),
        ]);

        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('isFontSubsettingEnabled', true);
        $pdf->setOption('isRemoteEnabled', false);

        $filename = 'kesehatan-'.$child->slug.'-'.now()->format('Y-m-d').'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate PDF for child growth records.
     */
    public function exportGrowthRecords(Child $child): Response
    {
        $child->load(['growths' => fn ($q) => $q->latest('measured_at')]);

        $pdf = Pdf::loadView('exports.growth-records', [
            'child' => $child,
            'growths' => $child->growths,
            'title' => 'Riwayat Pertumbuhan — '.($child->nickname ?? $child->name),
        ]);

        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('isFontSubsettingEnabled', true);
        $pdf->setOption('isRemoteEnabled', false);

        $filename = 'pertumbuhan-'.$child->slug.'-'.now()->format('Y-m-d').'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export all child data as a ZIP archive containing PDFs.
     */
    public function exportChildZip(Child $child): BinaryFileResponse|Response
    {
        $child->load([
            'familyMembers',
            'timelines',
            'albums.media',
            'diaries',
            'documents',
            'healthRecords',
            'events',
            'growths',
        ]);

        $zip = new ZipArchive;
        $tempFile = tempnam(sys_get_temp_dir(), 'formysha_export_');

        if ($zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Gagal membuat file ZIP.');
        }

        $slug = $child->slug;
        $date = now()->format('Y-m-d');

        // 1. Profil Anak PDF
        $profilePdf = Pdf::loadView('exports.child-profile', [
            'child' => $child,
            'title' => 'Profil Anak — '.($child->nickname ?? $child->name),
        ])->setPaper('a4', 'portrait')->output();
        $zip->addFromString("profil-{$slug}-{$date}.pdf", $profilePdf);

        // 2. Riwayat Kesehatan PDF
        $healthPdf = Pdf::loadView('exports.health-records', [
            'child' => $child,
            'healthRecords' => $child->healthRecords,
            'title' => 'Riwayat Kesehatan — '.($child->nickname ?? $child->name),
        ])->setPaper('a4', 'portrait')->output();
        $zip->addFromString("kesehatan-{$slug}-{$date}.pdf", $healthPdf);

        // 3. Riwayat Pertumbuhan PDF
        $growthPdf = Pdf::loadView('exports.growth-records', [
            'child' => $child,
            'growths' => $child->growths,
            'title' => 'Riwayat Pertumbuhan — '.($child->nickname ?? $child->name),
        ])->setPaper('a4', 'portrait')->output();
        $zip->addFromString("pertumbuhan-{$slug}-{$date}.pdf", $growthPdf);

        // 4. Timeline JSON
        $timelineData = $child->timelines->map(fn ($t) => [
            'title' => $t->title,
            'description' => $t->description,
            'event_date' => $t->event_date,
            'mood' => $t->mood,
            'location' => $t->location,
            'tags' => $t->tags,
            'is_featured' => $t->is_featured,
        ])->toArray();
        $zip->addFromString("timeline-{$slug}.json", json_encode($timelineData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // 5. Diary JSON
        $diaryData = $child->diaries->map(fn ($d) => [
            'title' => $d->title,
            'content' => $d->content,
            'diary_date' => $d->diary_date,
            'mood' => $d->mood,
            'weather' => $d->weather,
        ])->toArray();
        $zip->addFromString("diary-{$slug}.json", json_encode($diaryData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // 6. Dokumen metadata JSON
        $docData = $child->documents->map(fn ($d) => [
            'name' => $d->name,
            'type' => $d->type,
            'description' => $d->description,
            'file_path' => $d->file_path,
            'file_size' => $d->file_size,
            'issued_date' => $d->issued_date,
            'expiry_date' => $d->expiry_date,
        ])->toArray();
        $zip->addFromString("dokumen-{$slug}.json", json_encode($docData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $zip->close();

        $filename = "formysha-{$slug}-{$date}.zip";

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }
}
