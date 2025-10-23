<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasBranchScope;

class Part extends Model
{
    use HasBranchScope;
    protected $fillable = ['part_type_id', 'part_number', 'specifications', 'price'];

    protected $casts = [
        'specifications' => 'array',
    ];

    public function partType()
    {
        return $this->belongsTo(PartType::class);
    }

    public function laptops()
    {
        return $this->belongsToMany(Laptop::class, 'laptop_parts')
            ->withPivot('is_original', 'notes')
            ->withTimestamps();
    }

    // الأجهزة الأصلية لهذه القطعة
    public function originalLaptops()
    {
        return $this->belongsToMany(Laptop::class, 'laptop_parts')
            ->wherePivot('is_original', true);
    }

    // الأجهزة المتوافقة مع هذه القطعة
    public function compatibleLaptops()
    {
        return $this->belongsToMany(Laptop::class, 'part_compatibilities', 'part_id', 'compatible_laptop_id')
            ->withPivot('verified', 'notes')
            ->withTimestamps();
    }

    // جميع الأجهزة (الأصلية + المتوافقة)
    public function allCompatibleLaptops()
    {
        return $this->laptops()->get()->merge($this->compatibleLaptops()->get())->unique('id');
    }
}
