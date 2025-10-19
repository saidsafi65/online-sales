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
       DB::table('branches')->insert([
            ['name' => 'خانيونس', 'location' => 'خانيونس', 'phone' => '0599111111', 'created_at' => now()],
            ['name' => 'الدير', 'location' => 'الدير', 'phone' => '0599222222', 'created_at' => now()],
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
