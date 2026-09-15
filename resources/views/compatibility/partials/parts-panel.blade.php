<div class="mb-4">
    <h6 class="fw-bold mb-2">القطع الحالية</h6>
    @if($laptop->parts->isEmpty())
        <div class="text-muted small mb-3">ما في قطع مربوطة بهالجهاز لسا</div>
    @else
        <table class="table table-sm align-middle">
            <thead>
                <tr>
                    <th>النوع</th>
                    <th>رقم القطعة</th>
                    <th>المواصفات</th>
                    <th>أصلية؟</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($laptop->parts as $part)
                    <tr>
                        <td><span class="badge bg-secondary">{{ $part->partType->name ?? '-' }}</span></td>
                        <td>{{ $part->part_number }}</td>
                        <td>
                            @foreach(($part->specifications ?? []) as $key => $value)
                                <span class="badge bg-light text-dark border">{{ $key }}: {{ $value }}</span>
                            @endforeach
                        </td>
                        <td>{{ $part->pivot->is_original ? 'نعم' : 'لا' }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-danger detach-part-btn"
                                data-laptop-id="{{ $laptop->id }}" data-part-id="{{ $part->id }}">فك الربط</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="border rounded-3 p-3 h-100">
            <h6 class="fw-bold mb-2">ربط قطعة موجودة</h6>
            <form class="attach-existing-part-form" data-laptop-id="{{ $laptop->id }}">
                <div class="mb-2">
                    <label class="form-label small">اختر القطعة</label>
                    <select name="part_id" class="form-select form-select-sm" required>
                        <option value="">اختر...</option>
                        @foreach(\App\Models\Part::with('partType')->orderBy('part_number')->get() as $part)
                            <option value="{{ $part->id }}">{{ $part->partType->name ?? '' }} — {{ $part->part_number }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-sm btn-success">ربط</button>
            </form>
        </div>
    </div>

    <div class="col-md-6">
        <div class="border rounded-3 p-3 h-100">
            <h6 class="fw-bold mb-2">إضافة قطعة جديدة وربطها</h6>
            <form class="add-new-part-form" data-laptop-id="{{ $laptop->id }}">
                <div class="mb-2">
                    <label class="form-label small">نوع القطعة</label>
                    <select name="part_type_id" class="form-select form-select-sm" required>
                        <option value="">اختر...</option>
                        @foreach($partTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label small">رقم القطعة</label>
                    <input type="text" name="part_number" class="form-control form-control-sm" required>
                </div>
                <div class="mb-2">
                    <label class="form-label small">السعر (اختياري)</label>
                    <input type="number" step="0.01" name="price" class="form-control form-control-sm">
                </div>
                <div class="mb-2">
                    <label class="form-label small">المواصفات (مثال: نوع الكونكتور، عدد الأطراف، المقاس...)</label>
                    <div class="spec-rows"></div>
                    <button type="button" class="btn btn-sm btn-outline-secondary add-spec-row-btn mt-1">+ إضافة مواصفة</button>
                </div>
                <button type="submit" class="btn btn-sm btn-primary">حفظ وربط</button>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    const panel = document.currentScript.closest('#partsContent') || document;

    function addSpecRow(container) {
        const row = document.createElement('div');
        row.className = 'input-group input-group-sm mb-1';
        row.innerHTML = `
            <input type="text" name="spec_keys[]" class="form-control" placeholder="مثال: نوع الكونكتور">
            <input type="text" name="spec_values[]" class="form-control" placeholder="مثال: 30 بن eDP">
            <button type="button" class="btn btn-outline-danger remove-spec-row-btn">×</button>
        `;
        container.appendChild(row);
        row.querySelector('.remove-spec-row-btn').addEventListener('click', () => row.remove());
    }

    document.querySelectorAll('.add-spec-row-btn').forEach(btn => {
        const container = btn.closest('form').querySelector('.spec-rows');
        addSpecRow(container); // صف واحد افتراضي
        btn.addEventListener('click', () => addSpecRow(container));
    });

    document.querySelectorAll('.attach-existing-part-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const laptopId = this.dataset.laptopId;
            const partId = this.querySelector('[name=part_id]').value;
            if (!partId) return;

            fetch('{{ route('compatibility.attach-part') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ laptop_id: laptopId, part_id: partId, is_original: false })
            }).then(r => r.json()).then(() => window.reloadPartsPanel(laptopId));
        });
    });

    document.querySelectorAll('.add-new-part-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const laptopId = this.dataset.laptopId;
            const formData = new FormData(this);
            const specKeys = formData.getAll('spec_keys[]');
            const specValues = formData.getAll('spec_values[]');

            fetch('{{ route('compatibility.store-part') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    laptop_id: laptopId,
                    part_type_id: formData.get('part_type_id'),
                    part_number: formData.get('part_number'),
                    price: formData.get('price'),
                    spec_keys: specKeys,
                    spec_values: specValues,
                })
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    window.reloadPartsPanel(laptopId);
                } else if (data.errors) {
                    alert(Object.values(data.errors).flat().join('\n'));
                }
            });
        });
    });

    document.querySelectorAll('.detach-part-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('تأكيد فك ربط هالقطعة عن الجهاز؟')) return;
            const laptopId = this.dataset.laptopId;
            const partId = this.dataset.partId;

            fetch('{{ route('compatibility.detach-part') }}', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ laptop_id: laptopId, part_id: partId })
            }).then(r => r.json()).then(() => window.reloadPartsPanel(laptopId));
        });
    });
})();
</script>
