<?php

namespace App\Providers;

use App\Http\Middleware\DmsAdminToken;
use App\Observers\DmsAdminPublishObserver;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class DmsAdminServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(config_path('dms_admin.php'), 'dms_admin');
    }

    public function boot(): void
    {
        Route::prefix('api/v1/admin')
            ->middleware(['api', DmsAdminToken::class])
            ->group(base_path('routes/dms_admin.php'));

        // TT-17: purge + IndexNow automatically when a blog is saved or published.
        // The site's model is App\\Models\\Blogs (plural); the others are fallbacks.
        foreach (['App\\Models\\Blogs', 'App\\Models\\Blog', 'App\\Blog'] as $blog) {
            if (class_exists($blog)) {
                $blog::observe(DmsAdminPublishObserver::class);
                break;
            }
        }
    }
}