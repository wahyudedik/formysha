<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    /**
     * Switch the application locale.
     */
    public function switch(Request $request, string $locale): RedirectResponse|JsonResponse
    {
        $availableLocales = ['id', 'en'];

        if (! in_array($locale, $availableLocales, true)) {
            $message = 'Bahasa tidak didukung.';

            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => $message], 422)
                : back()->with('error', $message);
        }

        // Store in session
        session(['locale' => $locale]);

        // Update user preference if authenticated
        $user = $request->user();
        if ($user) {
            $user->language = $locale;
            $user->saveQuietly();
        }

        // Set locale for current request
        App::setLocale($locale);

        $message = $locale === 'id' ? 'Bahasa berhasil diubah ke Indonesia.' : 'Language switched to English.';

        return $request->expectsJson()
            ? response()->json(['success' => true, 'message' => $message, 'locale' => $locale])
            : back()->with('success', $message);
    }
}
