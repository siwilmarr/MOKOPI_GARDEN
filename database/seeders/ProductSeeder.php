<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Espresso',
                'description' => 'Kopi espresso murni dengan cita rasa kuat dan pekat',
                'price' => 15000,
                'stock' => 50
            ],
            [
                'name' => 'Americano',
                'description' => 'Espresso dicampur air panas untuk rasa yang lebih ringan',
                'price' => 18000,
                'stock' => 45
            ],
            [
                'name' => 'Cappuccino',
                'description' => 'Espresso dengan susu dan busa yang creamy',
                'price' => 25000,
                'stock' => 40
            ],
            [
                'name' => 'Latte',
                'description' => 'Espresso dengan susu banyak untuk rasa yang smooth',
                'price' => 25000,
                'stock' => 35
            ],
            [
                'name' => 'Macchiato',
                'description' => 'Espresso ditandai dengan sedikit busa susu',
                'price' => 20000,
                'stock' => 30
            ],
            [
                'name' => 'Mocha',
                'description' => 'Kombinasi espresso, susu, dan cokelat yang nikmat',
                'price' => 28000,
                'stock' => 25
            ],
            [
                'name' => 'Affogato',
                'description' => 'Vanilla ice cream dengan espresso panas di atasnya',
                'price' => 22000,
                'stock' => 20
            ],
            [
                'name' => 'Flat White',
                'description' => 'Espresso dengan microfoam milk yang halus dan creamy',
                'price' => 24000,
                'stock' => 28
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
