<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('maintenance_parts', function (Blueprint $table) {
            $table->id();
            $table->string('device_name'); // اسم الجهاز (مثل: لابتوب)
            $table->string('brand'); // العلامة التجارية (مثل: HP)
            $table->string('model'); // الموديل (مثل: 250 G6)
            
            // قطع الصيانة
            $table->string('screen')->nullable(); // شاشة
            $table->string('motherboard')->nullable(); // لوحة أم
            $table->string('screen_cover')->nullable(); // شلد شاشة
            $table->string('battery')->nullable(); // بطارية
            $table->string('keyboard')->nullable(); // لوحة مفاتيح
            $table->string('wifi_card')->nullable(); // قطعة wifi
            $table->string('hard_drive')->nullable(); // هارد
            $table->string('ram')->nullable(); // رام
            $table->string('charger')->nullable(); // شاحن
            $table->string('fan')->nullable(); // مروحة
            $table->text('other_parts')->nullable(); // قطع أخرى
            
            // معلومات إضافية
            $table->text('notes')->nullable(); // ملاحظات
            $table->enum('status', ['متوفر', 'غير متوفر', 'قيد الطلب'])->default('متوفر');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_parts');
    }
};
