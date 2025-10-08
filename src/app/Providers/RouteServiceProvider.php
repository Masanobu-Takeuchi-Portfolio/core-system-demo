<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    // ユーザーがログインしたら通常は/dashboardに移動する
    public const HOME = '/dashboard';
    // オーナーのダッシュボードにログインさせる
    public const OWNER_HOME = '/owner/dashboard';
    // オーナーのダッシュボードにログインさせる
    public const ADMIN_HOME = '/admin/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    // bootメソッドはサービスプロバイダーが読み込まれた後に実行される
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // 管理者権限のroot情報
            Route::prefix('admin')
                ->as('admin.') // prefixの別名指定だが今回は同じadminを設定している 
                ->middleware('web')
                ->group(base_path('routes/admin.php'));

            // オーナー権限のroot情報
            Route::prefix('owner')
                ->as('owner.')
                ->middleware('web')
                ->group(base_path('routes/owner.php'));

            // ユーザー権限のroot情報
            Route::prefix('/') // URLに/adminや/ownerがない場合は全てこっちのルート
                ->as('user.')
                ->middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
