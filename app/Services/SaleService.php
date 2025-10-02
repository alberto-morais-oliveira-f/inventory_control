<?php

namespace App\Services;

use App\Jobs\ProcessSale;
use App\Models\Sale;
use App\Repositories\Contracts\InventoryRepositoryInterface;
use App\Repositories\Contracts\SaleItemsRepositoryInterface;
use App\Repositories\Contracts\SaleRepositoryInterface;
use App\Services\Interfaces\SaleServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

readonly class SaleService implements SaleServiceInterface
{
    public function __construct(
        private SaleRepositoryInterface $saleRepository,
        private InventoryRepositoryInterface $inventoryRepository,
        private SaleItemsRepositoryInterface $saleItemsRepository,
    ) {
    }

    /**
     * @throws Throwable
     */
    public function register(array $data): Sale
    {
        $items = $data['items'] ?? [];
        $this->validateStock($items);
        $sale = $this->createSaleWithItems($items);
        $this->dispatchSaleJob($sale, $items);

        return $sale;
    }


    public function validateStock(array $items): void
    {
        foreach ($items as $item) {
            $productId = $item['product_id'];
            $requested = $item['quantity'];

            $stock = $this->inventoryRepository->countItem($productId);

            if ($stock < $requested) {
                throw ValidationException::withMessages([
                    "items" => "Estoque insuficiente para o produto {$productId}. Disponível: {$stock}",
                ]);
            }
        }
    }

    public function calculateTotals(array $items, Collection $productsMap): array
    {
        $totalAmount = 0;
        $totalCost = 0;
        foreach ($items as $item) {
            $product = $productsMap[$item['product_id']];
            $quantity = (int) $item['quantity'];

            $totalAmount += $quantity * (float) $product->sale_price;
            $totalCost += $quantity * (float) $product->cost_price;
        }
        

        $totalProfit = $totalAmount - $totalCost;

        return [$totalAmount, $totalCost, $totalProfit];
    }

    public function getSalesReport(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->saleRepository->getSalesReport($filters, $perPage);
    }

    private function createSaleWithItems(array &$items): Sale
    {
        return DB::transaction(function () use (&$items) {
            $sale = $this->saleRepository->store([
                'total_amount' => 0,
                'total_cost' => 0,
                'total_profit' => 0,
                'status' => 'pending',
            ]);

            foreach ($items as &$item) {
                $saleItem = $this->saleItemsRepository->store([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => (int) $item['quantity'],
                ]);
                $item['id'] = $saleItem->id;
            }

            return $sale;
        });
    }

    private function dispatchSaleJob(Sale $sale, array $items): void
    {
        ProcessSale::dispatch($items, $sale->id)->afterCommit();
    }
}
