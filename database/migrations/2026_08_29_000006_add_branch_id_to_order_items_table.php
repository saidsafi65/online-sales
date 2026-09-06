<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // إذا العمود موجود أصلاً (من محاولة سابقة فشلت بمنتصفها)، منتخطى الإضافة
        if (!Schema::hasColumn('order_items', 'branch_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('product_id')->constrained()->nullOnDelete();
            });
        } else {
            // العمود موجود بس بدون الـ foreign key (لأن المحاولة السابقة فشلت عليه تحديداً)
            // نتأكد الـ constraint موجود، وإذا مش موجود نضيفه لحاله
            $hasForeignKey = collect(Schema::getForeignKeys('order_items'))
                ->contains(fn ($fk) => in_array('branch_id', $fk['columns']));

            if (!$hasForeignKey) {
                Schema::table('order_items', function (Blueprint $table) {
                    $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('branch_id');
        });
    }
};