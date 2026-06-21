<table>
  <tr><th colspan="4">InventoryPro Report</th></tr>
  <tr><td>From</td><td>{{ $from }}</td><td>To</td><td>{{ $to }}</td></tr>
  <tr></tr>
  <tr><th>Metric</th><th>Value</th></tr>
  <tr><td>Invoices</td><td>{{ $summary['invoiceCount'] }}</td></tr>
  <tr><td>Revenue</td><td>{{ number_format($summary['revenue'], 2, '.', '') }}</td></tr>
  <tr><td>Items Sold</td><td>{{ $summary['itemsSold'] }}</td></tr>
  <tr><td>GST Bills</td><td>{{ $summary['gstInvoices'] }}</td></tr>
  <tr><td>Without GST Bills</td><td>{{ $summary['nonGstInvoices'] }}</td></tr>
  <tr><td>Low Stock Products</td><td>{{ $summary['lowStockCount'] }}</td></tr>
  <tr><td>Current Stock Value</td><td>{{ number_format($summary['stockValue'], 2, '.', '') }}</td></tr>
  <tr></tr>
  <tr><th colspan="4">Top Products</th></tr>
  <tr><th>Product</th><th>Company</th><th>Qty</th><th>Total</th></tr>
  @foreach ($topProducts as $product)
    <tr>
      <td>{{ $product['name'] }}</td>
      <td>{{ $product['company'] }}</td>
      <td>{{ $product['qty'] }}</td>
      <td>{{ number_format($product['total'], 2, '.', '') }}</td>
    </tr>
  @endforeach
  <tr></tr>
  <tr><th colspan="6">Invoices</th></tr>
  <tr><th>Invoice</th><th>Date</th><th>Customer</th><th>GST</th><th>Items</th><th>Total</th></tr>
  @foreach ($invoices as $invoice)
    <tr>
      <td>{{ $invoice->invoice_no }}</td>
      <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
      <td>{{ $invoice->customer->name ?? 'Walk-in' }}</td>
      <td>{{ $invoice->gst_applied ? 'Applied' : 'No GST' }}</td>
      <td>{{ $invoice->items->sum('qty') }}</td>
      <td>{{ number_format((float) $invoice->total, 2, '.', '') }}</td>
    </tr>
  @endforeach
  <tr></tr>
  <tr><th colspan="4">Low Stock</th></tr>
  <tr><th>Product</th><th>Category</th><th>Stock</th><th>Sell Price</th></tr>
  @foreach ($lowStockProducts as $product)
    <tr>
      <td>{{ $product->name }}</td>
      <td>{{ $product->category->name ?? '' }}</td>
      <td>{{ $product->stock }}</td>
      <td>{{ number_format((float) $product->sell_price, 2, '.', '') }}</td>
    </tr>
  @endforeach
</table>
