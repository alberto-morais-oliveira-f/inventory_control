<?php

namespace App\Providers;

use App\Services\Interfaces\InventoryServiceInterface;
use App\Services\Interfaces\ProductServiceInterface;
use App\Services\Interfaces\ReportServiceInterface;
use App\Services\Interfaces\SaleItemsServiceInterface;
use App\Services\Interfaces\SaleServiceInterface;
use App\Services\InventoryService;
use App\Services\ProductService;
use App\Services\ReportService;
use App\Services\SaleItemsService;
use App\Services\SaleService;
use Illuminate\Support\ServiceProvider;

class ServicesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ReportServiceInterface::class, ReportService::class);
        $this->app->bind(SaleItemsServiceInterface::class, SaleItemsService::class);
        $this->app->bind(SaleServiceInterface::class, SaleService::class);
        $this->app->bind(ProductServiceInterface::class, ProductService::class);
        $this->app->bind(InventoryServiceInterface::class, InventoryService::class);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            ReportServiceInterface::class,
            SaleItemsServiceInterface::class,
            SaleServiceInterface::class,
            ProductServiceInterface::class,
            InventoryServiceInterface::class,
        ];
    }
}
