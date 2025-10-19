@extends('layout.app')

@section('title', 'إدارة الفروع')

@section('content')
<div class="container-fluid">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="font-size: 2rem; font-weight: 900; color: #1e293b;">🏢 إدارة الفروع</h1>
        <a href="{{ route('branches.create') }}" class="btn btn-primary" style="padding: 0.75rem 1.5rem; border-radius: 12px; font-weight: 600;">
            <i class="fas fa-plus"></i> إضافة فرع
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="border-radius: 12px; padding: 1rem; margin-bottom: 1.5rem;">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="border-radius: 12px; padding: 1rem; margin-bottom: 1.5rem;">
            ❌ {{ session('error') }}
        </div>
    @endif

    <!-- شبكة الفروع -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 2rem;">
        @forelse($branches as $branch)
            <div class="card" style="border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.1); transition: all 0.3s ease;">
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.5rem; color: white;">
                    <h3 style="font-size: 1.5rem; font-weight: 900; margin: 0;">
                        🏢 {{ $branch->name }}
                    </h3>
                </div>
                
                <div style="padding: 1.5rem;">
                    <div style="margin-bottom: 1rem;">
                        <p style="margin: 0; color: #64748b; font-size: 0.9rem; margin-bottom: 0.25rem;">
                            <i class="fas fa-map-marker-alt" style="color: #ef4444; margin-left: 0.5rem;"></i>
                            <strong>الموقع:</strong> {{ $branch->location }}
                        </p>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <p style="margin: 0; color: #64748b; font-size: 0.9rem; margin-bottom: 0.25rem;">
                            <i class="fas fa-phone" style="color: #3b82f6; margin-left: 0.5rem;"></i>
                            <strong>الهاتف:</strong> {{ $branch->phone ?? 'لا يوجد' }}
                        </p>
                    </div>

                    @if($branch->description)
                        <div style="margin-bottom: 1rem;">
                            <p style="margin: 0; color: #64748b; font-size: 0.9rem;">
                                <i class="fas fa-info-circle" style="color: #10b981; margin-left: 0.5rem;"></i>
                                <strong>الوصف:</strong> {{ $branch->description }}
                            </p>
                        </div>
                    @endif

                    <div style="background: #f0f9ff; padding: 1rem; border-radius: 12px; margin-bottom: 1rem; border: 2px solid #bae6fd;">
                        <p style="margin: 0; color: #0c4a6e; font-weight: 600;">
                            👥 عدد المستخدمين: <span style="background: #dbeafe; padding: 0.25rem 0.75rem; border-radius: 20px; font-weight: 900;">{{ $branch->users_count }}</span>
                        </p>
                    </div>

                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('branches.edit', $branch) }}" class="btn btn-sm btn-warning" style="flex: 1; border-radius: 10px; font-weight: 600;">
                            ✏️ تعديل
                        </a>
                        <form method="POST" action="{{ route('branches.destroy', $branch) }}" style="flex: 1;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" style="width: 100%; border-radius: 10px; font-weight: 600;" onclick="return confirm('هل أنت متأكد؟')">
                                🗑️ حذف
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: #64748b;">
                <p style="font-size: 1.2rem; margin: 0;">لا توجد فروع حتى الآن 🔍</p>
            </div>
        @endforelse
    </div>

    <!-- التصفيح -->
    <div style="margin-top: 2rem;">
        {{ $branches->links() }}
    </div>
</div>
@endsection