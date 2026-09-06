<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_laptop_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_laptop_id')->constrained()->onDelete('cascade');
            $table->string('image'); // مسار الصورة داخل storage/app/public
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_laptop_images');
    }
};
