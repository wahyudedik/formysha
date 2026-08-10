<?php

namespace App\Http\Controllers\Auth;

use App\Enums\TenantType;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TenantService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class FacilityRegisteredUserController extends Controller
{
    public function __construct(
        private TenantService $tenantService,
    ) {}

    /**
     * Display the facility registration view.
     */
    public function create(): View
    {
        $facilityTypes = collect(TenantType::cases())->filter(fn (TenantType $type) => $type->isB2B());

        return view('auth.register-facility', compact('facilityTypes'));
    }

    /**
     * Handle an incoming facility registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $facilityTypes = collect(TenantType::cases())->filter(fn (TenantType $type) => $type->isB2B());
        $validTypes = $facilityTypes->pluck('value')->implode(',');

        $request->validate([
            // Account fields
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            // Facility fields
            'facility_name' => ['required', 'string', 'max:255'],
            'facility_type' => ['required', 'string', 'in:'.$validTypes],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:50'],
            'license_number' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        // Create user account
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Create B2B tenant (facility)
        $tenant = $this->tenantService->createB2BTenant([
            'name' => $request->facility_name,
            'type' => $request->facility_type,
            'address' => $request->address,
            'phone' => $request->phone,
            'license_number' => $request->license_number,
            'description' => $request->description,
        ], $user);

        event(new Registered($user));

        Auth::login($user);

        // Set tenant in session
        $this->tenantService->switchTenant($tenant);

        return redirect(route('facility.dashboard', absolute: false));
    }
}
