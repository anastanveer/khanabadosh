@extends('layouts.app')

@section('content')
  <main class="kb-collection">
    <div class="container">
      <div class="kb-page-title">Search</div>
      <div class="kb-page-sub">Find products, colors, or collections in Khanabadosh.</div>

      <form class="kb-search-bar" action="{{ route('search') }}" method="GET">
        <input type="search" name="q" value="{{ $query }}" placeholder="Search by name, tag, or collection" autofocus>
        <button class="kb-btn-primary" type="submit">Search</button>
      </form>

      @if ($query === '')
        <div class="kb-empty-state">
          <div class="kb-empty-title">Start typing to explore the catalog.</div>
          <div class="kb-empty-sub">Try "Winter", "Men", or "Oxford".</div>
        </div>
      @else
        <div class="kb-page-sub mt-3">{{ $results->count() }} results for "{{ $query }}"</div>

        <div class="row g-4 mt-2">
          @forelse ($results as $product)
            @php
              $image = optional($product->images->sortBy('position')->first())->src;
              $altImage = optional($product->images->sortBy('position')->skip(1)->first())->src;
              $priceValue = $product->effectivePrice();
              $price = \App\Support\CurrencyFormatter::format($priceValue);
              $badge = \App\Support\ProductBadge::resolve($product);
            @endphp
            <div class="col-6 col-md-4 col-lg-3">
              <div class="kb-product-card position-relative">
                @if ($badge)
                  <span class="{{ $badge['class'] }}">{{ $badge['label'] }}</span>
                @endif
                <div class="kb-product-media">
                  <a class="text-decoration-none text-dark" href="{{ route('products.show', ['collection' => 'men-all', 'slug' => $product->handle]) }}">
                    @if ($image)
                      <img class="kb-product-img kb-product-img--main kb-ratio-tall" src="{{ $image }}" alt="{{ $product->title }}">
                    @else
                      <div class="kb-ph kb-ratio-tall no-label"></div>
                    @endif
                    @if ($altImage)
                      <img class="kb-product-img kb-product-img--alt kb-ratio-tall" src="{{ $altImage }}" alt="{{ $product->title }}">
                    @endif
                  </a>
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
                <div class="name">{{ $product->title }}</div>
                <div class="price">{{ $price }}</div>
              </div>
            </div>
          @empty
            <div class="kb-empty-state">
              <div class="kb-empty-title">No results found.</div>
              <div class="kb-empty-sub">Try a different keyword or browse collections.</div>
            </div>
          @endforelse
        </div>
      @endif
    </div>
  </main>
@endsection
