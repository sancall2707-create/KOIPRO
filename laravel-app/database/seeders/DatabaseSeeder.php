<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;
use App\Models\CoffeeTable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin default
        Admin::firstOrCreate(
            ['username' => 'admin'],
            ['password' => Hash::make('admin123')]
        );

        // Kategori
        $categories = [
            ['name' => 'Coffee'],
            ['name' => 'Non Coffee'],
            ['name' => 'Food'],
            ['name' => 'Snacks'],
            ['name' => 'Dessert'],
        ];
        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat['name']]);
        }

        $coffee = Category::where('name', 'Coffee')->first();
        $nonCoffee = Category::where('name', 'Non Coffee')->first();
        $food = Category::where('name', 'Food')->first();

        // Produk
        $products = [
            ['category_id' => $coffee->id,    'name' => 'Kopi Hitam',        'price' => 12000, 'stock' => 50, 'description' => 'Kopi hitam pilihan dari biji robusta lokal'],
            ['category_id' => $coffee->id,    'name' => 'Kopi Susu',         'price' => 18000, 'stock' => 50, 'description' => 'Kopi susu creamy dengan espresso double shot'],
            ['category_id' => $coffee->id,    'name' => 'Cappuccino',        'price' => 22000, 'stock' => 30, 'description' => 'Espresso dengan steamed milk dan foam tebal'],
            ['category_id' => $coffee->id,    'name' => 'Americano',         'price' => 18000, 'stock' => 40, 'description' => 'Espresso dengan air panas, rasa bersih dan kuat'],
            ['category_id' => $nonCoffee->id, 'name' => 'Matcha Latte',      'price' => 22000, 'stock' => 25, 'description' => 'Matcha premium Jepang dengan susu segar'],
            ['category_id' => $nonCoffee->id, 'name' => 'Teh Tarik',         'price' => 15000, 'stock' => 40, 'description' => 'Teh hitam kental dengan susu condensed'],
            ['category_id' => $food->id,      'name' => 'Roti Bakar',        'price' => 20000, 'stock' => 20, 'description' => 'Roti bakar dengan selai kacang dan cokelat'],
            ['category_id' => $food->id,      'name' => 'Nasi Goreng Kopi',  'price' => 35000, 'stock' => 15, 'description' => 'Nasi goreng special dengan aroma kopi'],
        ];

        foreach ($products as $p) {
            Product::firstOrCreate(['name' => $p['name']], array_merge($p, ['status' => 'active']));
        }

        // Meja
        for ($i = 1; $i <= 10; $i++) {
            CoffeeTable::firstOrCreate(
                ['table_number' => (string) $i],
                ['status' => 'available']
            );
        }
    }
}
