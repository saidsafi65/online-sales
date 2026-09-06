<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('obligations', function (Blueprint $table) {
            $table->id();
            $table->string('expense_type');  // نوع البند (الرواتب، الإيجار، ... إلخ)
            $table->string('payment_type');  // طريقة الدفع
            $table->decimal('cash_amount', 10, 2)->nullable();  // حقل المبلغ النقدي (إن وجد)
            $table->decimal('bank_amount', 10, 2)->nullable();  // حقل المبلغ البنكي (إن وجد)
            $table->date('date');  // تاريخ الالتزام
            $table->text('detail')->nullable();  // تفاصيل إضافية
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obligations');
    }
};
