<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

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

    // إنشاء نسخة احتياطية جديدة
    public function store(Request $request)
    {
        try {
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $path = storage_path('app/backups/' . $filename);

            // إنشاء المجلد إذا لم يكن موجوداً
            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }

            // الحصول على معلومات قاعدة البيانات
            $host = Config::get('database.connections.mysql.host');
            $username = Config::get('database.connections.mysql.username');
            $password = Config::get('database.connections.mysql.password');
            $database = Config::get('database.connections.mysql.database');

            // تنفيذ أمر mysqldump
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > %s',
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($database),
                escapeshellarg($path)
            );

            exec($command, $output, $returnVar);

            if ($returnVar === 0 && file_exists($path)) {
                return redirect()->route('backup.index')
                    ->with('success', 'تم إنشاء النسخة الاحتياطية بنجاح!');
            } else {
                return redirect()->back()
                    ->with('error', 'فشل إنشاء النسخة الاحتياطية. تأكد من تثبيت mysqldump.');
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
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
        $request->validate([
            'backup_file' => 'required|file|mimes:sql|max:102400' // 100MB max
        ], [
            'backup_file.required' => 'يرجى اختيار ملف النسخة الاحتياطية',
            'backup_file.mimes' => 'يجب أن يكون الملف بصيغة SQL',
            'backup_file.max' => 'حجم الملف يجب أن لا يتجاوز 100 ميجابايت'
        ]);

        try {
            $file = $request->file('backup_file');
            $filename = 'uploaded_' . date('Y-m-d_H-i-s') . '.sql';
            
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

            $host = Config::get('database.connections.mysql.host');
            $username = Config::get('database.connections.mysql.username');
            $password = Config::get('database.connections.mysql.password');
            $database = Config::get('database.connections.mysql.database');

            // تنفيذ أمر الاستعادة
            $command = sprintf(
                'mysql --user=%s --password=%s --host=%s %s < %s',
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($database),
                escapeshellarg($path)
            );

            exec($command, $output, $returnVar);

            if ($returnVar === 0) {
                return redirect()->route('backup.index')
                    ->with('success', 'تم استعادة النسخة الاحتياطية بنجاح!');
            } else {
                return redirect()->back()
                    ->with('error', 'فشلت عملية الاستعادة');
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

        $files = glob($backupPath . '/*.sql');
        $backups = [];

        foreach ($files as $file) {
            $backups[] = [
                'name' => basename($file),
                'size' => $this->formatBytes(filesize($file)),
                'date' => date('Y-m-d H:i:s', filemtime($file))
            ];
        }

        // ترتيب حسب التاريخ (الأحدث أولاً)
        usort($backups, function($a, $b) {
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
}