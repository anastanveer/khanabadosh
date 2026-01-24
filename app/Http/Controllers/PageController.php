<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Support\CurrencyFormatter;
use Illuminate\Http\JsonResponse;

class PageController extends Controller
{
    public function home()
    {
        $heroProducts = Product::query()
            ->with('images')
            ->orderByDesc('source_created_at')
            ->take(5)
            ->get();

        $newMenProducts = $this->productsFromCollection('new-arrivals', 12, ['Men']);
        if ($newMenProducts->isEmpty()) {
            $newMenProducts = $this->productsByTags(['Men', 'New'], 12);
        }
        if ($newMenProducts->isEmpty()) {
            $newMenProducts = $this->productsByTags(['Men'], 12);
        }

        $menProducts = $this->productsFromCollection('men-all', 12);
        if ($menProducts->isEmpty()) {
            $menProducts = $this->productsByTags(['Men'], 12);
        }

        $womenProducts = $this->productsFromCollection('women-all', 12);
        if ($womenProducts->isEmpty()) {
            $womenProducts = $this->productsByTags(['Women'], 12);
        }

        return view('home', [
            'pageTitle' => 'Home',
            'heroProducts' => $heroProducts,
            'newMenProducts' => $newMenProducts,
            'menProducts' => $menProducts,
            'womenProducts' => $womenProducts,
        ]);
    }

    public function shop(Request $request)
    {
        $pageTitle = $request->query('title', 'Men Winter');
        $results = (int) $request->query('results', 18);
        $collectionSlug = Str::slug($pageTitle);
        $popularProducts = Product::query()
            ->where('is_popular', true)
            ->with('images')
            ->take(3)
            ->get();

        $collection = Collection::query()
            ->where('handle', $collectionSlug)
            ->with(['products.images'])
            ->first();

        if ($collection && $collection->products->count() > 0) {
            $products = $collection->products;
            $results = $products->count();
        } else {
            $products = $this->fallbackProductsForSlug($collectionSlug);
            $results = $products->count();
        }

        if ($popularProducts->isEmpty()) {
            $popularProducts = Product::query()
                ->with('images')
                ->orderByDesc('source_created_at')
                ->take(3)
                ->get();
        }

        $inStockCount = $products->filter(fn ($product) => $product->available ?? true)->count();
        $sidebarGroups = $this->resolveSidebarGroups();

        return view('shop', [
            'pageTitle' => $pageTitle,
            'results' => $results,
            'products' => $products,
            'collectionSlug' => $collectionSlug,
            'sidebarMen' => $sidebarGroups['men'],
            'sidebarWomen' => $sidebarGroups['women'],
            'inStockCount' => $inStockCount,
            'popularProducts' => $popularProducts,
        ]);
    }

    public function collection(string $slug)
    {
        $collection = Collection::query()
            ->where('handle', $slug)
            ->with(['products.images'])
            ->first();
        if (!$collection) {
            $alias = $this->resolveSaleAlias($slug);
            if ($alias) {
                $collection = Collection::query()
                    ->where('handle', $alias)
                    ->with(['products.images'])
                    ->first();
            }
        }

        $pageTitle = $collection?->title ?? $this->resolveCollectionTitle($slug);
        if ($collection && $collection->products->count() > 0) {
            $products = $collection->products;
            $results = $products->count();
        } else {
            $products = $this->fallbackProductsForSlug($slug);
            $results = $products->count();
        }

        $popularProducts = Product::query()
            ->where('is_popular', true)
            ->with('images')
            ->take(3)
            ->get();
        if ($popularProducts->isEmpty()) {
            $popularProducts = Product::query()
                ->with('images')
                ->orderByDesc('source_created_at')
                ->take(3)
                ->get();
        }

        return view('shop', [
            'pageTitle' => $pageTitle,
            'results' => $results,
            'products' => $products,
            'collectionSlug' => $slug,
            'sidebarMen' => $this->resolveSidebarGroups()['men'],
            'sidebarWomen' => $this->resolveSidebarGroups()['women'],
            'inStockCount' => $products->filter(fn ($product) => $product->available ?? true)->count(),
            'popularProducts' => $popularProducts,
        ]);
    }

