<?php

namespace App\Http;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Console\Scheduling\Schedule;

class Kernel extends HttpKernel
{

     /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // نسخة احتياطية يومية (الساعة 2 صباحاً)
        $schedule->command('db:auto-backup --type=daily')
            ->dailyAt('02:00')
            ->name('daily-database-backup')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/backup.log'));

        // نسخة احتياطية أسبوعية (كل جمعة الساعة 3 صباحاً)
        $schedule->command('db:auto-backup --type=weekly')
            ->weeklyOn(5, '03:00')
            ->name('weekly-database-backup')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/backup.log'));

        // نسخة احتياطية شهرية (أول يوم من كل شهر)
        $schedule->command('db:auto-backup --type=monthly')
            ->monthlyOn(1, '04:00')
            ->name('monthly-database-backup')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/backup.log'));

        // تنظيف الكاش الأسبوعي
        $schedule->command('cache:clear')
            ->weekly()
            ->name('clear-cache');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'check.branch' => \App\Http\Middleware\CheckBranchAccess::class,
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'section.permission' => \App\Http\Middleware\CheckSectionPermission::class,
        'mobile.shop.only' => \App\Http\Middleware\CheckMobileShopAccess::class,
    ];
}
