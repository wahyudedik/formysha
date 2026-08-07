<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Services\ExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function __construct(
        private ExportService $exportService,
    ) {}

    /**
     * Export child profile as PDF.
     */
    public function childProfile(Child $child): Response|RedirectResponse
    {
        try {
            return $this->exportService->exportChildProfile($child);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat PDF profil: '.$e->getMessage());
        }
    }

    /**
     * Export child health records as PDF.
     */
    public function healthRecords(Child $child): Response|RedirectResponse
    {
        try {
            return $this->exportService->exportHealthRecords($child);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat PDF kesehatan: '.$e->getMessage());
        }
    }

    /**
     * Export child growth records as PDF.
     */
    public function growthRecords(Child $child): Response|RedirectResponse
    {
        try {
            return $this->exportService->exportGrowthRecords($child);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat PDF pertumbuhan: '.$e->getMessage());
        }
    }

    /**
     * Export all child data as ZIP archive.
     */
    public function childZip(Child $child): BinaryFileResponse|RedirectResponse
    {
        try {
            return $this->exportService->exportChildZip($child);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat ZIP: '.$e->getMessage());
        }
    }
}
