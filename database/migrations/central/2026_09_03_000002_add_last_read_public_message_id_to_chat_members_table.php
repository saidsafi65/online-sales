<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'central';

    public function up(): void
    {
        Schema::connection('central')->table('chat_members', function (Blueprint $table) {
            $table->unsignedBigInteger('last_read_public_message_id')->nullable()->after('last_seen_public_at');
        });
    }

    public function down(): void
    {
        Schema::connection('central')->table('chat_members', function (Blueprint $table) {
            $table->dropColumn('last_read_public_message_id');
        });
    }
};
