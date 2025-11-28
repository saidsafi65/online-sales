<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use ZipArchive;

class AutoBackupDatabase extends Command
{
    /**
     * اسم الأمر
     */
    protected $signature = 'db:auto-backup {--type=daily}';

    /**
     * وصف الأمر
     */
    protected $description = 'إنشاء نسخة احتياطية تلقائية من قاعدة البيانات';

    /**
     * تنفيذ الأمر
     */
    public function handle()
    {
        $type = $this->option('type');
        $this->info("🔄 بدء عملية النسخ الاحتياطي التلقائي ({$type})...");

        try {
            $timestamp = date('Y-m-d_H-i-s');
            $filename = "auto_{$type}_backup_{$timestamp}.sql";
            $zipFilename = "auto_{$type}_backup_{$timestamp}.zip";
            
            // مسار مجلد النسخ الاحتياطية المحمي
            $protectedBackupPath = storage_path('backups/protected');
            $regularBackupPath = storage_path('app/backups');
            
            // إنشاء المجلدات إذا لم تكن موجودة
            if (!file_exists($protectedBackupPath)) {
                mkdir($protectedBackupPath, 0755, true);
            }
            if (!file_exists($regularBackupPath)) {
                mkdir($regularBackupPath, 0755, true);
            }

            $sqlPath = $protectedBackupPath . '/' . $filename;
            $zipPath = $protectedBackupPath . '/' . $zipFilename;

            // الحصول على معلومات قاعدة البيانات
            $host = Config::get('database.connections.mysql.host');
            $port = Config::get('database.connections.mysql.port', '3306');
            $username = Config::get('database.connections.mysql.username');
            $password = Config::get('database.connections.mysql.password');
            $database = Config::get('database.connections.mysql.database');

            $this->info("📦 جاري تصدير قاعدة البيانات: {$database}");

            // بناء أمر mysqldump
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s --port=%s %s --routines --triggers --single-transaction --quick --lock-tables=false > %s 2>&1',
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($database),
                escapeshellarg($sqlPath)
            );

            // تنفيذ الأمر
            exec($command, $output, $returnVar);

            if ($returnVar === 0 && file_exists($sqlPath) && filesize($sqlPath) > 0) {
                $this->info("✅ تم إنشاء النسخة الاحتياطية بنجاح!");
                
                // إنشاء ملف معلومات
                $infoFile = $protectedBackupPath . "/info_{$timestamp}.txt";
                $this->createBackupInfo($infoFile, $database, $type);
                
                // ضغط الملفات
                if ($this->createZipBackup($sqlPath, $infoFile, $zipPath)) {
                    $this->info("🗜️ تم ضغط النسخة الاحتياطية");
                    
                    // حذف الملفات المؤقتة
                    @unlink($sqlPath);
                    @unlink($infoFile);
                    
                    // نسخ إلى المجلد العادي أيضاً
                    copy($zipPath, $regularBackupPath . '/' . $zipFilename);
                    
                    $fileSize = $this->formatBytes(filesize($zipPath));
                    $this->info("📊 حجم الملف: {$fileSize}");
                    $this->info("📁 المسار المحمي: {$zipPath}");
                    $this->info("📁 المسار العادي: " . $regularBackupPath . '/' . $zipFilename);
                } else {
                    $this->warn("⚠️ فشل الضغط، تم حفظ الملف بصيغة SQL");
                }
                
                // تنظيف النسخ القديمة
                $this->cleanOldBackups($protectedBackupPath, $type);
                
                return Command::SUCCESS;
            } else {
                $errorMsg = 'فشل إنشاء النسخة الاحتياطية.';
                if (!empty($output)) {
                    $errorMsg .= "\nالخطأ: " . implode("\n", $output);
                }
                $this->error($errorMsg);
                return Command::FAILURE;
            }

        } catch (\Exception $e) {
            $this->error("❌ خطأ: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * إنشاء ملف معلومات النسخة الاحتياطية
     */
    private function createBackupInfo($infoFile, $database, $type)
    {
        $info = "=== معلومات النسخة الاحتياطية ===\n\n";
        $info .= "النوع: {$type}\n";
        $info .= "التاريخ: " . date('Y-m-d H:i:s') . "\n";
        $info .= "قاعدة البيانات: {$database}\n";
        $info .= "إصدار Laravel: " . app()->version() . "\n";
        $info .= "إصدار PHP: " . PHP_VERSION . "\n\n";
        
        $info .= "=== الجداول المضمنة ===\n\n";
        $tables = [
            'users' => 'المستخدمين',
            'branches' => 'الفروع',
            'sales' => 'المبيعات',
            'repairs' => 'الصيانة',
            'purchases' => 'المشتريات',
            'catalog_items' => 'الكتالوج',
            'invoices' => 'الفواتير',
            'debts' => 'الديون',
            'stores' => 'المخزن',
            'obligations' => 'الالتزامات',
            'daily_handovers' => 'التسليمات اليومية',
            'customer_orders' => 'طلبات العملاء',
            'returned_goods' => 'البضائع المرتجعة',
            'maintenance_deposits' => 'إيداعات الصيانة',
            'maintenance_parts' => 'قطع الصيانة',
            'laptops' => 'أجهزة اللابتوب',
            'parts' => 'القطع',
            'mobile_maintenance' => 'صيانة الجوال',
            'mobile_sales' => 'مبيعات الجوال',
            'mobile_inventory' => 'مخزون الجوال',
        ];

        foreach ($tables as $table => $description) {
            $info .= "- {$table} ({$description})\n";
        }
        
        $info .= "\n=== ملاحظة مهمة ===\n";
        $info .= "هذه نسخة احتياطية محمية. لا تحذف هذا الملف.\n";
        $info .= "يمكن استعادتها في حالة حدوث أي مشكلة.\n";

        file_put_contents($infoFile, $info);
    }

    /**
     * إنشاء ملف مضغوط
     */
    private function createZipBackup($sqlPath, $infoPath, $zipPath)
    {
        if (!class_exists('ZipArchive')) {
            return false;
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            $zip->addFile($sqlPath, basename($sqlPath));
            $zip->addFile($infoPath, basename($infoPath));
            $zip->close();
            return true;
        }

        return false;
    }

    /**
     * تنظيف النسخ القديمة
     */
    private function cleanOldBackups($backupPath, $type)
    {
        $keepCount = match($type) {
            'daily' => 30,      // الاحتفاظ بآخر 30 يوم
            'weekly' => 12,     // الاحتفاظ بآخر 12 أسبوع
            'monthly' => 12,    // الاحتفاظ بآخر 12 شهر
            'pre-migration' => 10, // الاحتفاظ بآخر 10 نسخ قبل Migration
            default => 7
        };

        $pattern = "auto_{$type}_backup_*.zip";
        $files = glob($backupPath . '/' . $pattern);

        if (count($files) <= $keepCount) {
            return;
        }

        // ترتيب حسب وقت التعديل
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        // حذف الملفات الزائدة
        $filesToDelete = array_slice($files, $keepCount);
        $deletedCount = 0;
        
        foreach ($filesToDelete as $file) {
            if (@unlink($file)) {
                $deletedCount++;
                // حذف ملف المعلومات المرافق إن وجد
                $infoFile = str_replace('.zip', '.txt', $file);
                $infoFile = str_replace('auto_', 'info_', $infoFile);
                @unlink($infoFile);
            }
        }

        if ($deletedCount > 0) {
            $this->info("🗑️ تم حذف {$deletedCount} نسخة احتياطية قديمة ({$type})");
        }
    }

    /**
     * تنسيق حجم الملف
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}