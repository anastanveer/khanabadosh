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
    $paymentMethodLabels = [
        'cod' => 'Cash on Delivery',
        'card' => 'Card',
        'bank' => 'Bank Transfer',
    ];
    $deliveryMethodLabels = [
        'standard' => 'Standard Delivery',
        'express' => 'Express Delivery',
    ];
    $paymentMethod = $paymentMethodLabels[$order->payment_method] ?? ucfirst($order->payment_method ?? '');
    $deliveryMethod = $deliveryMethodLabels[$order->delivery_method] ?? ucfirst($order->delivery_method ?? '');
    $cardLast4 = $order->payment_details['card_last4'] ?? null;
    $bankRef = $order->payment_details['bank_reference'] ?? null;
  @endphp
  <main class="kb-collection" data-track-order-page>
    <div class="container">
      <div class="kb-page-title">Track Order</div>
      <div class="kb-page-sub">Here is the latest update for your order.</div>

      <div class="row g-4 mt-3">
        <div class="col-12 col-lg-6">
          <div class="kb-card">
            <div class="kb-card-title">Order Status</div>
            <div class="kb-card-sub">Order #{{ $order->order_number }} • {{ $order->created_at->format('d M Y, H:i') }}</div>
            <div class="mt-3">
              <span class="kb-status-pill kb-status-pill--{{ $order->status }}">{{ $statusLabel }}</span>
            </div>
            <div class="kb-card-sub mt-3">{{ $statusMessage }}</div>
            <div class="kb-summary-note mt-3">Updates are also sent to {{ $order->email }}.</div>
          </div>
        </div>

        <div class="col-12 col-lg-6">
          <div class="kb-card">
            <div class="kb-card-title">Shipping</div>
            <div class="kb-card-sub">{{ $order->customer_name }} • {{ $order->phone }}</div>
            <div class="kb-order-meta mt-3">
              <div><span>Address</span><strong>{{ $order->address }}</strong></div>
              <div><span>City</span><strong>{{ $order->city }}</strong></div>
              @if ($order->postal_code)
                <div><span>Postal Code</span><strong>{{ $order->postal_code }}</strong></div>
              @endif
              <div><span>Delivery</span><strong>{{ $deliveryMethod }}</strong></div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4 mt-3">
        <div class="col-12 col-lg-7">
          <div class="kb-checkout-panel">
            <div class="kb-card-title">Items in your order</div>
            <div class="kb-card-sub">{{ $order->items_count }} items • {{ \App\Support\CurrencyFormatter::format($order->total) }}</div>
            <div class="kb-checkout-list mt-3">
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
          </div>
        </div>

        <div class="col-12 col-lg-5">
          <div class="kb-card">
            <div class="kb-card-title">Payment</div>
            <div class="kb-card-sub">Method: {{ $paymentMethod }}</div>
            @if ($cardLast4)
              <div class="kb-card-sub mt-2">Card ending in {{ $cardLast4 }}</div>
            @elseif ($bankRef)
              <div class="kb-card-sub mt-2">Bank reference: {{ $bankRef }}</div>
            @endif
            @if (!empty($order->notes))
              <div class="kb-card-sub mt-3">Notes: {{ $order->notes }}</div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </main>
@endsection
