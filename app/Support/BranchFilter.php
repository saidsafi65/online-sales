<?php

namespace App\Support;

class BranchFilter
{
    /**
     * يقيّد أي استعلام (query builder يدوي، مش Eloquent) بفرع المستخدم الحالي، مع إبقاء
     * السجلات يلي بدون فرع محدد (branch_id = NULL) مرئية للجميع بدل ما تختفي بصمت —
     * نفس منطق BranchScope تماماً (شوف app/Models/Scopes/BranchScope.php)، بس هون
     * للاستعلامات اليدوية يلي مش عم تمر على الـ Eloquent global scope أصلاً.
     */
    public static function apply($query, string $column = 'branch_id')
    {
        $branchId = auth()->user()->branch_id;

        return $query->where(function ($q) use ($column, $branchId) {
            $q->where($column, $branchId)->orWhereNull($column);
        });
    }
}
