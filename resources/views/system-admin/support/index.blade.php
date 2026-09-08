@extends('layout.platform')

@section('title', 'الدعم الفني - مدير النظام')

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
    <h1 style="font-size: 1.8rem; font-weight: 900; color: #1e293b; margin: 0;">
        <i class="fas fa-headset"></i> تذاكر الدعم الفني
        @if ($openCount > 0)
            <span style="background:#ef4444; color:white; font-size:.85rem; font-weight:900; border-radius:20px; padding: 0.15rem 0.7rem; vertical-align:middle;">{{ $openCount }} مفتوحة</span>
        @endif
    </h1>

    <div style="display:flex; gap:0.5rem; flex-wrap: wrap;">
        @php $statuses = ['' => 'الكل'] + \App\Models\SupportTicket::STATUSES; @endphp
        @foreach ($statuses as $value => $label)
            <a href="{{ route('system-admin.support.index', $value ? ['status' => $value] : []) }}"
               class="btn btn-sm {{ $statusFilter === $value ? 'btn-primary' : 'btn-outline-secondary' }}"
               style="border-radius: 20px; font-weight: 600;">
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>

@if ($tickets->isEmpty())
    <div style="background: white; border-radius: 16px; padding: 3rem; text-align: center; color: #94a3b8; box-shadow: 0 10px 25px rgba(0,0,0,0.06);">
        لا توجد تذاكر دعم فني {{ $statusFilter ? 'بهالحالة' : '' }} حالياً
    </div>
@else
    <div style="background: white; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.06); overflow: hidden;">
        <table class="table mb-0" style="text-align: right;">
            <thead style="background: #f8fafc;">
                <tr>
                    <th style="padding: 1rem;">المعرض</th>
                    <th>النوع</th>
                    <th>الموضوع</th>
                    <th>مقدّم الطلب</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tickets as $ticket)
                    @php
                        $statusColors = [
                            'open' => ['bg' => '#fef3c7', 'text' => '#b45309'],
                            'in_progress' => ['bg' => '#dbeafe', 'text' => '#1d4ed8'],
                            'resolved' => ['bg' => '#d1fae5', 'text' => '#047857'],
                            'closed' => ['bg' => '#f1f5f9', 'text' => '#64748b'],
                        ];
                        $sc = $statusColors[$ticket->status] ?? $statusColors['closed'];
                    @endphp
                    <tr>
                        <td style="padding: 0.9rem 1rem; font-weight: 700;">{{ $ticket->tenant->name ?? '—' }}</td>
                        <td>{{ \App\Models\SupportTicket::TYPES[$ticket->type] ?? $ticket->type }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($ticket->subject, 40) }}</td>
                        <td>{{ $ticket->submitter_name }}</td>
                        <td>
                            <span class="badge" style="background: {{ $sc['bg'] }}; color: {{ $sc['text'] }}; font-weight: 700;">
                                {{ \App\Models\SupportTicket::STATUSES[$ticket->status] ?? $ticket->status }}
                            </span>
                        </td>
                        <td style="font-size: 0.85rem; color: #64748b;">{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('system-admin.support.show', $ticket) }}" class="btn btn-sm btn-outline-primary" style="border-radius: 8px;">
                                فتح
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
