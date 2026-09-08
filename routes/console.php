<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;
use App\Http\Controllers\BackupController;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// جدولة النسخ الاحتياطي التلقائي — لكل معرض نشط (Console ما بيمر عبر
// ResolveTenantDatabase middleware، فـ autoBackupAllTenants بتعمل نفس تبديل الاتصال يدوياً
// لكل معرض بدورها بدل ما تعتمد على اتصال mysql الافتراضي بـ .env).
Schedule::call(function () {
    (new BackupController())->autoBackupAllTenants();
    Log::info('Daily backup run finished at ' . now());
})->daily()->at('02:00')->name('daily-backup')->withoutOverlapping();

// نسخة احتياطية أسبوعية (يوم الجمعة الساعة 3 صباحاً)
Schedule::call(function () {
    (new BackupController())->autoBackupAllTenants();
    Log::info('Weekly backup run finished at ' . now());
})->weeklyOn(5, '03:00')->name('weekly-backup');

// تنظيف النسخ القديمة (كل شهر) — الاحتفاظ بآخر 30 نسخة لكل معرض لحاله، مش 30 مجموع كل
// المعارض مع بعض (وإلا معرض نشيط ممكن ياكل حصة معرض تاني بالغلط).
Schedule::call(function () {
    $backupPath = storage_path('app/backups');
    $files = glob($backupPath . '/auto_backup_*.{sql,zip}', GLOB_BRACE);

    $groups = [];
    foreach ($files as $file) {
        $tag = preg_match('/auto_backup_(t\d+_)/', basename($file), $m) ? $m[1] : '';
        $groups[$tag][] = $file;
    }

    $deletedCount = 0;
    foreach ($groups as $group) {
        usort($group, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        foreach (array_slice($group, 30) as $file) {
            if (@unlink($file)) {
                $deletedCount++;
            }
        }
    }

    Log::info("Cleaned $deletedCount old backup files");
})->monthly()->name('cleanup-old-backups');