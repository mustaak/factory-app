<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'description' => 'Electronic devices, gadgets and accessories.',
                'image' => 'headphones.jpg',
            ],
            [
                'name' => 'Fashion',
                'description' => 'Clothing, footwear and fashion accessories.',
                'image' => 'classic-tshirt.jpg',
            ],
            [
                'name' => 'Home & Kitchen',
                'description' => 'Furniture, kitchenware and home essentials.',
                'image' => 'watch.jpg',
            ],
            [
                'name' => 'Beauty & Personal Care',
                'description' => 'Beauty products and personal care essentials.',
                'image' => 'classic-tshirt.jpg',
            ],
            [
                'name' => 'Sports & Outdoors',
                'description' => 'Sports equipment, fitness and outdoor products.',
                'image' => 'sneakers.jpg',
            ],
            [
                'name' => 'Books & Stationery',
                'description' => 'Books, office supplies and stationery.',
                'image' => 'watch.jpg',
            ],
        ];

        foreach ($categories as $item) {
            $slug = Str::slug($item['name']);
            $imagePath = null;
            $source = 'products/' . $item['image'];
            $destination = 'categories/' . $item['image'];

            if (Storage::disk('public')->exists($source)) {
                if (! Storage::disk('public')->exists($destination)) {
                    Storage::disk('public')->copy($source, $destination);
                }

                $imagePath = $destination;
            }

            Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'image_path' => $imagePath,
                    'is_active' => true,
                ]
            );
        }
    }
}
