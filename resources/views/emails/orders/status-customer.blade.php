@extends('emails.layout')

@section('content')
  <p>Hi {{ $order->customer_name }},</p>
  <p>Your order status has been updated to <strong>{{ $statusLabel }}</strong>.</p>

  @if ($order->status === 'refunded')
    <div class="note">Your refund is complete and has been sent back to your original payment method.</div>
  @elseif ($order->status === 'delivered')
    <div class="note">Your order has been delivered. Thank you for shopping with us.</div>
  @elseif ($order->status === 'shipped')
    <div class="note">Your order is on the way. We will share tracking details if available.</div>
  @elseif ($order->status === 'approved')
    <div class="note">Your order has been approved and is being prepared for dispatch.</div>
  @elseif ($order->status === 'cancelled')
    <div class="note">Your order was cancelled. If you have already paid, the refund will be processed back to your original method.</div>
  @endif

  @include('emails.partials.order-summary', [
      'order' => $order,
      'statusLabel' => $statusLabel,
      'previousStatusLabel' => $previousStatusLabel,
  ])
  @include('emails.partials.order-items', ['order' => $order])

  <p class="muted">Track your order anytime:</p>
  <a class="cta" href="{{ route('orders.track', ['order_number' => $order->order_number, 'email' => $order->email]) }}">Track Order</a>

  <p class="muted">Questions? Reply to this email and our team will help you.</p>
@endsection
