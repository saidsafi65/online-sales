<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * لا في أي قناة توصل بها إشعار "الصيانة جاهزة" للزبون — رقم الجوال موجود بس
 * ما في تكامل SMS بالمشروع. أضفنا إيميل اختياري عند استلام الجهاز حتى نقدر
 * نبعتله إشعار حقيقي لما الصيانة تجهز.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repairs', function (Blueprint $table) {
            $table->string('email')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('repairs', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
};
