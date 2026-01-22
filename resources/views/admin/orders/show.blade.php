@extends('layouts.app')

@section('content')
  <main class="kb-admin kb-order-page">
    <div class="container">
      <div class="kb-admin-header">
        <div>
          <div class="kb-page-title">Order {{ $order->order_number }}</div>
          <div class="kb-page-sub">Placed {{ $order->created_at->format('d M Y, H:i') }}</div>
        </div>
        <div class="kb-admin-actions">
          <span class="kb-status-pill kb-status-pill--{{ $order->status }}">{{ ucfirst($order->status) }}</span>
          <a class="kb-btn-outline" href="{{ route('admin.orders.index') }}">Back to Orders</a>
        </div>
      </div>

      @if (session('status'))
        <div class="alert alert-success mt-3">{{ session('status') }}</div>
      @endif

      <div class="kb-order-actions mt-3">
        <form method="POST" action="{{ route('admin.orders.update', $order) }}">
          @csrf
          @method('PATCH')
          <input type="hidden" name="status" value="approved">
          <button class="kb-btn-outline" type="submit">Approve</button>
        </form>
        <form method="POST" action="{{ route('admin.orders.update', $order) }}">
          @csrf
          @method('PATCH')
          <input type="hidden" name="status" value="shipped">
          <button class="kb-btn-outline" type="submit">Mark Shipped</button>
        </form>
        <form method="POST" action="{{ route('admin.orders.update', $order) }}">
          @csrf
          @method('PATCH')
          <input type="hidden" name="status" value="delivered">
          <button class="kb-btn-outline" type="submit">Mark Delivered</button>
        </form>
        <form method="POST" action="{{ route('admin.orders.update', $order) }}">
          @csrf
          @method('PATCH')
          <input type="hidden" name="status" value="cancelled">
          <button class="kb-btn-outline" type="submit">Cancel</button>
        </form>
        <form method="POST" action="{{ route('admin.orders.update', $order) }}">
          @csrf
          @method('PATCH')
          <input type="hidden" name="status" value="refunded">
          <button class="kb-btn-outline" type="submit">Mark Refunded</button>
        </form>
        <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" onsubmit="return confirm('Delete this order permanently?');">
          @csrf
          @method('DELETE')
          <button class="kb-btn-outline" type="submit">Delete</button>
        </form>
      </div>

      <div class="row g-4 mt-2">
        <div class="col-12 col-lg-7">
          <div class="kb-card">
            <div class="kb-card-title">Order Items</div>
            <div class="kb-card-sub">{{ $order->items_count }} items • {{ \App\Support\CurrencyFormatter::format($order->total) }}</div>
            <div class="kb-admin-table mt-3">
              <table>
                <thead>
                  <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Line Total</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($order->items as $item)
                    @php
                      $image = optional($item->product?->images?->sortBy('position')->first())->src;
                    @endphp
                    <tr>
                      <td>
                        <div class="kb-order-product">
                          @if ($image)
                            <img src="{{ $image }}" alt="{{ $item->title }}">
                          @else
                            <div class="kb-admin-thumb-sm"></div>
                          @endif
                          <div>
                            <div class="kb-order-title">{{ $item->title }}</div>
                            <div class="kb-card-sub">{{ $item->product_handle }}</div>
                          </div>
                        </div>
                      </td>
                      <td>{{ \App\Support\CurrencyFormatter::format($item->price) }}</td>
                      <td>{{ $item->quantity }}</td>
                      <td>{{ \App\Support\CurrencyFormatter::format($item->line_total) }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-5">
          <div class="kb-card mb-4">
            <div class="kb-card-title">Customer & Shipping</div>
            <div class="kb-card-sub">{{ $order->customer_name }}</div>
            <div class="kb-order-meta mt-3">
              <div><span>Email</span><strong>{{ $order->email }}</strong></div>
              <div><span>Phone</span><strong>{{ $order->phone }}</strong></div>
              <div><span>City</span><strong>{{ $order->city }}</strong></div>
              <div><span>Address</span><strong>{{ $order->address }}</strong></div>
              @if ($order->postal_code)
                <div><span>Postal Code</span><strong>{{ $order->postal_code }}</strong></div>
              @endif
              <div><span>Delivery</span><strong>{{ ucfirst($order->delivery_method) }}</strong></div>
            </div>
          </div>

          <div class="kb-card">
            <div class="kb-card-title">Payment</div>
            <div class="kb-card-sub">Method: {{ strtoupper($order->payment_method) }}</div>
            @if (!empty($order->payment_details))
              <div class="kb-order-meta mt-3">
                @foreach ($order->payment_details as $label => $value)
                  <div><span>{{ ucwords(str_replace('_', ' ', $label)) }}</span><strong>{{ $value }}</strong></div>
                @endforeach
              </div>
            @else
              <div class="kb-card-sub mt-3">No payment details captured.</div>
            @endif
            @if ($order->payment_proof_path)
              <div class="kb-proof mt-3">
                <div class="kb-card-sub">Payment Proof</div>
                <a href="{{ asset('storage/' . $order->payment_proof_path) }}" target="_blank" rel="noopener">
                  <img src="{{ asset('storage/' . $order->payment_proof_path) }}" alt="Payment proof">
                </a>
              </div>
            @elseif ($order->payment_method === 'bank')
              <div class="kb-card-sub mt-3">Payment proof not uploaded yet.</div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </main>
@endsection
