@extends('layouts.app')

@section('content')
  <main class="kb-collection" data-track-order-gate>
    <div class="container">
      <div class="kb-page-title">Track Order</div>
      <div class="kb-page-sub">Secure access for customers only.</div>

      <div class="kb-cart-hero">
        <div>
          <div class="kb-cart-hero-title">{{ $messageTitle ?? 'Order link required' }}</div>
          <div class="kb-cart-hero-sub">{{ $messageBody ?? 'This page is only available from your confirmation email.' }}</div>
        </div>
        <a class="kb-btn-primary text-decoration-none text-white" href="{{ route('home') }}">Back to Home</a>
      </div>

      <div class="kb-empty-state">
        <div class="kb-empty-title">Need help finding your order?</div>
        <div class="kb-empty-sub">{{ $helpBody ?? 'Contact support and we will resend your tracking link.' }}</div>
        <a class="kb-btn-outline mt-3 text-decoration-none" href="mailto:info@khanabadoshfashion.ca">Email Support</a>
      </div>
    </div>
  </main>
@endsection
