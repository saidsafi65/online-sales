@extends('layout.app')

@section('title', 'أمانات الصيانة')

@section('content')
<div class="welcome-section">
    <h1 class="welcome-title">🔧 أمانات الصيانة</h1>
    <p class="welcome-subtitle">إدارة ومتابعة أمانات الصيانة</p>
</div>

<!-- Success Message -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 15px; border: none; background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #065f46;">
    <i class="fas fa-check-circle me-2"></i>
    <strong>{{ session('success') }}</strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row justify-content-center">
    <div class="col-12">
        <!-- Header Card -->
        <div class="service-card card-primary mb-2" style="max-height: 40%;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="mb-1" style="color: var(--text-primary);">
                        <i class="fas fa-list-alt text-primary me-2"></i>
                        قائمة الأمانات
                    </h4>
                    <p class="mb-0 text-muted">إجمالي الأمانات: <strong>{{ $deposits->count() }}</strong></p>
                </div>
                <a href="{{ route('deposits.create') }}" class="btn btn-lg" style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%); color: white; padding: 12px 30px; border-radius: 50px; border: none; font-weight: 600; box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3); transition: all 0.3s ease;">
                    <i class="fas fa-plus me-2"></i>
                    إضافة أمانة جديدة
                </a>
            </div>
        </div>

        <!-- Table Card -->
        <div class="service-card card-success">
            <div style="overflow-x: auto; border-radius: 12px; border: 2px solid #e2e8f0;">
                <table class="table mb-0" style="min-width: 900px;">
                    <thead style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%); color: white;">
                        <tr>
                            <th style="padding: 1rem; text-align: center; width: 60px;">#</th>
                            <th style="padding: 1rem;">القطعة</th>
                            <th style="padding: 1rem;">النوع</th>
                            <th style="padding: 1rem;">سبب الأخذ</th>
                            <th style="padding: 1rem; width: 180px;">وقت الأخذ</th>
                            <th style="padding: 1rem; width: 180px;">وقت الإرجاع</th>
                            <th style="padding: 1rem; text-align: center; width: 120px;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($deposits as $index => $deposit)
                            <tr style="transition: all 0.2s ease;">
                                <td style="padding: 0.75rem; text-align: center; vertical-align: middle;">
                                    <span style="background: #10b981; color: white; padding: 0.4rem 0.8rem; border-radius: 8px; font-weight: 600;">{{ $index + 1 }}</span>
                                </td>
                                <td style="padding: 0.75rem; vertical-align: middle;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="fas fa-microchip" style="color: #10b981;"></i>
                                        <span style="font-weight: 600; color: #1e293b;">{{ $deposit->piece }}</span>
                                    </div>
                                </td>
                                <td style="padding: 0.75rem; vertical-align: middle;">
                                    <span style="background: #dbeafe; color: #1e40af; padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.9rem; font-weight: 500;">
                                        {{ $deposit->type }}
                                    </span>
                                </td>
                                <td style="padding: 0.75rem; vertical-align: middle; color: #475569;">
                                    {{ $deposit->reason }}
                                </td>
                                <td style="padding: 0.75rem; vertical-align: middle;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; color: #64748b; font-size: 0.9rem;">
                                        <i class="fas fa-clock" style="color: #f59e0b;"></i>
                                        {{ \Carbon\Carbon::parse($deposit->taken_at)->format('Y-m-d H:i') }}
                                    </div>
                                </td>
                                <td style="padding: 0.75rem; vertical-align: middle;">
                                    @if($deposit->returned_at)
                                        <div style="display: flex; align-items: center; gap: 0.5rem; color: #64748b; font-size: 0.9rem;">
                                            <i class="fas fa-check-circle" style="color: #10b981;"></i>
                                            {{ \Carbon\Carbon::parse($deposit->returned_at)->format('Y-m-d H:i') }}
                                        </div>
                                    @else
                                        <span style="background: #fef3c7; color: #b45309; padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.85rem; font-weight: 600;">
                                            <i class="fas fa-hourglass-half"></i> لم يُرجع
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 0.75rem; text-align: center; vertical-align: middle;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                        <a href="{{ route('deposits.edit', $deposit->id) }}" 
                                           class="btn btn-sm" 
                                           style="background: #0ea5e9; color: white; border: none; padding: 0.5rem 0.75rem; border-radius: 8px; transition: all 0.3s ease;"
                                           title="تعديل"
                                           onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(14, 165, 233, 0.4)';"
                                           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('deposits.destroy', $deposit->id) }}" 
                                              method="POST" 
                                              style="margin: 0; display: inline;"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذه الأمانة؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm" 
                                                    style="background: #ef4444; color: white; border: none; padding: 0.5rem 0.75rem; border-radius: 8px; transition: all 0.3s ease; cursor: pointer;"
                                                    title="حذف"
                                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(239, 68, 68, 0.4)';"
                                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="padding: 3rem; text-align: center;">
                                    <div style="color: #94a3b8;">
                                        <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                                        <p style="font-size: 1.1rem; font-weight: 600; margin-bottom: 0.5rem;">لا توجد أمانات</p>
                                        <p style="font-size: 0.9rem;">ابدأ بإضافة أمانة جديدة</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table tbody tr:hover {
        background: #f8fafc;
    }

    .alert {
        animation: slideInDown 0.5s ease-out;
    }

    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush
@endsection