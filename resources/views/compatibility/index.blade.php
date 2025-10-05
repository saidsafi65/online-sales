@extends('layout.app')

@section('title', 'متطابقات الأجهزة')

@push('styles')
<style>
    .compatibility-header {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: var(--shadow-md);
        margin-bottom: 2rem;
        border: 2px solid var(--border-color);
    }

    .page-title {
        font-size: 2rem;
        font-weight: 900;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        background: linear-gradient(135deg, #1e40af 0%, #6366f1 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .page-subtitle {
        color: var(--text-secondary);
        font-size: 1rem;
        font-weight: 500;
    }

    .filter-section {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: var(--shadow-md);
        margin-bottom: 2rem;
        border: 2px solid var(--border-color);
    }

    .filter-label {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.75rem;
        display: block;
        font-size: 1rem;
    }

    .filter-select {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid var(--border-color);
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: white;
        color: var(--text-primary);
        font-weight: 500;
    }

    .filter-select:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(30, 64, 175, 0.1);
    }

    .table-container {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: var(--shadow-lg);
        overflow: hidden;
        border: 2px solid var(--border-color);
    }

    .compatibility-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .compatibility-table thead tr {
        background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
    }

    .compatibility-table thead th {
        padding: 1rem;
        color: white;
        font-weight: 700;
        font-size: 0.95rem;
        text-align: center;
        border: none;
        white-space: nowrap;
    }

    .compatibility-table thead th:first-child {
        text-align: right;
        border-radius: 12px 0 0 0;
    }

    .compatibility-table thead th:last-child {
        border-radius: 0 12px 0 0;
    }

    .compatibility-table tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid var(--border-color);
    }

    .compatibility-table tbody tr:hover {
        background: #f8fafc;
        transform: scale(1.01);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .compatibility-table tbody td {
        padding: 1.25rem 1rem;
        border: none;
        vertical-align: middle;
    }

    .laptop-cell {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .laptop-image {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 12px;
        box-shadow: var(--shadow-sm);
        border: 2px solid var(--border-color);
    }

    .laptop-info {
        flex: 1;
    }

    .laptop-brand {
        font-weight: 700;
        color: var(--text-primary);
        font-size: 1.05rem;
        margin-bottom: 0.25rem;
    }

    .laptop-model {
        color: var(--text-secondary);
        font-size: 0.9rem;
        font-weight: 500;
    }

    .part-available-btn {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 0.625rem 1.25rem;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
    }

    .part-available-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
    }

    .part-not-available {
        color: var(--text-secondary);
        font-size: 0.9rem;
        font-weight: 500;
    }

    .details-link {
        color: var(--primary-color);
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        background: rgba(30, 64, 175, 0.1);
    }

    .details-link:hover {
        background: rgba(30, 64, 175, 0.2);
        transform: translateX(-3px);
    }

    .manage-btn {
        background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
        color: white;
        padding: 0.875rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-md);
        border: none;
    }

    .manage-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    /* Modal Styles */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        animation: fadeIn 0.3s ease;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-container {
        background: white;
        border-radius: 24px;
        box-shadow: var(--shadow-xl);
        max-width: 900px;
        width: 100%;
        max-height: 90vh;
        overflow: hidden;
        animation: slideUp 0.3s ease;
    }

    .modal-header {
        background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
        color: white;
        padding: 2rem;
        position: relative;
    }

    .modal-title {
        font-size: 1.75rem;
        font-weight: 900;
        margin-bottom: 0.5rem;
    }

    .modal-subtitle {
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.95rem;
        font-weight: 500;
    }

    .modal-close {
        position: absolute;
        top: 1.5rem;
        left: 1.5rem;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        font-size: 1.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-close:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg);
    }

    .modal-body {
        padding: 2rem;
        max-height: 60vh;
        overflow-y: auto;
    }

    .compatible-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
    }

    .compatible-card {
        border: 2px solid var(--border-color);
        border-radius: 16px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        background: white;
    }

    .compatible-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--primary-color);
    }

    .compatible-card-content {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .compatible-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 10px;
        border: 2px solid var(--border-color);
    }

    .compatible-info {
        flex: 1;
    }

    .compatible-brand {
        font-weight: 700;
        color: var(--text-primary);
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }

    .compatible-model {
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    .loading-spinner {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 3rem;
    }

    .spinner {
        width: 50px;
        height: 50px;
        border: 4px solid var(--border-color);
        border-top-color: var(--primary-color);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--text-secondary);
    }

    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .empty-state-text {
        font-size: 1.1rem;
        font-weight: 600;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    @media (max-width: 768px) {
        .compatibility-header {
            padding: 1.5rem;
        }

        .page-title {
            font-size: 1.5rem;
        }

        .table-container {
            padding: 1rem;
            overflow-x: auto;
        }

        .laptop-image {
            width: 50px;
            height: 50px;
        }

        .modal-container {
            margin: 1rem;
        }

        .compatible-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<!-- Header Section -->
<div class="compatibility-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h1 class="page-title">
                <i class="fas fa-puzzle-piece ms-2"></i>
                متطابقات الأجهزة
            </h1>
            <p class="page-subtitle">استعرض وإدارة توافق القطع مع الأجهزة المختلفة</p>
        </div>
        <a href="{{ route('compatibility.manage') }}" class="manage-btn">
            <i class="fas fa-cog"></i>
            <span>إدارة الأجهزة</span>
        </a>
    </div>
</div>

<!-- Filter Section -->
<div class="filter-section">
    <label class="filter-label">
        <i class="fas fa-filter ms-2"></i>
        فلتر حسب نوع القطعة
    </label>
    <div class="row">
        <div class="col-md-4">
            <select id="partTypeFilter" class="filter-select">
                <option value="">جميع القطع</option>
                @foreach($partTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<!-- Table Section -->
<div class="table-container">
    <div class="table-responsive">
        <table class="compatibility-table">
            <thead>
                <tr>
                    <th>الجهاز</th>
                    @foreach($partTypes as $type)
                        <th class="part-type-col" data-part-type="{{ $type->id }}">
                            {{ $type->name }}
                        </th>
                    @endforeach
                    <th>التفاصيل</th>
                </tr>
            </thead>
            <tbody>
                @foreach($laptops as $laptop)
                <tr>
                    <td>
                        <div class="laptop-cell">
                            @if($laptop->image)
                                <img src="{{ asset('storage/'.$laptop->image) }}" 
                                     alt="{{ $laptop->full_name }}" 
                                     class="laptop-image">
                            @endif
                            <div class="laptop-info">
                                <div class="laptop-brand">{{ $laptop->brand }}</div>
                                <div class="laptop-model">{{ $laptop->model }}</div>
                            </div>
                        </div>
                    </td>
                    
                    @foreach($partTypes as $type)
                        @php
                            $part = $laptop->parts->where('part_type_id', $type->id)->first();
                        @endphp
                        <td class="text-center part-type-col" data-part-type="{{ $type->id }}">
                            @if($part)
                                <button 
                                    class="part-available-btn view-compatible-btn"
                                    data-laptop-id="{{ $laptop->id }}"
                                    data-part-type-id="{{ $type->id }}"
                                    data-part-number="{{ $part->part_number }}">
                                    <i class="fas fa-check-circle"></i>
                                    <span>{{ $part->part_number }}</span>
                                </button>
                            @else
                                <span class="part-not-available">
                                    <i class="fas fa-minus-circle"></i>
                                </span>
                            @endif
                        </td>
                    @endforeach
                    
                    <td class="text-center">
                        <a href="{{ route('compatibility.show', $laptop->id) }}" class="details-link">
                            <span>عرض التفاصيل</span>
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="compatibleModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <button id="closeModal" class="modal-close">
                <i class="fas fa-times"></i>
            </button>
            <h3 class="modal-title">
                <i class="fas fa-laptop ms-2"></i>
                الأجهزة المتوافقة
            </h3>
            <div id="modalPartInfo" class="modal-subtitle"></div>
        </div>
        
        <div class="modal-body" id="modalContent">
            <div class="loading-spinner">
                <div class="spinner"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('compatibleModal');
    const closeModal = document.getElementById('closeModal');
    const modalContent = document.getElementById('modalContent');
    const modalPartInfo = document.getElementById('modalPartInfo');
    const partTypeFilter = document.getElementById('partTypeFilter');

    // Filter functionality
    partTypeFilter.addEventListener('change', function() {
        const selectedType = this.value;
        const columns = document.querySelectorAll('.part-type-col');
        
        columns.forEach(col => {
            if (selectedType === '' || col.dataset.partType === selectedType) {
                col.style.display = '';
            } else {
                col.style.display = 'none';
            }
        });
    });

    // View compatible devices
    document.querySelectorAll('.view-compatible-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const laptopId = this.dataset.laptopId;
            const partTypeId = this.dataset.partTypeId;
            const partNumber = this.dataset.partNumber;
            
            modal.classList.add('active');
            modalPartInfo.innerHTML = `<strong>رقم القطعة:</strong> ${partNumber}`;
            modalContent.innerHTML = '<div class="loading-spinner"><div class="spinner"></div></div>';
            
            // Fetch data
            fetch('{{ route("compatibility.get-compatible") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    laptop_id: laptopId,
                    part_type_id: partTypeId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.compatible_laptops.length > 0) {
                    let html = '<div class="compatible-grid">';
                    data.compatible_laptops.forEach(laptop => {
                        html += `
                            <div class="compatible-card">
                                <div class="compatible-card-content">
                                    ${laptop.image ? `<img src="/storage/${laptop.image}" class="compatible-image" alt="${laptop.brand}">` : ''}
                                    <div class="compatible-info">
                                        <div class="compatible-brand">${laptop.brand}</div>
                                        <div class="compatible-model">${laptop.model}</div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                    modalContent.innerHTML = html;
                } else {
                    modalContent.innerHTML = `
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <p class="empty-state-text">لا توجد أجهزة متوافقة حالياً</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                modalContent.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-exclamation-triangle text-danger"></i>
                        </div>
                        <p class="empty-state-text text-danger">حدث خطأ في تحميل البيانات</p>
                    </div>
                `;
            });
        });
    });

    // Close modal
    closeModal.addEventListener('click', () => modal.classList.remove('active'));
    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.remove('active');
    });
});
</script>
@endpush