@php
    use App\Support\CurrencyFormatter;
@endphp

<div class="section-title">Items</div>
<table class="items" role="presentation" cellspacing="0" cellpadding="0">
  <thead>
    <tr>
      <th>Item</th>
      <th class="amount">Qty</th>
      <th class="amount">Price</th>
      <th class="amount">Line total</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($order->items as $item)
      <tr>
        <td>{{ $item->title }}</td>
        <td class="amount">{{ $item->quantity }}</td>
        <td class="amount">{{ CurrencyFormatter::format($item->price) }}</td>
        <td class="amount">{{ CurrencyFormatter::format($item->line_total) }}</td>
      </tr>
    @endforeach
    <tr class="total-row">
      <td colspan="3">Total</td>
      <td class="amount">{{ CurrencyFormatter::format($order->total) }}</td>
    </tr>
  </tbody>
</table>
