<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Http\Controllers\BackupController;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// جدولة النسخ الاحتياطي التلقائي
Schedule::call(function () {
    $backupController = new BackupController();
    $result = $backupController->autoBackup();
    
    if ($result) {
        \Log::info('Auto backup completed successfully at ' . now());
    } else {
        \Log::error('Auto backup failed at ' . now());
    }
})->daily()->at('02:00')->name('daily-backup')->withoutOverlapping();

// نسخة احتياطية أسبوعية (يوم الجمعة الساعة 3 صباحاً)
Schedule::call(function () {
    $backupController = new BackupController();
    $result = $backupController->autoBackup();
    
    if ($result) {
        \Log::info('Weekly backup completed successfully at ' . now());
    }
})->weeklyOn(5, '03:00')->name('weekly-backup');

// تنظيف النسخ القديمة (كل شهر)
Schedule::call(function () {
    $backupPath = storage_path('app/backups');
    $files = glob($backupPath . '/auto_backup_*.{sql,zip}', GLOB_BRACE);
    
    // ترتيب حسب التاريخ
    usort($files, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });
    
    // الاحتفاظ بآخر 30 نسخة
    $filesToDelete = array_slice($files, 30);
    $deletedCount = 0;
    
    foreach ($filesToDelete as $file) {
        if (@unlink($file)) {
            $deletedCount++;
        }
    }
    
    \Log::info("Cleaned $deletedCount old backup files");
})->monthly()->name('cleanup-old-backups');