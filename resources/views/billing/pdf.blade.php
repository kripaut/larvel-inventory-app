@php
  $baseTotal = $invoice->items->sum(fn($item) => (float) $item->price * $item->qty);
  $lineTotal = $invoice->items->sum(fn($item) => (float) $item->subtotal);
  $gstTotal = $lineTotal - $baseTotal;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>{{ $invoice->invoice_no }}</title>
  <style>
    body { font-family: Arial, sans-serif; color: #111827; margin: 32px; }
    .top { display: flex; justify-content: space-between; gap: 24px; margin-bottom: 28px; }
    h1 { margin: 0 0 8px; font-size: 28px; }
    table { width: 100%; border-collapse: collapse; margin-top: 24px; }
    th, td { border: 1px solid #d1d5db; padding: 10px; text-align: left; }
    th { background: #f3f4f6; }
    .right { text-align: right; }
    .totals { width: 320px; margin-left: auto; margin-top: 24px; }
    .totals div { display: flex; justify-content: space-between; padding: 6px 0; }
    .grand { font-size: 22px; font-weight: bold; border-top: 1px solid #111827; margin-top: 6px; padding-top: 10px; }
    .actions { margin-bottom: 20px; }
    @media print { .actions { display: none; } body { margin: 0; } }
  </style>
</head>
<body>
  <div class="actions">
    <button onclick="window.print()">Print / Save PDF</button>
  </div>
  <div class="top">
    <div>
      <h1>Invoice</h1>
      <strong>{{ $invoice->invoice_no }}</strong><br>
      {{ $invoice->created_at->format('d M Y, h:i A') }}
    </div>
    <div class="right">
      <strong>Customer</strong><br>
      {{ $invoice->customer->name ?? 'Walk-in' }}<br>
      {{ $invoice->customer->phone ?? '' }}<br>
      {{ $invoice->customer->email ?? '' }}
    </div>
  </div>
  <table>
    <thead>
      <tr>
        <th>Product</th>
        <th>Qty</th>
        <th>Price</th>
        @if ($invoice->gst_applied)<th>GST %</th>@endif
        <th class="right">Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($invoice->items as $item)
        <tr>
          <td>{{ $item->product->name ?? 'Deleted product' }}</td>
          <td>{{ $item->qty }}</td>
          <td>Rs {{ number_format((float) $item->price, 2) }}</td>
          @if ($invoice->gst_applied)<td>{{ number_format((float) $item->gst, 2) }}</td>@endif
          <td class="right">Rs {{ number_format((float) $item->subtotal, 2) }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <div class="totals">
    <div><span>Subtotal</span><strong>Rs {{ number_format($baseTotal, 2) }}</strong></div>
    @if ($invoice->gst_applied)
      <div><span>GST</span><strong>Rs {{ number_format($gstTotal, 2) }}</strong></div>
    @endif
    <div class="grand"><span>Total</span><span>Rs {{ number_format((float) $invoice->total, 2) }}</span></div>
  </div>
</body>
</html>
