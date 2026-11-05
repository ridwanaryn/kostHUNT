<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user === null) {
            abort(401);
        }

        $notifications = $user->notifications()
            ->latest()
            ->paginate((int) config('kosthunt.pagination.per_page'));

        return view('notifications.index', ['notifications' => $notifications]);
    }

    public function read(Request $request, string $id): RedirectResponse
    {
        $user = $request->user();

        if ($user === null) {
            abort(401);
        }

        $notification = $user->notifications()->whereKey($id)->first();

        if ($notification !== null) {
            $notification->markAsRead();
        }

        return back();
    }

    public function readAll(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user === null) {
            abort(401);
        }

        $user->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('status', 'All notifications marked as read.');
    }
}
