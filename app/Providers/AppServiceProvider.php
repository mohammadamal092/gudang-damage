<?php

namespace App\Providers;

use App\Models\WarehouseLocation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
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

        // Nama hari/bulan dalam Bahasa Indonesia (translatedFormat).
        Carbon::setLocale('id');

        Route::bind('location', function (string $value) {
            return WarehouseLocation::findOrFail($value);
        });
    }
}
