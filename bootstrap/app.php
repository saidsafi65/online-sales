<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // يجب أن يشتغل أول شي على كل طلب، قبل أي كود يلمس بيانات المعرض (users, products...)
        $middleware->prepend(\App\Http\Middleware\ResolveTenantDatabase::class);

        // إضافة middleware مخصص
        $middleware->alias([
            'mobile.shop.only' => \App\Http\Middleware\MobileShopOnly::class,
            'section.permission' => \App\Http\Middleware\CheckSectionPermission::class,
            'ensure.active' => \App\Http\Middleware\EnsureUserIsActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                $status = 500;
                if (method_exists($e, 'getStatusCode')) {
                    $status = $e->getStatusCode();
                } elseif ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                    $status = 404;
                }

                return response()->json([
                    'success' => false,
                    'message' => config('app.debug')
                        ? $e->getMessage()
                        : 'حدث خطأ غير متوقع، حاول مرة أخرى.',
                ], $status);
            }
        });
    })->create();