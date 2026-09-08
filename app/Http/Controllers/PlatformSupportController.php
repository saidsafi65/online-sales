<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\Request;

class PlatformSupportController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::with('tenant')->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $tickets = $query->get();
        $openCount = SupportTicket::whereIn('status', ['open', 'in_progress'])->count();

        return view('system-admin.support.index', [
            'tickets' => $tickets,
            'openCount' => $openCount,
            'statusFilter' => $request->input('status', ''),
        ]);
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load('tenant');

        return view('system-admin.support.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'admin_reply' => 'required|string|max:3000',
            'status' => 'required|in:' . implode(',', array_keys(SupportTicket::STATUSES)),
        ], [
            'admin_reply.required' => 'اكتب نص الرد',
            'status.required' => 'اختر حالة التذكرة',
        ]);

        $ticket->update([
            'admin_reply' => $validated['admin_reply'],
            'admin_reply_at' => now(),
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'تم إرسال الرد وتحديث حالة التذكرة');
    }

    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(SupportTicket::STATUSES)),
        ]);

        $ticket->update(['status' => $validated['status']]);

        return back()->with('success', 'تم تحديث حالة التذكرة');
    }
}
