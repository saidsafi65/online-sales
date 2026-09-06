@extends('layout.app')

@section('title', 'إدارة الأجهزة')

@section('content')
<div class="container mx-auto px-4 py-8" dir="rtl">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-cogs text-blue-600 ml-2"></i>
                إدارة الأجهزة
            </h1>
            <div class="flex gap-3">
                <button id="addLaptopBtn" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة جهاز جديد
                </button>
                <a href="{{ route('compatibility.index') }}" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition">
                    <i class="fas fa-eye ml-2"></i>
                    عرض المتطابقات
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 rounded mb-4">
            <i class="fas fa-check-circle ml-2"></i>
            {{ session('success') }}
        </div>
        @endif

        <!-- جدول الأجهزة -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-4 py-3 text-right">الصورة</th>
                        <th class="border border-gray-300 px-4 py-3 text-right">الجهاز</th>
                        <th class="border border-gray-300 px-4 py-3 text-center">عدد القطع</th>
                        <th class="border border-gray-300 px-4 py-3 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($laptops as $laptop)
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-300 px-4 py-3">
                            @if($laptop->image)
                            <img src="{{ asset('storage/'.$laptop->image) }}" alt="{{ $laptop->full_name }}" class="w-20 h-20 object-cover rounded">
                            @else
                            <div class="w-20 h-20 bg-gray-200 rounded flex items-center justify-center">
                                <i class="fas fa-laptop text-2xl text-gray-400"></i>
                            </div>
                            @endif
                        </td>
                        <td class="border border-gray-300 px-4 py-3">
                            <div class="font-bold text-gray-800">{{ $laptop->brand }}</div>
                            <div class="text-sm text-gray-600">{{ $laptop->model }}</div>
                            @if($laptop->description)
                            <div class="text-xs text-gray-500 mt-1">{{ Str::limit($laptop->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="border border-gray-300 px-4 py-3 text-center">
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                                {{ $laptop->parts->count() }}
                            </span>
                        </td>
                        <td class="border border-gray-300 px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <button class="manage-parts-btn bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition text-sm"
                                    data-laptop-id="{{ $laptop->id }}"
                                    data-laptop-name="{{ $laptop->full_name }}">
                                    <i class="fas fa-tools ml-1"></i>
                                    إدارة القطع
                                </button>
                                <a href="{{ route('compatibility.show', $laptop->id) }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition text-sm">
                                    <i class="fas fa-eye ml-1"></i>
                                    عرض
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $laptops->links() }}
        </div>
    </div>
</div>

<!-- Modal إضافة جهاز جديد -->
<div id="addLaptopModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full" dir="rtl">
        <div class="bg-gradient-to-r from-green-600 to-green-800 text-white p-6 rounded-t-lg">
            <div class="flex justify-between items-center">
                <h3 class="text-2xl font-bold">
                    <i class="fas fa-plus-circle ml-2"></i>
                    إضافة جهاز جديد
                </h3>
                <button class="close-modal text-white hover:text-gray-200 text-3xl leading-none">&times;</button>
            </div>
        </div>
        
        <form action="{{ route('compatibility.store-laptop') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">الشركة المصنعة *</label>
                    <input type="text" name="brand" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="مثال: HP, Lenovo, Dell">
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">الموديل *</label>
                    <input type="text" name="model" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="مثال: 250 G6, Ideapad 110">
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">الوصف</label>
                    <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="وصف مختصر للجهاز..."></textarea>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">صورة الجهاز</label>
                    <input type="file" name="image" accept="image/*" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <p class="text-xs text-gray-500 mt-1">الحجم الأقصى: 2MB</p>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" class="close-modal bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">
                    إلغاء
                </button>
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-save ml-2"></i>
                    حفظ الجهاز
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal إدارة القطع -->
<div id="managePartsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden" dir="rtl">
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-2xl font-bold">
                        <i class="fas fa-tools ml-2"></i>
                        إدارة قطع الجهاز
                    </h3>
                    <p id="modalLaptopName" class="text-blue-100 mt-1"></p>
                </div>
                <button class="close-modal text-white hover:text-gray-200 text-3xl leading-none">&times;</button>
            </div>
        </div>
        
        <div class="p-6 overflow-y-auto max-h-[70vh]">
            <div id="partsContent">
                <div class="text-center py-8">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                    <p class="text-gray-600 mt-4">جاري التحميل...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addLaptopModal = document.getElementById('addLaptopModal');
    const managePartsModal = document.getElementById('managePartsModal');
    const addLaptopBtn = document.getElementById('addLaptopBtn');
    
    // فتح modal إضافة جهاز
    addLaptopBtn.addEventListener('click', () => {
        addLaptopModal.classList.remove('hidden');
    });

    // فتح modal إدارة القطع
    document.querySelectorAll('.manage-parts-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const laptopId = this.dataset.laptopId;
            const laptopName = this.dataset.laptopName;
            
            document.getElementById('modalLaptopName').textContent = laptopName;
            managePartsModal.classList.remove('hidden');
            
            loadLaptopParts(laptopId);
        });
    });

    // إغلاق modals
    document.querySelectorAll('.close-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            addLaptopModal.classList.add('hidden');
            managePartsModal.classList.add('hidden');
        });
    });

    // إغلاق عند النقر خارج المودال
    [addLaptopModal, managePartsModal].forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
    });

    // تحميل قطع الجهاز
    function loadLaptopParts(laptopId) {
        const partsContent = document.getElementById('partsContent');
        // هنا يمكن إضافة AJAX لتحميل القطع الفعلية
        partsContent.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-info-circle text-4xl mb-3"></i>
                <p>يمكنك إضافة وظيفة إدارة القطع هنا</p>
                <p class="text-sm mt-2">استخدم صفحة التفاصيل لعرض القطع المرتبطة</p>
            </div>
        `;
    }
});
</script>
@endpush
@endsection