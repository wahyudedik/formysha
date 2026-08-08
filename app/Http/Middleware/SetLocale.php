<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * Determine the locale from session, query parameter, user preference,
     * or default config. Then set it for the application and Carbon.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);

        App::setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }

    /**
     * Resolve the locale to use for the request.
     */
    private function resolveLocale(Request $request): string
    {
        $availableLocales = ['id', 'en'];
        $defaultLocale = config('app.locale', 'id');

        // 1. Check query parameter first
        if ($request->query('locale') && in_array($request->query('locale'), $availableLocales, true)) {
            $locale = $request->query('locale');
            session(['locale' => $locale]);

            return $locale;
        }

        // 2. Check session
        if ($request->session()->has('locale') && in_array($request->session()->get('locale'), $availableLocales, true)) {
            return $request->session()->get('locale');
        }

        // 3. Check user preference
        $user = $request->user();
        if ($user && property_exists($user, 'getAttributes') && isset($user->language) && in_array($user->language, $availableLocales, true)) {
            session(['locale' => $user->language]);

            return $user->language;
        }

        // 4. Check Accept-Language header
        $browserLocale = $request->getPreferredLanguage($availableLocales);
        if ($browserLocale && in_array($browserLocale, $availableLocales, true)) {
            session(['locale' => $browserLocale]);

            return $browserLocale;
        }

        // 5. Default
        return $defaultLocale;
    }
}
