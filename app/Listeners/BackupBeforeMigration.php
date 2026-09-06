<?php

namespace App\Listeners;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class BackupBeforeMigration
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CommandStarting $event): void
    {
        // قائمة الأوامر التي تحتاج نسخة احتياطية قبل تنفيذها
        $dangerousCommands = [
            'migrate:refresh',
            'migrate:reset',
            'migrate:fresh',
            'db:wipe',
        ];

        $command = $event->command;

        // التحقق من أن الأمر المنفذ من الأوامر الخطرة
        if (in_array($command, $dangerousCommands)) {
            try {
                echo "\n🔒 تحذير: أنت على وشك تنفيذ أمر خطير!\n";
                echo "🔄 جاري إنشاء نسخة احتياطية قبل المتابعة...\n\n";
                
                // إنشاء نسخة احتياطية تلقائية
                Artisan::call('db:auto-backup', ['--type' => 'pre-migration']);
                
                echo "✅ تم إنشاء النسخة الاحتياطية بنجاح!\n";
                echo "📁 يمكنك استعادة قاعدة البيانات من: storage/backups/protected\n\n";
                
                // الانتظار 3 ثواني قبل المتابعة
                echo "⏳ المتابعة خلال 3 ثواني...\n";
                sleep(3);
                
                Log::info("تم إنشاء نسخة احتياطية تلقائية قبل تنفيذ: {$command}");
                
            } catch (\Exception $e) {
                echo "\n⚠️ تحذير: فشل إنشاء النسخة الاحتياطية!\n";
                echo "الخطأ: " . $e->getMessage() . "\n";
                echo "هل تريد المتابعة على أي حال؟ (yes/no): ";
                
                $handle = fopen("php://stdin", "r");
                $line = fgets($handle);
                
                if (trim($line) != 'yes') {
                    echo "تم إلغاء العملية.\n";
                    exit(1);
                }
                
                Log::error("فشل إنشاء نسخة احتياطية قبل: {$command}", [
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}