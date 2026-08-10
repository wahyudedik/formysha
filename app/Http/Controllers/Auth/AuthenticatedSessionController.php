<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\TenantService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Set tenant in session from user's tenant_id if not already set
        $user = $request->user();
        if ($user && $user->tenant_id && ! session()->has('tenant_id')) {
            $tenantService = app(TenantService::class);
            $tenant = $user->tenant;
            if ($tenant) {
                $tenantService->switchTenant($tenant);
            }
        }

        // Smart redirect based on user type: B2B facility admins go to facility dashboard
        if ($user && $user->isFacilityAdmin()) {
            return redirect()->intended(route('facility.dashboard', absolute: false));
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
