<?php

namespace App\Services;

use App\Exceptions\ProductException;
use App\Models\Inventory;
use App\Repositories\InventoryRepository;
use App\Services\Interfaces\InventoryServiceInterface;
use App\Services\Interfaces\ProductServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

readonly class InventoryService implements InventoryServiceInterface
{
    public const CACHE_KEY_INVENTORY = 'inventory_summary';

    public function __construct(
        private InventoryRepository $inventoryRepository,
        private ProductServiceInterface $productService,
    ) {}

    // Implement the methods of InventoryServiceInterface

    /**
     * @throws Throwable|ProductException
     */
    public function register(array $data): Inventory
    {
        return DB::transaction(function () use ($data) {
            [$partialProduct, $inventory] = $this->handleDataRegister($data);
            throw_unless($this->productService->updateById($partialProduct, $inventory['product_id']), new ProductException('Erro ao atualizar produto'));
            $inventory = $this->inventoryRepository->store($inventory);

            Cache::forget(self::CACHE_KEY_INVENTORY);

            return $inventory;
        });
    }

    private function handleDataRegister($data): array
    {
        return [
            [
                'cost_price' => $data['cost_price'],
                'sale_price' => $data['sale_price'],
            ],
            [
                'quantity' => $data['quantity'],
                'product_id' => $data['product_id'],
                'last_updated' => now(),
            ],
        ];
    }

    public function getInventory(): Collection
    {
        Cache::forget(self::CACHE_KEY_INVENTORY);

        $data = Cache::remember(self::CACHE_KEY_INVENTORY, 300, function () {
            return $this->inventoryRepository->list();
        });

        return $data;
    }

    public function validateStock(array $items): void
    {
        foreach ($items as $item) {
            $productId = $item['product_id'];
            $requested = $item['quantity'];

            $stock = Inventory::where('product_id', $productId)->sum('quantity');

            if ($stock < $requested) {
                throw ValidationException::withMessages([
                    'items' => "Estoque insuficiente para o produto {$productId}. Disponível: {$stock}",
                ]);
            }
        }
    }
}
