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
        Schema::create('repairs', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name'); // الاسم
            $table->string('device_name'); // اسم الجهاز
            $table->string('model'); // الموديل
            $table->text('issue'); // المشكلة / العطل
            $table->string('phone', 20); // رقم الجوال
            $table->dateTime('received_date'); // تاريخ الاستلام
            $table->decimal('cost_cash', 10, 2)->default(0); // ا��تكلفة
            $table->decimal('cost_bank', 10, 2)->default(0); // ا��تكلفة
            $table->string('payment_method', 20); // الدفع: cash/app/mixed
            $table->dateTime('delivery_date')->nullable(); // تاريخ التسليم
            $table->string('received_by'); // الموظف المستلم
            $table->boolean('is_returned')->default(false); // مرجع
            $table->text('return_reason')->nullable(); // سبب الارجاع
            $table->dateTime('return_date')->nullable(); // تاريخ الارجاع
            $table->decimal('return_cost', 10, 2)->nullable(); // التكلفة الارجاع
            $table->dateTime('return_delivery_date')->nullable(); // تاريخ تسليم المرجع
            $table->text('notes')->nullable(); // ملاحظات
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repairs');
    }
};
