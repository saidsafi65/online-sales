<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalog_items', function (Blueprint $table) {
            $table->id();
            // نجعل الطول 120 لكل حقل لضمان أن الفهرس المركب لا يتجاوز 1000 بايت مع utf8mb4
            $table->string('product', 120); // اسم المنتج
            $table->string('type', 120);    // النوع / الموديل
            $table->string('quantity', 120);    // الكمية
            $table->string('wholesale_price', 120);    // سعر الجملة
            $table->string('sale_price', 120);    // سعر البيع
            // إنشاء فهرس مركب على product و type لضمان عدم التكرار
            $table->unique(['product', 'type']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_items');
    }
};
