@extends('layout.app')

@section('title', 'الطلبات الإلكترونية')

@push('styles')
<style>
    .orders-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .orders-title { font-size: 1.7rem; font-weight: 900; color: var(--text-primary); margin: 0; }

    .status-filters {
        display: flex;
        gap: .6rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }
    .status-filter-chip {
        padding: .5rem 1.1rem;
        border-radius: 50px;
        background: white;
        border: 1.5px solid var(--border-color);
        color: var(--text-secondary);
        font-weight: 700;
        font-size: .85rem;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: .5rem;
        transition: all .2s;
    }
    .status-filter-chip:hover { border-color: var(--primary-color); color: var(--primary-color); }
    .status-filter-chip.active {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
    }
    .status-filter-chip .count-badge {
        background: rgba(0,0,0,.08);
        border-radius: 50px;
        padding: .1rem .5rem;
        font-size: .75rem;
    }
    .status-filter-chip.active .count-badge { background: rgba(255,255,255,.25); }

    .search-box {
        background: white;
        border-radius: 14px;
        box-shadow: var(--shadow-sm);
        padding: .8rem 1.2rem;
        margin-bottom: 1.5rem;
        display: flex;
        gap: .8rem;
    }
    .search-box input {
        flex: 1;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: .6rem 1rem;
        font-family: inherit;
    }
    .search-box button {
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 10px;
        padding: .6rem 1.5rem;
        font-weight: 700;
    }

    .orders-table-wrap {
        background: white;
        border-radius: 16px;
        box-shadow: var(--shadow-md);
        overflow: hidden;
    }
    .orders-table { width: 100%; border-collapse: collapse; }
    .orders-table th {
        background: var(--light-bg);
        padding: 1rem;
        font-size: .82rem;
        font-weight: 800;
        color: var(--text-secondary);
        text-align: right;
        border-bottom: 1px solid var(--border-color);
    }
    .orders-table td {
        padding: 1rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: .9rem;
        vertical-align: middle;
    }
    .orders-table tr:last-child td { border-bottom: none; }
    .orders-table tr:hover { background: #fafbfc; }

    .order-id-link { font-weight: 800; color: var(--primary-color); text-decoration: none; }
    .status-badge { padding: .3rem .8rem; border-radius: 50px; font-size: .78rem; font-weight: 700; white-space: nowrap; }
    .status-pending    { background: #fef3c7; color: #b45309; }
    .status-paid       { background: #d1fae5; color: #047857; }
    .status-processing { background: #dbeafe; color: #1d4ed8; }
    .status-shipped    { background: #e0e7ff; color: #4338ca; }
    .status-delivered  { background: #d1fae5; color: #047857; }
    .status-failed     { background: #fee2e2; color: #b91c1c; }
    .status-cancelled  { background: #e2e8f0; color: #475569; }

    .empty-orders { text-align: center; padding: 4rem 2rem; color: var(--text-secondary); }
    .empty-orders i { font-size: 3rem; opacity: .3; margin-bottom: 1rem; display: block; }
</style>
@endpush

@section('content')
<div class="orders-header">
    <h1 class="orders-title"><i class="fas fa-shopping-bag"></i> الطلبات الإلكترونية</h1>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="status-filters">
    <a href="{{ route('online-orders.index') }}" class="status-filter-chip {{ !request('status') ? 'active' : '' }}">
        الكل <span class="count-badge">{{ $statusCounts->sum() }}</span>
    </a>
    @foreach ($statusFlow as $key => $label)
        <a href="{{ route('online-orders.index', ['status' => $key]) }}"
           class="status-filter-chip {{ request('status') === $key ? 'active' : '' }}">
            {{ $label }} <span class="count-badge">{{ $statusCounts[$key] ?? 0 }}</span>
        </a>
    @endforeach
</div>

<form method="GET" action="{{ route('online-orders.index') }}" class="search-box">
    @if (request('status'))
        <input type="hidden" name="status" value="{{ request('status') }}">
    @endif
    <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث برقم الطلب، اسم العميل، أو رقم الهاتف...">
    <button type="submit"><i class="fas fa-search"></i> بحث</button>
</form>

<div class="orders-table-wrap">
    @if ($orders->isEmpty())
        <div class="empty-orders">
            <i class="fas fa-inbox"></i>
            <h5>ما في طلبات مطابقة</h5>
        </div>
    @else
        <table class="orders-table">
            <thead>
                <tr>
                    <th>رقم الطلب</th>
                    <th>العميل</th>
                    <th>الهاتف</th>
                    <th>الإجمالي</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('online-orders.show', $order) }}" class="order-id-link">#{{ $order->id }}</a>
                        </td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->customer_phone }}</td>
                        <td class="fw-bold">{{ number_format($order->total, 2) }} ₪</td>
                        <td>
                            <span class="status-badge status-{{ $order->status }}">
                                {{ $statusFlow[$order->status] ?? $order->status }}
                            </span>
                        </td>
                        <td class="text-secondary">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('online-orders.show', $order) }}" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<div class="mt-3">
    {{ $orders->links() }}
</div>
@endsection