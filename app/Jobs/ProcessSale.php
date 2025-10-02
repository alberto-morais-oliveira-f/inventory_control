<?php

namespace App\Jobs;

use App\Models\Sale;
use App\Repositories\Contracts\InventoryRepositoryInterface;
use App\Repositories\Contracts\ProductsRepositoryInterface;
use App\Repositories\Contracts\SaleItemsRepositoryInterface;
use App\Repositories\Contracts\SaleRepositoryInterface;
use App\Services\Interfaces\SaleServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProcessSale implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public array $items,
        public Sale $sale,
    ) {
    }

    /**
     * Execute the job.
     *
     * @throws Throwable
     */
    public function handle(
        SaleRepositoryInterface $saleRepository,
        InventoryRepositoryInterface $inventoryRepository,
        SaleItemsRepositoryInterface $saleItemsRepository,
        ProductsRepositoryInterface $productsRepository,
        SaleServiceInterface $saleService
    ): void {
        DB::transaction(function () use (
            $saleRepository,
            $inventoryRepository,
            $saleItemsRepository,
            $productsRepository,
            $saleService
        ) {
            foreach ($this->items as $item) {
                $productIds = array_column($this->items, 'product_id');
                $products = $productsRepository->getByValuesIn('id', $productIds);
                $productsMap = $products->keyBy('id')->all();
                $product = $productsMap[$item['product_id']];

                $inventoryRepository->store([
                    'product_id' => $item['product_id'],
                    'quantity' => -(int) $item['quantity'],
                    'last_updated' => now(),
                ]);

                $saleItemsRepository->store([
                    'sale_id' => $this->sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => (int) $item['quantity'],
                    'unit_price' => $product->sale_price,
                    'unit_cost' => $product->cost_price,
                ]);
            }

            [$totalAmount, $totalCost, $totalProfit] = $saleService->calculateTotals($this->items, $productsMap);

            $saleRepository->updateById([
                'total_amount' => $totalAmount,
                'total_cost' => $totalCost,
                'total_profit' => $totalProfit,
                'status' => 'completed',
            ], $this->sale->id);
        });
    }
}
