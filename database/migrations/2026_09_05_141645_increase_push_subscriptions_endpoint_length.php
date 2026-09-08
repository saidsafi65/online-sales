<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use NotificationChannels\WebPush\PushSubscription;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // نفس ملاحظة create_push_subscriptions_table: ما منستخدم config('webpush.database_connection')
        // لأنها ثابتة على 'mysql' وبتخلي هاي الميغريشن تتجاهل --database (بتكسر ترحيل معارض جديدة).
        $table = config('webpush.table_name');

        Schema::table($table, function (Blueprint $blueprint): void {
            $blueprint->dropUnique(['endpoint']);
        });

        Schema::table($table, function (Blueprint $blueprint): void {
            $blueprint->string('endpoint', PushSubscription::ENDPOINT_MAX_LENGTH)
                ->charset('ascii')
                ->unique()
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table = config('webpush.table_name');

        Schema::table($table, function (Blueprint $blueprint): void {
            $blueprint->dropUnique(['endpoint']);
        });

        Schema::table($table, function (Blueprint $blueprint): void {
            $blueprint->string('endpoint', 500)
                ->unique()
                ->change();
        });
    }
};
