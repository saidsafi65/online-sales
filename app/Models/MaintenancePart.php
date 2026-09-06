<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenancePart extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_name',
        'brand',
        'model',
        'screen',
        'motherboard',
        'screen_cover',
        'battery',
        'keyboard',
        'wifi_card',
        'hard_drive',
        'ram',
        'charger',
        'fan',
        'other_parts',
        'notes',
        'status'
    ];

    // الحصول على جميع القطع المتوفرة
    public function getAvailablePartsAttribute()
    {
        $parts = [];
        
        $partFields = [
            'screen' => 'شاشة',
            'motherboard' => 'لوحة أم',
            'screen_cover' => 'شلد شاشة',
            'battery' => 'بطارية',
            'keyboard' => 'لوحة مفاتيح',
            'wifi_card' => 'قطعة WiFi',
            'hard_drive' => 'هارد',
            'ram' => 'رام',
            'charger' => 'شاحن',
            'fan' => 'مروحة'
        ];

        foreach ($partFields as $field => $label) {
            if (!empty($this->$field)) {
                $parts[$label] = $this->$field;
            }
        }

        return $parts;
    }

    // البحث
    public function scopeSearch($query, $search)
    {
        return $query->where('device_name', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
    }
}