<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\ActivityLog;
use App\Models\SupportTicket;
use Illuminate\Pagination\Paginator;
use App\Services\Provisioning\TenantProvisioner;
use App\Services\Provisioning\CPanelTenantProvisioner;
use App\Services\Provisioning\LocalTenantProvisioner;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TenantProvisioner::class, function () {
            // ملاحظة: env() مباشرة هون كانت بترجع القيمة الافتراضية 'local' دايماً بعد
            // أول php artisan config:cache (لارافيل بيتجاهل .env الحقيقي لما يكون
            // فيه كاش)، فكانت تفعّل مزوّد local الخاطئ بالإنتاج بصمت. config() بيقرأ
            // القيمة الصحيحة سواء الكاش مفعّل أو لأ.
            return config('services.tenant_provisioner') === 'cpanel'
                ? new CPanelTenantProvisioner()
                : new LocalTenantProvisioner();
        });
    }

    /**
     * الموديلات المستثناة من التسجيل (نظامية أو حساسة أو الجدول نفسه)
     */
    protected array $excludedModels = [
        \App\Models\ActivityLog::class,
        \App\Models\StoreNotification::class,
        \Illuminate\Notifications\DatabaseNotification::class,
    ];

    public function boot(): void
    {
         Paginator::useBootstrapFive();

        // نافذة الدعم الفني ظاهرة بشريط التنقل بكل صفحات لوحة مدير النظام، فبنحسب
        // عدد التذاكر المفتوحة مرة وحدة هون بدل ما كل PlatformController يمررها يدوياً.
        View::composer('layout.platform', function ($view) {
            $view->with('openSupportCount', Auth::guard('platform')->check()
                ? SupportTicket::whereIn('status', ['open', 'in_progress'])->count()
                : 0);
        });

        Event::listen('eloquent.created: *', function ($event, $data) {
            $this->log('created', $data[0] ?? null);
        });

        Event::listen('eloquent.updated: *', function ($event, $data) {
            $this->log('updated', $data[0] ?? null);
        });

        Event::listen('eloquent.deleted: *', function ($event, $data) {
            $this->log('deleted', $data[0] ?? null);
        });
    }

    protected function log(string $action, ?Model $model): void
    {
        if (!$model || in_array(get_class($model), $this->excludedModels)) {
            return;
        }

        // تجاهل جداول النظام الداخلية (جلسات، توكنات، الخ) لو صادفت موديل عليها بالغلط
        if (str_contains(get_class($model), 'Illuminate\\')) {
            return;
        }

        $actor = null;
        $actorType = null;
        $actorName = null;
        $branchId = null;

        if (Auth::guard('web')->check()) {
            $actor = Auth::guard('web')->user();
            $actorType = get_class($actor);
            $actorName = $actor->name ?? null;
            $branchId = $actor->branch_id ?? null;
        } elseif (Auth::guard('customer')->check()) {
            $actor = Auth::guard('customer')->user();
            $actorType = get_class($actor);
            $actorName = $actor->name ?? null;
        }

        $changes = null;
        if ($action === 'updated') {
            $dirty = $model->getChanges();
            unset($dirty['updated_at']);
            if (empty($dirty)) {
                return; // ما في تغيير حقيقي (مثلاً بس updated_at)
            }
            $changes = [];
            foreach ($dirty as $field => $newValue) {
                $changes[$field] = [
                    'old' => $model->getOriginal($field),
                    'new' => $newValue,
                ];
            }
        } elseif ($action === 'created') {
            $changes = $model->getAttributes();
        }

        $label = $model->name
            ?? $model->title
            ?? $model->customer_name
            ?? $model->product ?? null;

        if ($label && !empty($model->type ?? null)) {
            $label .= ' — ' . $model->type;
        }

        $label ??= $model->device_name
            ?? $model->supplier_name
            ?? $model->organization_name
            ?? $model->invoice_number
            ?? $model->claim_reference
            ?? ('#' . $model->getKey());

        try {
            ActivityLog::create([
                'actor_type'  => $actorType,
                'actor_id'    => $actor?->getKey(),
                'actor_name'  => $actorName,
                'action'      => $action,
                'model_type'  => get_class($model),
                'model_id'    => $model->getKey(),
                'model_label' => (string) $label,
                'changes'     => $changes,
                'branch_id'   => $branchId,
            ]);
        } catch (\Throwable $e) {
            // لا نكسر العملية الأساسية أبداً بسبب فشل تسجيل اللوق
            report($e);
        }
    }
}