<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'central';

    public function up(): void
    {
        Schema::connection('central')->create('chat_public_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_member_id')->constrained('chat_members')->cascadeOnDelete();
            $table->text('body')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('central')->dropIfExists('chat_public_messages');
    }
};
