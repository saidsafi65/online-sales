<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasBranchScope;

class PartType extends Model
{
    use HasBranchScope;
    protected $fillable = ['name', 'name_en'];

    public function parts()
    {
        return $this->hasMany(Part::class);
    }
}
