<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('returned_goods', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name');
            $table->string('product_name');
            $table->text('reason');
            $table->date('issue_discovered_date');
            $table->enum('status', ['pending', 'returned', 'replaced', 'refunded'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('returned_goods');
    }
};
