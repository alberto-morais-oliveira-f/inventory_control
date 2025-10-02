<?php

use App\Http\Controllers\InventoryListController;
use App\Http\Controllers\InventoryStoreController;
use App\Http\Controllers\ProductStoreController;
use App\Http\Controllers\ReportSaleController;
use App\Http\Controllers\SaleStoreController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/product', ProductStoreController::class)->name('product.store');
    Route::post('/inventory', InventoryStoreController::class)->name('inventory.store');
    Route::post('/sales', SaleStoreController::class)->name('sale-store');
    Route::get('/inventory', InventoryListController::class)->name('inventory.index');
    Route::get('/sales', InventoryStoreController::class)->name('sales');
    Route::get('/report/sales', ReportSaleController::class)->name('report.sales');
});
