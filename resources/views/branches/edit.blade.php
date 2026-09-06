@extends('layout.app')

@section('title', 'تعديل الفرع')

@section('content')
<div class="container-fluid">
    <div style="max-width: 700px; margin: 0 auto;">
        <h1 style="font-size: 2rem; font-weight: 900; margin-bottom: 2rem; color: #1e293b;">✏️ تعديل الفرع</h1>

        <div class="card" style="border-radius: 20px; padding: 2rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
            <form method="POST" action="{{ route('branches.update', $branch) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">اسم الفرع</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $branch->name) }}" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.875rem;" required>
                    @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">الموقع</label>
                    <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $branch->location) }}" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.875rem;" required>
                    @error('location')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">رقم الهاتف</label>
                    <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $branch->phone) }}" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.875rem;">
                    @error('phone')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.75rem;">الوصف</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.875rem; min-height: 120px;">{{ old('description', $branch->description) }}</textarea>
                    @error('description')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; padding: 0.875rem; border-radius: 10px; font-weight: 600; font-size: 1rem;">
                        ✅ حفظ التعديلات
                    </button>
                    <a href="{{ route('branches.index') }}" class="btn btn-secondary" style="flex: 1; padding: 0.875rem; border-radius: 10px; font-weight: 600; font-size: 1rem; text-decoration: none; text-align: center;">
                        ❌ إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection