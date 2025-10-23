<?php

namespace App\Models\Traits;

use App\Models\Scopes\BranchScope;

trait HasBranchScope
{
    public static function bootHasBranchScope()
    {
        static::addGlobalScope(new BranchScope());
    }
}
