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
        // جدول الصيانة
        Schema::create('mobile_maintenance', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('phone_number');
            $table->text('problem_description');
            $table->string('mobile_type');
            $table->enum('payment_method', ['نقدي', 'تطبيق', 'مختلط']);
            $table->decimal('cost', 10, 2);
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->timestamps();
        });

        // جدول المبيعا��
        Schema::create('mobile_sales', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('product_type');
            $table->integer('quantity');
            $table->enum('payment_method', ['نقدي', 'تطبيق', 'مختلط']);
            $table->decimal('cost', 10, 2);
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->timestamps();
        });

        // جدول المنتجات في المخزن
        Schema::create('mobile_inventory', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('model_type');
            $table->integer('quantity');
            $table->decimal('wholesale_price', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->timestamps();
        });

        // جدول الديون
        Schema::create('mobile_debts', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('phone_number');
            $table->string('type');
            $table->decimal('cash_amount', 10, 2)->default(0);
            $table->decimal('bank_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->date('debt_date');
            $table->date('payment_date')->nullable();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->timestamps();
        });

        // جدول المصروفات
        Schema::create('mobile_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('type');
            $table->integer('quantity')->default(1);
            $table->enum('payment_method', ['نقدي', 'بنكي', 'مختلط']);
            $table->decimal('cash_amount', 10, 2)->default(0);
            $table->decimal('bank_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->date('expense_date');
            $table->string('supplier_name')->nullable();
            $table->string('supplier_phone')->nullable();
            $table->string('id_photo')->nullable();
            $table->string('reference')->nullable();
            $table->string('defect')->nullable();
            $table->date('return_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_expenses');
        Schema::dropIfExists('mobile_debts');
        Schema::dropIfExists('mobile_inventory');
        Schema::dropIfExists('mobile_sales');
        Schema::dropIfExists('mobile_maintenance');
    }
};
