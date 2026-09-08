<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_claims', function (Blueprint $table) {
            $table->id();
            $table->string('claim_reference')->unique(); // مثال: OS-FC-310826-01
            $table->date('claim_date');
            $table->string('tor_number')->nullable();
            $table->string('currency', 10)->default('ILS');
            $table->enum('language', ['ar', 'en'])->default('ar');
            $table->string('organization_name');
            $table->string('attention_name')->nullable();
            $table->string('position')->nullable();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('financial_claim_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_claim_id')->constrained()->cascadeOnDelete();
            $table->integer('item_number');
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_claim_items');
        Schema::dropIfExists('financial_claims');
    }
};
