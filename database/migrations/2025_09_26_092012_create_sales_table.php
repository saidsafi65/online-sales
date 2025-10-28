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
        Schema::create('sales', function (Blueprint $table) {
            $table->id(); // المعرف الفريد لكل عملية بيع
            $table->string('product'); // اسم المنتج
            $table->string('type'); // النوع أو الموديل
            $table->dateTime('sale_date'); // تاريخ ووقت البيع
            $table->string('payment_method', 20); // طريقة الدفع
            $table->decimal('cash_amount', 10, 2)->nullable(); // مبلغ الكاش
            $table->decimal('app_amount', 10, 2)->nullable(); // مبلغ الدفع عبر التطبيق
            $table->boolean('is_returned')->default(false); // هل هي عملية مرجعة أم لا
            $table->text('notes')->nullable(); // الملاحظات
            $table->timestamps(); // timestamps (created_at, updated_at)
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
