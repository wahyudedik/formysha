<?php

namespace App\Http\Middleware;

use App\Models\Child;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureChildOwnership
{
    /**
     * Handle an incoming request.
     *
     * Ensures the authenticated user owns the child resource being accessed.
     * The {child} route parameter must be resolved to a Child model.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Child $child */
        $child = $request->route('child');

        if (! $child instanceof Child) {
            abort(404, 'Data anak tidak ditemukan.');
        }

        if ($child->user_id !== $request->user()->id) {
            abort(403, 'Anda tidak memiliki akses ke data anak ini.');
        }

        return $next($request);
    }
}
