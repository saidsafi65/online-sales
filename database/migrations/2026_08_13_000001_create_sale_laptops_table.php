<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_laptops', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // اسم اللابتوب (مثال: Dell XPS 15)
            $table->string('brand');                 // الماركة (Dell, HP, Lenovo...)
            $table->string('model')->nullable();     // الموديل / الرقم التسلسلي للموديل
            $table->string('processor');              // المعالج
            $table->string('ram');                    // الرام (مثال: 16GB DDR5)
            $table->string('storage');                // الهارد (مثال: 512GB SSD)
            $table->string('gpu')->nullable();        // كرت الشاشة
            $table->string('battery_life')->nullable(); // مدة البطارية (مثال: حتى 10 ساعات)
            $table->decimal('price', 10, 2);
            $table->decimal('discount', 5, 2)->default(0); // نسبة الخصم %
            $table->text('description')->nullable();
            $table->boolean('is_out_of_stock')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_laptops');
    }
};
