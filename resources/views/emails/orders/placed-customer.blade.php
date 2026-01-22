@extends('emails.layout')

@section('content')
  <p>Hi {{ $order->customer_name }},</p>
  <p>We have received your order and are getting it ready. You will receive another email when it ships.</p>

  @if ($order->payment_method === 'bank')
    <div class="note">We have received your bank transfer details and will confirm once the payment is verified.</div>
  @endif

  @include('emails.partials.order-summary', ['order' => $order])
  @include('emails.partials.order-items', ['order' => $order])

  <p class="muted">Track your order anytime:</p>
  <a class="cta" href="{{ route('orders.track', ['order_number' => $order->order_number, 'email' => $order->email]) }}">Track Order</a>

  <p class="muted">If you need to update your order, reply to this email within 12 hours.</p>
@endsection
