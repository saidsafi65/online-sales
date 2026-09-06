@extends('layout.platform')

@section('title', 'إدارة المعارض')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="font-size: 1.8rem; font-weight: 900; color: #1e293b;">🏬 إدارة المعارض</h1>
        <div style="display:flex; gap:0.75rem;">
            <a href="{{ route('system-admin.tenants.createAuto') }}" class="btn btn-primary" style="padding: 0.75rem 1.5rem; border-radius: 12px; font-weight: 600;">
                <i class="fas fa-bolt"></i> إنشاء معرض تلقائياً
            </a>
            <a href="{{ route('system-admin.tenants.create') }}" class="btn btn-outline-secondary" style="padding: 0.75rem 1.5rem; border-radius: 12px; font-weight: 600;">
                تسجيل معرض بقاعدة بيانات جاهزة
            </a>
        </div>
    </div>

    <!-- شبكة المعارض -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); gap: 2rem;">
        @forelse($tenants as $tenant)
            <div class="card" style="border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.5rem; color: white; display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="font-size: 1.4rem; font-weight: 900; margin: 0;">🏬 {{ $tenant->name }}</h3>
                    @if($tenant->is_active)
                        <span style="background: #22c55e; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 700;">فعّال</span>
                    @else
                        <span style="background: #ef4444; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 700;">معطّل</span>
                    @endif
                </div>

                <div style="padding: 1.5rem;">
                    <p style="margin: 0 0 0.75rem; color: #64748b; font-size: 0.9rem;">
                        <i class="fas fa-globe" style="color: #3b82f6; margin-left: 0.5rem;"></i>
                        <strong>الدومين:</strong> {{ $tenant->domain }}
                    </p>
                    <p style="margin: 0 0 0.75rem; color: #64748b; font-size: 0.9rem;">
                        <i class="fas fa-database" style="color: #10b981; margin-left: 0.5rem;"></i>
                        <strong>القاعدة:</strong> {{ $tenant->db_database }} @ {{ $tenant->db_host }}
                    </p>

                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 1rem;">
                        <a href="{{ route('system-admin.tenants.edit', $tenant) }}" class="btn btn-sm btn-warning" style="flex: 1; border-radius: 10px; font-weight: 600;">
                            ✏️ تعديل
                        </a>

                        <form method="POST" action="{{ route('system-admin.tenants.migrate', $tenant) }}" style="flex: 1;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-info" style="width: 100%; border-radius: 10px; font-weight: 600;" onclick="return confirm('رح يتم ترحيل (migrate) كل جداول النظام لقاعدة بيانات هالمعرض. متأكد؟')">
                                🚀 ترحيل الجداول
                            </button>
                        </form>

                        <form method="POST" action="{{ route('system-admin.tenants.toggle', $tenant) }}" style="flex: 1;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $tenant->is_active ? 'btn-secondary' : 'btn-success' }}" style="width: 100%; border-radius: 10px; font-weight: 600;" onclick="return confirm('{{ $tenant->is_active ? 'رح يتوقف هالمعرض عن العمل. متأكد؟' : 'رح يتفعّل هالمعرض من جديد. متأكد؟' }}')">
                                {{ $tenant->is_active ? '⏸️ تعطيل' : '▶️ تفعيل' }}
                            </button>
                        </form>
                    </div>

                    <div style="margin-top: 0.5rem;">
                        <button type="button" class="btn btn-sm btn-outline-danger" style="width: 100%; border-radius: 10px; font-weight: 600;" onclick="toggleDeleteConfirm({{ $tenant->id }})">
                            🗑️ حذف نهائي (المعرض + كل بياناته)
                        </button>
                        <div id="delete-confirm-{{ $tenant->id }}" style="display:none; margin-top: 0.75rem; background:#fef2f2; border:2px solid #fecaca; border-radius:10px; padding:0.85rem;">
                            <p style="font-size:0.8rem; color:#991b1b; margin-bottom:0.6rem; font-weight:600;">
                                ⚠️ إجراء نهائي — بيحذف قاعدة بيانات المعرض بالكامل (منتجات، طلبات، عملاء، كل شي) ومستحيل نرجعه. اكتب <code>{{ $tenant->domain }}</code> بالأسفل للتأكيد:
                            </p>
                            <form method="POST" action="{{ route('system-admin.tenants.destroy', $tenant) }}">
                                @csrf
                                @method('DELETE')
                                <input type="text" id="confirm-input-{{ $tenant->id }}" name="confirm_domain" oninput="checkDeleteConfirm({{ $tenant->id }}, '{{ $tenant->domain }}')" placeholder="{{ $tenant->domain }}" autocomplete="off" style="width:100%; border-radius:8px; border:2px solid #fca5a5; padding:0.5rem 0.75rem; margin-bottom:0.6rem;">
                                <button type="submit" id="confirm-btn-{{ $tenant->id }}" class="btn btn-sm btn-danger" style="width:100%; border-radius:8px; font-weight:700;" disabled>
                                    تأكيد الحذف النهائي
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: #64748b;">
                <p style="font-size: 1.2rem; margin: 0;">لا يوجد معارض مسجّلة حتى الآن 🔍</p>
            </div>
        @endforelse
    </div>

    <!-- صيانة عامة -->
    <div class="card" style="border-radius: 20px; padding: 1.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.08); max-width: 500px; margin-top: 2.5rem; background: white;">
        <h5 style="font-weight: 700; margin-bottom: 1rem;">🛠️ صيانة قاعدة البيانات المرتبطة بهالدومين الحالي</h5>
        <p style="color:#94a3b8; font-size:0.8rem; margin-top:-0.5rem; margin-bottom:1rem;">لصيانة كل المعارض دفعة وحدة، استخدم زر الصيانة الجماعية من الرئيسية.</p>
        <div style="display: flex; gap: 0.75rem;">
            <form method="POST" action="{{ route('system-admin.maintenance.migrate') }}" style="flex: 1;">
                @csrf
                <button type="submit" class="btn btn-outline-primary" style="width: 100%; border-radius: 10px; font-weight: 600;" onclick="return confirm('رح يتم تشغيل ترحيل الجداول المعلّقة (migrate) على قاعدة البيانات الحالية. متأكد؟')">
                    ترحيل معلّق
                </button>
            </form>
            <form method="POST" action="{{ route('system-admin.maintenance.clear-cache') }}" style="flex: 1;">
                @csrf
                <button type="submit" class="btn btn-outline-secondary" style="width: 100%; border-radius: 10px; font-weight: 600;">
                    مسح الكاش
                </button>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleDeleteConfirm(id) {
            const el = document.getElementById('delete-confirm-' + id);
            el.style.display = el.style.display === 'none' ? 'block' : 'none';
        }
        function checkDeleteConfirm(id, expectedDomain) {
            const input = document.getElementById('confirm-input-' + id).value;
            document.getElementById('confirm-btn-' + id).disabled = (input !== expectedDomain);
        }
    </script>
    @endpush
@endsection
