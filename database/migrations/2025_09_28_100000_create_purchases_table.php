<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('item'); // الصنف
            $table->string('type'); // النوع
            $table->unsignedInteger('quantity')->default(1); // الكمية
            $table->enum('payment_method', ['cash', 'app', 'mixed']); // الدفع
            // $table->decimal('amount', 10, 2)->default(0); // المبلغ الإجمالي
            $table->decimal('amount_cash', 10, 2)->default(0);
            $table->decimal('amount_bank', 10, 2)->default(0);
            $table->dateTime('purchase_date')->nullable(); // التاريخ
            $table->string('supplier_name')->nullable(); // اسم المورد
            $table->string('phone')->nullable(); // رقم الجوال
            $table->string('id_image')->nullable(); // صورة الهوية إن وجدت (مسار الملف)
            $table->boolean('is_returned')->default(false); // مرجع
            $table->text('issue')->nullable(); // العطل
            $table->dateTime('return_date')->nullable(); // تاريخ الارجاع
            $table->text('notes')->nullable(); // ملاحظات
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
