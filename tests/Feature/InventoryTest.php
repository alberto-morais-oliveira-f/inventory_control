<?php

namespace Tests\Feature;

use App\Http\Resources\InventoryResource;
use App\Models\Inventory;
use App\Models\Product;
use App\Repositories\Contracts\InventoryRepositoryInterface;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserSanctum();
    }

    #[Test]
    public function it_create_inventory_success(): void
    {
        $costPrice = $this->faker->randomFloat(2, 10, 100);

        $response = $this->postJson(route('inventory.store'), [
            'quantity' => $this->faker->numberBetween(1, 50),
            'product_id' => Product::inRandomOrder()->first()->id,
            'cost_price' => $costPrice,
            'sale_price' => addPercentage(10, $costPrice),
        ]);


        $response->assertJsonFragment(['message' => 'Inventory registrado com sucesso.']);
        $response->assertStatus(Response::HTTP_OK);
    }

    #[Test]
    public function it_create_inventory_validate_error(): void
    {
        $costPrice = $this->faker->randomFloat(2, 10, 100);

        $response = $this->postJson(route('inventory.store'), [
            'product_id' => Product::inRandomOrder()->first()->id,
            'cost_price' => $costPrice,
            'sale_price' => addPercentage(10, $costPrice),
        ]);

        $response->assertJsonFragment([
            'message' => 'The quantity field is required.',
            'errors' => [
                'quantity' => ['The quantity field is required.'],
            ],
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    #[Test]
    public function it_show_inventory_success(): void
    {
        Inventory::factory(10)->create();
        $response = $this->getJson(route('inventory.index'));
        $inventory = $this->app->make(InventoryRepositoryInterface::class);
        $data = $inventory->list();
        $expected = InventoryResource::collection($data)->resolve();

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['data' => $expected])
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'product_id',
                        'sku',
                        'name',
                        'total_quantity',
                        'total_cost',
                        'total_sale',
                        'projected_profit',
                    ],
                ],
            ]);
    }
}
