<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
            $this->app->bind(
                \App\RepositoryInterface\CategoryInterface::class,
                \App\Repositories\CategoryRepository::class
            );
            $this->app->bind(
                \App\RepositoryInterface\ProductInterface::class,
                \App\Repositories\ProductRepository::class
            );
            $this->app->bind(
                \App\RepositoryInterface\BrandInterface::class,
                \App\Repositories\BrandRepository::class
            );
            $this->app->bind(
                \App\Services\Interface\BrandServiceInterface::class,
                \App\Services\BrandService::class
            );
            $this->app->bind(
                \App\RepositoryInterface\UnitInterface::class,
                \App\Repositories\UnitRepository::class
            );
            $this->app->bind(
                \App\Services\Interface\UnitServiceInterface::class,
                \App\Services\UnitService::class
            );
            $this->app->bind(
                \App\RepositoryInterface\CustomerInterface::class,
                \App\Repositories\CustomerRepository::class
            );
            $this->app->bind(
                \App\Services\Interface\CustomerServiceInterface::class,
                \App\Services\CustomerService::class
            );
            $this->app->bind(
                \App\RepositoryInterface\OrderInterface::class,
                \App\Repositories\OrderRepository::class
            );
            $this->app->bind(
                \App\Services\Interface\OrderServiceInterface::class,
                \App\Services\OrderService::class
            );
            $this->app->bind(
                \App\RepositoryInterface\PurchaseRepositoryInterface::class,
                \App\Repositories\PurchaseRepository::class
            );
            $this->app->bind(
                \App\Services\Interface\PurchaseServiceInterface::class,
                \App\Services\PurchaseService::class
            );
            $this->app->bind(
                \App\RepositoryInterface\SupplierInterface::class,
                \App\Repositories\SupplierRepository::class
            );
            $this->app->bind(
                \App\Services\Interface\SupplierServiceInterface::class,
                \App\Services\SupplierService::class
            );
            $this->app->bind(
                \App\RepositoryInterface\UserInterface::class,
                \App\Repositories\UserRepository::class
            );
            $this->app->bind(
                \App\Services\Interface\UserServiceInterface::class,
                \App\Services\UserService::class
            );
            $this->app->bind(
                \App\RepositoryInterface\ReportInterface::class,
                \App\Repositories\ReportRepository::class
            );
            $this->app->bind(
                \App\Services\Interface\ReportServiceInterface::class,
                \App\Services\ReportService::class
            );
            
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
         Schema::defaultStringLength(191);
    }
}
