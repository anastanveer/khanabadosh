@extends('layouts.app')

@section('content')
  <main class="kb-collection">
    <div class="container">
      @php
        $priceValues = collect($products)->map(function ($product) {
            $isModel = $product instanceof \App\Models\Product;
            $basePrice = $isModel ? $product->effectivePrice() : ($product['price'] ?? null);
            if (!is_numeric($basePrice)) {
                $basePrice = (float) preg_replace('/[^\d.]/', '', (string) $basePrice);
            }
            return \App\Support\CurrencyFormatter::convert($basePrice);
        })->filter(fn ($price) => $price !== null)->values();
        $minPrice = (int) floor($priceValues->min() ?? 0);
        $maxPrice = (int) ceil($priceValues->max() ?? 10000);
        if ($maxPrice < $minPrice) {
            $maxPrice = $minPrice;
        }
        $range = $maxPrice - $minPrice;
        if ($range < 10) {
            $pad = max((int) round($maxPrice * 0.1), 100);
            $minPrice = max(0, $minPrice - $pad);
            $maxPrice = $maxPrice + $pad;
            $range = $maxPrice - $minPrice;
        }
        if ($range <= 0) {
            $maxPrice = $minPrice + 1;
            $range = 1;
        }
        $priceStep = max((int) round($range / 200), 1);
      @endphp
      <div class="row g-4">
        <aside class="col-12 col-lg-3 kb-shop-sidebar" id="kb-mobile-filters" data-mobile-filter-panel>
          <div class="kb-sidebar">
            <div class="kb-filter">
              <div class="kb-filter-title">Categories</div>
              @php
                $menOpen = \Illuminate\Support\Str::contains($collectionSlug ?? '', 'men');
                $womenOpen = \Illuminate\Support\Str::contains($collectionSlug ?? '', 'women');
              @endphp
              <button class="kb-filter-toggle {{ $menOpen ? 'is-open' : '' }}" type="button" data-filter-toggle="men" aria-expanded="{{ $menOpen ? 'true' : 'false' }}">
                <span>Men</span>
                <span class="kb-toggle-icon">+</span>
              </button>
              <ul class="kb-filter-list kb-collapse {{ $menOpen ? 'is-open' : '' }}" data-filter-panel="men">
                <li>
                  <a href="{{ route('collections.show', ['slug' => 'men-all']) }}">
                    <span>All Men</span>
                  </a>
                </li>
                @foreach ($sidebarMen ?? [] as $collection)
                  @continue($collection->handle === 'men-all')
                  <li>
                    <a href="{{ route('collections.show', ['slug' => $collection->handle]) }}">
                      <span>{{ $collection->title }}</span>
                      <span class="kb-count">{{ $collection->products_count ?? '' }}</span>
                    </a>
                  </li>
                @endforeach
              </ul>

              <button class="kb-filter-toggle {{ $womenOpen ? 'is-open' : '' }}" type="button" data-filter-toggle="women" aria-expanded="{{ $womenOpen ? 'true' : 'false' }}">
                <span>Women</span>
                <span class="kb-toggle-icon">+</span>
              </button>
              <ul class="kb-filter-list kb-collapse {{ $womenOpen ? 'is-open' : '' }}" data-filter-panel="women">
                <li>
                  <a href="{{ route('collections.show', ['slug' => 'women-all']) }}">
                    <span>All Women</span>
                  </a>
                </li>
                @foreach ($sidebarWomen ?? [] as $collection)
                  @continue($collection->handle === 'women-all')
                  <li>
                    <a href="{{ route('collections.show', ['slug' => $collection->handle]) }}">
                      <span>{{ $collection->title }}</span>
                      <span class="kb-count">{{ $collection->products_count ?? '' }}</span>
                    </a>
                  </li>
                @endforeach
              </ul>
            </div>

            <div class="kb-filter">
              <div class="kb-filter-title">Availability</div>
              <label class="kb-check">
                <input type="checkbox" checked>
                In stock ({{ $inStockCount ?? $results }})
              </label>
            </div>

            <div class="kb-filter">
              <div class="kb-filter-title">Price</div>
              <div class="kb-price-filter" data-price-symbol="{{ \App\Support\CurrencyFormatter::symbol() }}">
                <div class="kb-range-stack">
                  <input class="kb-range kb-range--min" type="range" min="{{ $minPrice }}" max="{{ $maxPrice }}" step="{{ $priceStep }}" value="{{ $minPrice }}" data-price-min-range>
                  <input class="kb-range kb-range--max" type="range" min="{{ $minPrice }}" max="{{ $maxPrice }}" step="{{ $priceStep }}" value="{{ $maxPrice }}" data-price-max-range>
                </div>
                <div class="kb-range-values">
                  <span data-price-min>{{ \App\Support\CurrencyFormatter::symbol() }} {{ number_format($minPrice, 2) }}</span>
                  <span data-price-selected>{{ \App\Support\CurrencyFormatter::symbol() }} {{ number_format($minPrice, 2) }} - {{ \App\Support\CurrencyFormatter::symbol() }} {{ number_format($maxPrice, 2) }}</span>
                  <span data-price-max>{{ \App\Support\CurrencyFormatter::symbol() }} {{ number_format($maxPrice, 2) }}</span>
                </div>
                <div class="kb-price-feedback" data-price-feedback></div>
                <button class="kb-btn-outline kb-price-reset" type="button" data-price-reset>Reset Filter</button>
              </div>
            </div>

            <div class="kb-filter">
              <div class="kb-filter-title">Popular Product</div>
              @if (!empty($popularProducts) && $popularProducts->count())
                @foreach ($popularProducts as $popular)
                  @php
                    $image = optional($popular->images->sortBy('position')->first())->src;
                    $price = \App\Support\CurrencyFormatter::format($popular->effectivePrice());
                  @endphp
                  <div class="kb-mini">
                    @if ($image)
                      <img class="kb-mini-thumb" src="{{ $image }}" alt="{{ $popular->title }}">
                    @else
                      <div class="kb-mini-thumb"></div>
                    @endif
                    <div>
                      <div class="kb-mini-title">{{ $popular->title }}</div>
                      <div class="kb-mini-price">{{ $price }}</div>
                    </div>
                  </div>
                @endforeach
              @else
                  <div class="kb-mini">
                    <div class="kb-mini-thumb"></div>
                    <div>
                      <div class="kb-mini-title">Oxford</div>
                      <div class="kb-mini-price">{{ \App\Support\CurrencyFormatter::format(4290) }}</div>
                    </div>
                  </div>
              @endif
            </div>
          </div>
        </aside>

        <section class="col-12 col-lg-9 kb-shop-products">
          <div class="kb-shop-toolbar">
            <div class="kb-shop-heading">
              <div class="kb-page-title">{{ $pageTitle }}</div>
              <div class="kb-view mt-2">
                <button class="kb-view-btn active" type="button" data-view-btn="grid" aria-label="Grid view">
                  <i class="bi bi-grid-3x3-gap"></i>
                </button>
                <button class="kb-view-btn" type="button" data-view-btn="list" aria-label="List view">
                  <i class="bi bi-grid"></i>
                </button>
              </div>
            </div>

            <div class="kb-shop-actions">
              <button class="kb-filter-btn" type="button" data-mobile-filter-toggle aria-controls="kb-mobile-filters" aria-expanded="false">
                <i class="bi bi-sliders2"></i>
                <span>Filters</span>
              </button>
              <div class="kb-page-sub mb-0">Showing <span data-results-count>{{ $results }}</span> Results</div>
              <div class="kb-sort" data-sort>
                <button class="kb-sort-toggle" type="button" data-sort-toggle aria-expanded="false">
                  <span data-sort-label>Best Selling</span>
                  <i class="bi bi-chevron-down"></i>
                </button>
                <div class="kb-sort-menu" role="listbox" aria-label="Sort products">
                  <button class="kb-sort-option is-active" type="button" data-sort-option="best">Best Selling</button>
                  <button class="kb-sort-option" type="button" data-sort-option="new">New Arrivals</button>
                  <button class="kb-sort-option" type="button" data-sort-option="price-asc">Price: Low to High</button>
                  <button class="kb-sort-option" type="button" data-sort-option="price-desc">Price: High to Low</button>
                  <button class="kb-sort-option" type="button" data-sort-option="title">Name: A-Z</button>
                </div>
              </div>
            </div>
          </div>

          <div class="kb-filter-alert d-none" data-filter-alert></div>

          <div class="row g-4 mt-1 kb-products-grid" data-product-grid>
            @foreach ($products as $product)
              @php
                $isModel = $product instanceof \App\Models\Product;
                $productName = $isModel ? $product->title : $product['name'];
                $productSlug = $isModel ? $product->handle : ($product['slug'] ?? \Illuminate\Support\Str::slug($product['name']));
                $productPriceValue = $isModel ? $product->effectivePrice() : $product['price'];
                if (!is_numeric($productPriceValue)) {
                    $productPriceValue = (float) preg_replace('/[^\d.]/', '', (string) $productPriceValue);
                }
                $productPrice = \App\Support\CurrencyFormatter::format($productPriceValue);
                $compareValue = null;
                if ($isModel && $product->hasActiveDiscount() && $product->price) {
                    $compareValue = $product->price;
                } elseif ($isModel && $product->compare_at_price && $product->price && $product->compare_at_price > $product->price) {
                    $compareValue = $product->compare_at_price;
                }
                $comparePrice = $compareValue ? \App\Support\CurrencyFormatter::format($compareValue) : null;
                $productImage = $isModel ? optional($product->images->sortBy('position')->first())->src : null;
                $productAltImage = $isModel ? optional($product->images->sortBy('position')->skip(1)->first())->src : null;
                $productDesc = $isModel ? strip_tags($product->body_html ?? '') : 'Classic winter fabric.';
                $filterPrice = \App\Support\CurrencyFormatter::convert($productPriceValue) ?? 0;
                $badge = \App\Support\ProductBadge::resolve($product, $compareValue);
              @endphp
              @php
                $createdTimestamp = 0;
                if ($isModel && $product->source_created_at) {
                    $createdTimestamp = $product->source_created_at->timestamp;
                } elseif ($isModel && $product->created_at) {
                    $createdTimestamp = $product->created_at->timestamp;
                } elseif (is_array($product) && !empty($product['created_at'])) {
                    $createdTimestamp = (int) strtotime($product['created_at']);
                }
              @endphp
              <div class="col-6 col-md-4 kb-product-col" data-price-value="{{ $filterPrice }}" data-title="{{ \Illuminate\Support\Str::of($productName)->lower() }}" data-created="{{ $createdTimestamp }}">
                <div class="kb-product-card position-relative">
                  @if ($badge)
                    <span class="{{ $badge['class'] }}">{{ $badge['label'] }}</span>
                  @endif
                  <div class="kb-product-media">
                    <a class="text-decoration-none text-dark" href="{{ route('products.show', ['collection' => $collectionSlug ?? 'men-all', 'slug' => $productSlug]) }}">
                      @if ($productImage)
                        <img class="kb-product-img kb-product-img--main kb-ratio-tall" src="{{ $productImage }}" alt="{{ $productName }}">
                      @else
                        <div class="kb-ph kb-ratio-tall no-label"></div>
                      @endif
                      @if ($productAltImage)
                        <img class="kb-product-img kb-product-img--alt kb-ratio-tall" src="{{ $productAltImage }}" alt="{{ $productName }}">
                      @endif
                    </a>
                    <div class="kb-product-actions">
                      <button class="kb-action-btn js-cart" type="button" data-product-id="{{ $productSlug }}" aria-label="Add to cart">
                        <i class="bi bi-bag"></i>
                      </button>
                      <button class="kb-action-btn js-wishlist" type="button" data-product-id="{{ $productSlug }}" aria-label="Wishlist">
                        <i class="bi bi-heart"></i>
                      </button>
                    <button class="kb-action-btn js-zoom" type="button"
                      data-title="{{ $productName }}"
                      data-price="{{ $productPrice }}"
                      data-image="{{ $productImage }}"
                      data-description="{{ \Illuminate\Support\Str::limit($productDesc, 220) }}"
                      data-url="{{ route('products.show', ['collection' => $collectionSlug ?? 'men-all', 'slug' => $productSlug]) }}"
                      data-product-id="{{ $productSlug }}"
                      aria-label="Quick view">
                      <i class="bi bi-zoom-in"></i>
                    </button>
                    </div>
                  </div>
                  <div class="name">{{ $productName }}</div>
                  <div class="price">
                    {{ $productPrice }}
                    @if ($comparePrice)
                      <span class="kb-compare-price">{{ $comparePrice }}</span>
                    @endif
                  </div>
                  <div class="kb-product-desc">{{ \Illuminate\Support\Str::limit($productDesc, 140) }}</div>
                </div>
              </div>
            @endforeach
          </div>
        </section>
      </div>
    </div>
  </main>
@endsection
