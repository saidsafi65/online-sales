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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('product_type');
            $table->integer('quantity');
            $table->string('supplier_name');
            $table->decimal('wholesale_price', 10, 2);
            $table->enum('payment_method', ['نقدي', 'بنكي', 'مختلط']);
            $table->decimal('cash_amount', 10, 2);
            $table->decimal('bank_amount', 10, 2);
            $table->date('date_added');
            $table->timestamps(); // for created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
