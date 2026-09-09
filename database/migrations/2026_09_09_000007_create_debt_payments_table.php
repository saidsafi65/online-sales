<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debt_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debt_id')->constrained()->cascadeOnDelete();
            // لو الدفعة جايه من فاتورة جملة آجلة/مختلطة، مربوطة هون — حذف دفعة الفاتورة
            // بيحذف دفعة الدين المقابلة تلقائياً (FK cascade)، فما بيصير تعارض بين الاثنين.
            $table->foreignId('wholesale_invoice_payment_id')->nullable()->constrained()->cascadeOnDelete();
            $table->decimal('cash_amount', 10, 2)->default(0);
            $table->decimal('bank_amount', 10, 2)->default(0);
            $table->date('payment_date');
            $table->string('received_by')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debt_payments');
    }
};
