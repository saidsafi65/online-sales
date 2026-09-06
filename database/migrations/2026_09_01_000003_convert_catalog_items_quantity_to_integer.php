<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * catalog_items.quantity was a plain VARCHAR(120), not a number — nothing at
 * the DB layer stopped it from ever holding a negative or non-numeric value.
 * Sanitize any existing non-numeric/negative data first (so the type change
 * itself can't fail on production data we haven't seen), then convert to a
 * real unsigned integer.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('catalog_items')->whereRaw("quantity NOT REGEXP '^[0-9]+$'")->update(['quantity' => 0]);

        Schema::table('catalog_items', function (Blueprint $table) {
            $table->unsignedInteger('quantity')->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('catalog_items', function (Blueprint $table) {
            $table->string('quantity', 120)->change();
        });
    }
};
