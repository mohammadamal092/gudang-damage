<?php

namespace App\Providers;

use App\Models\WarehouseLocation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Route::bind('location', function (string $value) {
            return WarehouseLocation::findOrFail($value);
        });
    }
}
