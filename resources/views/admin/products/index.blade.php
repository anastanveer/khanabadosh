@extends('layouts.app')

@section('content')
  <main class="kb-admin">
    <div class="container">
      <div class="kb-admin-header">
        <div>
          <div class="kb-page-title">Manage Products</div>
          <div class="kb-page-sub">Update pricing, stock, discounts, and catalog visibility.</div>
        </div>
        <div class="kb-admin-actions">
          <a class="kb-btn-outline" href="{{ route('admin.index') }}">Back to Dashboard</a>
          <a class="kb-btn-primary" href="{{ route('admin.products.create') }}">Add Product</a>
          <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button class="kb-btn-outline" type="submit">Logout</button>
          </form>
        </div>
      </div>

      @if (session('status'))
        <div class="alert alert-success mt-3">{{ session('status') }}</div>
      @endif

      <form class="kb-admin-search" method="GET" action="{{ route('admin.products.index') }}">
        <input type="text" name="q" placeholder="Search by title, handle, or tags" value="{{ $search }}">
        <button class="kb-btn-outline" type="submit">Search</button>
      </form>

      <div class="kb-admin-table">
        <table>
          <thead>
            <tr>
              <th>Product</th>
              <th>Price</th>
              <th>Stock</th>
              <th>Discount</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($products as $product)
              @php
                $image = optional($product->images->sortBy('position')->first())->src;
                $stock = $product->variants->sum('inventory_quantity');
                $priceValue = $product->effectivePrice();
                $price = \App\Support\CurrencyFormatter::format($priceValue);
                $comparePrice = $product->hasActiveDiscount() && $product->price
                    ? \App\Support\CurrencyFormatter::format($product->price)
                    : null;
              @endphp
              <tr>
                <td>
                  <div class="d-flex align-items-center gap-3">
                    @if ($image)
                      <img class="kb-admin-thumb-sm" src="{{ $image }}" alt="{{ $product->title }}">
                    @else
                      <div class="kb-admin-thumb-sm"></div>
                    @endif
                    <div>
                      <div class="fw-semibold">{{ $product->title }}</div>
                      <div class="text-muted" style="font-size:.75rem;">{{ $product->handle }}</div>
                    </div>
                  </div>
                </td>
                <td>
                  <div>{{ $price }}</div>
                  @if ($comparePrice)
                    <div class="kb-compare-price">{{ $comparePrice }}</div>
                  @endif
                </td>
                <td>
                  <span class="kb-pill {{ $stock > 0 ? '' : 'kb-pill-muted' }}">{{ $stock }}</span>
                </td>
                <td>
                  @if ($product->hasActiveDiscount())
                    @php
                      $discountLabel = $product->discount_type === 'percent'
                          ? $product->discount_value . '%'
                          : \App\Support\CurrencyFormatter::format($product->discount_value);
                    @endphp
                    <span class="kb-pill">{{ $discountLabel }}</span>
                  @else
                    <span class="kb-pill kb-pill-muted">None</span>
                  @endif
                </td>
                <td>
                  @if ($product->available)
                    <span class="kb-pill">Active</span>
                  @else
                    <span class="kb-pill kb-pill-muted">Hidden</span>
                  @endif
                </td>
                <td>
                  <div class="d-flex gap-2 flex-wrap">
                    <a class="kb-btn-outline" href="{{ route('admin.products.edit', $product) }}">Edit</a>
                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">
                      @csrf
                      @method('DELETE')
                      <button class="kb-btn-primary" type="submit">Delete</button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </main>
@endsection
