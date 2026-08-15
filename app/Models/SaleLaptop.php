<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleLaptop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand',
        'model',
        'processor',
        'ram',
        'storage',
        'gpu',
        'battery_life',
        'price',
        'discount',
        'description',
        'is_out_of_stock',
    ];

    protected $casts = [
        'price'            => 'decimal:2',
        'discount'         => 'decimal:2',
        'is_out_of_stock'  => 'boolean',
    ];

    public function images()
    {
        return $this->hasMany(SaleLaptopImage::class);
    }

    /**
     * الصورة الرئيسية (أول صورة في المعرض) تستخدم كصورة غلاف في الكروت
     */
    public function mainImage()
    {
        return $this->hasOne(SaleLaptopImage::class)->oldestOfMany();
    }

    public function getFinalPriceAttribute()
    {
        return $this->discount > 0
            ? $this->price * (1 - $this->discount / 100)
            : (float) $this->price;
    }
}
