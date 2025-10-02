<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleItemFactory extends Factory
{
    protected $model = SaleItem::class;

    public function definition(): array
    {
        $product = Product::inRandomOrder()->first();

        return [
            'sale_id' => Sale::factory()->create()->id,
            'product_id' => $product->id,
            'quantity' => $this->faker->numberBetween(1, 10),
            'unit_price' => $product->sale_price,
            'unit_cost' => $product->cost_price,
        ];
    }
}