    public function product(string $collection, string $slug)
    {
        $collectionModel = Collection::query()->where('handle', $collection)->first();
        $productModel = Product::query()
            ->where('handle', $slug)
            ->with(['images', 'variants'])
            ->first();

        $collectionTitle = $collectionModel?->title ?? $this->resolveCollectionTitle($collection);
        $product = $productModel ?? $this->resolveProduct($slug);

        return view('product', [
            'pageTitle' => $productModel?->title ?? $product['name'],
            'collectionSlug' => $collection,
            'collectionTitle' => $collectionTitle,
            'product' => $product,
        ]);
    }

    public function search(Request $request)
    {
        $query = trim((string) $request->query('q', ''));
        $results = collect();

        if ($query !== '') {
            $results = Product::query()
                ->with('images')
                ->where(function ($builder) use ($query) {
                    $builder->where('title', 'like', '%' . $query . '%')
                        ->orWhere('handle', 'like', '%' . $query . '%')
                        ->orWhere('tags', 'like', '%' . $query . '%')
                        ->orWhere('product_type', 'like', '%' . $query . '%');
                })
                ->orderBy('title')
                ->get();
        }

        return view('search', [
            'pageTitle' => 'Search',
            'query' => $query,
            'results' => $results,
        ]);
    }

    public function wishlist()
    {
        return view('wishlist', [
            'pageTitle' => 'Wishlist',
        ]);
    }

    public function cart()
    {
        return view('cart', [
            'pageTitle' => 'Cart',
        ]);
    }

    public function checkout()
    {
        return view('checkout', [
            'pageTitle' => 'Checkout',
            'bankName' => \App\Models\Setting::getValue('bank_name', 'Khanabadosh Bank'),
            'bankTitle' => \App\Models\Setting::getValue('bank_account_title', 'Khanabadosh Fashion'),
            'bankAccount' => \App\Models\Setting::getValue('bank_account_number', '0001-2233-4455'),
            'bankIban' => \App\Models\Setting::getValue('bank_iban', 'PK00KB0000000000000001'),
            'bankNote' => \App\Models\Setting::getValue('bank_note', 'Send payment to the bank account and upload the transfer screenshot below.'),
        ]);
    }

    public function trackOrder(Request $request)
    {
        $orderNumber = strtoupper(trim((string) $request->query('order_number', '')));
        $email = strtolower(trim((string) $request->query('email', '')));

        if ($orderNumber === '' || $email === '') {
            return view('track-order-gate', [
                'pageTitle' => 'Track Order',
                'messageTitle' => 'Order link required',
                'messageBody' => 'This page is only available from your confirmation email.',
                'helpBody' => 'If you placed an order and did not receive the email, contact support and we will resend it.',
            ]);
        }

        $order = Order::query()
            ->with('items')
            ->whereRaw('upper(order_number) = ?', [$orderNumber])
            ->whereRaw('lower(email) = ?', [$email])
            ->first();

        if (!$order) {
            return view('track-order-gate', [
                'pageTitle' => 'Track Order',
                'messageTitle' => 'We could not find that order',
                'messageBody' => 'Please make sure you opened the tracking link from your order email.',
                'helpBody' => 'If you think this is a mistake, contact support with your order number and email.',
            ]);
        }

        return view('track-order', [
            'pageTitle' => 'Track Order',
            'order' => $order,
        ]);
    }

