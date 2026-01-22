@extends('layouts.app')

@section('content')
  <main class="kb-collection" data-cart-page>
    <div class="container">
      <div class="kb-page-title">Cart</div>
      <div class="kb-page-sub">Review items and proceed to checkout.</div>
      <div class="kb-cart-hero">
        <div>
          <div class="kb-cart-hero-title">Ready to check out?</div>
          <div class="kb-cart-hero-sub">Adjust quantities, apply offers, and keep browsing.</div>
        </div>
        <a class="kb-btn-outline text-decoration-none" href="{{ route('collections.show', ['slug' => 'men-all']) }}">Continue Shopping</a>
      </div>

      <div class="kb-empty-state" data-cart-empty>
        <div class="kb-empty-title">Your cart is empty.</div>
        <div class="kb-empty-sub">Add products to see them here.</div>
        <a class="kb-btn-primary mt-3" href="{{ route('collections.show', ['slug' => 'men-all']) }}">Start Shopping</a>
      </div>

      <div class="row g-4 mt-3" data-cart-content>
        <div class="col-12 col-lg-8">
          <div class="kb-cart-panel">
            <div class="kb-cart-panel-head">
              <div>
                <div class="kb-cart-panel-title">Items in your bag</div>
                <div class="kb-cart-panel-sub">Update quantities, remove, or move to wishlist.</div>
              </div>
              <button class="kb-btn-outline" type="button" data-cart-clear>Clear Cart</button>
            </div>
            <div class="kb-cart-list" data-cart-list></div>
            <div class="kb-cart-promise">
              <div><i class="bi bi-truck"></i> Fast shipping across Pakistan</div>
              <div><i class="bi bi-shield-check"></i> Quality checked before dispatch</div>
              <div><i class="bi bi-headset"></i> 24/7 support on WhatsApp</div>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-4">
          <div class="kb-card kb-cart-summary">
            <div class="kb-summary-head">
              <div class="kb-card-title">Order Summary</div>
              <div class="kb-summary-sub">Secure checkout with instant confirmation.</div>
            </div>
            <div class="kb-summary-row">
              <span>Subtotal</span>
              <span data-cart-subtotal>--</span>
            </div>
            <div class="kb-summary-row">
              <span>Estimated Shipping</span>
              <span>Calculated at checkout</span>
            </div>
            <div class="kb-summary-coupon">
              <input type="text" placeholder="Promo code">
              <button class="kb-btn-outline" type="button">Apply</button>
            </div>
            <div class="kb-cart-total">
              <span>Total</span>
              <span data-cart-total>--</span>
            </div>
            <div class="kb-summary-actions">
              <a class="kb-btn-primary w-100 text-decoration-none text-white text-center" href="{{ route('checkout') }}">Proceed to Checkout</a>
              <a class="kb-btn-outline w-100 text-decoration-none text-center" href="{{ route('collections.show', ['slug' => 'men-all']) }}">Continue Shopping</a>
            </div>
            <div class="kb-summary-note">By placing an order you agree to our policies.</div>
          </div>
        </div>
      </div>
    </div>
  </main>
@endsection
