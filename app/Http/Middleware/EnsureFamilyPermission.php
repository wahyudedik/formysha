<?php

namespace App\Http\Middleware;

use App\Enums\FamilyMemberPermission;
use App\Models\Child;
use App\Models\FamilyMember;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFamilyPermission
{
    /**
     * Check if the authenticated user has the required permission level
     * for the family member's child data.
     *
     * Parameterized: family.permission:view, family.permission:edit, family.permission:admin
     */
    public function handle(Request $request, Closure $next, string $requiredLevel): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401, 'Silakan masuk terlebih dahulu.');
        }

        // Super admin and tenant admin bypass permission check
        if (in_array($user->role, ['super_admin', 'tenant_admin'])) {
            return $next($request);
        }

        /** @var Child|null $child */
        $child = $request->route('child');

        // If user owns the child, they have full access
        if ($child && $child->user_id === $user->id) {
            return $next($request);
        }

        // Check if the user is a linked family member with sufficient permissions
        $required = FamilyMemberPermission::tryFrom($requiredLevel);

        if (! $required) {
            abort(400, 'Level permission tidak valid.');
        }

        $familyMember = FamilyMember::where('child_id', $child?->id)
            ->where('user_id', $user->id)
            ->first();

        if (! $familyMember) {
            abort(403, 'Anda tidak memiliki akses ke data anak ini.');
        }

        if (! $familyMember->hasPermission($required)) {
            abort(403, 'Anda tidak memiliki permission yang cukup untuk melakukan aksi ini.');
        }

        return $next($request);
    }
}
