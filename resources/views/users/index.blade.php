@extends('layout.app')

@section('title', 'إدارة المستخدمين')

@section('content')
<div class="container-fluid">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="font-size: 2rem; font-weight: 900; color: #1e293b;">👥 إدارة المستخدمين</h1>
        <a href="{{ route('users.create') }}" class="btn btn-primary" style="padding: 0.75rem 1.5rem; border-radius: 12px; font-weight: 600;">
            <i class="fas fa-plus"></i> إضافة مستخدم
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

    <!-- فيلتر البحث -->
    <div class="card" style="border-radius: 20px; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
        <form method="GET" action="{{ route('users.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <input type="text" name="search" placeholder="ابحث بالاسم أو البريد..." value="{{ request('search') }}" class="form-control" style="border-radius: 10px; border: 2px solid #e2e8f0;">
            
            <select name="branch_id" class="form-control" style="border-radius: 10px; border: 2px solid #e2e8f0;">
                <option value="">-- كل الفروع --</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>

            <select name="role" class="form-control" style="border-radius: 10px; border: 2px solid #e2e8f0;">
                <option value="">-- كل الأدوار --</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>مدير النظام</option>
                <option value="manager" {{ request('role') === 'manager' ? 'selected' : '' }}>مدير الفرع</option>
                <option value="employee" {{ request('role') === 'employee' ? 'selected' : '' }}>موظف</option>
            </select>

            <select name="status" class="form-control" style="border-radius: 10px; border: 2px solid #e2e8f0;">
                <option value="">-- كل الحالات --</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
            </select>

            <button type="submit" class="btn btn-info" style="border-radius: 10px; font-weight: 600;">
                <i class="fas fa-search"></i> بحث
            </button>
        </form>
    </div>

    <!-- جدول المستخدمين -->
    <div class="card" style="border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
        <table class="table table-hover" style="margin: 0;">
            <thead style="background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%); color: white;">
                <tr>
                    <th style="padding: 1rem;">الاسم</th>
                    <th>البريد الإلكتروني</th>
                    <th>الفرع</th>
                    <th>الدور</th>
                    <th>الحالة</th>
                    <th style="text-align: center;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 1rem; font-weight: 500;">
                            <span style="display: inline-flex; align-items: center; gap: 0.5rem;">
                                @if($user->isAdmin())
                                    <span style="background: #ef4444; color: white; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">👑 Admin</span>
                                @elseif($user->isManager())
                                    <span style="background: #3b82f6; color: white; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">📋 Manager</span>
                                @else
                                    <span style="background: #10b981; color: white; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">👤 Employee</span>
                                @endif
                                {{ $user->name }}
                            </span>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span style="background: #e0f2fe; color: #0c4a6e; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 500;">
                                📍 {{ $user->branch?->name ?? 'بدون فرع' }}
                            </span>
                        </td>
                        <td>
                            @if($user->isAdmin())
                                <span style="color: #ef4444; font-weight: 600;">مدير النظام</span>
                            @elseif($user->isManager())
                                <span style="color: #3b82f6; font-weight: 600;">مدير الفرع</span>
                            @else
                                <span style="color: #10b981; font-weight: 600;">موظف</span>
                            @endif
                        </td>
                        <td>
                            @if($user->isActive())
                                <span style="background: #d1fae5; color: #065f46; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.85rem;">
                                    ✅ نشط
                                </span>
                            @else
                                <span style="background: #fee2e2; color: #991b1b; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.85rem;">
                                    ❌ غير نشط
                                </span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning" style="border-radius: 8px; padding: 0.5rem 1rem; font-size: 0.85rem;">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                            <form method="POST" action="{{ route('users.destroy', $user) }}" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" style="border-radius: 8px; padding: 0.5rem 1rem; font-size: 0.85rem;" onclick="return confirm('هل أنت متأكد؟')">
                                    <i class="fas fa-trash"></i> حذف
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem; color: #64748b;">
                            لا توجد مستخدمين 🔍
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- التصفيح -->
    <div style="margin-top: 2rem;">
        {{ $users->links() }}
    </div>
</div>
@endsection
