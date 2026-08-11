<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Services\AccountDeletionService;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ErasureController extends Controller
{
    public function __construct(
        private AccountDeletionService $deletionService,
        private AuditService $auditService,
    ) {}

    /**
     * Tampilkan halaman ringkasan data untuk hak penghapusan.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $children = $user->children()->get();
        $userSummary = $this->deletionService->getUserDataSummary($user);

        $childSummaries = [];
        foreach ($children as $child) {
            $childSummaries[$child->id] = [
                'child' => $child,
                'summary' => $this->deletionService->getChildDataSummary($child),
            ];
        }

        return view('erasure.index', [
            'user' => $user,
            'children' => $children,
            'userSummary' => $userSummary,
            'childSummaries' => $childSummaries,
        ]);
    }

    /**
     * Hapus data milik satu child.
     */
    public function destroyChild(Request $request, Child $child): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        $childData = $this->deletionService->getChildDataSummary($child);
        $this->deletionService->deleteChildData($child);

        $this->auditService->log(
            'erasure.child_deleted',
            $child,
            $request->user(),
            ['child_name' => $child->name],
            ['data_summary' => $childData],
        );

        return redirect()->route('erasure.index')
            ->with('success', __('status.child_data_deleted', ['name' => $child->name]));
    }

    /**
     * Hapus seluruh akun dan data pengguna.
     */
    public function destroyAccount(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'current_password'],
            'confirmation' => ['required', 'string', 'in:HAPUS AKUN SAYA'],
        ]);

        $user = $request->user();
        $userData = $this->deletionService->getUserDataSummary($user);

        // Log BEFORE deleting user — FK constraint requires user to exist
        $this->auditService->log(
            'erasure.account_deleted',
            $user,
            $user,
            ['user_name' => $user->name, 'email' => $user->email],
            ['data_summary' => $userData],
        );

        $this->deletionService->deleteUserData($user);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('status', __('status.account_deleted_permanently'));
    }
}
