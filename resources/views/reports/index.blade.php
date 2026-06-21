<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>InventoryPro - Reports</title>
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .report-stat {
      min-height: 110px;
      border-radius: .9rem;
      background: #fff;
      box-shadow: 0 2px 10px rgba(30,41,59,0.08);
      padding: 1.25rem;
    }
    .report-value {
      font-size: 1.65rem;
      font-weight: 700;
      color: #1E293B;
    }
    .report-label {
      color: #64748B;
    }
  </style>
</head>
<body>
  <div id="sidebar-container"></div>
  <div class="main-content">
    <div id="navbar-container"></div>
    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Reports</h3>
        <a href="{{ route('reports.export', request()->query()) }}" class="btn btn-success"><i class="fa-solid fa-file-excel me-2"></i>Export Excel</a>
      </div>

      <form method="GET" action="{{ route('reports.index') }}" class="glass-card p-4 mb-4">
        <div class="row g-3 align-items-end">
          <div class="col-md-4">
            <label class="form-label">From Date</label>
            <input type="date" name="from" value="{{ $from }}" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">To Date</label>
            <input type="date" name="to" value="{{ $to }}" class="form-control">
          </div>
          <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill"><i class="fa-solid fa-filter me-2"></i>Apply</button>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">Reset</a>
          </div>
        </div>
      </form>

      <div class="row g-4 mb-4">
        <div class="col-md-3 col-6"><div class="report-stat"><div class="report-value">{{ number_format($summary['invoiceCount']) }}</div><div class="report-label">Invoices</div></div></div>
        <div class="col-md-3 col-6"><div class="report-stat"><div class="report-value">Rs {{ number_format($summary['revenue'], 2) }}</div><div class="report-label">Revenue</div></div></div>
        <div class="col-md-3 col-6"><div class="report-stat"><div class="report-value">{{ number_format($summary['itemsSold']) }}</div><div class="report-label">Items Sold</div></div></div>
        <div class="col-md-3 col-6"><div class="report-stat"><div class="report-value">{{ number_format($summary['lowStockCount']) }}</div><div class="report-label">Low Stock</div></div></div>
        <div class="col-md-3 col-6"><div class="report-stat"><div class="report-value">{{ number_format($summary['gstInvoices']) }}</div><div class="report-label">GST Bills</div></div></div>
        <div class="col-md-3 col-6"><div class="report-stat"><div class="report-value">{{ number_format($summary['nonGstInvoices']) }}</div><div class="report-label">Without GST Bills</div></div></div>
        <div class="col-md-6 col-12"><div class="report-stat"><div class="report-value">Rs {{ number_format($summary['stockValue'], 2) }}</div><div class="report-label">Current Stock Value By Buy Price</div></div></div>
      </div>

      <div class="row g-4 mb-4">
        <div class="col-lg-6">
          <div class="glass-card p-4 h-100">
            <h5 class="mb-3">Top Products</h5>
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead><tr><th>Product</th><th>Company</th><th>Qty</th><th>Total</th></tr></thead>
                <tbody>
                  @forelse ($topProducts as $product)
                    <tr>
                      <td>{{ $product['name'] }}</td>
                      <td>{{ $product['company'] ?: '-' }}</td>
                      <td>{{ $product['qty'] }}</td>
                      <td>Rs {{ number_format($product['total'], 2) }}</td>
                    </tr>
                  @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No sales in this period.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="glass-card p-4 h-100">
            <h5 class="mb-3">Low Stock Products</h5>
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead><tr><th>Product</th><th>Category</th><th>Stock</th><th>Sell Price</th></tr></thead>
                <tbody>
                  @forelse ($lowStockProducts as $product)
                    <tr>
                      <td>{{ $product->name }}</td>
                      <td>{{ $product->category->name ?? '-' }}</td>
                      <td><span class="badge bg-danger">{{ $product->stock }}</span></td>
                      <td>Rs {{ number_format((float) $product->sell_price, 2) }}</td>
                    </tr>
                  @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No low stock products.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="glass-card p-4">
        <h5 class="mb-3">Invoice Report</h5>
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Invoice</th>
                <th>Date</th>
                <th>Customer</th>
                <th>GST</th>
                <th>Items</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($invoices as $invoice)
                <tr>
                  <td><a href="{{ route('billing.show', $invoice) }}">{{ $invoice->invoice_no }}</a></td>
                  <td>{{ $invoice->created_at->format('d M Y') }}</td>
                  <td>{{ $invoice->customer->name ?? 'Walk-in' }}</td>
                  <td>{{ $invoice->gst_applied ? 'Applied' : 'No GST' }}</td>
                  <td>{{ $invoice->items->sum('qty') }}</td>
                  <td>Rs {{ number_format((float) $invoice->total, 2) }}</td>
                </tr>
              @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No invoices found.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        {{ $invoices->links() }}
      </div>
    </div>
  </div>
  <div id="globalLoader" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background:rgba(255,255,255,0.7);z-index:9999;display:none;">
    <div class="loader"></div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
  <script>window.csrfField = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';</script>
  <script src="{{ asset('assets/js/components.js')}}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
  <script>
    document.getElementById('sidebar-container').innerHTML = window.renderSidebar('reports');
    document.getElementById('navbar-container').innerHTML = window.renderNavbar('Reports');
  </script>
</body>
</html>
