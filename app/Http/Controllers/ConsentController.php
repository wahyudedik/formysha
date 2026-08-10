<?php

namespace App\Http\Controllers;

use App\Enums\ConsentType;
use App\Models\Child;
use App\Services\ConsentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsentController extends Controller
{
    public function __construct(
        private ConsentService $consentService,
    ) {}

    /**
     * Tampilkan halaman consent management untuk child.
     */
    public function index(Request $request, Child $child): View
    {
        $statuses = $this->consentService->getConsentStatuses($request->user(), $child);

        return view('consent.index', [
            'child' => $child,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Berikan atau revoke consent.
     */
    public function update(Request $request, Child $child): RedirectResponse
    {
        $validated = $request->validate([
            'consent_type' => ['required', 'string', 'in:data_collection,photo_sharing,medical_records,public_profile,data_export'],
            'action' => ['required', 'string', 'in:grant,revoke'],
        ]);

        $type = ConsentType::from($validated['consent_type']);
        $user = $request->user();

        if ($validated['action'] === 'grant') {
            $this->consentService->grant(
                $user,
                $child,
                $type,
                $request->input('notes'),
                $request->ip(),
                $request->userAgent(),
            );

            return redirect()->route('consent.index', $child)
                ->with('success', __('Consent ":type" berhasil diberikan.', ['type' => $type->label()]));
        }

        $this->consentService->revoke($user, $child, $type);

        return redirect()->route('consent.index', $child)
            ->with('success', __('Consent ":type" berhasil dicabut.', ['type' => $type->label()]));
    }
}
