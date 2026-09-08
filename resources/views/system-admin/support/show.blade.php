@extends('layout.platform')

@section('title', 'تذكرة دعم فني - مدير النظام')

@section('content')
<a href="{{ route('system-admin.support.index') }}" style="color:#64748b; text-decoration:none; font-weight:600; display:inline-block; margin-bottom:1rem;">
    <i class="fas fa-arrow-right"></i> رجوع لكل التذاكر
</a>

@php
    $statusColors = [
        'open' => ['bg' => '#fef3c7', 'text' => '#b45309'],
        'in_progress' => ['bg' => '#dbeafe', 'text' => '#1d4ed8'],
        'resolved' => ['bg' => '#d1fae5', 'text' => '#047857'],
        'closed' => ['bg' => '#f1f5f9', 'text' => '#64748b'],
    ];
    $sc = $statusColors[$ticket->status] ?? $statusColors['closed'];
@endphp

<div style="background: white; border-radius: 16px; padding: 1.75rem; box-shadow: 0 10px 25px rgba(0,0,0,0.06); margin-bottom: 1.5rem;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:0.75rem; margin-bottom: 1rem;">
        <div>
            <span class="badge" style="background: #eef2ff; color: #4338ca; font-weight: 700;">
                {{ \App\Models\SupportTicket::TYPES[$ticket->type] ?? $ticket->type }}
            </span>
            <h2 style="font-weight: 900; font-size: 1.4rem; margin: 0.5rem 0 0;">{{ $ticket->subject }}</h2>
        </div>
        <span class="badge" style="background: {{ $sc['bg'] }}; color: {{ $sc['text'] }}; font-weight: 700; font-size: 0.9rem;">
            {{ \App\Models\SupportTicket::STATUSES[$ticket->status] ?? $ticket->status }}
        </span>
    </div>

    <div style="display:flex; gap: 1.5rem; flex-wrap: wrap; font-size: 0.85rem; color: #64748b; margin-bottom: 1.25rem; padding-bottom: 1.25rem; border-bottom: 1px solid #f1f5f9;">
        <div><i class="fas fa-store"></i> {{ $ticket->tenant->name ?? '—' }} ({{ $ticket->tenant->domain ?? '' }})</div>
        <div><i class="fas fa-user"></i> {{ $ticket->submitter_name }}</div>
        <div><i class="fas fa-clock"></i> {{ $ticket->created_at->format('Y-m-d H:i') }}</div>
    </div>

    <p style="white-space: pre-wrap; line-height: 1.7; color: #1e293b;">{{ $ticket->message }}</p>

    @if ($ticket->admin_reply)
        <div style="margin-top: 1.5rem; background: #f8fafc; border-right: 4px solid #667eea; padding: 1rem 1.25rem; border-radius: 8px;">
            <div style="font-weight: 700; font-size: 0.85rem; color: #667eea; margin-bottom: 0.4rem;">
                <i class="fas fa-reply"></i> الرد المُرسل
            </div>
            <div style="white-space: pre-wrap;">{{ $ticket->admin_reply }}</div>
            @if ($ticket->admin_reply_at)
                <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.4rem;">{{ $ticket->admin_reply_at->format('Y-m-d H:i') }}</div>
            @endif
        </div>
    @endif
</div>

<div style="background: white; border-radius: 16px; padding: 1.75rem; box-shadow: 0 10px 25px rgba(0,0,0,0.06);">
    <h5 style="font-weight: 800; margin-bottom: 1rem;">
        <i class="fas fa-paper-plane"></i> {{ $ticket->admin_reply ? 'تعديل الرد' : 'إرسال رد' }}
    </h5>
    <form method="POST" action="{{ route('system-admin.support.reply', $ticket) }}">
        @csrf
        <div class="mb-3">
            <textarea name="admin_reply" rows="5" class="form-control" placeholder="اكتب الرد اللي رح يشوفه صاحب المعرض..." required>{{ old('admin_reply', $ticket->admin_reply) }}</textarea>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap: wrap; gap: 0.75rem;">
            <select name="status" class="form-select" style="width: auto; border-radius: 8px;">
                @foreach (\App\Models\SupportTicket::STATUSES as $value => $label)
                    <option value="{{ $value }}" {{ old('status', $ticket->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary" style="border-radius: 10px; font-weight: 700;">
                <i class="fas fa-check"></i> إرسال وتحديث الحالة
            </button>
        </div>
    </form>
</div>
@endsection
