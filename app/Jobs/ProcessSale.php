<?php

namespace App\Jobs;

use App\Repositories\Contracts\InventoryRepositoryInterface;
use App\Repositories\Contracts\ProductsRepositoryInterface;
use App\Repositories\Contracts\SaleItemsRepositoryInterface;
use App\Repositories\Contracts\SaleRepositoryInterface;
use App\Services\Interfaces\SaleServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ProcessSale implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public array $items,
        public int $saleId,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(
        SaleRepositoryInterface $saleRepository,
        InventoryRepositoryInterface $inventoryRepository,
        SaleItemsRepositoryInterface $saleItemsRepository,
        ProductsRepositoryInterface $productsRepository,
        SaleServiceInterface $saleService
    ): void {
        $sale = $this->getSale($saleRepository);
        if (! $sale) {
            return;
        }

        $productsMap = $this->loadProducts($productsRepository);

        foreach ($this->items as $item) {
            $this->processItem(
                $item,
                $productsMap[$item['product_id']],
                $inventoryRepository,
                $saleItemsRepository
            );
        }

        $this->updateSaleTotals($sale, $productsMap, $saleRepository, $saleService);
    }

    private function getSale(SaleRepositoryInterface $saleRepository): ?Model
    {
        $sale = $saleRepository->getById($this->saleId);
        if (! $sale) {
            Log::error("Venda ({$this->saleId}) não encontrada!");
        }

        return $sale;
    }

    private function loadProducts(ProductsRepositoryInterface $productsRepository): Collection
    {
        $productIds = array_column($this->items, 'product_id');

        return $productsRepository->getByValuesIn('id', $productIds)->keyBy('id');
    }

    private function processItem(
        array $item,
        $product,
        InventoryRepositoryInterface $inventoryRepository,
        SaleItemsRepositoryInterface $saleItemsRepository
    ): void {
        Cache::lock("product_stock_{$product->id}", 10)->block(5, function () use ($item, $product, $inventoryRepository, $saleItemsRepository) {
            DB::transaction(function () use ($item, $product, $inventoryRepository, $saleItemsRepository) {
                $this->validateStock($item, $product, $inventoryRepository);
                $this->updateInventory($item, $product, $inventoryRepository);
                $this->updateSaleItem($item, $product, $saleItemsRepository);
            });
        });
    }

    private function validateStock(array $item, $product, InventoryRepositoryInterface $inventoryRepository): void
    {
        $available = $inventoryRepository->countItem($product->id);
        if ($available < $item['quantity']) {
            throw new RuntimeException("Estoque insuficiente para o produto {$product->id}");
        }
    }

    private function updateInventory(array $item, $product, InventoryRepositoryInterface $inventoryRepository): void
    {
        $inventoryRepository->store([
            'product_id' => $product->id,
            'quantity' => -(int) $item['quantity'],
            'last_updated' => now(),
        ]);
    }

    private function updateSaleItem(array $item, $product, SaleItemsRepositoryInterface $saleItemsRepository): void
    {
        $saleItemsRepository->updateById([
            'unit_price' => $product->sale_price,
            'unit_cost' => $product->cost_price,
        ], $item['id']);
    }

    private function updateSaleTotals($sale, Collection $productsMap, SaleRepositoryInterface $saleRepository,
        SaleServiceInterface $saleService): void
    {
        [$totalAmount, $totalCost, $totalProfit] = $saleService->calculateTotals($this->items, $productsMap);
        $saleRepository->updateById([
            'total_amount' => $totalAmount,
            'total_cost' => $totalCost,
            'total_profit' => $totalProfit,
            'status' => 'completed',
        ], $sale->id);
    }
}
