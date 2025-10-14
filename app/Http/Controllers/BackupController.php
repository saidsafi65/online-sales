<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BackupController extends Controller
{
    // عرض صفحة النسخ الاحتياطي
    public function index()
    {
        $backups = $this->getBackupFiles();

        return view('backup.index', compact('backups'));
    }

    // صفحة إنشاء نسخة احتياطية
    public function create()
    {
        return view('backup.create');
    }

    // إنشاء نسخة احتياطية جديدة (شاملة)
    public function store(Request $request)
    {
        try {
            $timestamp = date('Y-m-d_H-i-s');
            $filename = 'backup_' . $timestamp . '.sql';
            $zipFilename = 'backup_' . $timestamp . '.zip';
            $path = storage_path('app/backups/' . $filename);
            $zipPath = storage_path('app/backups/' . $zipFilename);

            // إنشاء المجلد إذا لم يكن موجوداً
            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }

            // الحصول على معلومات قاعدة البيانات
            $host = Config::get('database.connections.mysql.host');
            $port = Config::get('database.connections.mysql.port', '3306');
            $username = Config::get('database.connections.mysql.username');
            $password = Config::get('database.connections.mysql.password');
            $database = Config::get('database.connections.mysql.database');

            // بناء أمر mysqldump مع خيارات متقدمة
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s --port=%s %s --routines --triggers --single-transaction --quick --lock-tables=false > %s 2>&1',
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($database),
                escapeshellarg($path)
            );

            // تنفيذ الأمر
            exec($command, $output, $returnVar);

            // التحقق من نجاح العملية
            if ($returnVar === 0 && file_exists($path) && filesize($path) > 0) {
                
                // إنشاء ملف معلومات النسخة الاحتياطية
                $infoFile = storage_path('app/backups/info_' . $timestamp . '.txt');
                $this->createBackupInfo($infoFile, $database);

                // ضغط الملفات
                if ($this->createZipBackup($path, $infoFile, $zipPath)) {
                    // حذف الملفات المؤقتة
                    @unlink($path);
                    @unlink($infoFile);

                    return redirect()->route('backup.index')
                        ->with('success', 'تم إنشاء النسخة الاحتياطية بنجاح! الملف: ' . $zipFilename);
                } else {
                    return redirect()->route('backup.index')
                        ->with('success', 'تم إنشاء النسخة الاحتياطية بنجاح! (بدون ضغط)');
                }
            } else {
                $errorMsg = 'فشل إنشاء النسخة الاحتياطية.';
                if (!empty($output)) {
                    $errorMsg .= ' الخطأ: ' . implode("\n", $output);
                }
                
                return redirect()->back()
                    ->with('error', $errorMsg);
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    // إنشاء ملف معلومات النسخة الاحتياطية
    private function createBackupInfo($infoFile, $database)
    {
        $info = "=== معلومات النسخة الاحتياطية ===\n\n";
        $info .= "التاريخ: " . date('Y-m-d H:i:s') . "\n";
        $info .= "قاعدة البيانات: " . $database . "\n";
        $info .= "إصدار Laravel: " . app()->version() . "\n";
        $info .= "إصدار PHP: " . PHP_VERSION . "\n\n";
        
        $info .= "=== الجداول المضمنة في النسخة الاحتياطية ===\n\n";
        $tables = [
            'sales' => 'المبيعات',
            'repairs' => 'الصيانة',
            'purchases' => 'المشتريات',
            'catalog_items' => 'قائمة المنتجات',
            'maintenance_deposits' => 'إيداعات الصيانة',
            'obligations' => 'الالتزامات',
            'invoices' => 'الفواتير',
            'invoice_items' => 'بنود الفواتير',
            'laptops' => 'أجهزة اللابتوب',
            'part_types' => 'أنواع القطع',
            'parts' => 'القطع',
            'laptop_parts' => 'قطع اللابتوب',
            'part_compatibilities' => 'توافق القطع',
            'customer_orders' => 'طلبات العملاء',
            'daily_handovers' => 'التسليمات اليومية',
            'returned_goods' => 'البضائع المرتجعة',
            'stores' => 'المخزن',
            'debts' => 'الديون',
            'maintenance_parts' => 'قطع الصيانة',
            'users' => 'المستخدمين',
        ];

        foreach ($tables as $table => $description) {
            $info .= "- {$table} ({$description})\n";
        }

        file_put_contents($infoFile, $info);
    }

    // إنشاء ملف مضغوط
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

    // صفحة رفع نسخة احتياطية
    public function upload()
    {
        return view('backup.upload');
    }

    // رفع وحفظ النسخة الاحتياطية
    public function storeUpload(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sql,zip|max:512000', // 500MB max
        ], [
            'backup_file.required' => 'يرجى اختيار ملف النسخة الاحتياطية',
            'backup_file.mimes' => 'يجب أن يكون الملف بصيغة SQL أو ZIP',
            'backup_file.max' => 'حجم الملف يجب أن لا يتجاوز 500 ميجابايت',
        ]);

        try {
            $file = $request->file('backup_file');
            $extension = $file->getClientOriginalExtension();
            $filename = 'uploaded_' . date('Y-m-d_H-i-s') . '.' . $extension;

            // حفظ الملف
            $file->storeAs('backups', $filename);

            return redirect()->route('backup.index')
                ->with('success', 'تم رفع النسخة الاحتياطية بنجاح!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء رفع الملف: ' . $e->getMessage());
        }
    }

    // تحميل نسخة احتياطية
    public function download($filename)
    {
        $path = storage_path('app/backups/' . $filename);

        if (!file_exists($path)) {
            return redirect()->back()
                ->with('error', 'الملف غير موجود');
        }

        return response()->download($path);
    }

    // استعادة نسخة احتياطية
    public function restore($filename)
    {
        try {
            $path = storage_path('app/backups/' . $filename);

            if (!file_exists($path)) {
                return redirect()->back()
                    ->with('error', 'الملف غير موجود');
            }

            // إذا كان الملف مضغوط، فك الضغط أولاً
            if (pathinfo($filename, PATHINFO_EXTENSION) === 'zip') {
                $extractPath = storage_path('app/backups/temp_restore/');
                
                if (!file_exists($extractPath)) {
                    mkdir($extractPath, 0755, true);
                }

                $zip = new ZipArchive();
                if ($zip->open($path) === TRUE) {
                    $zip->extractTo($extractPath);
                    $zip->close();

                    // البحث عن ملف SQL
                    $sqlFiles = glob($extractPath . '*.sql');
                    if (empty($sqlFiles)) {
                        return redirect()->back()
                            ->with('error', 'لم يتم العثور على ملف SQL في الملف المضغوط');
                    }
                    $path = $sqlFiles[0];
                } else {
                    return redirect()->back()
                        ->with('error', 'فشل فك ضغط الملف');
                }
            }

            $host = Config::get('database.connections.mysql.host');
            $port = Config::get('database.connections.mysql.port', '3306');
            $username = Config::get('database.connections.mysql.username');
            $password = Config::get('database.connections.mysql.password');
            $database = Config::get('database.connections.mysql.database');

            // تنفيذ أمر الاستعادة
            $command = sprintf(
                'mysql --user=%s --password=%s --host=%s --port=%s %s < %s 2>&1',
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($database),
                escapeshellarg($path)
            );

            exec($command, $output, $returnVar);

            // حذف المجلد المؤقت إذا كان موجوداً
            if (isset($extractPath) && file_exists($extractPath)) {
                $this->deleteDirectory($extractPath);
            }

            if ($returnVar === 0) {
                // مسح الكاش بعد الاستعادة
                Artisan::call('cache:clear');
                Artisan::call('config:clear');
                
                return redirect()->route('backup.index')
                    ->with('success', 'تم استعادة النسخة الاحتياطية بنجاح!');
            } else {
                $errorMsg = 'فشلت عملية الاستعادة';
                if (!empty($output)) {
                    $errorMsg .= ': ' . implode("\n", $output);
                }
                
                return redirect()->back()
                    ->with('error', $errorMsg);
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    // حذف نسخة احتياطية
    public function destroy($filename)
    {
        try {
            $path = storage_path('app/backups/' . $filename);

            if (file_exists($path)) {
                unlink($path);

                return redirect()->back()
                    ->with('success', 'تم حذف النسخة الاحتياطية بنجاح!');
            }

            return redirect()->back()
                ->with('error', 'الملف غير موجود');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    // الحصول على قائمة ملفات النسخ الاحتياطي
    private function getBackupFiles()
    {
        $backupPath = storage_path('app/backups');

        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0755, true);
            return [];
        }

        $files = array_merge(
            glob($backupPath . '/*.sql'),
            glob($backupPath . '/*.zip')
        );
        
        $backups = [];

        foreach ($files as $file) {
            if (strpos(basename($file), 'info_') === 0) {
                continue; // تخطي ملفات المعلومات
            }

            $backups[] = [
                'name' => basename($file),
                'size' => $this->formatBytes(filesize($file)),
                'date' => date('Y-m-d H:i:s', filemtime($file)),
                'type' => pathinfo($file, PATHINFO_EXTENSION),
            ];
        }

        // ترتيب حسب التاريخ (الأحدث أولاً)
        usort($backups, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return $backups;
    }

    // تنسيق حجم الملف
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    // حذف مجلد وكل محتوياته
    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }

    // إنشاء نسخة احتياطية تلقائية (يمكن استخدامها مع Scheduler)
    public function autoBackup()
    {
        try {
            $timestamp = date('Y-m-d_H-i-s');
            $filename = 'auto_backup_' . $timestamp . '.sql';
            $path = storage_path('app/backups/' . $filename);

            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }

            $host = Config::get('database.connections.mysql.host');
            $port = Config::get('database.connections.mysql.port', '3306');
            $username = Config::get('database.connections.mysql.username');
            $password = Config::get('database.connections.mysql.password');
            $database = Config::get('database.connections.mysql.database');

            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s --port=%s %s --routines --triggers --single-transaction > %s 2>&1',
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($database),
                escapeshellarg($path)
            );

            exec($command, $output, $returnVar);

            // حذف النسخ القديمة (الاحتفاظ بآخر 7 نسخ تلقائية)
            $this->cleanOldBackups();

            return $returnVar === 0;

        } catch (\Exception $e) {
            \Log::error('Auto backup failed: ' . $e->getMessage());
            return false;
        }
    }

    // حذف النسخ الاحتياطية القديمة
    private function cleanOldBackups($keepCount = 7)
    {
        $backupPath = storage_path('app/backups');
        $files = glob($backupPath . '/auto_backup_*.{sql,zip}', GLOB_BRACE);

        if (count($files) <= $keepCount) {
            return;
        }

        // ترتيب حسب وقت التعديل
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        // حذف الملفات الزائدة
        $filesToDelete = array_slice($files, $keepCount);
        foreach ($filesToDelete as $file) {
            @unlink($file);
        }
    }
}