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
        Schema::create('part_compatibilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_id')->constrained()->onDelete('cascade');
            $table->foreignId('compatible_laptop_id')->constrained('laptops')->onDelete('cascade');
            $table->boolean('verified')->default(false); // تم التحقق من التوافق
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['part_id', 'compatible_laptop_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('part_compatibilities');
    }
};
