<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'central';

    public function up(): void
    {
        Schema::connection('central')->create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_one_id')->constrained('chat_members')->cascadeOnDelete();
            $table->foreignId('member_two_id')->constrained('chat_members')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['member_one_id', 'member_two_id']);
        });
    }

    public function down(): void
    {
        Schema::connection('central')->dropIfExists('chat_conversations');
    }
};
