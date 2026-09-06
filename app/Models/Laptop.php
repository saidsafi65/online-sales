<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasBranchScope;

// Model 1: Laptop.php
class Laptop extends Model
{
    use HasBranchScope;
    protected $fillable = ['brand', 'model', 'description', 'image'];

    // القطع المرتبطة بهذا اللابتوب
    public function parts()
    {
        return $this->belongsToMany(Part::class, 'laptop_parts')
            ->withPivot('is_original', 'notes')
            ->withTimestamps();
    }

    // القطع الأصلية فقط
    public function originalParts()
    {
        return $this->belongsToMany(Part::class, 'laptop_parts')
            ->wherePivot('is_original', true)
            ->withPivot('notes')
            ->withTimestamps();
    }

    // الأجهزة المتوافقة (عبر القطع المشتركة)
    public function getCompatibleLaptops($partTypeId = null)
    {
        $query = self::whereHas('parts', function ($q) {
            $q->whereIn('part_id', $this->parts->pluck('id'));
        })->where('id', '!=', $this->id);

        if ($partTypeId) {
            $query->whereHas('parts', function ($q) use ($partTypeId) {
                $q->where('part_type_id', $partTypeId)
                    ->whereIn('part_id', $this->parts->where('part_type_id', $partTypeId)->pluck('id'));
            });
        }

        return $query->with('parts')->get();
    }

    // الحصول على قطعة معينة
    public function getPartByType($partTypeId)
    {
        return $this->parts()->where('part_type_id', $partTypeId)->first();
    }

    public function getFullNameAttribute()
    {
        return "{$this->brand} {$this->model}";
    }
}
