<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleLaptopImage extends Model
{
    use HasFactory;

    protected $fillable = ['sale_laptop_id', 'image'];

    public function laptop()
    {
        return $this->belongsTo(SaleLaptop::class, 'sale_laptop_id');
    }
}
