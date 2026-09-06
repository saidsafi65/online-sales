<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Services\Provisioning\TenantProvisioner;
use App\Services\TenantDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use PDO;
use PDOException;
use RuntimeException;

class TenantManagementController extends Controller
{
    public function index()
    {
        $tenants = Tenant::on('central')->latest()->get();
        return view('system-admin.tenants.index', compact('tenants'));
    }

    public function createAuto()
    {
        return view('system-admin.tenants.create-auto');
    }

    public function storeAuto(Request $request, TenantProvisioner $provisioner)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:central.tenants,domain',
            'db_name' => 'nullable|string|max:32|regex:/^[a-zA-Z][a-zA-Z0-9_]*$/',
        ], [
            'name.required' => 'اسم المعرض مطلوب',
            'domain.required' => 'الدومين مطلوب',
            'domain.unique' => 'هذا الدومين مسجّل مسبقاً لمعرض آخر',
            'db_name.regex' => 'اسم قاعدة البيانات لازم يبدأ بحرف إنجليزي، وما يحتوي إلا على حروف/أرقام/شرطة سفلية',
        ]);

        try {
            $credentials = $provisioner->provision($validated['name'], $validated['db_name'] ?? null);
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $error = $this->testConnection($credentials);
        if ($error) {
            return back()->withInput()->with('error', 'تم إنشاء قاعدة البيانات لكن تعذّر الاتصال بها: '.$error);
        }

        $tenant = Tenant::on('central')->create([
            'name' => $validated['name'],
            'domain' => $validated['domain'],
            ...$credentials,
        ]);
        $output = $this->runMigrationsFor($tenant);

        return redirect()->route('system-admin.tenants.index')
            ->with('success', "تم إنشاء معرض \"{$tenant->name}\" وقاعدة بياناته تلقائياً بنجاح ✅")
            ->with('migrate_output', $output);
    }

    public function create()
    {
        return view('system-admin.tenants.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateTenant($request);

        $error = $this->testConnection($validated);
        if ($error) {
            return back()->withInput()->with('error', $error);
        }

        Tenant::on('central')->create($validated);

        return redirect()->route('system-admin.tenants.index')->with('success', 'تم تسجيل المعرض بنجاح. الخطوة التالية: ترحيل الجداول له.');
    }

    public function edit(Tenant $tenant)
    {
        return view('system-admin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $this->validateTenant($request, $tenant->id);

        $error = $this->testConnection($validated);
        if ($error) {
            return back()->withInput()->with('error', $error);
        }

        $tenant->update($validated);

        return redirect()->route('system-admin.tenants.index')->with('success', 'تم تحديث بيانات المعرض بنجاح');
    }

    public function toggle(Tenant $tenant)
    {
        $tenant->update(['is_active' => ! $tenant->is_active]);

        return back()->with('success', $tenant->is_active ? 'تم تفعيل المعرض' : 'تم تعطيل المعرض');
    }

    /**
     * Permanently deletes a tenant AND its entire database (irreversible).
     * Requires the exact domain typed as confirmation (checked here too,
     * not just in the JS, in case the confirm step is ever bypassed).
     */
    public function destroy(Request $request, Tenant $tenant, TenantProvisioner $provisioner)
    {
        if ($request->input('confirm_domain') !== $tenant->domain) {
            return back()->with('error', 'لازم تكتب دومين المعرض بالضبط للتأكيد. لم يُحذف شيء.');
        }

        try {
            $provisioner->deprovision($tenant);
        } catch (RuntimeException $e) {
            return back()->with('error', 'تعذّر حذف قاعدة البيانات: '.$e->getMessage().' — لم يُحذف المعرض من السجل.');
        }

        $name = $tenant->name;
        $tenant->delete();

        return redirect()->route('system-admin.tenants.index')->with('success', "تم حذف معرض \"{$name}\" وكل بياناته نهائياً");
    }

    public function migrate(Tenant $tenant)
    {
        return back()->with('migrate_output', $this->runMigrationsFor($tenant));
    }

    public function migrateAll()
    {
        $tenants = Tenant::on('central')->where('is_active', true)->get();

        $report = $tenants->map(function (Tenant $tenant) {
            return "=== {$tenant->name} ({$tenant->domain}) ===\n".$this->runMigrationsFor($tenant);
        })->implode("\n\n");

        return back()->with('migrate_output', $report ?: 'لا يوجد معارض فعّالة حالياً.');
    }

    public function maintenanceMigrate()
    {
        Artisan::call('migrate', ['--force' => true]);
        $migrateOutput = Artisan::output();
        Artisan::call('storage:link');
        $linkOutput = Artisan::output();

        return back()->with('migrate_output', $migrateOutput."\n---\n".$linkOutput);
    }

    public function maintenanceClearCache()
    {
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('config:cache');

        return back()->with('success', 'تم مسح الكاش بنجاح (config + route + view)');
    }

    /**
     * Runs the standard (non-central) migrations against one tenant's DB.
     * Shared by the single-tenant "migrate" button, the auto-create flow,
     * and bulk maintenance across every tenant.
     */
    private function runMigrationsFor(Tenant $tenant): string
    {
        $connectionName = TenantDatabase::connect($tenant);

        try {
            Artisan::call('migrate', [
                '--database' => $connectionName,
                '--force' => true,
            ]);

            return Artisan::output();
        } catch (\Throwable $e) {
            return 'فشل الترحيل: '.$e->getMessage();
        } finally {
            DB::purge($connectionName);
        }
    }

    private function validateTenant(Request $request, ?int $ignoreId = null): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:central.tenants,domain,'.($ignoreId ?? 'NULL').',id',
            'db_host' => 'required|string|max:255',
            'db_port' => 'nullable|string|max:10',
            'db_database' => 'required|string|max:255',
            'db_username' => 'required|string|max:255',
            'db_password' => 'required|string|max:255',
        ], [
            'name.required' => 'اسم المعرض مطلوب',
            'domain.required' => 'الدومين مطلوب',
            'domain.unique' => 'هذا الدومين مسجّل مسبقاً لمعرض آخر',
            'db_host.required' => 'عنوان قاعدة البيانات مطلوب',
            'db_database.required' => 'اسم قاعدة البيانات مطلوب',
            'db_username.required' => 'اسم مستخدم قاعدة البيانات مطلوب',
            'db_password.required' => 'كلمة مرور قاعدة البيانات مطلوبة',
        ]);

        $validated['db_port'] = $validated['db_port'] ?: '3306';

        return $validated;
    }

    /**
     * Returns an Arabic error message if the given DB credentials can't be reached, null if they're fine.
     */
    private function testConnection(array $credentials): ?string
    {
        try {
            new PDO(
                "mysql:host={$credentials['db_host']};port={$credentials['db_port']};dbname={$credentials['db_database']}",
                $credentials['db_username'],
                $credentials['db_password'],
                [PDO::ATTR_TIMEOUT => 5]
            );
        } catch (PDOException $e) {
            return 'تعذّر الاتصال بقاعدة البيانات المُدخلة، تأكد من إنشائها وصحة البيانات أولاً عبر cPanel. ('.$e->getMessage().')';
        }

        return null;
    }
}
