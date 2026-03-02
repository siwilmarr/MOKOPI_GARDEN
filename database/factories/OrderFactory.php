<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;
use App\Models\Product;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $product = Product::factory()->create();
        $quantity = fake()->numberBetween(1, 5);

        return [
            'product_id' => $product->id,
            'quantity' => $quantity,
            'total_price' => $product->price * $quantity,
            'customer_name' => fake()->name(),
            'status' => 'pending',
        ];
    }
}
