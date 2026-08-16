<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Software extends Model
{
    use HasFactory;

    protected $table = 'softwares';

    protected $fillable = [
        'name',
        'developer',
        'version',
        'category',
        'platform',
        'license_type',
        'price',
        'description',
        'image',
        'is_out_of_stock',
    ];

    protected $casts = [
        'price'           => 'decimal:2',
        'is_out_of_stock' => 'boolean',
    ];
}
