<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class BranchScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     * Only applies when a non-admin user is authenticated and the table has a branch_id column.
     */
    public function apply(Builder $builder, Model $model)
    {
        try {
            if (!auth()->check()) {
                return;
            }

            $user = auth()->user();
            if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
                return; // admin sees all branches
            }

            $table = $model->getTable();

            // Only apply if the table actually has a branch_id column
            if (!Schema::hasColumn($table, 'branch_id')) {
                return;
            }

            $builder->where($table . '.branch_id', $user->branch_id);
        } catch (\Exception $e) {
            // Fail-safe: if anything goes wrong (no DB connection during some commands), do not apply scope.
            return;
        }
    }
}
