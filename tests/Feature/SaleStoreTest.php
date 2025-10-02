<?php

namespace Tests\Feature;

use App\Http\Resources\InventoryResource;
use App\Models\Inventory;
use App\Models\Product;
use App\Repositories\Contracts\InventoryRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        Inventory::factory(10)->create();
        $response = $this->postJson(route('sale-store'), [
            'items' => [
                [
                    'product_id' => Product::inRandomOrder()->first()->id,
                    'quantity' => $this->faker->numberBetween(1, 10),
                ],
                [
                    'product_id' => Product::inRandomOrder()->first()->id,
                    'quantity' => $this->faker->numberBetween(1, 10),
                ],
            ],
        ]);
        dd($response);
        $response->assertStatus(Response::HTTP_OK);
    }
}
