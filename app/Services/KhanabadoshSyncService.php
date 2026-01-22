<?php

namespace App\Services;

use App\Models\Collection;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class KhanabadoshSyncService
{
    public function sync(): array
    {
        $baseUrl = rtrim(config('khanabadosh.source_url'), '/');

        $collections = $this->fetchPaginated("{$baseUrl}/collections.json?limit=250", 'collections');
        $products = $this->fetchPaginated("{$baseUrl}/products.json?limit=250", 'products');

        $collectionModels = [];
        $productModels = [];

        return DB::transaction(function () use ($collections, $products, $baseUrl, &$collectionModels, &$productModels) {
            foreach ($collections as $collection) {
                $collectionModels[$collection['handle']] = Collection::updateOrCreate(
                    ['shopify_id' => $collection['id']],
                    [
                        'title' => $collection['title'] ?? $collection['handle'],
                        'handle' => $collection['handle'],
                        'source_updated_at' => $collection['updated_at'] ?? null,
                    ]
                );
            }

            foreach ($products as $product) {
                $firstVariant = $product['variants'][0] ?? [];

                $rawTags = $product['tags'] ?? null;
                $tags = is_array($rawTags) ? implode(', ', $rawTags) : $rawTags;

                $productModel = Product::updateOrCreate(
                    ['shopify_id' => $product['id']],
                    [
                        'title' => $product['title'] ?? $product['handle'],
                        'handle' => $product['handle'],
                        'body_html' => $product['body_html'] ?? null,
                        'product_type' => $product['product_type'] ?? null,
                        'vendor' => $product['vendor'] ?? null,
                        'tags' => $tags,
                        'price' => $firstVariant['price'] ?? null,
                        'compare_at_price' => $firstVariant['compare_at_price'] ?? null,
                        'available' => $firstVariant['available'] ?? true,
                        'source_created_at' => $product['created_at'] ?? null,
                        'source_updated_at' => $product['updated_at'] ?? null,
                    ]
                );

                $productModel->variants()->delete();
                foreach ($product['variants'] ?? [] as $variant) {
                    $productModel->variants()->create([
                        'shopify_id' => $variant['id'],
                        'title' => $variant['title'] ?? 'Default Title',
                        'sku' => $variant['sku'] ?? null,
                        'option1' => $variant['option1'] ?? null,
                        'option2' => $variant['option2'] ?? null,
                        'option3' => $variant['option3'] ?? null,
                        'price' => $variant['price'] ?? null,
                        'compare_at_price' => $variant['compare_at_price'] ?? null,
                        'inventory_quantity' => $variant['inventory_quantity'] ?? null,
                        'position' => $variant['position'] ?? 1,
                        'available' => $variant['available'] ?? true,
                    ]);
                }

                $productModel->images()->delete();
                foreach ($product['images'] ?? [] as $image) {
                    $productModel->images()->create([
                        'shopify_id' => $image['id'],
                        'src' => $image['src'] ?? '',
                        'position' => $image['position'] ?? 1,
                        'width' => $image['width'] ?? null,
                        'height' => $image['height'] ?? null,
                    ]);
                }

                $productModels[$product['id']] = $productModel;
            }

            DB::table('collection_product')->delete();
            $pivotRows = [];

            foreach ($collectionModels as $handle => $collectionModel) {
                $collectionProducts = $this->fetchCollectionProducts($baseUrl, $handle);
                foreach ($collectionProducts as $collectionProduct) {
                    $productModel = $productModels[$collectionProduct['id']] ?? null;
                    if (!$productModel) {
                        continue;
                    }
                    $pivotRows[] = [
                        'collection_id' => $collectionModel->id,
                        'product_id' => $productModel->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if ($pivotRows) {
                DB::table('collection_product')->insert($pivotRows);
            }

            return [
                'collections' => count($collectionModels),
                'products' => count($productModels),
                'variants' => DB::table('product_variants')->count(),
                'images' => DB::table('product_images')->count(),
                'collection_links' => count($pivotRows),
            ];
        });
    }

    private function fetchJson(string $url): array
    {
        return Http::retry(3, 200)
            ->get($url)
            ->throw()
            ->json();
    }

    private function fetchCollectionProducts(string $baseUrl, string $handle): array
    {
        $url = "{$baseUrl}/collections/{$handle}/products.json?limit=250";

        try {
            return $this->fetchPaginated($url, 'products');
        } catch (\Throwable $exception) {
            return [];
        }
    }

    private function fetchPaginated(string $url, string $key): array
    {
        $items = [];
        $nextUrl = $url;

        while ($nextUrl) {
            $response = Http::retry(3, 200)
                ->get($nextUrl);

            if (!$response->successful()) {
                break;
            }

            $payload = $response->json();
            $items = array_merge($items, $payload[$key] ?? []);
            $nextUrl = $this->nextPageUrl($response->header('Link'));
        }

        return $items;
    }

    private function nextPageUrl(?string $linkHeader): ?string
    {
        if (!$linkHeader) {
            return null;
        }

        $parts = explode(',', $linkHeader);
        foreach ($parts as $part) {
            if (str_contains($part, 'rel="next"')) {
                if (preg_match('/<([^>]+)>/', $part, $matches)) {
                    return $matches[1] ?? null;
                }
            }
        }

        return null;
    }
}
