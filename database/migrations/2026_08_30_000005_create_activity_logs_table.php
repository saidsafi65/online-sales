<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('actor_type')->nullable(); // App\Models\User أو App\Models\Customer
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('actor_name')->nullable(); // نسخة ثابتة من الاسم وقت التعديل
            $table->string('action'); // created, updated, deleted
            $table->string('model_type'); // App\Models\Product ...
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('model_label')->nullable(); // وصف مختصر (مثلاً اسم المنتج)
            $table->json('changes')->nullable(); // {"field": {"old": ..., "new": ...}}
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
            $table->index(['actor_type', 'actor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};