@extends('layout.app')

@section('title', 'سجل الرسائل المرسلة')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📱 سجل الرسائل المرسلة</h2>
    </div>

    <div class="alert alert-warning">
        <i class="fas fa-triangle-exclamation me-1"></i>
        النظام حالياً بوضع تجريبي (وهمي) — الرسائل يلي حالتها "وهمي" ما انبعتت فعلياً لجوال حقيقي، بس مسجّلة هون حتى تتأكد شكل الرسالة صحيح قبل ما نربط شركة SMS حقيقية.
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الجوال</th>
                            <th>نص الرسالة</th>
                            <th>الغرض</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            @php
                                $statusColors = ['mock' => 'secondary', 'sent' => 'success', 'failed' => 'danger'];
                            @endphp
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td dir="ltr" class="text-end">{{ $log->phone }}</td>
                                <td style="max-width: 420px; white-space: pre-wrap;">{{ $log->message }}</td>
                                <td>{{ $log->purpose ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $statusColors[$log->status] }}">
                                        {{ \App\Models\SmsLog::STATUS_LABELS[$log->status] }}
                                    </span>
                                </td>
                                <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">ما في رسائل مسجّلة بعد</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
