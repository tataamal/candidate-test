<?php

namespace App\Providers;

use App\Models\Layups;
use App\Models\Supplier;
use App\Policies\LayupPolicy;
use App\Policies\SupplierPolicy;
use Illuminate\Support\Facades\Gate;
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
        Gate::policy(Supplier::class, SupplierPolicy::class);
        Gate::policy(Layups::class, LayupPolicy::class);
    }
}
