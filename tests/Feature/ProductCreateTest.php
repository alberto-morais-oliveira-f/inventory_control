<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductCreateTest extends TestCase
{
    use withFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserSanctum();
    }

    #[Test]
    public function it_create_product_error_validation(): void
    {
        $costPrice = $this->faker->randomFloat(2, 10, 100);
        $response = $this->postJson('/api/product', [
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->words(20, true),
            'cost_price' => $costPrice,
            'sale_price' => addPercentage(10, $costPrice),
        ]);
        $response->assertJsonFragment(['message' => 'The sku field is required.']);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    #[Test]
    public function it_create_product_success(): void
    {
        $costPrice = $this->faker->randomFloat(2, 10, 100);

        $response = $this->postJson('/api/product', [
            'sku' => $this->faker->unique()->text(14),
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->words(20, true),
            'cost_price' => $costPrice,
            'sale_price' => addPercentage(10, $costPrice),
        ]);

        $response->assertJsonFragment(['message' => 'Produto cadastrado com sucesso!']);
        $response->assertStatus(Response::HTTP_OK);
    }
}
