<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wholesale_invoices', function (Blueprint $table) {
            $table->decimal('cash_paid_now', 10, 2)->nullable()->after('due_date');
            $table->foreignId('debt_id')->nullable()->after('cash_paid_now')->constrained('debts')->nullOnDelete();
        });

        // نضيف 'mixed' لقائمة شروط الدفع — جزء نقدي بيتحصل فوراً والباقي يصير دين آجل
        DB::statement("ALTER TABLE wholesale_invoices MODIFY payment_terms ENUM('cash', 'credit', 'mixed') DEFAULT 'cash'");
    }

    public function down(): void
    {
        Schema::table('wholesale_invoices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('debt_id');
            $table->dropColumn('cash_paid_now');
        });

        DB::statement("ALTER TABLE wholesale_invoices MODIFY payment_terms ENUM('cash', 'credit') DEFAULT 'cash'");
    }
};
