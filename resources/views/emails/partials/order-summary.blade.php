@php
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
@endphp

<div class="section-title">Order summary</div>
<table class="summary" role="presentation" cellspacing="0" cellpadding="0">
  <tr>
    <td class="label">Order number</td>
    <td class="value">{{ $order->order_number }}</td>
  </tr>
  <tr>
    <td class="label">Placed on</td>
    <td class="value">{{ $order->created_at?->format('M d, Y · h:i A') }}</td>
  </tr>
  @if (!empty($statusLabel))
    <tr>
      <td class="label">Status</td>
      <td class="value">{{ $statusLabel }}</td>
    </tr>
  @endif
  @if (!empty($previousStatusLabel))
    <tr>
      <td class="label">Previous status</td>
      <td class="value">{{ $previousStatusLabel }}</td>
    </tr>
  @endif
  <tr>
    <td class="label">Delivery</td>
    <td class="value">{{ $deliveryMethod }}</td>
  </tr>
  <tr>
    <td class="label">Payment</td>
    <td class="value">{{ $paymentMethod }}</td>
  </tr>
  <tr>
    <td class="label">Customer</td>
    <td class="value">{{ $order->customer_name }}</td>
  </tr>
  <tr>
    <td class="label">Contact</td>
    <td class="value">{{ $order->email }}<br>{{ $order->phone }}</td>
  </tr>
  <tr>
    <td class="label">Ship to</td>
    <td class="value">
      {{ $order->address }}<br>
      {{ $order->city }}{{ $order->postal_code ? ', ' . $order->postal_code : '' }}
    </td>
  </tr>
  @if (!empty($order->notes))
    <tr>
      <td class="label">Notes</td>
      <td class="value">{{ $order->notes }}</td>
    </tr>
  @endif
</table>
