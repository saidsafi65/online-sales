<?php

namespace App\Models\Traits;

use App\Models\Scopes\BranchScope;
use Illuminate\Support\Facades\Schema;

trait HasBranchScope
{
    public static function bootHasBranchScope()
    {
        static::addGlobalScope(new BranchScope());

        // Automatically set branch_id on create if applicable
        static::creating(function ($model) {
            try {
                if (! auth()->check()) {
                    return;
                }

                $user = auth()->user();
                // Don't set branch for admins
                if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
                    return;
                }

                $table = $model->getTable();
                if (! Schema::hasColumn($table, 'branch_id')) {
                    return;
                }

                // Only set if not provided already
                if (empty($model->branch_id)) {
                    $model->branch_id = $user->branch_id;
                }
            } catch (\Exception $e) {
                // fail-safe: avoid breaking CLI/migrations
                return;
            }
        });
    }
}
