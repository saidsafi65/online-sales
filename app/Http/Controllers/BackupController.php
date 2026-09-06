<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
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
        $startedAt = microtime(true);

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
            // ملاحظة: stderr بينحط بملف منفصل (مش 2>&1 داخل ملف الـ SQL نفسه) — mysqldump
            // بيطبع تحذيره القياسي "Using a password on the command line..." على stderr بكل
            // مرة، ولو انخلط مع الملف بيصير أول سطر فيه، وأي استعادة بعدين بتفشل بخطأ SQL syntax
            // لأنه أول سطر مش SQL أصلاً.
            $errLogPath = storage_path('app/backups/.mysqldump_err_' . $timestamp . '.log');
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s --port=%s %s --routines --triggers --single-transaction --quick --lock-tables=false > %s 2> %s',
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($database),
                escapeshellarg($path),
                escapeshellarg($errLogPath)
            );

            // تنفيذ الأمر
            exec($command, $output, $returnVar);

            $stderr = file_exists($errLogPath) ? trim(file_get_contents($errLogPath)) : '';
            @unlink($errLogPath);
            $duration = round(microtime(true) - $startedAt, 2);

            // التحقق من نجاح العملية
            if ($returnVar === 0 && file_exists($path) && filesize($path) > 0) {

                $tableCount = substr_count(file_get_contents($path), '-- Table structure for table');

                // إنشاء ملف معلومات النسخة الاحتياطية
                $infoFile = storage_path('app/backups/info_' . $timestamp . '.txt');
                $this->createBackupInfo($infoFile, $database);

                // ضغط الملفات
                $zipped = $this->createZipBackup($path, $zipPath, $infoFile);
                $finalFilename = $zipped ? $zipFilename : $filename;
                $finalSize = $this->formatBytes(filesize($zipped ? $zipPath : $path));

                if ($zipped) {
                    // حذف الملفات المؤقتة
                    @unlink($path);
                    @unlink($infoFile);
                }

                $details = "الملف: {$finalFilename}\n"
                    . "عدد الجداول: {$tableCount}\n"
                    . "حجم الملف: {$finalSize}" . ($zipped ? '' : ' (بدون ضغط)') . "\n"
                    . "المدة: {$duration} ثانية";

                return redirect()->route('backup.index')
                    ->with('success', $zipped
                        ? 'تم إنشاء النسخة الاحتياطية بنجاح! الملف: ' . $zipFilename
                        : 'تم إنشاء النسخة الاحتياطية بنجاح! (بدون ضغط)')
                    ->with('details', $details);
            } else {
                $errorMsg = 'فشل إنشاء النسخة الاحتياطية.';
                $details = "رمز الخطأ: {$returnVar}\n"
                    . "المدة: {$duration} ثانية"
                    . (!empty($stderr) ? "\nرسالة الخطأ:\n{$stderr}" : '');

                return redirect()->back()
                    ->with('error', $errorMsg)
                    ->with('details', $details);
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
            'branches' => 'الفروع',
            'settings' => 'الإعدادات',
            'roles' => 'الأدوار والصلاحيات',
            'mobile_maintenance' => 'صيانة الموبايلات',
            'mobile_sales' => 'مبيعات الموبايلات',
            'mobile_inventory' => 'جرد الموبايلات',
            'mobile_debts' => 'ديون الموبايلات',
            'mobile_expenses' => 'مصروفات الموبايلات',
        ];

        foreach ($tables as $table => $description) {
            $info .= "- {$table} ({$description})\n";
        }

        file_put_contents($infoFile, $info);
    }

    // إنشاء ملف مضغوط
    private function createZipBackup($sqlPath, $zipPath, $infoPath = null)
    {
        if (!class_exists('ZipArchive')) {
            return false;
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            $zip->addFile($sqlPath, basename($sqlPath));
            if ($infoPath && file_exists($infoPath)) {
                $zip->addFile($infoPath, basename($infoPath));
            }
            $zip->close();
            return true;
        }

        return false;
    }

    // إزالة أي أسطر تشخيصية من mysqldump (مش SQL) من ملف نسخة احتياطية قبل استيرادها —
    // mysqldump بيطبع تحذيرات كهيك على stderr حتى بنجاح كامل (كلمة السر بسطر الأوامر،
    // أو نقص صلاحية PROCESS عالاستضافة المشتركة)، وأي ملف مصدره خارج التطبيق (رفع يدوي،
    // phpMyAdmin، سيرفر ثاني) ممكن توصل ملوثة بنفس الطريقة.
    private function stripMysqldumpDiagnosticLines(string $path): void
    {
        $content = file_get_contents($path);
        if ($content === false) {
            return;
        }

        $lines = explode("\n", $content);
        $kept = [];
        $removed = 0;
        foreach ($lines as $line) {
            if (str_starts_with(ltrim($line), 'mysqldump:')) {
                $removed++;
                continue;
            }
            $kept[] = $line;
        }

        if ($removed > 0) {
            file_put_contents($path, implode("\n", $kept));
        }
    }

    // صفحة رفع نسخة احتياطية
    public function upload()
    {
        return view('backup.upload');
    }

    // رفع وحفظ النسخة الاحتياطية
    public function storeUpload(Request $request)
    {
        // ملاحظة: قاعدة mimes:sql مش موثوقة أبداً لملفات SQL نصية — بتعتمد على فحص محتوى
        // الملف عبر fileinfo، وملف SQL نصي عادي بينكشف كـ text/plain (مش sql)، فبيفشل التحقق
        // حتى لو الملف صحيح 100%. نتحقق يدوياً من امتداد الملف بدلها، بنفس الطريقة يلي
        // download()/restore() أصلاً بيعتمدوا عليها (pathinfo/getClientOriginalExtension).
        $request->validate([
            'backup_file' => 'required|file|max:512000', // 500MB max
        ], [
            'backup_file.required' => 'يرجى اختيار ملف النسخة الاحتياطية',
            'backup_file.max' => 'حجم الملف يجب أن لا يتجاوز 500 ميجابايت',
        ]);

        $file = $request->file('backup_file');
        $extension = strtolower($file->getClientOriginalExtension());

        if (! in_array($extension, ['sql', 'zip'], true)) {
            return redirect()->back()
                ->withErrors(['backup_file' => 'يجب أن يكون الملف بصيغة SQL أو ZIP'])
                ->withInput();
        }

        try {
            $filename = 'uploaded_' . date('Y-m-d_H-i-s') . '.' . $extension;
            $backupsDir = storage_path('app/backups');

            if (!file_exists($backupsDir)) {
                mkdir($backupsDir, 0755, true);
            }

            // نستخدم move() لملف مسار خام مطابق لبقية الميثودز (store/download/restore/destroy)
            // بدل storeAs() اللي بتحفظ افتراضياً تحت storage/app/private (جذر قرص 'local' بلارافيل
            // 11+) — مكان ما بتشوفه أي وظيفة تانية بهاد الكونترولر، فالملف المرفوع كان يختفي فعلياً
            // رغم ظهور رسالة النجاح.
            $file->move($backupsDir, $filename);

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
        $filename = basename($filename);
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
        $startedAt = microtime(true);

        try {
            $filename = basename($filename);
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

            // ننضّف الملف من أي أسطر تشخيصية من mysqldump (مش SQL صحيح) قبل الاستيراد —
            // النسخة الاحتياطية ممكن تكون جاية من أي مصدر (تصدير التطبيق نفسه، phpMyAdmin،
            // سيرفر الإنتاج...) ومش بالضرورة نضيفة، فبنعالجها هون بدل ما نعتمد إنه كل مصدر
            // بيصدّرها نضيفة من الأساس.
            $this->stripMysqldumpDiagnosticLines($path);

            $host = Config::get('database.connections.mysql.host');
            $port = Config::get('database.connections.mysql.port', '3306');
            $username = Config::get('database.connections.mysql.username');
            $password = Config::get('database.connections.mysql.password');
            $database = Config::get('database.connections.mysql.database');

            $beforeCounts = $this->tableRowCounts();

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
            $duration = round(microtime(true) - $startedAt, 2);
            $rawOutput = trim(implode("\n", $output));

            if ($returnVar === 0) {
                // مسح الكاش بعد الاستعادة
                Artisan::call('cache:clear');
                Artisan::call('config:clear');

                $afterCounts = $this->tableRowCounts();
                $details = "المدة: {$duration} ثانية\n\n" . $this->formatRowCountDiff($beforeCounts, $afterCounts);

                // حذف المجلد المؤقت إذا كان موجوداً (بعد ما خلصنا نستخدم $path منه)
                if (isset($extractPath) && file_exists($extractPath)) {
                    $this->deleteDirectory($extractPath);
                }

                return redirect()->route('backup.index')
                    ->with('success', 'تمت الاستعادة بنجاح')
                    ->with('details', $details);
            } else {
                [$location, $errorLine] = $this->locateSqlError($path, $rawOutput);

                $errorMsg = 'فشلت عملية الاستعادة';
                $details = "المشكلة: " . ($location ?: 'غير معروف مكانها بالتحديد') . "\n"
                    . "رسالة الخطأ:\n" . ($errorLine ?: ($rawOutput ?: 'لا توجد رسالة خطأ من mysql'));

                // حذف المجلد المؤقت إذا كان موجوداً
                if (isset($extractPath) && file_exists($extractPath)) {
                    $this->deleteDirectory($extractPath);
                }

                return redirect()->back()
                    ->with('error', $errorMsg)
                    ->with('details', $details);
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    // عدد الصفوف بكل جدول بقاعدة البيانات الحالية (تستخدم قبل وبعد الاستعادة لعرض
    // الفرق بشكل خفيف ومنظّم بدل تفاصيل تقنية كتيرة).
    private function tableRowCounts(): array
    {
        $counts = [];

        try {
            $tables = collect(DB::select('SHOW TABLES'))
                ->map(fn ($row) => array_values((array) $row)[0]);

            foreach ($tables as $table) {
                try {
                    $counts[$table] = DB::table($table)->count();
                } catch (\Throwable $e) {
                    // جدول ما قدرنا نعده (صلاحيات، إلخ) — نتجاهله من المقارنة
                }
            }
        } catch (\Throwable $e) {
            // لو فشل حتى SHOW TABLES (مثلاً قبل أول استعادة على قاعدة فاضية) منرجع مصفوفة فاضية
        }

        return $counts;
    }

    // يبني ملخص خفيف لأي جدول تغيّر عدد صفوفه بعد الاستعادة (زيادة أو نقصان)
    private function formatRowCountDiff(array $before, array $after): string
    {
        $allTables = array_unique(array_merge(array_keys($before), array_keys($after)));
        sort($allTables);

        $changed = [];
        foreach ($allTables as $table) {
            $b = $before[$table] ?? 0;
            $a = $after[$table] ?? 0;
            if ($b !== $a) {
                $delta = $a - $b;
                $sign = $delta > 0 ? '+' : '';
                $changed[] = "  {$table}: {$b} ← {$a} ({$sign}{$delta})";
            }
        }

        if (empty($changed)) {
            return 'لم يتغيّر عدد الصفوف بأي جدول.';
        }

        return "الجداول اللي تغيّرت (" . count($changed) . "):\n" . implode("\n", $changed);
    }

    // لو فشلت الاستعادة، بنحدد أقرب جدول لموقع الخطأ (من رسالة mysql "at line N")
    // حتى يكون واضح وين المشكلة بالضبط، مش بس كود خطأ عام
    private function locateSqlError(string $sqlPath, string $rawOutput): array
    {
        if (!preg_match('/at line (\d+)/', $rawOutput, $lineMatch)) {
            return [null, null];
        }

        $errorLineNumber = (int) $lineMatch[1];
        $errorLine = null;
        if (preg_match('/^(ERROR .+)$/m', $rawOutput, $errMatch)) {
            $errorLine = trim($errMatch[1]);
        }

        $tableName = null;
        if (file_exists($sqlPath)) {
            $lines = file($sqlPath);
            $upperBound = min($errorLineNumber, count($lines));
            for ($i = $upperBound - 1; $i >= 0; $i--) {
                if (preg_match('/-- Table structure for table `(.+?)`/', $lines[$i], $tableMatch)) {
                    $tableName = $tableMatch[1];
                    break;
                }
            }
        }

        $location = $tableName
            ? "بالقرب من جدول `{$tableName}` (السطر {$errorLineNumber})"
            : "السطر {$errorLineNumber}";

        return [$location, $errorLine];
    }

    // حذف نسخة احتياطية
    public function destroy($filename)
    {
        try {
            $filename = basename($filename);
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

            // stderr بملف منفصل حتى ما ينخلط تحذير mysqldump القياسي (كلمة المرور بسطر
            // الأوامر) مع محتوى الملف — لو انخلط بيصير أول سطر مش SQL وتفشل أي استعادة لاحقة.
            $errLogPath = storage_path('app/backups/.mysqldump_err_' . $timestamp . '.log');
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s --port=%s %s --routines --triggers --single-transaction > %s 2> %s',
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($database),
                escapeshellarg($path),
                escapeshellarg($errLogPath)
            );

            exec($command, $output, $returnVar);
            @unlink($errLogPath);

            // ضغط الملف إذا نجح التصدير (نفس منطق store() — يوحّد صيغة كل النسخ الاحتياطية)
            if ($returnVar === 0 && file_exists($path) && filesize($path) > 0) {
                $zipPath = storage_path('app/backups/auto_backup_' . $timestamp . '.zip');
                if ($this->createZipBackup($path, $zipPath)) {
                    @unlink($path);
                }
            }

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