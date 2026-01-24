@extends('layouts.app')

@section('content')
  <!-- ===== Hero Slider (Full Width) ===== -->
  <section class="kb-hero">
    <div id="heroCarousel" class="carousel slide carousel-fade kb-hero-slider" data-bs-ride="carousel" data-bs-interval="4000" data-bs-pause="false">
      <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
      </div>

      <div class="carousel-inner">
        <div class="carousel-item active">
          <div class="kb-hero-slide kb-ratio-hero">
            <picture>
              <source media="(max-width: 576px)" srcset="{{ asset('assets/hero/mobile/12.webp') }}">
              <img class="kb-hero-image" src="{{ asset('assets/hero/1.webp') }}" alt="Khanabadosh hero 1">
            </picture>
          </div>
        </div>
        <div class="carousel-item">
          <div class="kb-hero-slide kb-ratio-hero">
            <picture>
              <source media="(max-width: 576px)" srcset="{{ asset('assets/hero/mobile/13.webp') }}">
              <img class="kb-hero-image" src="{{ asset('assets/hero/2.webp') }}" alt="Khanabadosh hero 2">
            </picture>
          </div>
        </div>
        <div class="carousel-item">
          <div class="kb-hero-slide kb-ratio-hero">
            <picture>
              <source media="(max-width: 576px)" srcset="{{ asset('assets/hero/mobile/14.webp') }}">
              <img class="kb-hero-image" src="{{ asset('assets/hero/3.webp') }}" alt="Khanabadosh hero 3">
            </picture>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== Men / Women Category Blocks ===== -->
  <section class="py-5">
    <div class="container">
      <div class="row g-4 justify-content-center">
        <div class="col-12 col-md-6 col-lg-5">
          <div class="kb-cat">
            <div class="kb-cat-image">
              <img src="{{ asset('assets/categories/men.webp') }}" alt="Men collection">
            </div>
            <a class="kb-cat-btn" href="{{ route('collections.show', 'men-all') }}">Men</a>
          </div>
        </div>
        <div class="col-12 col-md-6 col-lg-5">
          <div class="kb-cat">
            <div class="kb-cat-image">
              <img src="{{ asset('assets/categories/woman.webp') }}" alt="Women collection">
            </div>
            <a class="kb-cat-btn" href="{{ route('collections.show', 'women-all') }}">Women</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== New Arrivals ===== -->
  <section class="pb-5">
    <div class="container text-center">
      <div class="kb-title">NEW ARRIVALS</div>
      <div class="kb-sub">Pick your Desired One Before it's getting sold!</div>

      <div class="kb-slider" data-slider>
        <button class="kb-slider-btn kb-slider-prev" type="button" aria-label="Previous">
          <i class="bi bi-chevron-left"></i>
        </button>
        <div class="kb-slider-track">
          @forelse ($newMenProducts as $product)
            @php
              $image = optional($product->images->sortBy('position')->first())->src;
              $altImage = optional($product->images->sortBy('position')->skip(1)->first())->src;
              $priceValue = $product->effectivePrice();
              $price = \App\Support\CurrencyFormatter::format($priceValue);
              $compareValue = null;
              if ($product->hasActiveDiscount() && $product->price) {
                  $compareValue = $product->price;
              } elseif ($product->compare_at_price && $product->price && $product->compare_at_price > $product->price) {
                  $compareValue = $product->compare_at_price;
              }
              $comparePrice = $compareValue ? \App\Support\CurrencyFormatter::format($compareValue) : null;
              $badge = \App\Support\ProductBadge::resolve($product, $compareValue);
            @endphp
            <div class="kb-slide">
              <div class="kb-fabric position-relative kb-product-card">
                @if ($badge)
                  <span class="{{ $badge['class'] }}">{{ $badge['label'] }}</span>
                @endif
                <div class="kb-product-media">
                  @if ($image)
                    <img class="kb-product-img kb-product-img--main kb-ratio-card" src="{{ $image }}" alt="{{ $product->title }}">
                  @else
                    <div class="kb-ph kb-ratio-card" data-size="FABRIC"></div>
                  @endif
                  @if ($altImage)
                    <img class="kb-product-img kb-product-img--alt kb-ratio-card" src="{{ $altImage }}" alt="{{ $product->title }}">
                  @endif
                  <div class="kb-product-actions">
                    <button class="kb-action-btn js-cart" type="button" data-product-id="{{ $product->handle }}" aria-label="Add to cart">
                      <i class="bi bi-bag"></i>
                    </button>
                    <button class="kb-action-btn js-wishlist" type="button" data-product-id="{{ $product->handle }}" aria-label="Wishlist">
                      <i class="bi bi-heart"></i>
                    </button>
                    <button class="kb-action-btn js-zoom" type="button"
                      data-title="{{ $product->title }}"
                      data-price="{{ $price }}"
                      data-image="{{ $image }}"
                      data-description="{{ \Illuminate\Support\Str::limit(strip_tags($product->body_html ?? ''), 220) }}"
                      data-url="{{ route('products.show', ['collection' => 'men-all', 'slug' => $product->handle]) }}"
                      data-product-id="{{ $product->handle }}"
                      aria-label="Quick view">
                      <i class="bi bi-zoom-in"></i>
                    </button>
                  </div>
                </div>
                <a class="kb-card-link" href="{{ route('products.show', ['collection' => 'men-all', 'slug' => $product->handle]) }}" aria-label="View {{ $product->title }}"></a>
                <div class="name">{{ $product->title }}</div>
                <div class="price">
                  {{ $price }}
                  @if ($comparePrice)
                    <span class="kb-compare-price">{{ $comparePrice }}</span>
                  @endif
                </div>
              </div>
            </div>
          @empty
            <div class="kb-slide">
              <div class="kb-fabric position-relative">
                <span class="kb-badge kb-badge--new">New</span>
                <div class="kb-ph kb-ratio-card" data-size="FABRIC"></div>
                <div class="name">New Arrival</div>
                <div class="price">{{ \App\Support\CurrencyFormatter::format(0) }}</div>
              </div>
            </div>
          @endforelse
        </div>
        <button class="kb-slider-btn kb-slider-next" type="button" aria-label="Next">
          <i class="bi bi-chevron-right"></i>
        </button>
      </div>

      <div class="mt-4">
        <a class="btn btn-dark px-4 py-2 rounded-3 text-uppercase fw-semibold" style="letter-spacing:.08em;font-size:.8rem;" href="{{ route('collections.show', ['slug' => 'men-all']) }}">
          Explore All
        </a>
      </div>
    </div>
  </section>

  <!-- ===== Choose the Category + Product Slider ===== -->
  <section class="pb-5">
    <div class="container text-center">
      <div class="kb-title">Choose the Category</div>
      <div class="kb-sub">Pick your desired category</div>

      <div class="kb-tabs">
        <button class="kb-tab active" type="button" data-tab-target="women">Women</button>
        <button class="kb-tab" type="button" data-tab-target="men">Men</button>
      </div>

      <div class="kb-tab-panel" data-tab-panel="women">
        <div class="kb-slider" data-slider>
          <button class="kb-slider-btn kb-slider-prev" type="button" aria-label="Previous">
            <i class="bi bi-chevron-left"></i>
          </button>
          <div class="kb-slider-track">
            @foreach ($womenProducts as $product)
              @php
                $image = optional($product->images->sortBy('position')->first())->src;
                $altImage = optional($product->images->sortBy('position')->skip(1)->first())->src;
              $priceValue = $product->effectivePrice();
              $price = \App\Support\CurrencyFormatter::format($priceValue);
                $compareValue = null;
                if ($product->hasActiveDiscount() && $product->price) {
                    $compareValue = $product->price;
                } elseif ($product->compare_at_price && $product->price && $product->compare_at_price > $product->price) {
                    $compareValue = $product->compare_at_price;
                }
              $comparePrice = $compareValue ? \App\Support\CurrencyFormatter::format($compareValue) : null;
              $badge = \App\Support\ProductBadge::resolve($product, $compareValue);
            @endphp
              <div class="kb-slide">
                <div class="kb-product position-relative kb-product-card">
                  @if ($badge)
                    <span class="{{ $badge['class'] }}">{{ $badge['label'] }}</span>
                  @endif
                  <div class="kb-product-media">
                    @if ($image)
                      <img class="kb-product-img kb-product-img--main kb-ratio-tall" src="{{ $image }}" alt="{{ $product->title }}">
                    @else
                      <div class="kb-ph kb-ratio-tall" data-size="PRODUCT"></div>
                    @endif
                    @if ($altImage)
                      <img class="kb-product-img kb-product-img--alt kb-ratio-tall" src="{{ $altImage }}" alt="{{ $product->title }}">
                    @endif
                    <div class="kb-product-actions">
                      <button class="kb-action-btn js-cart" type="button" data-product-id="{{ $product->handle }}" aria-label="Add to cart">
                        <i class="bi bi-bag"></i>
                      </button>
                      <button class="kb-action-btn js-wishlist" type="button" data-product-id="{{ $product->handle }}" aria-label="Wishlist">
                        <i class="bi bi-heart"></i>
                      </button>
                    <button class="kb-action-btn js-zoom" type="button"
                      data-title="{{ $product->title }}"
                      data-price="{{ $price }}"
                      data-image="{{ $image }}"
                      data-description="{{ \Illuminate\Support\Str::limit(strip_tags($product->body_html ?? ''), 220) }}"
                      data-url="{{ route('products.show', ['collection' => 'women-all', 'slug' => $product->handle]) }}"
                      data-product-id="{{ $product->handle }}"
                      aria-label="Quick view">
                      <i class="bi bi-zoom-in"></i>
                    </button>
                    </div>
                  </div>
                  <a class="kb-card-link" href="{{ route('products.show', ['collection' => 'women-all', 'slug' => $product->handle]) }}" aria-label="View {{ $product->title }}"></a>
                  <div class="code">{{ $product->handle }}</div>
                  <div class="now">
                    {{ $price }}
                    @if ($comparePrice)
                      <span class="kb-compare-price">{{ $comparePrice }}</span>
                    @endif
                  </div>
                </div>
              </div>
            @endforeach
          </div>
          <button class="kb-slider-btn kb-slider-next" type="button" aria-label="Next">
            <i class="bi bi-chevron-right"></i>
          </button>
        </div>
      </div>

      <div class="kb-tab-panel d-none" data-tab-panel="men">
        <div class="kb-slider" data-slider>
          <button class="kb-slider-btn kb-slider-prev" type="button" aria-label="Previous">
            <i class="bi bi-chevron-left"></i>
          </button>
          <div class="kb-slider-track">
            @foreach ($menProducts as $product)
              @php
                $image = optional($product->images->sortBy('position')->first())->src;
                $altImage = optional($product->images->sortBy('position')->skip(1)->first())->src;
              $priceValue = $product->effectivePrice();
              $price = \App\Support\CurrencyFormatter::format($priceValue);
                $compareValue = null;
                if ($product->hasActiveDiscount() && $product->price) {
                    $compareValue = $product->price;
                } elseif ($product->compare_at_price && $product->price && $product->compare_at_price > $product->price) {
                    $compareValue = $product->compare_at_price;
                }
              $comparePrice = $compareValue ? \App\Support\CurrencyFormatter::format($compareValue) : null;
              $badge = \App\Support\ProductBadge::resolve($product, $compareValue);
            @endphp
              <div class="kb-slide">
                <div class="kb-product position-relative kb-product-card">
                  @if ($badge)
                    <span class="{{ $badge['class'] }}">{{ $badge['label'] }}</span>
                  @endif
                  <div class="kb-product-media">
                    @if ($image)
                      <img class="kb-product-img kb-product-img--main kb-ratio-tall" src="{{ $image }}" alt="{{ $product->title }}">
                    @else
                      <div class="kb-ph kb-ratio-tall" data-size="PRODUCT"></div>
                    @endif
                    @if ($altImage)
                      <img class="kb-product-img kb-product-img--alt kb-ratio-tall" src="{{ $altImage }}" alt="{{ $product->title }}">
                    @endif
                    <div class="kb-product-actions">
                      <button class="kb-action-btn js-cart" type="button" data-product-id="{{ $product->handle }}" aria-label="Add to cart">
                        <i class="bi bi-bag"></i>
                      </button>
                      <button class="kb-action-btn js-wishlist" type="button" data-product-id="{{ $product->handle }}" aria-label="Wishlist">
                        <i class="bi bi-heart"></i>
                      </button>
                    <button class="kb-action-btn js-zoom" type="button"
                      data-title="{{ $product->title }}"
                      data-price="{{ $price }}"
                      data-image="{{ $image }}"
                      data-description="{{ \Illuminate\Support\Str::limit(strip_tags($product->body_html ?? ''), 220) }}"
                      data-url="{{ route('products.show', ['collection' => 'men-all', 'slug' => $product->handle]) }}"
                      data-product-id="{{ $product->handle }}"
                      aria-label="Quick view">
                      <i class="bi bi-zoom-in"></i>
                    </button>
                    </div>
                  </div>
                  <a class="kb-card-link" href="{{ route('products.show', ['collection' => 'men-all', 'slug' => $product->handle]) }}" aria-label="View {{ $product->title }}"></a>
                  <div class="code">{{ $product->handle }}</div>
                  <div class="now">
                    {{ $price }}
                    @if ($comparePrice)
                      <span class="kb-compare-price">{{ $comparePrice }}</span>
                    @endif
                  </div>
                </div>
              </div>
            @endforeach
          </div>
          <button class="kb-slider-btn kb-slider-next" type="button" aria-label="Next">
            <i class="bi bi-chevron-right"></i>
          </button>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== Black Info Strip ===== -->
  <section class="kb-info">
    <div class="container">
      <div class="row gy-3">
        <div class="col-12 col-md-4">
          <div class="item">
            <i class="bi bi-truck"></i>
            <div>
              <div class="t">FREE SHIPPING</div>
              <div class="d">Free shipping across Pakistan</div>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="item">
            <i class="bi bi-shield-check"></i>
            <div>
              <div class="t">MONEY GUARANTEE</div>
              <div class="d">10 days money back guarantee</div>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="item">
            <i class="bi bi-headset"></i>
            <div>
              <div class="t">ONLINE SUPPORT</div>
              <div class="d">We support online 24/7 on day</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
