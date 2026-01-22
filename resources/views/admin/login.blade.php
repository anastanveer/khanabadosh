@extends('layouts.app')

@section('content')
  <main class="kb-admin">
    <div class="container">
      <div class="kb-admin-login">
        <div class="kb-admin-login-card">
          <div class="kb-admin-login-hero">
            <div class="kb-admin-login-badge">Khanabadosh Console</div>
            <div class="kb-page-title">Admin Login</div>
            <div class="kb-page-sub">Manage products, inventory, pricing, and campaigns.</div>
          </div>

          @if ($errors->any())
            <div class="alert alert-danger mt-3">
              {{ $errors->first() }}
            </div>
          @endif

          <form class="mt-3" method="POST" action="{{ route('admin.login.submit') }}">
            @csrf
            <div class="kb-form-grid">
              <div>
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
              </div>
              <div>
                <label>Password</label>
                <input type="password" name="password" required>
              </div>
            </div>
            <button class="kb-btn-primary mt-3" type="submit">Login</button>
          </form>
          <div class="kb-admin-login-note">
            Secure access for catalog control and pricing updates.
          </div>
        </div>
      </div>
    </div>
  </main>
@endsection
