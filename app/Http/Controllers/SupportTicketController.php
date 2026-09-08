<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Notifications\NewSupportTicketNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SupportTicketController extends Controller
{
    public function index()
    {
        $tenant = app('currentTenant');

        $tickets = SupportTicket::where('tenant_id', $tenant->id)
            ->orderByDesc('created_at')
            ->get();

        return view('support.index', compact('tickets'));
    }

    public function create()
    {
        return view('support.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:' . implode(',', array_keys(SupportTicket::TYPES)),
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:3000',
        ], [
            'type.required' => 'اختر نوع الطلب',
            'type.in' => 'نوع الطلب غير صحيح',
            'subject.required' => 'اكتب عنوان مختصر للموضوع',
            'subject.max' => 'العنوان طويل كتير (الحد الأقصى 150 حرف)',
            'message.required' => 'اكتب تفاصيل المشكلة أو الطلب',
            'message.max' => 'التفاصيل طويلة كتير (الحد الأقصى 3000 حرف)',
        ]);

        $tenant = app('currentTenant');

        $ticket = SupportTicket::create([
            'tenant_id' => $tenant->id,
            'local_user_id' => auth()->id(),
            'submitter_name' => auth()->user()->name,
            'type' => $validated['type'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'open',
        ]);

        $alertEmail = config('services.error_alert.email');
        if ($alertEmail) {
            try {
                Notification::route('mail', $alertEmail)->notify(new NewSupportTicketNotification($ticket));
            } catch (\Throwable $e) {
                // ما بدنا فشل الإيميل يمنع تسجيل التذكرة نفسها — هي محفوظة بقاعدة البيانات
                // أصلاً وبتظهر بلوحة الدعم الفني حتى لو الإشعار فشل.
                Log::warning('Failed to send support ticket notification email: ' . $e->getMessage());
            }
        }

        return redirect()->route('support.index')->with('success', 'تم إرسال طلبك بنجاح، وبيتم الرد عليك قريباً');
    }
}
