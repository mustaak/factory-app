<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class DemoProductsSeeder extends Seeder
{
    private const VENDOR_ID = 1;

    /**
     * Download demo product images into storage/app/public/products.
     */
    private function downloadDemoImages(): void
    {
        $images = [
            'tshirt-black-m.jpg'  => 'photo-1521572163474-6864f9cf17ab',
            'tshirt-black-l.jpg'  => 'photo-1503342217505-b0a15ec3261c',
            'tshirt-white-m.jpg'  => 'photo-1521572163474-6864f9cf17ab',
            'tshirt-white-l.jpg'  => 'photo-1503342217505-b0a15ec3261c',

            'sneakers-red-40.jpg'  => 'photo-1542291026-7eec264c27ff',
            'sneakers-red-42.jpg'  => 'photo-1542291026-7eec264c27ff',
            'sneakers-blue-40.jpg' => 'photo-1600185365483-26d7a4cc7519',
            'sneakers-blue-42.jpg' => 'photo-1600185365483-26d7a4cc7519',

            'headphones-black.jpg' => 'photo-1505740420928-5e560c06d30e',
            'headphones-white.jpg' => 'photo-1505740420928-5e560c06d30e',

            'watch-silver.jpg' => 'photo-1524805444758-089113d48a6d',
            'watch-gold.jpg'   => 'photo-1524805444758-089113d48a6d',
        ];

        $disk = Storage::disk('public');
        $disk->makeDirectory('products');

        foreach ($images as $filename => $photoId) {
            $path = 'products/' . $filename;

            // Reuse an existing, non-empty image.
            if ($disk->exists($path) && $disk->size($path) > 0) {
                $this->command?->info("Image already exists: {$filename}");
                continue;
            }

            $url = "https://images.unsplash.com/{$photoId}"
                . '?fit=crop&w=900&h=900&fm=jpg&q=85';

            try {
                $response = Http::timeout(30)
                    ->retry(2, 500)
                    ->get($url);

                $body = $response->body();
                $contentType = strtolower(
                    $response->header('Content-Type', '')
                );

                if (
                    ! $response->successful()
                    || ! str_starts_with($contentType, 'image/')
                    || strlen($body) < 1000
                    || @getimagesizefromstring($body) === false
                ) {
                    throw new RuntimeException(
                        "Downloaded file is not a valid image: {$filename}"
                    );
                }

                $disk->put($path, $body);

                $this->command?->info("Image downloaded: {$filename}");
            } catch (\Throwable $e) {
                throw new RuntimeException(
                    "Could not download {$filename}. "
                    . "Check your internet connection and try again. "
                    . $e->getMessage(),
                    0,
                    $e
                );
            }
        }
    }

    public function run(): void
    {
        // Validate the vendor before downloading images or modifying data.
        $vendor = DB::table('vendors')
            ->where('id', self::VENDOR_ID)
            ->whereNull('deleted_at')
            ->first();

        if (
            ! $vendor
            || $vendor->status !== 'active'
            || $vendor->verification_status !== 'approved'
        ) {
            throw new RuntimeException(
                'Vendor ID 1 must be active and approved.'
            );
        }

        // Download all required images first.
        $this->downloadDemoImages();

        $products = $this->catalog();

        // Replace existing demo products and insert fresh records.
        DB::transaction(function () use ($products) {
            $this->removeDemoProducts();

            foreach ($products as $product) {
                $this->createProduct($product);
            }
        });

        $this->command?->info(
            'Demo products, variants and product images seeded successfully.'
        );
    }

    /**
     * Remove previous demo records so rerunning the seeder
     * does not create duplicate demo products.
     */
    private function removeDemoProducts(): void
    {
        $ids = DB::table('products')
            ->where('vendor_id', self::VENDOR_ID)
            ->where('slug', 'like', 'demo-%')
            ->pluck('id');

        if ($ids->isEmpty()) {
            return;
        }

        DB::table('product_images')
            ->whereIn('product_id', $ids)
            ->delete();

        DB::table('product_variants')
            ->whereIn('product_id', $ids)
            ->delete();

        DB::table('products')
            ->whereIn('id', $ids)
            ->delete();
    }

    /**
     * Insert a product, its variants, and its product image records.
     */
    private function createProduct(array $item): void
    {
        $now = now();

        $categoryId = $item['category']
            ? DB::table('categories')
                ->where('slug', $item['category'])
                ->value('id')
            : null;

        $productId = DB::table('products')->insertGetId([
            'vendor_id'        => self::VENDOR_ID,
            'category_id'      => $categoryId,
            'name'             => $item['name'],
            'slug'             => $item['slug'],
            'short_description' => $item['short_description'],
            'description'      => $item['description'],
            'brand'            => $item['brand'],
            'status'           => 'active',
            'approval_status'  => 'approved',
            'rejection_reason' => null,
            'reviewed_by'      => null,
            'reviewed_at'      => null,
            'deleted_at'       => null,
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);

        foreach ($item['variants'] as $index => $variant) {
            DB::table('product_variants')->insert([
                'product_id'       => $productId,
                'sku'              => $variant['sku'],
                'size'             => $variant['size'],
                'color'            => $variant['color'],
                'price'            => $variant['price'],
                'compare_at_price' => $variant['compare_at_price'],
                'stock_quantity'   => $variant['stock_quantity'],
                'image_path'       => $variant['image'],
                'is_active'        => true,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);

            DB::table('product_images')->insert([
                'product_id' => $productId,
                'image_path' => $variant['image'],
                'sort_order' => $index + 1,
                'is_primary' => $index === 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $this->command?->info("Product created: {$item['name']}");
    }

    /**
     * Demo product catalog.
     */
    private function catalog(): array
    {
        return [
            [
                'name' => 'Classic Cotton T-Shirt',
                'slug' => 'demo-classic-cotton-tshirt',
                'brand' => 'DemoWear',
                'category' => 'clothing',
                'short_description' => 'Comfortable everyday cotton T-shirt.',
                'description' => "A versatile cotton T-shirt for everyday wear.\n"
                    . 'Available in Black and White, sizes M and L.',
                'variants' => [
                    [
                        'sku' => 'DEMO-TEE-BLK-M',
                        'color' => 'Black',
                        'size' => 'M',
                        'price' => 499,
                        'compare_at_price' => 699,
                        'stock_quantity' => 50,
                        'image' => 'products/tshirt-black-m.jpg',
                    ],
                    [
                        'sku' => 'DEMO-TEE-BLK-L',
                        'color' => 'Black',
                        'size' => 'L',
                        'price' => 499,
                        'compare_at_price' => 699,
                        'stock_quantity' => 40,
                        'image' => 'products/tshirt-black-l.jpg',
                    ],
                    [
                        'sku' => 'DEMO-TEE-WHT-M',
                        'color' => 'White',
                        'size' => 'M',
                        'price' => 549,
                        'compare_at_price' => 749,
                        'stock_quantity' => 30,
                        'image' => 'products/tshirt-white-m.jpg',
                    ],
                    [
                        'sku' => 'DEMO-TEE-WHT-L',
                        'color' => 'White',
                        'size' => 'L',
                        'price' => 549,
                        'compare_at_price' => 749,
                        'stock_quantity' => 35,
                        'image' => 'products/tshirt-white-l.jpg',
                    ],
                ],
            ],
            [
                'name' => 'Sport Sneakers',
                'slug' => 'demo-sport-sneakers',
                'brand' => 'DemoStep',
                'category' => 'footwear',
                'short_description' => 'Sporty sneakers for daily activities.',
                'description' => 'Lightweight sneakers for casual everyday use.',
                'variants' => [
                    [
                        'sku' => 'DEMO-SNK-RED-40',
                        'color' => 'Red',
                        'size' => '40',
                        'price' => 1499,
                        'compare_at_price' => 1999,
                        'stock_quantity' => 20,
                        'image' => 'products/sneakers-red-40.jpg',
                    ],
                    [
                        'sku' => 'DEMO-SNK-RED-42',
                        'color' => 'Red',
                        'size' => '42',
                        'price' => 1499,
                        'compare_at_price' => 1999,
                        'stock_quantity' => 25,
                        'image' => 'products/sneakers-red-42.jpg',
                    ],
                    [
                        'sku' => 'DEMO-SNK-BLU-40',
                        'color' => 'Blue',
                        'size' => '40',
                        'price' => 1599,
                        'compare_at_price' => 2099,
                        'stock_quantity' => 18,
                        'image' => 'products/sneakers-blue-40.jpg',
                    ],
                    [
                        'sku' => 'DEMO-SNK-BLU-42',
                        'color' => 'Blue',
                        'size' => '42',
                        'price' => 1599,
                        'compare_at_price' => 2099,
                        'stock_quantity' => 22,
                        'image' => 'products/sneakers-blue-42.jpg',
                    ],
                ],
            ],
            [
                'name' => 'Wireless Headphones',
                'slug' => 'demo-wireless-headphones',
                'brand' => 'DemoSound',
                'category' => 'electronics',
                'short_description' => 'Headphones for music and everyday listening.',
                'description' => 'A demo audio product for your marketplace catalog.',
                'variants' => [
                    [
                        'sku' => 'DEMO-HPH-BLK',
                        'color' => 'Black',
                        'size' => null,
                        'price' => 1999,
                        'compare_at_price' => 2499,
                        'stock_quantity' => 30,
                        'image' => 'products/headphones-black.jpg',
                    ],
                    [
                        'sku' => 'DEMO-HPH-WHT',
                        'color' => 'White',
                        'size' => null,
                        'price' => 1999,
                        'compare_at_price' => 2499,
                        'stock_quantity' => 25,
                        'image' => 'products/headphones-white.jpg',
                    ],
                ],
            ],
            [
                'name' => 'Classic Wrist Watch',
                'slug' => 'demo-classic-wrist-watch',
                'brand' => 'DemoTime',
                'category' => 'accessories',
                'short_description' => 'Classic-style wrist watch.',
                'description' => 'A sample watch product for testing product listings.',
                'variants' => [
                    [
                        'sku' => 'DEMO-WTC-SLV',
                        'color' => 'Silver',
                        'size' => null,
                        'price' => 2999,
                        'compare_at_price' => 3999,
                        'stock_quantity' => 15,
                        'image' => 'products/watch-silver.jpg',
                    ],
                    [
                        'sku' => 'DEMO-WTC-GLD',
                        'color' => 'Gold',
                        'size' => null,
                        'price' => 3499,
                        'compare_at_price' => 4499,
                        'stock_quantity' => 12,
                        'image' => 'products/watch-gold.jpg',
                    ],
                ],
            ],
        ];
    }
}
