<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
    // Reset semua user jadi offline saat server restart
    \App\Models\User::query()->update([
        'status' => 'offline',
        'last_seen_at' => now(),
    ]);
}
}
