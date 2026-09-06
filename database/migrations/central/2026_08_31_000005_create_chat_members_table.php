<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'central';

    public function up(): void
    {
        Schema::connection('central')->create('chat_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->unsignedBigInteger('local_user_id');
            $table->string('name');
            $table->timestamp('last_seen_public_at')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'local_user_id']);
        });
    }

    public function down(): void
    {
        Schema::connection('central')->dropIfExists('chat_members');
    }
};
