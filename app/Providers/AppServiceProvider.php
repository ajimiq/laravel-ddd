<?php

namespace App\Providers;

use App\Packages\Order\Domains\OrderGetterInterface;
use App\Packages\Order\Domains\OrderRepositoryInterface;
use App\Packages\Order\Domains\OrderEventRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(OrderGetterInterface::class, function($app) {
            return new \App\Packages\Order\Infrastructures\TestMallOrderGetter();
        });
        $this->app->bind(OrderRepositoryInterface::class, function($app) {
            return new \App\Packages\Order\Infrastructures\OrderRepository();
        });
        $this->app->bind(OrderEventRepositoryInterface::class, function($app) {
            return new \App\Packages\Order\Infrastructures\OrderEventRepository();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
