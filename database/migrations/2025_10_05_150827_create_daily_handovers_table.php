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
        Schema::create('daily_handovers', function (Blueprint $table) {
            $table->id();
            $table->date('handover_date');
            $table->time('handover_time');
            $table->decimal('cash', 10, 2);
            $table->decimal('bank', 10, 2);
            $table->string('reason');
            $table->text('notes')->nullable();
            $table->string('received_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_handovers');
    }
};