    public function productsApi(Request $request): JsonResponse
    {
        $handles = collect(explode(',', (string) $request->query('handles', '')))
            ->map(fn ($handle) => trim($handle))
            ->filter()
            ->values();

        if ($handles->isEmpty()) {
            return response()->json(['items' => []]);
        }

        $products = Product::query()
            ->with('images')
            ->whereIn('handle', $handles)
            ->get()
            ->map(function ($product) {
                $image = optional($product->images->sortBy('position')->first())->src;
                $priceValue = $product->effectivePrice();
                $converted = CurrencyFormatter::convert($priceValue);

                return [
                    'handle' => $product->handle,
                    'title' => $product->title,
                    'price_value' => $converted ?? 0,
                    'price_label' => CurrencyFormatter::format($priceValue),
                    'image' => $image,
                    'url' => route('products.show', ['collection' => 'men-all', 'slug' => $product->handle]),
                    'available' => (bool) $product->available,
                ];
            });

        return response()->json([
            'items' => $products,
            'currency' => CurrencyFormatter::currency(),
            'symbol' => CurrencyFormatter::symbol(),
        ]);
    }

    public function policy(Request $request)
    {
        $pageTitle = $request->query('title', 'Policies');

        $policyMap = [
            'Policies' => [
                'intro' => 'Khanabadosh Fashion Canada shares details about shipping, returns, privacy, and terms. Please choose a policy from the menu to read full information.',
                'sections' => [
                    [
                        'title' => 'Quick Links',
                        'items' => [
                            'Shipping Policy',
                            'Exchange & Return Policy',
                            'FAQs',
                            'Terms & Conditions',
                            'Privacy Policy',
                        ],
                    ],
                ],
            ],
            'Shipping Policy' => [
                'intro' => 'Khanabadosh Fashion Canada processes and ships orders with care. Shipping rates and delivery timelines are shown at checkout.',
                'sections' => [
                    [
                        'title' => 'Order Processing',
                        'items' => [
                            'Processing time: 1-2 business days after order confirmation.',
                            'Orders placed on weekends or holidays process the next business day.',
                        ],
                    ],
                    [
                        'title' => 'Delivery',
                        'items' => [
                            'Estimated delivery in Canada: 3-7 business days after dispatch.',
                            'Tracking details are emailed when your order ships.',
                        ],
                    ],
                    [
                        'title' => 'Important Notes',
                        'items' => [
                            'Please ensure your shipping address is accurate.',
                            'Undelivered orders may be returned to sender and re-shipping fees may apply.',
                        ],
                    ],
                ],
            ],
            'Exchange & Return Policy' => [
                'intro' => 'Khanabadosh Fashion Canada accepts returns and exchanges with a simple process.',
                'sections' => [
                    [
                        'title' => 'Return Window',
                        'items' => [
                            'Returns accepted within 10 days of delivery.',
                            'Items must be unused, unwashed, and in original packaging.',
                        ],
                    ],
                    [
                        'title' => 'Exchanges',
                        'items' => [
                            'Exchanges depend on stock availability.',
                            'Sale items may be final sale unless otherwise stated.',
                        ],
                    ],
                    [
                        'title' => 'Refunds',
                        'items' => [
                            'Refunds are issued to the original payment method after inspection.',
                            'Please allow 5-7 business days for processing.',
                        ],
                    ],
                ],
            ],
            'FAQs' => [
                'intro' => 'Find quick answers from Khanabadosh Fashion Canada below.',
                'sections' => [
                    [
                        'title' => 'Orders',
                        'items' => [
                            'How do I place an order? Choose your product and complete checkout.',
                            'Can I change my order? Contact support as soon as possible after checkout.',
                        ],
                    ],
                    [
                        'title' => 'Shipping',
                        'items' => [
                            'How do I track my order? Use the tracking link sent to your email.',
                            'Do you ship across Canada? Yes, shipping is available nationwide.',
                        ],
                    ],
                    [
                        'title' => 'Products',
                        'items' => [
                            'Are colors exact? Colors may vary slightly due to screen settings.',
                            'Need help with size? Contact support for assistance.',
                        ],
                    ],
                ],
            ],
            'Terms & Conditions' => [
                'intro' => 'By using the Khanabadosh Fashion Canada website, you agree to the following terms.',
                'sections' => [
                    [
                        'title' => 'Use of Site',
                        'items' => [
                            'All content is provided for personal, non-commercial use.',
                            'Prices and availability may change without notice.',
                        ],
                    ],
                    [
                        'title' => 'Product Information',
                        'items' => [
                            'We aim for accurate product details and imagery.',
                            'Color variation may occur due to device settings.',
                        ],
                    ],
                    [
                        'title' => 'Liability',
                        'items' => [
                            'Khanabadosh Fashion Canada is not liable for indirect damages.',
                            'Our maximum liability is limited to the purchase value.',
                        ],
                    ],
                ],
            ],
            'Privacy Policy' => [
                'intro' => 'Khanabadosh Fashion Canada respects your privacy and keeps your data secure.',
                'sections' => [
                    [
                        'title' => 'Information We Collect',
                        'items' => [
                            'Name, contact details, and shipping address.',
                            'Order details and payment confirmation.',
                        ],
                    ],
                    [
                        'title' => 'How We Use It',
                        'items' => [
                            'To process orders and provide customer support.',
                            'To share updates and offers if you opt in.',
                        ],
                    ],
                    [
                        'title' => 'Data Sharing',
                        'items' => [
                            'We do not sell your data.',
                            'We share only with trusted service providers to fulfill orders.',
                        ],
                    ],
                ],
            ],
        ];

        $policy = $policyMap[$pageTitle] ?? $policyMap['Policies'];

        return view('policy', [
            'pageTitle' => $pageTitle,
            'intro' => $policy['intro'],
            'sections' => $policy['sections'],
        ]);
    }

