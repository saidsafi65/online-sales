@props([
    'action',
    'showSearch' => false,
    'searchPlaceholder' => 'ابحث...',
])

<div class="card filter-bar mb-4">
    <div class="card-body">
        <form method="GET" action="{{ $action }}" class="filter-bar-form">
            <div class="filter-bar-presets mb-3">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-preset="today">اليوم</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-preset="week">هذا الأسبوع</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-preset="month">هذا الشهر</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-preset="year">هذه السنة</button>
                <a href="{{ strtok($action, '?') }}" class="btn btn-sm btn-outline-danger float-end">
                    <i class="fas fa-times me-1"></i>مسح الفلاتر
                </a>
            </div>

            <div class="row g-2 align-items-end">
                <div class="col-auto">
                    <label class="form-label small mb-1">من تاريخ</label>
                    <input type="date" class="form-control form-control-sm" name="start_date" id="filter-start-date" value="{{ request('start_date') }}">
                </div>
                <div class="col-auto">
                    <label class="form-label small mb-1">إلى تاريخ</label>
                    <input type="date" class="form-control form-control-sm" name="end_date" id="filter-end-date" value="{{ request('end_date') }}">
                </div>

                @if ($showSearch)
                    <div class="col-auto flex-grow-1">
                        <label class="form-label small mb-1">بحث</label>
                        <input type="text" class="form-control form-control-sm" name="search" value="{{ request('search') }}" placeholder="{{ $searchPlaceholder }}">
                    </div>
                @endif

                {{ $extra ?? '' }}

                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter me-1"></i>تصفية
                    </button>
                </div>
            </div>

            @isset($collapsible)
                <details class="filter-bar-more mt-3">
                    <summary class="text-muted small">فلاتر إضافية</summary>
                    <div class="row g-2 align-items-end mt-2">
                        {{ $collapsible }}
                    </div>
                </details>
            @endisset
        </form>
    </div>
</div>

@once
    @push('scripts')
    <script>
        (function () {
            function fmt(d) {
                const y = d.getFullYear();
                const m = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return `${y}-${m}-${day}`;
            }

            document.querySelectorAll('.filter-bar-form').forEach(function (form) {
                const startInput = form.querySelector('#filter-start-date') || form.querySelector('[name="start_date"]');
                const endInput = form.querySelector('#filter-end-date') || form.querySelector('[name="end_date"]');

                form.querySelectorAll('[data-preset]').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        const now = new Date();
                        let start = new Date(now);
                        let end = new Date(now);

                        switch (btn.dataset.preset) {
                            case 'today':
                                break;
                            case 'week':
                                const day = (now.getDay() + 6) % 7; // Monday-based week
                                start.setDate(now.getDate() - day);
                                end = new Date(start);
                                end.setDate(start.getDate() + 6);
                                break;
                            case 'month':
                                start = new Date(now.getFullYear(), now.getMonth(), 1);
                                end = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                                break;
                            case 'year':
                                start = new Date(now.getFullYear(), 0, 1);
                                end = new Date(now.getFullYear(), 11, 31);
                                break;
                        }

                        if (startInput) startInput.value = fmt(start);
                        if (endInput) endInput.value = fmt(end);
                        form.submit();
                    });
                });
            });
        })();
    </script>
    @endpush
@endonce
