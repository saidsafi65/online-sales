<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartType extends Model
{
    protected $fillable = ['name', 'name_en'];

    public function parts()
    {
        return $this->hasMany(Part::class);
    }
}