    public function lookbook()
    {
        return view('lookbook', [
            'pageTitle' => 'Lookbook',
        ]);
    }

    private function buildProducts(int $results): array
    {
        $baseProducts = [
            ['name' => 'Ash Grey', 'slug' => 'ash-grey', 'price' => 4290],
            ['name' => 'Dove Brown', 'slug' => 'dove-brown', 'price' => 4290],
            ['name' => 'Charcoal Grey', 'slug' => 'charcoal-grey', 'price' => 4290],
            ['name' => 'Off White', 'slug' => 'off-white', 'price' => 4290],
            ['name' => 'Moss', 'slug' => 'moss', 'price' => 4290],
            ['name' => 'Navy Blue', 'slug' => 'navy-blue', 'price' => 4290],
            ['name' => 'Slate', 'slug' => 'slate', 'price' => 4290],
            ['name' => 'Forest Green', 'slug' => 'forest-green', 'price' => 4290],
            ['name' => 'Dark Grey', 'slug' => 'dark-grey', 'price' => 4290],
            ['name' => 'Steel', 'slug' => 'steel', 'price' => 4290],
            ['name' => 'Fawn Cream', 'slug' => 'fawn-cream', 'price' => 4290],
            ['name' => 'Dust Grey', 'slug' => 'dust-grey', 'price' => 4290],
            ['name' => 'Deep Navy', 'slug' => 'deep-navy', 'price' => 4290],
            ['name' => 'Black', 'slug' => 'black', 'price' => 4290],
            ['name' => 'Light Grey', 'slug' => 'light-grey', 'price' => 4290],
            ['name' => 'Stone', 'slug' => 'stone', 'price' => 4290],
            ['name' => 'Peanut Brown', 'slug' => 'peanut-brown', 'price' => 4290],
            ['name' => 'Olive', 'slug' => 'olive', 'price' => 4290],
        ];

        $products = [];
        if ($results > 0) {
            for ($i = 0; $i < $results; $i++) {
                $products[] = $baseProducts[$i % count($baseProducts)];
            }
        }

        return $products;
    }

    private function fallbackProductsForSlug(string $slug)
    {
        $products = Product::query()
            ->with('images')
            ->orderBy('id')
            ->get();

        if ($products->isNotEmpty()) {
            $filtered = $this->filterProductsBySlug($products, $slug);

            return $filtered->isNotEmpty() ? $filtered : $products;
        }

        return collect($this->buildProducts(18));
    }

