<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * app/Http/Controllers/Products/ProductController.php and CheckoutController
 * both read/write `products.quantity` and `products.branch_id`, but no prior
 * migration ever added either column (branch_id was backfilled onto most
 * tables by 2025_10_16_132305_add_branch_id_to_all_tables, but `products`
 * wasn't in that migration's table list) — adding a product or checking out
 * fails with "Unknown column" on a schema built from these migrations alone.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'quantity')) {
                $table->unsignedInteger('quantity')->default(0)->after('price');
            }
            if (! Schema::hasColumn('products', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->after('id')->constrained('branches')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'branch_id')) {
                $table->dropConstrainedForeignId('branch_id');
            }
            if (Schema::hasColumn('products', 'quantity')) {
                $table->dropColumn('quantity');
            }
        });
    }
};
