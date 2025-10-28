<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null')->after('password');
            $table->string('role')->default('employee')->after('branch_id');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn(['branch_id', 'role', 'status']);
        });
    }
};
