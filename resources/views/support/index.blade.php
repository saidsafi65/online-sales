@extends('layout.app')

@section('title', 'الدعم الفني')

@section('content')
    <div class="welcome-section">
        <h1 class="welcome-title"><i class="fas fa-headset"></i> الدعم الفني</h1>
        <p class="welcome-subtitle">تواصل معنا في حال وجود خطأ، مشكلة، أو طلب تعديل معيّن على النظام</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('support.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> تقديم طلب جديد
        </a>
    </div>

    @if ($tickets->isEmpty())
        <div class="alert alert-info">
            ما قدّمت أي طلب دعم فني لسا.
        </div>
    @else
        <div class="d-flex flex-column gap-3">
            @foreach ($tickets as $ticket)
                <div class="card" style="border-radius: 14px; border: 1px solid #e2e8f0;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                            <div>
                                <span class="badge" style="background: #eef2ff; color: #4338ca; font-weight: 700;">
                                    {{ \App\Models\SupportTicket::TYPES[$ticket->type] ?? $ticket->type }}
                                </span>
                                <strong class="ms-2" style="font-size: 1.05rem;">{{ $ticket->subject }}</strong>
                            </div>
                            @php
                                $statusColors = [
                                    'open' => ['bg' => '#fef3c7', 'text' => '#b45309'],
                                    'in_progress' => ['bg' => '#dbeafe', 'text' => '#1d4ed8'],
                                    'resolved' => ['bg' => '#d1fae5', 'text' => '#047857'],
                                    'closed' => ['bg' => '#f1f5f9', 'text' => '#64748b'],
                                ];
                                $sc = $statusColors[$ticket->status] ?? $statusColors['closed'];
                            @endphp
                            <span class="badge" style="background: {{ $sc['bg'] }}; color: {{ $sc['text'] }}; font-weight: 700;">
                                {{ \App\Models\SupportTicket::STATUSES[$ticket->status] ?? $ticket->status }}
                            </span>
                        </div>

                        <p style="color: #475569; white-space: pre-wrap;">{{ $ticket->message }}</p>

                        <div style="font-size: 0.8rem; color: #94a3b8;">
                            بتاريخ {{ $ticket->created_at->format('Y-m-d H:i') }} — بواسطة {{ $ticket->submitter_name }}
                        </div>

                        @if ($ticket->admin_reply)
                            <div style="margin-top: 1rem; background: #f8fafc; border-right: 4px solid #667eea; padding: 0.8rem 1.1rem; border-radius: 8px;">
                                <div style="font-weight: 700; font-size: 0.85rem; color: #667eea; margin-bottom: 0.3rem;">
                                    <i class="fas fa-reply"></i> رد الدعم الفني
                                </div>
                                <div style="white-space: pre-wrap;">{{ $ticket->admin_reply }}</div>
                                @if ($ticket->admin_reply_at)
                                    <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.3rem;">
                                        {{ $ticket->admin_reply_at->format('Y-m-d H:i') }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
