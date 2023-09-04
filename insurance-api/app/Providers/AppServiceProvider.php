<?php

namespace App\Providers;

use App\Services\FooInsuranceService;
use App\Services\BarInsuranceService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(InsuranceServiceInterface::class, FooInsuranceService::class);
        $this->app->bind('barInsurance', BarInsuranceService::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        
    }
}
