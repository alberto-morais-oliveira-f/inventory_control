<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SalesReportTest extends TestCase
{
    #[Test]
    public function it_sales_report_with_filters_and_pagination(): void
    {
        // Arrange
        $this->createUserSanctum();
        $products = Product::inRandomOrder()->get();
        $sales = Sale::factory(5)->create();
        foreach ($sales as $sale) {
            foreach ($products as $product) {
                SaleItem::factory()->create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => random_int(1, 5),
                    'unit_price' => $product->sale_price,
                    'unit_cost' => $product->cost_price,
                ]);
            }
        }

        $startDate = now()->subDay()->toDateString();
        $endDate = now()->addDay()->toDateString();
        $sku = $products[0]->sku;

        // Act
        $response = $this->getJson(route('report.sales', [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'product_sku' => $sku,
            'per_page' => 2,
        ]));

        // Assert
        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'status',
                    'total_amount',
                    'total_cost',
                    'total_profit',
                    'created_at',
                    'items' => [
                        '*' => [
                            'product_id',
                            'sku',
                            'name',
                            'quantity',
                            'unit_price',
                            'unit_cost',
                        ],
                    ],
                ],
            ],
            'paginate',
        ]);

        $this->assertNotEmpty($response->json('data.0.items'));
        $this->assertEquals($sku, $response->json('data.0.items.0.sku'));
    }
}
