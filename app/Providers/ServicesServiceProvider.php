<?php

namespace App\Providers;

use App\Services\Interfaces\InventoryServiceInterface;
use App\Services\Interfaces\ProductServiceInterface;
use App\Services\InventoryService;
use App\Services\ProductService;
use Illuminate\Support\ServiceProvider;

class ServicesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(\App\Services\Interfaces\ReportServiceInterface::class, \App\Services\ReportService::class);
        $this->app->bind(\App\Services\Interfaces\SaleItemsServiceInterface::class, \App\Services\SaleItemsService::class);
        $this->app->bind(\App\Services\Interfaces\SaleServiceInterface::class, \App\Services\SaleService::class);
        $this->app->bind(ProductServiceInterface::class, ProductService::class);
        $this->app->bind(InventoryServiceInterface::class, InventoryService::class);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            \App\Services\Interfaces\ReportServiceInterface::class,
            \App\Services\Interfaces\SaleItemsServiceInterface::class,
            \App\Services\Interfaces\SaleServiceInterface::class,
            ProductServiceInterface::class,
            InventoryServiceInterface::class,
        ];
    }
}
