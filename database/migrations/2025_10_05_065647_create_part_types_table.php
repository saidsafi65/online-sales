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
        Schema::create('part_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // شاشة, بطارية, لوحة أم, WiFi, هيكل
            $table->string('name_en')->nullable(); // Screen, Battery, Motherboard, WiFi, Case
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('part_types');
    }
};
