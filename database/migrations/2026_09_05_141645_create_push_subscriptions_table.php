<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use NotificationChannels\WebPush\PushSubscription;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** @var string $tableName */
        $tableName = config('webpush.table_name');

        // ملاحظة: ما منستخدم Schema::connection(config('webpush.database_connection')) هون
        // عن قصد — هاي القيمة ثابتة على 'mysql' دايماً (افتراضي env('DB_CONNECTION'))، فلو
        // استخدمناها كانت هاي الميغريشن رح تتجاهل أي --database تانية بتترحّل فيها (زي ترحيل
        // معرض جديد على قاعدة بياناته الخاصة)، وتاخد الجدول لقاعدة بيانات معرض تاني غلط.
        // بترك Schema::create بدون connection حتى تحترم نفس اتصال بقية الميغريشنز.
        Schema::create($tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('subscribable', 'push_subscriptions_subscribable_morph_idx');
            $table->string('endpoint', PushSubscription::ENDPOINT_MAX_LENGTH)
                ->charset('ascii')
                ->unique();
            $table->string('public_key')->nullable();
            $table->string('auth_token')->nullable();
            $table->string('content_encoding')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /** @var string $tableName */
        $tableName = config('webpush.table_name');

        Schema::dropIfExists($tableName);
    }
};
