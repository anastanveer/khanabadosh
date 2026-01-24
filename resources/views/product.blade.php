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
            <div class="kb-thumb-list" data-thumb-list>
              @if ($gallery->count())
                @foreach ($gallery as $thumb)
                  <button class="kb-thumb-btn {{ $loop->first ? 'is-active' : '' }}" type="button" data-thumb="{{ $thumb->src ?? '' }}" aria-label="View image">
                    <img class="kb-thumb" src="{{ $thumb->src ?? '' }}" alt="{{ $name }}">
                  </button>
                @endforeach
              @else
                <div class="kb-thumb" aria-hidden="true"></div>
                <div class="kb-thumb" aria-hidden="true"></div>
                <div class="kb-thumb" aria-hidden="true"></div>
              @endif
            </div>
            @if ($gallery->count())
              <div class="kb-main-frame" data-zoom-container>
                <img class="kb-main-image" src="{{ $gallery->first()->src ?? '' }}" alt="{{ $name }}" data-main-image>
              </div>
            @else
              <div class="kb-main-frame">
                <div class="kb-main-image"></div>
              </div>
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

@push('scripts')
  <script>
    (function () {
      var mainImage = document.querySelector('[data-main-image]');
      var thumbButtons = document.querySelectorAll('[data-thumb]');
      if (!mainImage || !thumbButtons.length) {
        return;
      }

      var frame = mainImage.closest('[data-zoom-container]');
      var scale = 1;
      var minScale = 1;
      var maxScale = 2.6;

      var resetZoom = function () {
        scale = 1;
        mainImage.style.transform = 'scale(1)';
        mainImage.style.transformOrigin = 'center center';
        if (frame) {
          frame.classList.remove('is-zoomed');
        }
      };

      thumbButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
          var src = btn.getAttribute('data-thumb');
          if (!src) {
            return;
          }
          mainImage.setAttribute('src', src);
          thumbButtons.forEach(function (item) {
            item.classList.remove('is-active');
          });
          btn.classList.add('is-active');
          resetZoom();
        });
      });

      if (!frame) {
        return;
      }

      frame.addEventListener('wheel', function (event) {
        event.preventDefault();
        var delta = event.deltaY || 0;
        if (delta > 0) {
          scale -= 0.12;
        } else {
          scale += 0.12;
        }
        scale = Math.max(minScale, Math.min(maxScale, scale));
        mainImage.style.transform = 'scale(' + scale.toFixed(2) + ')';
        frame.classList.toggle('is-zoomed', scale > 1.01);
      }, { passive: false });

      frame.addEventListener('mousemove', function (event) {
        if (scale <= 1.01) {
          mainImage.style.transformOrigin = 'center center';
          return;
        }
        var rect = frame.getBoundingClientRect();
        var x = ((event.clientX - rect.left) / rect.width) * 100;
        var y = ((event.clientY - rect.top) / rect.height) * 100;
        mainImage.style.transformOrigin = x.toFixed(2) + '% ' + y.toFixed(2) + '%';
      });

      frame.addEventListener('mouseleave', function () {
        if (scale <= 1.01) {
          resetZoom();
        }
      });
    })();
  </script>
@endpush
