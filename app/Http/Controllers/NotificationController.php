<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications.
     */
    public function index(Request $request): View
    {
        $notifications = Notification::where('user_id', $request->user()->id)
            ->with('child')
            ->latest()
            ->paginate(20);

        $unreadCount = Notification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->count();

        return view('notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Request $request, Notification $notification): RedirectResponse
    {
        abort_if($notification->user_id !== $request->user()->id, 403);

        $notification->markAsRead();

        if ($notification->action_url) {
            return redirect($this->resolveInternalRedirect($notification->action_url));
        }

        return redirect()->route('notifications.index');
    }

    /**
     * Resolve a safe internal redirect target from a stored action URL.
     *
     * Mencegah open redirect: hanya path relatif (mis. "/children") atau URL
     * absolut dengan host yang cocok dengan config('app.url') yang diizinkan.
     * URL eksternal/invalid jatuh kembali ke dashboard.
     */
    private function resolveInternalRedirect(?string $url): string
    {
        if (! $url) {
            return route('dashboard');
        }

        // Protocol-relative ("//evil.com") dan backslash tricks diblokir browser
        // sebagai redirect eksternal, jadi tolak di sini.
        if (Str::startsWith($url, '//') || Str::contains($url, '\\')) {
            return route('dashboard');
        }

        // Path relatif internal (mis. "/children")
        if (Str::startsWith($url, '/')) {
            return $url;
        }

        // URL absolut hanya diizinkan jika host-nya sama dengan app.url
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);
        $urlHost = parse_url($url, PHP_URL_HOST);

        if ($appHost && $urlHost && strcasecmp($appHost, $urlHost) === 0) {
            return $url;
        }

        return route('dashboard');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request): RedirectResponse
    {
        Notification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return redirect()->route('notifications.index')
            ->with('status', __('status.notifications_all_read'));
    }

    /**
     * Remove the specified notification.
     */
    public function destroy(Request $request, Notification $notification): RedirectResponse
    {
        abort_if($notification->user_id !== $request->user()->id, 403);

        $notification->delete();

        return redirect()->route('notifications.index')
            ->with('status', __('status.notification_deleted'));
    }
}
