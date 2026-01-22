@extends('layouts.app')

@section('content')
  <main class="kb-admin">
    <div class="container">
      <div class="kb-admin-header">
        <div>
          <div class="kb-page-title">Admin Dashboard</div>
          <div class="kb-page-sub">Inventory, pricing, discounts, and analytics in one place.</div>
        </div>
        <div class="kb-admin-actions">
          <a class="kb-btn-outline" href="{{ route('admin.orders.index') }}">Orders</a>
          <a class="kb-btn-outline" href="{{ route('admin.products.index') }}">Manage Products</a>
          <form method="POST" action="{{ route('admin.sync') }}">
            @csrf
            <button class="kb-btn-primary" type="submit">Sync Data</button>
          </form>
          <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button class="kb-btn-outline" type="submit">Logout</button>
          </form>
        </div>
      </div>

      @if ($status)
        <div class="alert alert-success mt-3">{{ $status }}</div>
      @endif

      <div class="kb-stat-grid">
        <div class="kb-stat-card">
          <div class="label">Total Products</div>
          <div class="value">{{ $totalProducts }}</div>
          <div class="meta">{{ $totalCollections }} collections • {{ $totalVariants }} variants</div>
        </div>
        <div class="kb-stat-card">
          <div class="label">Inventory Units</div>
          <div class="value">{{ number_format($totalStock) }}</div>
          <div class="meta">In-stock: {{ $inStockProducts }} • Low: {{ $lowStockProducts }}</div>
        </div>
        <div class="kb-stat-card">
          <div class="label">Potential Revenue</div>
          <div class="value">{{ \App\Support\CurrencyFormatter::format($potentialRevenue) }}</div>
          <div class="meta">Based on stock × price</div>
        </div>
        <div class="kb-stat-card">
          <div class="label">Discounted Items</div>
          <div class="value">{{ $discountedProducts }}</div>
          <div class="meta">Active discount campaigns</div>
        </div>
        <div class="kb-stat-card">
          <div class="label">Out of Stock</div>
          <div class="value">{{ $outOfStockProducts }}</div>
          <div class="meta">Requires restock</div>
        </div>
        <div class="kb-stat-card">
          <div class="label">Collections</div>
          <div class="value">{{ $totalCollections }}</div>
          <div class="meta">Catalog coverage</div>
        </div>
        <div class="kb-stat-card">
          <div class="label">Orders</div>
          <div class="value">{{ $ordersCount }}</div>
          <div class="meta"><a class="text-decoration-underline" href="{{ route('admin.orders.index') }}">View Orders</a></div>
        </div>
        <div class="kb-stat-card">
          <div class="label">Order Revenue</div>
          <div class="value">{{ \App\Support\CurrencyFormatter::format($ordersRevenue) }}</div>
          <div class="meta">All checkout totals</div>
        </div>
        <div class="kb-stat-card">
          <div class="label">Orders Today</div>
          <div class="value">{{ $ordersToday }}</div>
          <div class="meta">Last 24 hours</div>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-12">
          <div class="kb-card">
            <div class="kb-card-title">Recent Orders</div>
            <div class="kb-card-sub">Latest checkout submissions.</div>
            @if ($recentOrders->isNotEmpty())
              <div class="kb-admin-table mt-3">
                <table>
                  <thead>
                    <tr>
                      <th>Order</th>
                      <th>Customer</th>
                      <th>Payment</th>
                      <th>Total</th>
                      <th>Status</th>
                      <th>Placed</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($recentOrders as $order)
                      <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ strtoupper($order->payment_method) }}</td>
                        <td>{{ \App\Support\CurrencyFormatter::format($order->total) }}</td>
                        <td><span class="kb-status-pill kb-status-pill--{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                        <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                        <td><a class="kb-btn-outline kb-btn-sm" href="{{ route('admin.orders.show', $order) }}">View Order</a></td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              <div class="kb-card-sub mt-3">No orders yet.</div>
            @endif
          </div>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-12 col-lg-8">
          <div class="kb-card">
            <div class="kb-card-title">Collections Performance</div>
            <canvas id="collectionChart" height="160"></canvas>
          </div>
        </div>
        <div class="col-12 col-lg-4">
          <div class="kb-card">
            <div class="kb-card-title">Top Stock Products</div>
            <div class="kb-admin-list">
              @foreach ($topProducts as $product)
                @php
                  $image = optional($product->images->sortBy('position')->first())->src;
                  $stock = $product->variants->sum('inventory_quantity');
                @endphp
                <div class="kb-admin-list-item">
                  @if ($image)
                    <img src="{{ $image }}" alt="{{ $product->title }}">
                  @else
                    <div class="kb-admin-thumb"></div>
                  @endif
                  <div>
                    <div class="title">{{ $product->title }}</div>
                    <div class="meta">{{ $stock }} units • {{ $product->handle }}</div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-12 col-lg-6">
          <div class="kb-card">
            <div class="kb-card-title">Stock by Collection</div>
            <canvas id="stockChart" height="180"></canvas>
          </div>
        </div>
        <div class="col-12 col-lg-6">
          <div class="kb-card">
            <div class="kb-card-title">Order Status Snapshot</div>
            <div class="kb-card-sub">Live distribution of order progress.</div>
            <div class="kb-order-status-grid mt-3">
              <div class="kb-order-status-chart">
                <canvas id="ordersChart" height="160"></canvas>
              </div>
              <div class="kb-order-status-list">
                <div class="kb-order-status-item">
                  <span class="kb-status-pill kb-status-pill--pending">Pending</span>
                  <strong>{{ $ordersByStatus['pending'] ?? 0 }}</strong>
                </div>
                <div class="kb-order-status-item">
                  <span class="kb-status-pill kb-status-pill--approved">Approved</span>
                  <strong>{{ $ordersByStatus['approved'] ?? 0 }}</strong>
                </div>
                <div class="kb-order-status-item">
                  <span class="kb-status-pill kb-status-pill--shipped">Shipped</span>
                  <strong>{{ $ordersByStatus['shipped'] ?? 0 }}</strong>
                </div>
                <div class="kb-order-status-item">
                  <span class="kb-status-pill kb-status-pill--delivered">Delivered</span>
                  <strong>{{ $ordersByStatus['delivered'] ?? 0 }}</strong>
                </div>
                <div class="kb-order-status-item">
                  <span class="kb-status-pill kb-status-pill--cancelled">Cancelled</span>
                  <strong>{{ $ordersByStatus['cancelled'] ?? 0 }}</strong>
                </div>
                <div class="kb-order-status-item">
                  <span class="kb-status-pill kb-status-pill--refunded">Refunded</span>
                  <strong>{{ $ordersByStatus['refunded'] ?? 0 }}</strong>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-12 col-lg-6">
          <div class="kb-card">
            <div class="kb-card-title">Currency & Pricing</div>
            <div class="kb-card-sub">Switch storefront currency between PKR and CAD.</div>
            <form class="mt-3" method="POST" action="{{ route('admin.settings.currency') }}">
              @csrf
              <div class="kb-form-grid">
                <div>
                  <label>Currency</label>
                  <select name="currency">
                    <option value="PKR" {{ strtoupper($currency) === 'PKR' ? 'selected' : '' }}>PKR (Rs.)</option>
                    <option value="CAD" {{ strtoupper($currency) === 'CAD' ? 'selected' : '' }}>CAD (C$)</option>
                  </select>
                </div>
                <div>
                  <label>PKR → CAD Rate</label>
                  <input type="number" step="0.0001" name="cad_rate" value="{{ number_format($liveCadRate, 4, '.', '') }}">
                  <div class="kb-card-sub">Live rate: 1 PKR = {{ number_format($liveCadRate, 4) }} CAD</div>
                </div>
              </div>
              <button class="kb-btn-primary mt-3" type="submit">Apply Currency</button>
            </form>
            <form class="mt-2" method="POST" action="{{ route('admin.settings.currency.live') }}">
              @csrf
              <button class="kb-btn-outline" type="submit">Switch to CAD (Live Rate)</button>
            </form>
          </div>
        </div>
        <div class="col-12 col-lg-6">
          <div class="kb-card kb-highlight-card">
            <div class="kb-card-title">Live Currency</div>
            <div class="kb-card-sub">Current storefront pricing</div>
            <div class="kb-highlight-value">{{ strtoupper($currency) }}</div>
            <div class="kb-highlight-meta">1 PKR = {{ number_format($cadRate, 4) }} CAD</div>
            <div class="kb-card-note">Prices on the storefront auto-update after applying currency.</div>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-12 col-lg-8">
          <div class="kb-card">
            <div class="kb-card-title">Bank Transfer Settings</div>
            <div class="kb-card-sub">Shown on checkout for bank transfer payments.</div>
            <form class="mt-3" method="POST" action="{{ route('admin.settings.bank') }}">
              @csrf
              <div class="kb-form-grid">
                <div>
                  <label>Bank Name</label>
                  <input type="text" name="bank_name" value="{{ $bankName }}">
                </div>
                <div>
                  <label>Account Title</label>
                  <input type="text" name="bank_account_title" value="{{ $bankTitle }}">
                </div>
                <div>
                  <label>Account Number</label>
                  <input type="text" name="bank_account_number" value="{{ $bankAccount }}">
                </div>
                <div>
                  <label>IBAN</label>
                  <input type="text" name="bank_iban" value="{{ $bankIban }}">
                </div>
              </div>
              <div class="mt-2">
                <label>Bank Note</label>
                <input type="text" name="bank_note" value="{{ $bankNote }}">
              </div>
              <button class="kb-btn-primary mt-3" type="submit">Save Bank Details</button>
            </form>
          </div>
        </div>
        <div class="col-12 col-lg-4">
          <div class="kb-card kb-highlight-card">
            <div class="kb-card-title">Transfer Account</div>
            <div class="kb-card-sub">Active bank details</div>
            <div class="kb-card-note">
              <div>{{ $bankName }}</div>
              <div>{{ $bankTitle }}</div>
              <div>{{ $bankAccount }}</div>
              <div>{{ $bankIban }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-12 col-lg-8">
          <div class="kb-card">
            <div class="kb-card-title">Popular Products</div>
            <div class="kb-card-sub">Select up to 3 products to feature in the shop sidebar.</div>
            <form class="mt-3" method="POST" action="{{ route('admin.popular') }}">
              @csrf
              <div class="row g-3">
                @foreach ($products as $product)
                  <div class="col-12 col-md-6 col-lg-4">
                    <label class="kb-admin-check">
                      <input type="checkbox" name="popular[]" value="{{ $product->id }}" {{ $product->is_popular ? 'checked' : '' }}>
                      <span>{{ $product->title }}</span>
                    </label>
                  </div>
                @endforeach
              </div>
              <button class="kb-btn-outline mt-3" type="submit">Save Popular Products</button>
            </form>
          </div>
        </div>
      </div>

      @if ($summary)
        <div class="kb-info-card mt-4">
          <strong>Last Sync Summary</strong><br>
          Collections: {{ $summary['collections'] ?? 0 }}<br>
          Products: {{ $summary['products'] ?? 0 }}<br>
          Variants: {{ $summary['variants'] ?? 0 }}<br>
          Images: {{ $summary['images'] ?? 0 }}<br>
          Collection Links: {{ $summary['collection_links'] ?? 0 }}
        </div>
      @endif
    </div>
  </main>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <script>
    var collectionLabels = @json($topCollections->pluck('title'));
    var collectionCounts = @json($topCollections->pluck('count'));
    var stockLabels = @json($topStockCollections->pluck('title'));
    var stockValues = @json($topStockCollections->pluck('stock'));
    var orderStatusLabels = ['Pending', 'Approved', 'Shipped', 'Delivered', 'Cancelled', 'Refunded'];
    var orderStatusValues = [
      {{ $ordersByStatus['pending'] ?? 0 }},
      {{ $ordersByStatus['approved'] ?? 0 }},
      {{ $ordersByStatus['shipped'] ?? 0 }},
      {{ $ordersByStatus['delivered'] ?? 0 }},
      {{ $ordersByStatus['cancelled'] ?? 0 }},
      {{ $ordersByStatus['refunded'] ?? 0 }},
    ];

    var ctx = document.getElementById('collectionChart');
    if (ctx) {
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: collectionLabels,
          datasets: [{
            label: 'Products',
            data: collectionCounts,
            backgroundColor: 'rgba(17, 17, 17, 0.85)',
            borderRadius: 8,
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { display: false },
          },
          scales: {
            y: { beginAtZero: true, ticks: { precision: 0 } }
          }
        }
      });
    }

    var stockCtx = document.getElementById('stockChart');
    if (stockCtx) {
      new Chart(stockCtx, {
        type: 'line',
        data: {
          labels: stockLabels,
          datasets: [{
            label: 'Units',
            data: stockValues,
            borderColor: '#c23b2a',
            backgroundColor: 'rgba(194, 59, 42, 0.15)',
            borderWidth: 2,
            pointRadius: 3,
            tension: 0.3,
            fill: true,
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { display: false },
          },
          scales: {
            y: { beginAtZero: true, ticks: { precision: 0 } }
          }
        }
      });
    }

    var ordersCtx = document.getElementById('ordersChart');
    if (ordersCtx) {
      new Chart(ordersCtx, {
        type: 'doughnut',
        data: {
          labels: orderStatusLabels,
          datasets: [{
            data: orderStatusValues,
            backgroundColor: [
              'rgba(245,158,11,0.8)',
              'rgba(16,185,129,0.8)',
              'rgba(59,130,246,0.8)',
              'rgba(34,197,94,0.8)',
              'rgba(239,68,68,0.8)',
              'rgba(20,184,166,0.8)'
            ],
            borderWidth: 0,
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { display: false },
          },
          cutout: '68%',
        }
      });
    }
  </script>
@endpush
