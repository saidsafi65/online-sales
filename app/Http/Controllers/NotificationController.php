<?php

namespace App\Http\Controllers;

use App\Models\StoreNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $allowedTypes = [];
        if ($user->canViewSection('products')) {
            $allowedTypes[] = 'low_stock';
            $allowedTypes[] = 'out_of_stock';
            $allowedTypes[] = 'product_created';
            $allowedTypes[] = 'product_updated';
            $allowedTypes[] = 'product_deleted';
            $allowedTypes[] = 'laptop_created';
            $allowedTypes[] = 'laptop_updated';
            $allowedTypes[] = 'laptop_deleted';
        }
        if ($user->canViewSection('online_orders')) {
            $allowedTypes[] = 'new_order';
        }
        if ($user->canViewSection('catalog')) {
            $allowedTypes[] = 'catalog_created';
            $allowedTypes[] = 'catalog_updated';
            $allowedTypes[] = 'catalog_deleted';
        }

        if (empty($allowedTypes)) {
            return response()->json(['unread_count' => 0, 'notifications' => []]);
        }

        $base = StoreNotification::visibleTo($user)->whereIn('type', $allowedTypes);

        $unreadCount = (clone $base)->unread()->count();
        $notifications = (clone $base)->latest()->limit(20)->get();

        return response()->json([
            'unread_count'  => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    public function markRead(Request $request, StoreNotification $notification)
    {
        $user = $request->user();

        if (!$user->isAdmin() && $notification->branch_id && $notification->branch_id != $user->branch_id) {
            abort(403);
        }

        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        return response()->json(['status' => 'ok']);
    }

    public function markAllRead(Request $request)
    {
        $user = $request->user();
        StoreNotification::visibleTo($user)->unread()->update(['read_at' => now()]);

        return response()->json(['status' => 'ok']);
    }
}