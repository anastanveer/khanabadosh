@extends('layouts.app')

@section('content')
  <main class="kb-collection" data-checkout-page>
    <div class="container">
      <div class="kb-page-title">Checkout</div>
      <div class="kb-page-sub">Secure your order with fast processing and trusted delivery.</div>
      <div class="kb-checkout-steps">
        <div class="kb-step is-done">
          <span class="kb-step-dot"><i class="bi bi-check"></i></span>
          <span class="kb-step-label">Cart</span>
        </div>
        <div class="kb-step is-active">
          <span class="kb-step-dot">2</span>
          <span class="kb-step-label">Details</span>
        </div>
        <div class="kb-step">
          <span class="kb-step-dot">3</span>
          <span class="kb-step-label">Payment</span>
        </div>
        <div class="kb-step">
          <span class="kb-step-dot">4</span>
          <span class="kb-step-label">Confirm</span>
        </div>
      </div>

      <div class="row g-4 mt-3">
        <div class="col-12 col-lg-7">
          <div class="kb-checkout-panel">
            <div class="kb-card-title">Contact & Shipping</div>
            <div class="kb-card-sub">We will send order updates to your email and phone.</div>
            @if ($errors->any())
              <div class="kb-checkout-alert">
                <strong>Please fix the highlighted fields.</strong>
                <ul>
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif
            <form class="kb-checkout-form" method="POST" action="{{ route('checkout.place') }}" enctype="multipart/form-data">
              @csrf
              <input type="hidden" name="cart_payload" data-cart-payload>
              <div class="kb-form-grid">
                <div>
                  <label>Full Name</label>
                  <input type="text" name="customer_name" placeholder="Enter your name" value="{{ old('customer_name') }}" required>
                </div>
                <div>
                  <label>Email</label>
                  <input type="email" name="email" placeholder="you@example.com" value="{{ old('email') }}" required>
                </div>
                <div>
                  <label>Phone</label>
                  <input type="tel" name="phone" placeholder="+14375519575" value="{{ old('phone') }}" required>
                </div>
                <div>
                  <label>City</label>
                  <input type="text" name="city" placeholder="Lahore" value="{{ old('city') }}" required>
                </div>
                <div>
                  <label>Address</label>
                  <input type="text" name="address" placeholder="House, Street, Area" value="{{ old('address') }}" required>
                </div>
                <div>
                  <label>Postal Code</label>
                  <input type="text" name="postal_code" placeholder="54000" value="{{ old('postal_code') }}">
                </div>
              </div>
              <button class="kb-btn-outline kb-btn-sm" type="button" data-demo-shipping>Use demo details</button>

              <div class="kb-checkout-section">
                <div class="kb-card-title">Delivery Method</div>
                <div class="kb-pay-grid">
                  <label class="kb-pay-option">
                    <input type="radio" name="delivery_method" value="standard" {{ old('delivery_method', 'standard') === 'standard' ? 'checked' : '' }}>
                    <span class="kb-pay-dot"></span>
                    <span class="kb-pay-content">
                      <span class="kb-pay-title">Standard Delivery</span>
                      <span class="kb-pay-sub">3-7 business days</span>
                    </span>
                  </label>
                  <label class="kb-pay-option">
                    <input type="radio" name="delivery_method" value="express" {{ old('delivery_method') === 'express' ? 'checked' : '' }}>
                    <span class="kb-pay-dot"></span>
                    <span class="kb-pay-content">
                      <span class="kb-pay-title">Express Delivery</span>
                      <span class="kb-pay-sub">1-3 business days</span>
                    </span>
                  </label>
                </div>
              </div>

              <div class="kb-checkout-section">
                <div class="kb-card-title">Payment Method</div>
                <div class="kb-pay-grid">
                  <label class="kb-pay-option">
                    <input type="radio" name="payment_method" value="cod" data-payment-option {{ old('payment_method', 'cod') === 'cod' ? 'checked' : '' }}>
                    <span class="kb-pay-dot"></span>
                    <span class="kb-pay-content">
                      <span class="kb-pay-title">Cash on Delivery</span>
                      <span class="kb-pay-sub">Pay when your order arrives</span>
                    </span>
                  </label>
                  <label class="kb-pay-option">
                    <input type="radio" name="payment_method" value="card" data-payment-option {{ old('payment_method') === 'card' ? 'checked' : '' }}>
                    <span class="kb-pay-dot"></span>
                    <span class="kb-pay-content">
                      <span class="kb-pay-title">Card (Stripe)</span>
                      <span class="kb-pay-sub">Visa, Mastercard, American Express</span>
                    </span>
                  </label>
                  <label class="kb-pay-option">
                    <input type="radio" name="payment_method" value="bank" data-payment-option {{ old('payment_method') === 'bank' ? 'checked' : '' }}>
                    <span class="kb-pay-dot"></span>
                    <span class="kb-pay-content">
                      <span class="kb-pay-title">Bank Transfer</span>
                      <span class="kb-pay-sub">Instant transfer confirmation</span>
                    </span>
                  </label>
                </div>
                <div class="kb-payment-panels">
                  <div class="kb-payment-panel" data-payment-panel="cod">
                    <div class="kb-panel-title">Cash on Delivery</div>
                    <div class="kb-panel-sub">Keep payment ready on delivery. A representative will confirm before dispatch.</div>
                  </div>
                  <div class="kb-payment-panel d-none" data-payment-panel="card">
                    <div class="kb-panel-title">Card Details</div>
                    <div class="kb-card-ui">
                      <div class="kb-card-chip"></div>
                      <div class="kb-card-number">4242 4242 4242 4242</div>
                      <div class="kb-card-meta">
                        <span>Cardholder Name</span>
                        <span>MM/YY</span>
                      </div>
                    </div>
                    <div class="kb-form-grid">
                      <div>
                        <label>Cardholder Name</label>
                        <input type="text" name="card_name" placeholder="Ayesha Khan" value="{{ old('card_name') }}">
                      </div>
                      <div>
                        <label>Card Number</label>
                        <input type="text" name="card_number" placeholder="4242 4242 4242 4242" value="{{ old('card_number') }}">
                      </div>
                      <div>
                        <label>Expiry</label>
                        <input type="text" name="card_expiry" placeholder="08/28" value="{{ old('card_expiry') }}">
                      </div>
                      <div>
                        <label>CVC</label>
                        <input type="text" name="card_cvc" placeholder="123" value="{{ old('card_cvc') }}">
                      </div>
                    </div>
                    <button class="kb-btn-outline kb-btn-sm" type="button" data-demo-card>Use demo card</button>
                  </div>
                  <div class="kb-payment-panel d-none" data-payment-panel="bank">
                    <div class="kb-panel-title">Bank Transfer</div>
                    <div class="kb-bank-box">
                      <div>
                        <strong>Bank:</strong> {{ $bankName }}
                      </div>
                      <div>
                        <strong>Account Title:</strong> {{ $bankTitle }}
                      </div>
                      <div>
                        <strong>Account No:</strong> {{ $bankAccount }}
                      </div>
                      <div>
                        <strong>IBAN:</strong> {{ $bankIban }}
                      </div>
                    </div>
                    <div class="kb-panel-sub">{{ $bankNote }}</div>
                    <div class="kb-form-grid">
                      <div>
                        <label>Bank Name</label>
                        <input type="text" name="bank_name" placeholder="HBL / UBL / MCB" value="{{ old('bank_name') }}">
                      </div>
                      <div>
                        <label>Account / IBAN</label>
                        <input type="text" name="bank_account" placeholder="Enter your account or IBAN" value="{{ old('bank_account') }}">
                      </div>
                      <div>
                        <label>Transfer Reference</label>
                        <input type="text" name="bank_reference" placeholder="TXN-123456" value="{{ old('bank_reference') }}">
                      </div>
                      <div>
                        <label>Payment Screenshot</label>
                        <input type="file" name="payment_proof" accept="image/*">
                      </div>
                    </div>
                    <button class="kb-btn-outline kb-btn-sm" type="button" data-demo-bank>Use demo bank</button>
                  </div>
                </div>
              </div>

              <div class="kb-checkout-section">
                <div class="kb-card-title">Order Notes</div>
                <textarea name="notes" rows="3" placeholder="Add any delivery instructions...">{{ old('notes') }}</textarea>
              </div>

              <button class="kb-btn-primary w-100 mt-3" type="submit">Place Order</button>
              <div class="kb-checkout-note">By placing the order, you agree to our policies and terms.</div>
            </form>
          </div>
        </div>

        <div class="col-12 col-lg-5">
          <div class="kb-card kb-checkout-summary">
            <div class="kb-summary-head">
              <div class="kb-card-title">Order Summary</div>
              <div class="kb-summary-sub">Final review before payment.</div>
            </div>
            <div class="kb-checkout-list" data-checkout-list></div>
            <div class="kb-cart-row">
              <span>Subtotal</span>
              <span data-checkout-subtotal>--</span>
            </div>
            <div class="kb-cart-row">
              <span>Shipping</span>
              <span>Calculated at checkout</span>
            </div>
            <div class="kb-cart-total">
              <span>Total</span>
              <span data-checkout-total>--</span>
            </div>
            <div class="kb-summary-note">Secure checkout powered by Khanabadosh.</div>
            <div class="kb-summary-foot">
              <div><i class="bi bi-shield-lock"></i> SSL protected payments</div>
              <div><i class="bi bi-arrow-repeat"></i> 7-day exchange support</div>
              <div><i class="bi bi-truck"></i> Fast nationwide delivery</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
@endsection
