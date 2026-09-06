@php
    $flow = \App\Models\Order::STATUS_FLOW;
    $labels = \App\Models\Order::STATUS_LABELS;
    $isStopped = in_array($order->status, \App\Models\Order::STATUS_TERMINAL_FAILURE);
    $currentIndex = array_search($order->status, $flow);
@endphp

@if ($isStopped)
    <div style="background:#fee2e2; border:2px solid #fecaca; border-radius:12px; padding:1rem 1.2rem; display:flex; align-items:center; gap:.7rem;">
        <i class="fas fa-circle-exclamation" style="color:#b91c1c; font-size:1.3rem;"></i>
        <div>
            <div style="font-weight:800; color:#b91c1c;">{{ $labels[$order->status] }}</div>
            <div style="font-size:.8rem; color:#7f1d1d;">تواصل معنا لو عندك أي استفسار عن هالطلب.</div>
        </div>
    </div>
@else
    <div style="display:flex; align-items:flex-start; {{ $compact ?? false ? 'gap:.4rem;' : 'gap:0;' }}">
        @foreach ($flow as $i => $step)
            @php
                $done = $currentIndex !== false && $i <= $currentIndex;
            @endphp
            <div style="flex:1; display:flex; flex-direction:column; align-items:center; position:relative;">
                @if ($i > 0)
                    <div style="position:absolute; top:{{ ($compact ?? false) ? '9px' : '13px' }}; right:50%; width:100%; height:3px; background:{{ $done ? 'var(--primary-color, #dc2626)' : '#e2e8f0' }}; z-index:0;"></div>
                @endif
                <div style="width:{{ ($compact ?? false) ? '18px' : '26px' }}; height:{{ ($compact ?? false) ? '18px' : '26px' }}; border-radius:50%; background:{{ $done ? 'var(--primary-color, #dc2626)' : '#e2e8f0' }}; color:#fff; display:flex; align-items:center; justify-content:center; font-size:.7rem; z-index:1; flex-shrink:0;">
                    @if ($done)<i class="fas fa-check" style="font-size:.65rem;"></i>@endif
                </div>
                @unless ($compact ?? false)
                    <div style="font-size:.72rem; margin-top:.4rem; text-align:center; color:{{ $done ? 'var(--text-primary)' : 'var(--text-secondary)' }}; font-weight:{{ $done ? '700' : '500' }};">
                        {{ $labels[$step] }}
                    </div>
                @endunless
            </div>
        @endforeach
    </div>
    @if ($compact ?? false)
        <div style="font-size:.78rem; font-weight:700; color:var(--text-primary); margin-top:.4rem;">{{ $labels[$order->status] ?? $order->status }}</div>
    @endif
@endif
