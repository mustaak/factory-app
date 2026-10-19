<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class DemoProductsSeeder extends Seeder
{
    public function run(): void
    {
        $vendorId = 1;

        $vendor = DB::table('vendors')
            ->where('id', $vendorId)
            ->whereNull('deleted_at')
            ->first();

        if (
            ! $vendor ||
            $vendor->status !== 'active' ||
            $vendor->verification_status !== 'approved'
        ) {
            throw new RuntimeException(
                'Vendor ID 1 must be active and approved.'
            );
        }

        $disk = Storage::disk('public');

        $products = [
            [
                'name' => 'Classic Cotton T-Shirt',
                'slug' => 'demo-classic-cotton-tshirt',
                'brand' => 'DemoWear',
                'short_description' => 'Comfortable everyday cotton T-shirt.',
                'description' => 'A versatile cotton T-shirt for everyday wear.',
                'image' => 'products/classic-tshirt.jpg',
                'variants' => [
                    [
                        'sku' => 'DEMO-TSHIRT-BLK-M',
                        'size' => 'M',
                        'color' => 'Black',
                        'price' => 19.99,
                        'compare_at_price' => 24.99,
                        'stock_quantity' => 50,
                    ],
                    [
                        'sku' => 'DEMO-TSHIRT-WHT-L',
                        'size' => 'L',
                        'color' => 'White',
                        'price' => 21.99,
                        'compare_at_price' => 26.99,
                        'stock_quantity' => 35,
                    ],
                ],
            ],
            [
                'name' => 'Sport Sneakers',
                'slug' => 'demo-sport-sneakers',
                'brand' => 'DemoStep',
                'short_description' => 'Sporty sneakers for daily activities.',
                'description' => 'Lightweight-style sneakers for casual everyday use.',
                'image' => 'products/sneakers.jpg',
                'variants' => [
                    [
                        'sku' => 'DEMO-SNEAKER-40',
                        'size' => '40',
                        'color' => 'Red',
                        'price' => 49.99,
                        'compare_at_price' => 64.99,
                        'stock_quantity' => 20,
                    ],
                    [
                        'sku' => 'DEMO-SNEAKER-42',
                        'size' => '42',
                        'color' => 'Red',
                        'price' => 49.99,
                        'compare_at_price' => 64.99,
                        'stock_quantity' => 25,
                    ],
                ],
            ],
            [
                'name' => 'Wireless Headphones',
                'slug' => 'demo-wireless-headphones',
                'brand' => 'DemoSound',
                'short_description' => 'Headphones for music and everyday listening.',
                'description' => 'A demo audio product for your marketplace catalog.',
                'image' => 'products/headphones.jpg',
                'variants' => [
                    [
                        'sku' => 'DEMO-HEADPHONE-BLK',
                        'size' => null,
                        'color' => 'Black',
                        'price' => 39.99,
                        'compare_at_price' => 54.99,
                        'stock_quantity' => 30,
                    ],
                ],
            ],
            [
                'name' => 'Classic Wrist Watch',
                'slug' => 'demo-classic-wrist-watch',
                'brand' => 'DemoTime',
                'short_description' => 'Classic-style wrist watch.',
                'description' => 'A sample watch product for testing product listings.',
                'image' => 'products/watch.jpg',
                'variants' => [
                    [
                        'sku' => 'DEMO-WATCH-SILVER',
                        'size' => null,
                        'color' => 'Silver',
                        'price' => 59.99,
                        'compare_at_price' => 79.99,
                        'stock_quantity' => 15,
                    ],
                ],
            ],
        ];

        foreach ($products as $item) {
            if (! $disk->exists($item['image'])) {
                throw new RuntimeException(
                    "Missing product image: {$item['image']}"
                );
            }
        }

        DB::transaction(function () use ($products, $vendorId) {
            foreach ($products as $item) {
                $product = DB::table('products')
                    ->where('slug', $item['slug'])
                    ->first();

                $now = now();

                $productData = [
                    'vendor_id' => $vendorId,
                    'name' => $item['name'],
                    'slug' => $item['slug'],
                    'short_description' => $item['short_description'],
                    'description' => $item['description'],
                    'brand' => $item['brand'],
                    'status' => 'active',
                    'approval_status' => 'approved',
                    'rejection_reason' => null,
                    'reviewed_by' => null,
                    'reviewed_at' => null,
                    'deleted_at' => null,
                    'updated_at' => $now,
                ];

                if ($product) {
                    // Update only this demo product if it belongs to vendor 1.
                    if ((int) $product->vendor_id !== $vendorId) {
                        throw new RuntimeException(
                            "Slug {$item['slug']} belongs to another vendor."
                        );
                    }

                    DB::table('products')
                        ->where('id', $product->id)
                        ->update($productData);

                    $productId = $product->id;
                } else {
                    $productData['created_at'] = $now;

                    $productId = DB::table('products')
                        ->insertGetId($productData);
                }

                foreach ($item['variants'] as $variant) {
                    DB::table('product_variants')->updateOrInsert(
                        ['sku' => $variant['sku']],
                        [
                            'product_id' => $productId,
                            'size' => $variant['size'],
                            'color' => $variant['color'],
                            'price' => $variant['price'],
                            'compare_at_price' => $variant['compare_at_price'],
                            'stock_quantity' => $variant['stock_quantity'],
                            'image_path' => null,
                            'is_active' => true,
                            'updated_at' => $now,
                            'created_at' => $now,
                        ]
                    );
                }

                DB::table('product_images')->updateOrInsert(
                    [
                        'product_id' => $productId,
                        'image_path' => $item['image'],
                    ],
                    [
                        'sort_order' => 1,
                        'is_primary' => true,
                        'updated_at' => $now,
                        'created_at' => $now,
                    ]
                );
            }
        });

        $this->command->info('Demo products, variants and images seeded successfully.');
    }
}
