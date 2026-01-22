@extends('layouts.app')

@section('content')
  <main class="kb-admin">
    <div class="container">
      <div class="kb-admin-header">
        <div>
          <div class="kb-page-title">Orders</div>
          <div class="kb-page-sub">Review checkout submissions and payment choices.</div>
        </div>
        <div class="kb-admin-actions">
          <a class="kb-btn-outline" href="{{ route('admin.index') }}">Back to Dashboard</a>
        </div>
      </div>

      <div class="kb-card mt-3">
        <div class="kb-card-title">Latest Orders</div>
        <div class="kb-card-sub">Showing {{ $orders->count() }} recent orders.</div>
        @if (session('status'))
          <div class="alert alert-success mt-3">{{ session('status') }}</div>
        @endif
        @if ($orders->isNotEmpty())
          <div class="kb-admin-table mt-3">
            <table>
              <thead>
                <tr>
                  <th>Order</th>
                  <th>Customer</th>
                  <th>Payment</th>
                  <th>Delivery</th>
                  <th>Items</th>
                  <th>Total</th>
                  <th>Status</th>
                  <th>Date</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @foreach ($orders as $order)
                  <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>
                      <div>{{ $order->customer_name }}</div>
                      <div class="kb-card-sub">{{ $order->email }}</div>
                    </td>
                    <td>{{ strtoupper($order->payment_method) }}</td>
                    <td>{{ ucfirst($order->delivery_method) }}</td>
                    <td>{{ $order->items_count }}</td>
                    <td>{{ \App\Support\CurrencyFormatter::format($order->total) }}</td>
                    <td>
                      <span class="kb-status-pill kb-status-pill--{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                      @if ($order->payment_proof_path)
                        <span class="kb-proof-pill">Proof</span>
                      @endif
                    </td>
                    <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                    <td>
                      <a class="kb-btn-outline kb-btn-sm" href="{{ route('admin.orders.show', $order) }}">View Order</a>
                      <form class="d-inline" method="POST" action="{{ route('admin.orders.destroy', $order) }}" onsubmit="return confirm('Delete this order permanently?');">
                        @csrf
                        @method('DELETE')
                        <button class="kb-btn-outline kb-btn-sm" type="submit">Delete</button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="kb-card-sub mt-3">No orders placed yet.</div>
        @endif
      </div>
    </div>
  </main>
@endsection
