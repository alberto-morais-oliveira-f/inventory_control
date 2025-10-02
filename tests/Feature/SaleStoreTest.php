<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SaleStoreTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserSanctum();
    }

    #[Test]
    public function it_create_sale_success(): void
    {
        $product = Product::inRandomOrder()->first();
        Inventory::factory()->create(['product_id' => $product->id, 'quantity' => 10]);
        $response = $this->postJson(route('sale-store'), [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => $this->faker->numberBetween(1, 5),
                ],
            ],
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('sales', [
            'status' => 'pending', // porque vai pro job async
        ]);
    }
}
