@php
    $__current = old('permissions', ($user ?? null)?->permissions ?? []);
    $__standardActions = ['view' => 'عرض', 'create' => 'إضافة', 'edit' => 'تعديل', 'delete' => 'حذف'];
@endphp

<div class="table-responsive" style="border: 2px solid #e2e8f0; border-radius: 10px;">
    <table class="table table-sm align-middle mb-0" style="min-width: 640px;">
        <thead>
            <tr style="background: #f8fafc;">
                <th style="padding: 0.75rem;">
                    <label style="display:flex; gap:.5rem; align-items:center; font-weight: 700;">
                        <input type="checkbox" id="perm-select-all">
                        <span>القسم</span>
                    </label>
                </th>
                @foreach($__standardActions as $label)
                    <th class="text-center" style="padding: 0.75rem;">{{ $label }}</th>
                @endforeach
                <th style="padding: 0.75rem;">صلاحيات إضافية</th>
            </tr>
        </thead>
        <tbody>
            @foreach($registry as $module => $config)
                <tr class="perm-row" data-module="{{ $module }}">
                    <td style="padding: 0.6rem 0.75rem;">
                        <label style="display:flex; gap:.5rem; align-items:center; font-weight: 600;">
                            <input type="checkbox" class="perm-row-select">
                            <span>{{ $config['label'] }}</span>
                        </label>
                    </td>
                    @foreach(array_keys($__standardActions) as $action)
                        <td class="text-center">
                            @if(array_key_exists($action, $config['actions']))
                                <input type="checkbox" class="perm-checkbox" name="permissions[]"
                                       value="{{ $module }}.{{ $action }}"
                                       {{ in_array("{$module}.{$action}", $__current, true) ? 'checked' : '' }}>
                            @else
                                <span style="color:#cbd5e1;">—</span>
                            @endif
                        </td>
                    @endforeach
                    <td>
                        @foreach($config['actions'] as $action => $label)
                            @continue(array_key_exists($action, $__standardActions))
                            <label style="display:flex; gap:.4rem; align-items:center; font-size: 0.85rem; white-space: nowrap; margin-bottom: 2px;">
                                <input type="checkbox" class="perm-checkbox" name="permissions[]"
                                       value="{{ $module }}.{{ $action }}"
                                       {{ in_array("{$module}.{$action}", $__current, true) ? 'checked' : '' }}>
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
(function () {
    var selectAll = document.getElementById('perm-select-all');
    if (!selectAll) return;

    var allBoxes = document.querySelectorAll('.perm-checkbox');

    selectAll.addEventListener('change', function () {
        allBoxes.forEach(function (box) { box.checked = selectAll.checked; });
        document.querySelectorAll('.perm-row-select').forEach(function (rowBox) { rowBox.checked = selectAll.checked; });
    });

    document.querySelectorAll('.perm-row-select').forEach(function (rowSelect) {
        rowSelect.addEventListener('change', function () {
            var row = rowSelect.closest('.perm-row');
            row.querySelectorAll('.perm-checkbox').forEach(function (box) { box.checked = rowSelect.checked; });
        });
    });
})();
</script>