    private function filterProductsBySlug($products, string $slug)
    {
        $tagMap = [
            'men-all' => ['Men'],
            'men-winter' => ['Men', 'Winter'],
            'women-all' => ['Women'],
            'women-sale' => ['Women'],
            'men-sale' => ['Men'],
            'winter25' => ['Winter'],
            '11-11-sale' => ['New'],
            '11-11-sale-men' => ['Men'],
            '11-11-sale-women' => ['Women'],
            '12-12-sale' => ['New'],
            '12-12-sale-men' => ['Men'],
            '12-12-sale-women' => ['Women'],
            'peridot' => ['Peridot'],
            'coral' => ['Coral'],
        ];

        $tags = $tagMap[$slug] ?? [];
        if (empty($tags)) {
            return collect();
        }

        return $products->filter(function ($product) use ($tags) {
            $productTags = collect(explode(',', $product->tags ?? ''))
                ->map(fn ($tag) => trim($tag))
                ->map(fn ($tag) => strtolower($tag))
                ->filter()
                ->values();

            foreach ($tags as $tag) {
                if (!$productTags->contains(strtolower($tag))) {
                    return false;
                }
            }

            return true;
        })->values();
    }

    private function resolveSidebarCollections(string $slug)
    {
        $collections = Collection::query()
            ->withCount('products')
            ->has('products')
            ->orderBy('title')
            ->get();

        if ($collections->isEmpty()) {
            return collect();
        }

        if (Str::contains($slug, 'men')) {
            return $this->collectionsByTag('Men')->whenEmpty(fn () => $collections);
        }

        if (Str::contains($slug, 'women')) {
            return $this->collectionsByTag('Women')->whenEmpty(fn () => $collections);
        }

        if (Str::contains($slug, 'winter')) {
            return $this->collectionsByTag('Winter')->whenEmpty(fn () => $collections);
        }

        if (Str::contains($slug, 'sale')) {
            $saleHandles = [
                '11-11-sale',
                '11-11-sale-men',
                '11-11-sale-women',
                '12-12-sale',
                '12-12-sale-men',
                '12-12-sale-women',
                'sale',
                'men-sale',
                'women-sale',
            ];
            $saleCollections = $collections->whereIn('handle', $saleHandles)->values();

            return $saleCollections->isNotEmpty() ? $saleCollections : $collections;
        }

        return $collections;
    }

    private function resolveSidebarGroups(): array
    {
        $collections = Collection::query()
            ->withCount('products')
            ->has('products')
            ->orderBy('title')
            ->get();

        $menCollections = $this->collectionsByTag('Men');
        if ($menCollections->isEmpty()) {
            $menCollections = $collections->filter(function ($collection) {
                return Str::contains(strtolower($collection->handle), 'men');
            });
        }

        $womenCollections = $this->collectionsByTag('Women');
        if ($womenCollections->isEmpty()) {
            $womenCollections = $collections->filter(function ($collection) {
                return Str::contains(strtolower($collection->handle), 'women');
            });
        }

        return [
            'men' => $menCollections->values(),
            'women' => $womenCollections->values(),
        ];
    }

    private function collectionsByTag(string $tag)
    {
        return Collection::query()
            ->withCount('products')
            ->whereHas('products', function ($query) use ($tag) {
                $query->whereRaw("(',' || lower(tags) || ',') LIKE ?", ['%,' . strtolower($tag) . ',%']);
            })
            ->orderBy('title')
            ->get();
    }

    private function productsByTags(array $tags, int $limit)
    {
        $query = Product::query()->with('images');
        foreach ($tags as $tag) {
            $query->whereRaw("(',' || lower(tags) || ',') LIKE ?", ['%,' . strtolower($tag) . ',%']);
        }

        return $query->orderByDesc('source_created_at')->take($limit)->get();
    }

