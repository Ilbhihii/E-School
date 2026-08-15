<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $filter = $request->query('filter', 'all');

        $query = $user->notifications()->latest();

        if ($filter === 'unread') {
            $query->whereNull('read_at');
        }

        $notifications = $query->paginate(15)->withQueryString();

        $notificationLayout = match ($user->role) {
            'admin' => 'layouts.admin',
            'prof' => 'layouts.prof',
            'student' => 'layouts.student',
            'parent' => 'layouts.parent',
            default => 'layouts.app',
        };

        return view('notifications.index', compact(
            'notifications',
            'notificationLayout',
            'filter'
        ));
    }

    public function feed(Request $request): JsonResponse
    {
        $user = $request->user();

        $notifications = $user->notifications()
            ->latest()
            ->limit(8)
            ->get()
            ->map(function (DatabaseNotification $notification) {
                $data = $notification->data ?? [];

                return [
                    'id' => $notification->id,
                    'title' => (string) ($data['title'] ?? 'Notification'),
                    'message' => (string) ($data['message'] ?? ''),
                    'category' => (string) ($data['category'] ?? 'general'),
                    'icon' => $this->safeIcon((string) ($data['icon'] ?? 'bi bi-bell-fill')),
                    'priority' => (string) ($data['priority'] ?? 'normal'),
                    'is_read' => $notification->read_at !== null,
                    'time' => optional($notification->created_at)->diffForHumans() ?? '',
                    'open_url' => route('notifications.open', $notification->id),
                ];
            });

        return response()->json([
            'unread_count' => $user->unreadNotifications()->count(),
            'notifications' => $notifications,
        ]);
    }

    public function open(Request $request, string $notification): RedirectResponse
    {
        $item = $this->findOwnedNotification($request, $notification);

        if ($item->read_at === null) {
            $item->markAsRead();
        }

        return redirect()->to($this->safeDestination($request, $item));
    }

    public function markAsRead(Request $request, string $notification): RedirectResponse
    {
        $item = $this->findOwnedNotification($request, $notification);
        $item->markAsRead();

        return back()->with('success', 'Notification marquée comme lue.');
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    public function destroy(Request $request, string $notification): RedirectResponse
    {
        $item = $this->findOwnedNotification($request, $notification);
        $item->delete();

        return back()->with('success', 'Notification supprimée.');
    }

    public function clearRead(Request $request): RedirectResponse
    {
        $request->user()->readNotifications()->delete();

        return back()->with('success', 'Les notifications déjà lues ont été supprimées.');
    }

    protected function findOwnedNotification(Request $request, string $id): DatabaseNotification
    {
        return $request->user()
            ->notifications()
            ->whereKey($id)
            ->firstOrFail();
    }

    protected function safeDestination(
        Request $request,
        DatabaseNotification $notification
    ): string {
        $destination = data_get($notification->data, 'url');

        $fallback = method_exists($request->user(), 'dashboardRoute')
            ? $request->user()->dashboardRoute()
            : route('home');

        if (!is_string($destination) || trim($destination) === '') {
            return $fallback;
        }

        $destination = trim($destination);

        if (str_starts_with($destination, '/')) {
            return url($destination);
        }

        $parts = parse_url($destination);

        if ($parts === false) {
            return $fallback;
        }

        if (!isset($parts['host'])) {
            return url('/' . ltrim($destination, '/'));
        }

        if (strcasecmp((string) $parts['host'], $request->getHost()) !== 0) {
            return $fallback;
        }

        return $destination;
    }

    protected function safeIcon(string $icon): string
    {
        return preg_match('/^bi bi-[a-z0-9-]+$/i', $icon)
            ? $icon
            : 'bi bi-bell-fill';
    }
}
