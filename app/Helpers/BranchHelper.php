<?php
namespace App\Helpers;

class BranchHelper {
    /**
     * الحصول على user_branch_id الحالي
     */
    public static function getCurrentBranchId() {
        $user = auth()->user();
        return $user?->isAdmin() ? null : $user?->branch_id;
    }

    /**
     * التحقق من إذا كان المستخدم له صلاحية على الفرع
     */
    public static function canAccessBranch($branchId) {
        $user = auth()->user();
        return $user->isAdmin() || $user->branch_id == $branchId;
    }

    /**
     * تطبيق فيلتر الفرع على Query
     */
    public static function applyBranchFilter($query) {
        if (!auth()->user()->isAdmin()) {
            $query->where('branch_id', auth()->user()->branch_id);
        }
        return $query;
    }
}