    private function productsFromCollection(string $handle, int $limit, ?array $tags = null)
    {
        $collection = Collection::query()
            ->where('handle', $handle)
            ->with(['products.images'])
            ->first();

        if (!$collection) {
            return collect();
        }

        $products = $collection->products;
        if ($tags) {
            $products = $products->filter(function ($product) use ($tags) {
                return $this->productHasTags($product, $tags);
            });
        }

        return $products->sortByDesc('source_created_at')->take($limit)->values();
    }

    private function productHasTags($product, array $tags): bool
    {
        $productTags = collect(explode(',', $product->tags ?? ''))
            ->map(fn ($tag) => strtolower(trim($tag)))
            ->filter()
            ->values();

        foreach ($tags as $tag) {
            if (!$productTags->contains(strtolower($tag))) {
                return false;
            }
        }

        return true;
    }

    private function resolveProduct(string $slug): array
    {
        $map = [
            'dark-grey' => [
                'name' => 'Dark Grey',
                'sku' => 'KBMUS-DG-01',
                'price' => 4290,
                'description' => 'Premium winter fabric with a soft finish and structured drape, ideal for daily wear.',
                'details' => 'Unstitched • 4.0m fabric • Season: Winter • Care: Dry clean recommended.',
                'colors' => ['#2b2d31', '#6b7280', '#a3a3a3', '#111111'],
                'gallery' => [1, 2, 3, 4],
            ],
        ];

        if (isset($map[$slug])) {
            return $map[$slug];
        }

        $title = Str::of($slug)->replace('-', ' ')->title()->value();

        return [
            'name' => $title,
            'sku' => 'KB-' . strtoupper(Str::of($slug)->replace('-', '')->limit(6, '')->value()),
            'price' => 4290,
            'description' => 'Classic winter fabric designed for comfort and durability.',
            'details' => 'Unstitched • 4.0m fabric • Season: Winter • Care: Dry clean recommended.',
            'colors' => ['#2b2d31', '#6b7280', '#c0c0c0', '#f2f2f2'],
            'gallery' => [1, 2, 3, 4],
        ];
    }

    private function resolveCollectionTitle(string $slug): string
    {
        $map = [
            'men-all' => 'Men All',
            'women-all' => 'Women All',
            'winter25' => "Winter '25",
            'men-winter' => 'Men Winter',
            '11-11-sale' => '12.12 Sale',
            '11-11-sale-men' => '12.12 Sale Men',
            '11-11-sale-women' => '12.12 Sale Women',
            '12-12-sale' => '12.12 Sale',
            '12-12-sale-men' => '12.12 Sale Men',
            '12-12-sale-women' => '12.12 Sale Women',
            'all-season-men' => 'Men All Seasons',
            'dewan-e-khaas' => 'Dewan-e-Khaas Collection',
            'oxford' => 'Oxford Collection',
            'jasper' => 'Jasper Collection',
            'venus' => 'Venus Collection',
            'jupiter' => 'Jupiter Collection',
            'coral' => 'Coral Collection',
            'peridot' => 'Peridot Collection',
            'sang-e-marmar' => 'Sang e marmar Collection',
            'naltar' => 'Naltar Collection',
            'deosai' => 'Deosai Collection',
            'sale' => 'Sale Collection',
            'men-sale' => 'Men Sale',
            'women-sale' => 'Women Sale',
            'new-arrivals' => 'New Arrivals',
            'linen25' => 'Linen 25',
        ];

        if (isset($map[$slug])) {
            return $map[$slug];
        }

        return Str::of($slug)->replace('-', ' ')->title()->value();
    }

    private function resolveSaleAlias(string $slug): ?string
    {
        $aliases = [
            '12-12-sale' => '11-11-sale',
            '12-12-sale-men' => '11-11-sale-men',
            '12-12-sale-women' => '11-11-sale-women',
        ];

        return $aliases[$slug] ?? null;
    }
}
