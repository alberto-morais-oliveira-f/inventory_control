<?php

namespace App\Services;

use App\Jobs\ProcessSale;
use App\Models\Sale;
use App\Repositories\Contracts\InventoryRepositoryInterface;
use App\Repositories\Contracts\ProductsRepositoryInterface;
use App\Repositories\Contracts\SaleItemsRepositoryInterface;
use App\Repositories\Contracts\SaleRepositoryInterface;
use App\Services\Interfaces\SaleServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

readonly class SaleService implements SaleServiceInterface
{
    public function __construct(
        private SaleRepositoryInterface $saleRepository,
        private InventoryRepositoryInterface $inventoryRepository,
        private ProductsRepositoryInterface $productsRepository,
        private SaleItemsRepositoryInterface $saleItemsRepository,
    ) {
    }

    // Implement the methods of SaleServiceInterface

    /**
     * @throws Throwable
     */
    public function register(array $data): Sale
    {
        $items = $data['items'] ?? [];
        $this->validateStock($items);
        $sale = DB::transaction(function () use ($items) {
            $sale = $this->saleRepository->store([
                'status' => 'pending',
            ]);
            foreach ($items as $item) {
                $this->saleItemsRepository->store([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => (int) $item['quantity'],
                ]);
            }

            return $sale;
        });

        ProcessSale::dispatch(
            $items,
            $sale
        );

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

    public function calculateTotals(array $items, array $productsMap): array
    {
        $totalAmount = 0;
        $totalCost = 0;

        foreach ($items as $item) {
            $product = $productsMap[$item['product_id']];
            $quantity = (int) $item['quantity'];
            $unitPrice = (float) $product['unit_price'];
            $unitCost = (float) $product['unit_cost'];

            $totalAmount += $quantity * $unitPrice;
            $totalCost += $quantity * $unitCost;
        }

        $totalProfit = $totalAmount - $totalCost;

        return [$totalAmount, $totalCost, $totalProfit];
    }
}
