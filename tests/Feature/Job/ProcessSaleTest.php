<?php

namespace Tests\Feature\Job;

use App\Jobs\ProcessSale;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Repositories\Contracts\ProductsRepositoryInterface;
use App\Services\Interfaces\SaleServiceInterface;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

class ProcessSaleTest extends TestCase
{
    #[Test]
    public function it_job_processes_sale_when_stock_is_sufficient(): void
    {
        // Cria produtos e estoque
        $product = Product::inRandomOrder()->first();
        Inventory::factory()->create(['product_id' => $product->id, 'quantity' => 10]);

        $sale = Sale::factory()->create(['status' => 'pending']);
        $saleItem = SaleItem::factory()->create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        ProcessSale::dispatchSync(
            [['id' => $saleItem->id, 'product_id' => $product->id, 'quantity' => 5]],
            $sale->id
        );

        $this->assertDatabaseHas('inventory', [
            'product_id' => $product->id,
            'quantity' => -5,
        ]);

        $saleService = app(SaleServiceInterface::class);
        $productsRepository = app(ProductsRepositoryInterface::class);
        $products = $productsRepository->getByValuesIn('id', [$product->id])->keyBy('id');

        [$totalAmount, $totalCost, $totalProfit] = $saleService->calculateTotals([$saleItem->toArray()], $products);

        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'status' => 'completed',
            'total_amount' => $totalAmount, // 5 * 100
            'total_cost' => $totalCost,   // 5 * 60
            'total_profit' => $totalProfit, // 500 - 300
        ]);
    }

    #[Test]
    public function it_job_fails_when_stock_is_insufficient(): void
    {
        $product = Product::inRandomOrder()->first();
        Inventory::factory()->create(['product_id' => $product->id, 'quantity' => 3]);

        $sale = Sale::factory()->create();

        $saleItem = SaleItem::factory()->create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        $this->expectException(RuntimeException::class); // Estoque insuficiente

        ProcessSale::dispatchSync(
            [['id' => $saleItem->id, 'product_id' => $product->id, 'quantity' => 5]],
            $sale->id
        );

        $this->assertDatabaseHas('inventory', [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'status' => 'pending',
        ]);
    }
}
