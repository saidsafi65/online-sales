@if($laptops->isEmpty())
    <div class="text-center text-muted py-4">
        <i class="fas fa-search fa-2x mb-2"></i>
        <p class="mb-0">ما في نتائج مطابقة</p>
    </div>
@else
    @foreach($laptops as $laptop)
        <div class="border rounded-3 p-3 mb-2">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <strong>{{ $laptop->brand }}</strong> — {{ $laptop->model }}
                </div>
                <a href="{{ route('compatibility.show', $laptop->id) }}" class="btn btn-sm btn-outline-primary">عرض التفاصيل</a>
            </div>
            @if($laptop->parts->isEmpty())
                <div class="text-muted small">ما في قطع مضافة لهالجهاز لسا</div>
            @else
                <table class="table table-sm mb-0">
                    <tbody>
                        @foreach($laptop->parts as $part)
                            <tr>
                                <td class="text-nowrap"><span class="badge bg-secondary">{{ $part->partType->name ?? '-' }}</span></td>
                                <td class="text-nowrap">{{ $part->part_number }}</td>
                                <td>
                                    @foreach(($part->specifications ?? []) as $key => $value)
                                        <span class="badge bg-light text-dark border ms-1">{{ $key }}: {{ $value }}</span>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endforeach
@endif
