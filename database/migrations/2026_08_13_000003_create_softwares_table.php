<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('softwares', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // اسم البرنامج
            $table->string('developer')->nullable(); // الشركة المطورة
            $table->string('version')->nullable();    // الإصدار
            $table->string('category')->nullable();   // التصنيف (تصميم، مكتبي، ألعاب...)
            $table->string('platform')->nullable();    // نظام التشغيل المدعوم (Windows / Mac / الكل)
            $table->string('license_type')->nullable(); // نوع الترخيص (مدى الحياة، اشتراك سنوي...)
            $table->decimal('price', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_out_of_stock')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('softwares');
    }
};
