@php
  $baseTotal = $invoice->items->sum(fn($item) => (float) $item->price * $item->qty);
  $lineTotal = $invoice->items->sum(fn($item) => (float) $item->subtotal);
  $gstTotal = $lineTotal - $baseTotal;
@endphp
<table>
  <tr><th colspan="{{ $invoice->gst_applied ? 5 : 4 }}">Invoice {{ $invoice->invoice_no }}</th></tr>
  <tr><td>Date</td><td>{{ $invoice->created_at->format('d M Y, h:i A') }}</td></tr>
  <tr><td>Customer</td><td>{{ $invoice->customer->name ?? 'Walk-in' }}</td></tr>
  <tr><td>Payment</td><td>{{ ucfirst($invoice->payment_method) }}</td></tr>
  <tr></tr>
  <tr>
    <th>Product</th>
    <th>Qty</th>
    <th>Price</th>
    @if ($invoice->gst_applied)<th>GST %</th>@endif
    <th>Total</th>
  </tr>
  @foreach ($invoice->items as $item)
    <tr>
      <td>{{ $item->product->name ?? 'Deleted product' }}</td>
      <td>{{ $item->qty }}</td>
      <td>{{ number_format((float) $item->price, 2, '.', '') }}</td>
      @if ($invoice->gst_applied)<td>{{ number_format((float) $item->gst, 2, '.', '') }}</td>@endif
      <td>{{ number_format((float) $item->subtotal, 2, '.', '') }}</td>
    </tr>
  @endforeach
  <tr></tr>
  <tr><td colspan="{{ $invoice->gst_applied ? 4 : 3 }}">Subtotal</td><td>{{ number_format($baseTotal, 2, '.', '') }}</td></tr>
  @if ($invoice->gst_applied)
    <tr><td colspan="4">GST</td><td>{{ number_format($gstTotal, 2, '.', '') }}</td></tr>
  @endif
  <tr><td colspan="{{ $invoice->gst_applied ? 4 : 3 }}"><strong>Total</strong></td><td><strong>{{ number_format((float) $invoice->total, 2, '.', '') }}</strong></td></tr>
</table>
