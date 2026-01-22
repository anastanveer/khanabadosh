@extends('layouts.app')

@section('content')
  @php
    $isModel = $product instanceof \App\Models\Product;
    $name = $isModel ? $product->title : $product['name'];
    $sku = $isModel ? optional($product->variants->first())->sku : $product['sku'];
    $priceValue = $isModel ? $product->effectivePrice() : $product['price'];
    $price = $priceValue ? \App\Support\CurrencyFormatter::format($priceValue) : null;
    $compareValue = null;
    if ($isModel && $product->hasActiveDiscount() && $product->price) {
        $compareValue = $product->price;
    } elseif ($isModel && $product->compare_at_price && $product->price && $product->compare_at_price > $product->price) {
        $compareValue = $product->compare_at_price;
    }
    $comparePrice = $compareValue ? \App\Support\CurrencyFormatter::format($compareValue) : null;
    $gallery = $isModel ? $product->images->sortBy('position') : collect($product['gallery']);
    $description = $isModel ? $product->body_html : e($product['description']);
    $details = $isModel ? ($product->product_type ?: 'Unstitched • Winter Collection') : $product['details'];
  @endphp

  <main class="kb-product-detail">
    <div class="container">
      <div class="kb-breadcrumb">
        <a href="{{ route('home') }}">Home</a> /
        <a href="{{ route('collections.show', ['slug' => $collectionSlug]) }}">{{ $collectionTitle }}</a> /
        <span>{{ $name }}</span>
      </div>

      <div class="row g-4">
        <div class="col-12 col-lg-6">
          <div class="kb-product-hero">
            <div class="kb-thumb-list">
              @if ($gallery->count())
                @foreach ($gallery as $thumb)
                  <img class="kb-thumb" src="{{ $thumb->src ?? '' }}" alt="{{ $name }}">
                @endforeach
              @else
                <div class="kb-thumb" aria-hidden="true"></div>
                <div class="kb-thumb" aria-hidden="true"></div>
                <div class="kb-thumb" aria-hidden="true"></div>
              @endif
            </div>
            @if ($gallery->count())
              <img class="kb-main-image" src="{{ $gallery->first()->src ?? '' }}" alt="{{ $name }}">
            @else
              <div class="kb-main-image"></div>
            @endif
          </div>
        </div>

        <div class="col-12 col-lg-6">
          <div class="kb-detail-title">{{ $name }}</div>
          <div class="kb-sku">SKU: {{ $sku ?? 'N/A' }}</div>
          @if ($price)
            <div class="kb-price">
              {{ $price }}
              @if ($comparePrice)
                <span class="kb-compare-price">{{ $comparePrice }}</span>
              @endif
            </div>
          @endif

          <div class="mt-3">
            <div class="kb-filter-title">Color</div>
            <div class="kb-color-row">
              @php
                $swatches = $isModel ? ['#2b2d31', '#6b7280', '#a3a3a3', '#111111'] : $product['colors'];
              @endphp
              @foreach ($swatches as $index => $color)
                <span class="kb-swatch {{ $index === 0 ? 'active' : '' }}" style="background: {{ $color }};"></span>
              @endforeach
            </div>
          </div>

          <div class="mt-3">
            <div class="kb-filter-title">Quantity</div>
            <div class="kb-qty">
              <button type="button" aria-label="Decrease quantity">-</button>
              <input type="text" value="1" aria-label="Quantity">
              <button type="button" aria-label="Increase quantity">+</button>
            </div>
          </div>

          <div class="kb-action">
            <button class="kb-btn-primary" type="button">Add to Cart</button>
            <button class="kb-btn-outline" type="button">Buy It Now</button>
          </div>

          <div class="kb-info-card">
            <strong>Description</strong><br>
            @if ($isModel)
              {!! $description !!}
            @else
              {{ $description }}
            @endif
          </div>
          <div class="kb-info-card">
            <strong>Details</strong><br>
            {{ $details }}
          </div>
        </div>
      </div>
    </div>
  </main>
@endsection
