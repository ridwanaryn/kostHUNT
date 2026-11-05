<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(app()->environment('local'));

        View::composer('layouts.navigation', function (ViewContract $view): void {
            $user = auth()->user();

            $view->with(
                'unreadNotificationsCount',
                $user?->unreadNotifications()->count() ?? 0,
            );
        });
    }
}
