<?php

namespace App\Jobs;

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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class ProcessSale implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public array $items,
        public int $saleId,
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
        $sale = $saleRepository->getById($this->saleId);
        if (! $sale) {
            Log::error("Venda ($this->saleId) não encontrada!");

            return;
        }

        foreach ($this->items as $item) {
            $productId = $item['product_id'];

            Cache::lock("product_stock_{$productId}", 10)->block(5, function () use (
                $item,
                $inventoryRepository,
                $saleItemsRepository,
                $productsRepository,
            ) {
                DB::transaction(function () use (
                    $item,
                    $inventoryRepository,
                    $saleItemsRepository,
                    $productsRepository,
                ) {
                    $product = $productsRepository->getById($item['product_id']);
                    if (! $product) {
                        throw new RuntimeException("Produto {$item['product_id']} não encontrado");
                    }

                    // Revalida estoque dentro do lock
                    $available = $inventoryRepository->countItem($product->id);
                    if ($available < $item['quantity']) {
                        throw new RuntimeException("Estoque insuficiente para o produto {$product->id}");
                    }

                    // Atualiza estoque
                    $inventoryRepository->store([
                        'product_id' => $product->id,
                        'quantity' => -(int) $item['quantity'],
                        'last_updated' => now(),
                    ]);

                    // Atualiza itens da venda
                    $saleItemsRepository->updateById([
                        'unit_price' => $product->sale_price,
                        'unit_cost' => $product->cost_price,
                    ], $item['id']);
                });
            });
        }

        // Atualiza totais e status da venda
        $productIds = array_column($this->items, 'product_id');
        $products = $productsRepository->getByValuesIn('id', $productIds)->keyBy('id');
        [$totalAmount, $totalCost, $totalProfit] = $saleService->calculateTotals($this->items, $products);

        $saleRepository->updateById([
            'total_amount' => $totalAmount,
            'total_cost' => $totalCost,
            'total_profit' => $totalProfit,
            'status' => 'completed',
        ], $sale->id);
    }
}
