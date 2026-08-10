<?php

namespace App\Http\Middleware;

use App\Enums\StaffRole;
use App\Models\Staff;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaffRole
{
    /**
     * Check if the authenticated user has the required staff role(s).
     *
     * Parameterized middleware: staff.role:doctor, staff.role:nurse, etc.
     * Special value: staff.role:admin matches staff_admin role.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401, 'Silakan masuk terlebih dahulu.');
        }

        // Super admin bypasses all role checks
        if ($user->role === 'super_admin') {
            return $next($request);
        }

        $tenantId = $request->route('tenant')?->id ?? $user->tenant_id;

        if (! $tenantId) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        // Find staff record for this user in this tenant
        $staff = Staff::where('user_id', $user->id)
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->first();

        if (! $staff) {
            abort(403, 'Anda tidak memiliki peran staf di fasilitas ini.');
        }

        // Check if the staff role matches any of the required roles
        /** @var StaffRole $staffRole */
        $staffRole = $staff->staff_role;
        $staffRoleValue = $staffRole->value;

        // Also accept 'admin' as alias for 'staff_admin'
        $allowedRoles = collect($roles)->flatMap(function (string $role) {
            return $role === 'admin' ? ['admin', 'staff_admin'] : [$role];
        })->values()->all();

        if (! in_array($staffRoleValue, $allowedRoles)) {
            abort(403, 'Anda tidak memiliki peran yang diperlukan untuk akses ini.');
        }

        // Attach staff to request for downstream use
        $request->attributes->set('staff', $staff);

        return $next($request);
    }
}
