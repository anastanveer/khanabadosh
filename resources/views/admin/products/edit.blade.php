@extends('layouts.app')

@section('content')
  <main class="kb-admin">
    <div class="container">
      <div class="kb-admin-header">
        <div>
          <div class="kb-page-title">Edit Product</div>
          <div class="kb-page-sub">Manage pricing, inventory, discounts, and variants.</div>
        </div>
        <div class="kb-admin-actions">
          <a class="kb-btn-outline" href="{{ route('admin.products.index') }}">Back to Products</a>
          <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">
            @csrf
            @method('DELETE')
            <button class="kb-btn-primary" type="submit">Delete Product</button>
          </form>
          <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button class="kb-btn-outline" type="submit">Logout</button>
          </form>
        </div>
      </div>

      @if (session('status'))
        <div class="alert alert-success mt-3">{{ session('status') }}</div>
      @endif

      @if ($errors->any())
        <div class="alert alert-danger mt-3">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @php
        $primaryImage = optional($product->images->sortBy('position')->first())->src;
        $defaultVariant = $product->variants->sortBy('position')->first();
        $displayPrice = \App\Support\CurrencyFormatter::convert($product->price);
        $displayCompare = \App\Support\CurrencyFormatter::convert($product->compare_at_price);
        $displayDiscount = $product->discount_type === 'fixed'
            ? \App\Support\CurrencyFormatter::convert($product->discount_value)
            : $product->discount_value;
      @endphp

      <form class="kb-admin-form" method="POST" action="{{ route('admin.products.update', $product) }}">
        @csrf
        @method('PUT')
        <div class="kb-form-grid">
          <div>
            <label>Title</label>
            <input type="text" name="title" value="{{ old('title', $product->title) }}" required>
          </div>
          <div>
            <label>Handle (Slug)</label>
            <input type="text" name="handle" value="{{ old('handle', $product->handle) }}">
          </div>
          <div>
            <label>SKU</label>
            <input type="text" name="sku" value="{{ old('sku', optional($defaultVariant)->sku) }}">
          </div>
          <div>
            <label>Price ({{ \App\Support\CurrencyFormatter::currency() }})</label>
            <input type="number" step="0.01" name="price" value="{{ old('price', $displayPrice) }}">
          </div>
          <div>
            <label>Compare at Price ({{ \App\Support\CurrencyFormatter::currency() }})</label>
            <input type="number" step="0.01" name="compare_at_price" value="{{ old('compare_at_price', $displayCompare) }}">
          </div>
          <div>
            <label>Inventory Quantity</label>
            <input type="number" name="inventory_quantity" value="{{ old('inventory_quantity', optional($defaultVariant)->inventory_quantity) }}">
          </div>
          <div>
            <label>Discount Type</label>
            <select name="discount_type">
              <option value="" {{ old('discount_type', $product->discount_type) ? '' : 'selected' }}>None</option>
              <option value="percent" {{ old('discount_type', $product->discount_type) === 'percent' ? 'selected' : '' }}>Percent (%)</option>
              <option value="fixed" {{ old('discount_type', $product->discount_type) === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
            </select>
          </div>
          <div>
            <label>Discount Value</label>
            <input type="number" step="0.01" name="discount_value" value="{{ old('discount_value', $displayDiscount) }}">
          </div>
          <div>
            <label>Discount Start</label>
            <input type="datetime-local" name="discount_starts_at" value="{{ old('discount_starts_at', optional($product->discount_starts_at)->format('Y-m-d\TH:i')) }}">
          </div>
          <div>
            <label>Discount End</label>
            <input type="datetime-local" name="discount_ends_at" value="{{ old('discount_ends_at', optional($product->discount_ends_at)->format('Y-m-d\TH:i')) }}">
          </div>
          <div>
            <label>Tags</label>
            <input type="text" name="tags" value="{{ old('tags', $product->tags) }}">
          </div>
          <div>
            <label>Product Type</label>
            <input type="text" name="product_type" value="{{ old('product_type', $product->product_type) }}">
          </div>
          <div>
            <label>Vendor</label>
            <input type="text" name="vendor" value="{{ old('vendor', $product->vendor) }}">
          </div>
          <div>
            <label>Primary Image URL</label>
            <input type="url" name="image_url" value="{{ old('image_url', $primaryImage) }}">
          </div>
          <div>
            <label>Collections</label>
            <select name="collections[]" multiple>
              @foreach ($collections as $collection)
                <option value="{{ $collection->id }}" {{ collect(old('collections', $product->collections->pluck('id')->all()))->contains($collection->id) ? 'selected' : '' }}>
                  {{ $collection->title }}
                </option>
              @endforeach
            </select>
          </div>
          <div>
            <label>Available</label>
            <select name="available">
              <option value="1" {{ old('available', $product->available ? '1' : '0') === '1' ? 'selected' : '' }}>Active</option>
              <option value="0" {{ old('available', $product->available ? '1' : '0') === '0' ? 'selected' : '' }}>Hidden</option>
            </select>
          </div>
        </div>

        <div class="mt-3">
          <label>Description</label>
          <textarea name="body_html" rows="5">{{ old('body_html', $product->body_html) }}</textarea>
        </div>

        <div class="d-flex gap-2 mt-3 flex-wrap">
          <button class="kb-btn-primary" type="submit">Save Changes</button>
          <a class="kb-btn-outline" href="{{ route('admin.products.index') }}">Cancel</a>
        </div>
      </form>

      <div class="kb-card mt-4">
        <div class="kb-card-title">Variants</div>
        <div class="kb-card-sub">Manage stock and pricing for each variant.</div>

        <div class="kb-admin-table mt-3">
          <table>
            <thead>
              <tr>
                <th>Title</th>
                <th>SKU</th>
                <th>Price</th>
                <th>Compare</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($product->variants as $variant)
                <tr>
                  @php($formId = 'variant-update-' . $variant->id)
                  <td>
                    <form id="{{ $formId }}" method="POST" action="{{ route('admin.variants.update', $variant) }}">
                      @csrf
                      @method('PUT')
                      <input name="title" value="{{ $variant->title }}">
                    </form>
                  </td>
                  <td><input form="{{ $formId }}" name="sku" value="{{ $variant->sku }}"></td>
                  <td><input form="{{ $formId }}" type="number" step="0.01" name="price" value="{{ \App\Support\CurrencyFormatter::convert($variant->price) }}"></td>
                  <td><input form="{{ $formId }}" type="number" step="0.01" name="compare_at_price" value="{{ \App\Support\CurrencyFormatter::convert($variant->compare_at_price) }}"></td>
                  <td><input form="{{ $formId }}" type="number" name="inventory_quantity" value="{{ $variant->inventory_quantity }}"></td>
                  <td>
                    <select form="{{ $formId }}" name="available">
                      <option value="1" {{ $variant->available ? 'selected' : '' }}>Active</option>
                      <option value="0" {{ !$variant->available ? 'selected' : '' }}>Hidden</option>
                    </select>
                  </td>
                  <td>
                    <div class="d-flex gap-2 flex-wrap">
                      <button class="kb-btn-outline" type="submit" form="{{ $formId }}">Update</button>
                      <form method="POST" action="{{ route('admin.variants.destroy', $variant) }}" onsubmit="return confirm('Delete this variant?')">
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

        <form class="mt-3" method="POST" action="{{ route('admin.products.variants.store', $product) }}">
          @csrf
          <div class="kb-form-grid">
            <div>
              <label>Variant Title</label>
              <input type="text" name="title" required>
            </div>
            <div>
              <label>SKU</label>
              <input type="text" name="sku">
            </div>
            <div>
              <label>Price ({{ \App\Support\CurrencyFormatter::currency() }})</label>
              <input type="number" step="0.01" name="price">
            </div>
            <div>
              <label>Compare at Price ({{ \App\Support\CurrencyFormatter::currency() }})</label>
              <input type="number" step="0.01" name="compare_at_price">
            </div>
            <div>
              <label>Inventory</label>
              <input type="number" name="inventory_quantity">
            </div>
            <div>
              <label>Status</label>
              <select name="available">
                <option value="1">Active</option>
                <option value="0">Hidden</option>
              </select>
            </div>
          </div>
          <button class="kb-btn-primary mt-3" type="submit">Add Variant</button>
        </form>
      </div>
    </div>
  </main>
@endsection
