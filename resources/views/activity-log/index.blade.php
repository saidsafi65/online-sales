@extends('layout.app')

@section('title', 'سجل النشاطات')

@section('content')
    <div class="container py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="mb-0">سجل النشاطات</h1>
            @if ($isSingleRecordView)
                <a href="{{ route('activity-log.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-list me-2"></i>عرض جميع السجلات
                </a>
            @endif
        </div>

        @if ($isSingleRecordView)
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>عرض السجل الخاص بهاي العملية فقط. اضغط "عرض جميع السجلات" لتشوف كل الحركات.
            </div>
        @endif

        <div class="card filter-bar mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('activity-log.index') }}" class="row g-2 align-items-end">
                    <div class="col-auto">
                        <label class="form-label small mb-1">من تاريخ</label>
                        <input type="date" class="form-control form-control-sm" name="start_date" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-auto">
                        <label class="form-label small mb-1">إلى تاريخ</label>
                        <input type="date" class="form-control form-control-sm" name="end_date" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-auto">
                        <label class="form-label small mb-1">نوع العنصر</label>
                        <select name="model_type" class="form-select form-select-sm">
                            <option value="">الكل</option>
                            @foreach ($modelTypes as $mt)
                                <option value="{{ $mt }}" {{ request('model_type') === $mt ? 'selected' : '' }}>
                                    {{ $formatter->modelLabel($mt) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="form-label small mb-1">الموظف</label>
                        <select name="actor_id" class="form-select form-select-sm">
                            <option value="">الكل</option>
                            @foreach ($actors as $a)
                                <option value="{{ $a->actor_id }}" {{ (string) request('actor_id') === (string) $a->actor_id ? 'selected' : '' }}>
                                    {{ $a->actor_name ?? ('#' . $a->actor_id) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="form-label small mb-1">الإجراء</label>
                        <select name="action" class="form-select form-select-sm">
                            <option value="">الكل</option>
                            <option value="created" {{ request('action') === 'created' ? 'selected' : '' }}>إضافة</option>
                            <option value="updated" {{ request('action') === 'updated' ? 'selected' : '' }}>تعديل</option>
                            <option value="deleted" {{ request('action') === 'deleted' ? 'selected' : '' }}>حذف</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-filter me-1"></i>تصفية
                        </button>
                        <a href="{{ route('activity-log.index') }}" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-times me-1"></i>مسح
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>التاريخ</th>
                            <th>القائم بالعملية</th>
                            <th>الإجراء</th>
                            <th>العنصر</th>
                            <th>التفاصيل</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            @php
                                $badgeClass = match ($log->action) {
                                    'created' => 'bg-success',
                                    'updated' => 'bg-warning text-dark',
                                    'deleted' => 'bg-danger',
                                    default => 'bg-secondary',
                                };
                                $updateRows = $log->action === 'updated' ? $formatter->formatChangesForUpdate($log->changes) : [];
                                $createRows = $log->action === 'created' ? $formatter->formatAttributesForCreate($log->changes) : [];
                            @endphp
                            <tr>
                                <td class="text-nowrap small text-muted">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                <td>{{ $log->actor_name ?? '—' }}</td>
                                <td><span class="badge {{ $badgeClass }}">{{ $formatter->actionLabel($log->action) }}</span></td>
                                <td>
                                    <div class="fw-bold small">{{ $formatter->modelLabel($log->model_type) }}</div>
                                    <div class="text-muted small">{{ $log->model_label }}</div>
                                </td>
                                <td>
                                    @if ($log->action === 'updated' && count($updateRows))
                                        <ul class="list-unstyled mb-0 small">
                                            @foreach ($updateRows as $row)
                                                <li>
                                                    <strong>{{ $row['field'] }}:</strong>
                                                    <span class="text-danger text-decoration-line-through">{{ $row['old'] }}</span>
                                                    ←
                                                    <span class="text-success">{{ $row['new'] }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @elseif ($log->action === 'created' && count($createRows))
                                        <ul class="list-unstyled mb-0 small">
                                            @foreach ($createRows as $row)
                                                <li><strong>{{ $row['field'] }}:</strong> {{ $row['value'] }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">ما في سجلات مطابقة</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $logs->links() }}
        </div>
    </div>
@endsection
