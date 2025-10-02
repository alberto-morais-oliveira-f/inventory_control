<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $costPrice = $this->faker->randomFloat(2, 10, 100);

        return [
            'sku' => $this->faker->unique()->text(14),
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->words(20, true),
            'cost_price' => $costPrice,
            'sale_price' => addPercentage(10, $costPrice),
        ];
    }
}
