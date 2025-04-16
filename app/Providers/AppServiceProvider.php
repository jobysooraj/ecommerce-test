<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Order\OrderItemRepositoryInterface;
use App\Repositories\Order\OrderItemRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(OrderItemRepositoryInterface::class, OrderItemRepository::class);



    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
