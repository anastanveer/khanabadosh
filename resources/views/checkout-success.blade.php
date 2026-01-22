@extends('layouts.app')

@section('content')
  @php
    $statusLabels = [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
        'refunded' => 'Refunded',
    ];
    $statusMessages = [
        'pending' => 'We are reviewing your order and will update you soon.',
        'approved' => 'Your order is confirmed and being prepared for dispatch.',
        'shipped' => 'Your order is on the way. Tracking will be shared if available.',
        'delivered' => 'Your order has been delivered. Thank you for shopping with us.',
        'cancelled' => 'Your order was cancelled. If you already paid, a refund will be processed.',
        'refunded' => 'Your refund is complete and has been sent back to the original payment method.',
    ];
    $statusKey = $order->status ?? 'pending';
    $statusLabel = $statusLabels[$statusKey] ?? ucfirst($statusKey);
    $statusMessage = $statusMessages[$statusKey] ?? 'We will keep you updated by email.';
  @endphp
  <main class="kb-collection" data-checkout-page data-clear-cart>
    <div class="container">
      <div class="kb-page-title">Order Confirmed</div>
      <div class="kb-page-sub">Thank you! Your order has been placed successfully.</div>

      <div class="row g-4 mt-3">
        <div class="col-12 col-lg-7">
          <div class="kb-checkout-panel">
            <div class="kb-card-title">Order Details</div>
            <div class="kb-card-sub">Order #{{ $order->order_number }} - {{ $order->created_at->format('d M Y, H:i') }}</div>

            <div class="kb-checkout-section">
              <div class="kb-card-title">Status</div>
              <div class="kb-card-sub">
                <span class="kb-status-pill kb-status-pill--{{ $order->status }}">{{ $statusLabel }}</span>
              </div>
              <div class="kb-card-sub mt-2">{{ $statusMessage }}</div>
            </div>

            <div class="kb-checkout-section">
              <div class="kb-card-title">Shipping</div>
              <div class="kb-card-sub">
                {{ $order->customer_name }} • {{ $order->phone }}
              </div>
              <div class="kb-card-sub mt-2">
                {{ $order->address }}, {{ $order->city }}{{ $order->postal_code ? ', ' . $order->postal_code : '' }}
              </div>
              <div class="kb-card-sub mt-2">Delivery: {{ ucfirst($order->delivery_method) }}</div>
            </div>

            <div class="kb-checkout-section">
              <div class="kb-card-title">Payment</div>
              <div class="kb-card-sub">Method: {{ strtoupper($order->payment_method) }}</div>
              @if (!empty($order->payment_details))
                <div class="kb-card-sub mt-2">
                  @foreach ($order->payment_details as $label => $value)
                    <div>{{ ucwords(str_replace('_', ' ', $label)) }}: {{ $value }}</div>
                  @endforeach
                </div>
              @endif
              @if ($order->payment_proof_path)
                <div class="kb-card-sub mt-2">Payment proof received.</div>
              @endif
            </div>

            <a class="kb-btn-primary w-100 mt-3 text-decoration-none text-white text-center" href="{{ route('orders.track', ['order_number' => $order->order_number, 'email' => $order->email]) }}">Track Order</a>
            <a class="kb-btn-outline w-100 mt-3 text-decoration-none" href="{{ route('collections.show', ['slug' => 'men-all']) }}">Continue Shopping</a>
          </div>
        </div>

        <div class="col-12 col-lg-5">
          <div class="kb-card kb-checkout-summary">
            <div class="kb-summary-head">
            <div class="kb-card-title">Order Summary</div>
            <div class="kb-summary-sub">Items in your order.</div>
            </div>
            <div class="kb-checkout-list">
              @foreach ($order->items as $item)
                <div class="kb-checkout-item">
                  <div class="kb-cart-thumb"></div>
                  <div>
                    <div class="kb-cart-title">{{ $item->title }}</div>
                    <div class="kb-cart-price">{{ \App\Support\CurrencyFormatter::format($item->price) }}</div>
                    <div class="kb-cart-qty">Qty: <strong>{{ $item->quantity }}</strong></div>
                  </div>
                  <div class="kb-cart-price">{{ \App\Support\CurrencyFormatter::format($item->line_total) }}</div>
                </div>
              @endforeach
            </div>
            <div class="kb-cart-row">
              <span>Subtotal</span>
              <span>{{ \App\Support\CurrencyFormatter::format($order->subtotal) }}</span>
            </div>
            <div class="kb-cart-total">
              <span>Total</span>
              <span>{{ \App\Support\CurrencyFormatter::format($order->total) }}</span>
            </div>
            <div class="kb-summary-note">We will contact you shortly for delivery confirmation.</div>
          </div>
        </div>
      </div>
    </div>
  </main>
@endsection
