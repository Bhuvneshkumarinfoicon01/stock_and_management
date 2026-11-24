<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Store;
use App\Services\DateFormatService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerDateFormatHelper();

        View::composer('*', function ($view) {
            $stores = Store::all();  // Fetch all stores
            $view->with('stores', $stores);
        });
        View::composer('*', function ($view) {
            $categories = Category::all();  // Fetch all stores
            $view->with('categories', $categories);
        });
        View::composer('*', function ($view) {
            $user = auth()->user();
            $view->with('user', $user);
        });
        View::composer('*', function ($view) {
            $view->with('dateFormatConfig', app(DateFormatService::class)->config());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Validator::extend('app_date', function (string $attribute, mixed $value): bool {
            if ($value === null || $value === '') {
                return false;
            }

            if (function_exists('parse_date_input')) {
                return parse_date_input((string) $value) !== null;
            }

            return app(DateFormatService::class)->parseInput((string) $value) !== null;
        });
    }

    private function registerDateFormatHelper(): void
    {
        if (function_exists('format_date')) {
            return;
        }

        $path = app_path('Helpers/DateFormatHelper.php');

        if (is_file($path)) {
            require_once $path;
        }
    }
}
