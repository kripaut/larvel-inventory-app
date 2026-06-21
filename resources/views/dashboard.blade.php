<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>InventoryPro - Dashboard</title>
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .stat-card {
      min-height: 120px;
      display: flex;
      align-items: center;
      gap: 1.5rem;
      background: rgba(255,255,255,0.95);
      border-radius: 1rem;
      box-shadow: 0 4px 24px rgba(30,41,59,0.08);
      padding: 1.5rem;
    }
    .stat-icon {
      font-size: 2.4rem;
      padding: 1rem;
      border-radius: .9rem;
      color: #2563EB;
      background: linear-gradient(135deg, #2563EB22 0%, #10B98122 100%);
    }
    .stat-value {
      font-size: 2rem;
      font-weight: 700;
      color: #1E293B;
    }
    .stat-label {
      font-size: 1rem;
      color: #64748B;
    }
    .quick-action {
      border-radius: .9rem;
      box-shadow: 0 2px 8px rgba(16,185,129,0.08);
      background: #fff;
    }
    .low-stock-alert {
      background: #fff;
      border-left: 6px solid #EF4444;
      border-radius: .9rem;
      box-shadow: 0 2px 8px rgba(239,68,68,0.08);
      padding: 1rem 1.25rem;
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    .recent-bills-table {
      border-radius: 1rem;
      overflow: hidden;
      background: #fff;
      box-shadow: 0 2px 8px rgba(30,41,59,0.08);
    }
  </style>
</head>
<body>
  <div id="sidebar-container"></div>
  <div class="main-content">
    <div id="navbar-container"></div>
    <div class="container-fluid py-4">
      <div class="row g-4 mb-4">
        <div class="col-md-3 col-6">
          <div class="stat-card glass-card">
            <div class="stat-icon"><i class="fa-solid fa-cube"></i></div>
            <div>
              <div class="stat-value">{{ number_format($stats['products']) }}</div>
              <div class="stat-label">Total Products</div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="stat-card glass-card">
            <div class="stat-icon"><i class="fa-solid fa-cart-shopping"></i></div>
            <div>
              <div class="stat-value">{{ number_format($stats['sales']) }}</div>
              <div class="stat-label">Invoices</div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="stat-card glass-card">
            <div class="stat-icon"><i class="fa-solid fa-indian-rupee-sign"></i></div>
            <div>
              <div class="stat-value">Rs {{ number_format($stats['revenue'], 2) }}</div>
              <div class="stat-label">Revenue</div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="stat-card glass-card">
            <div class="stat-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <div>
              <div class="stat-value">{{ number_format($stats['lowStock']) }}</div>
              <div class="stat-label">Low Stock</div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4 mb-4">
        <div class="col-lg-8">
          <div class="glass-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="mb-0">Sales Analytics</h5>
              <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-primary">Reports</a>
            </div>
            <canvas id="salesChart" height="120"></canvas>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="glass-card p-4 h-100">
            <h5 class="mb-3">Low Stock Alerts</h5>
            @forelse ($lowStockProducts as $product)
              <div class="low-stock-alert">
                <i class="fa-solid fa-triangle-exclamation text-danger"></i>
                <span>
                  <b>{{ $product->name }}</b> ({{ $product->company->name ?? '-' }})<br>
                  <span class="text-danger">Only {{ $product->stock }} left</span>
                </span>
              </div>
            @empty
              <div class="text-success">All stocks are healthy.</div>
            @endforelse
          </div>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-lg-8">
          <div class="glass-card p-4 recent-bills-table">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="mb-0">Recent Bills</h5>
              <a href="{{ route('billing.index') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead>
                  <tr>
                    <th>Invoice</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Items</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($recentInvoices as $invoice)
                    <tr>
                      <td><a href="{{ route('billing.show', $invoice) }}">{{ $invoice->invoice_no }}</a></td>
                      <td>{{ $invoice->created_at->format('d M Y') }}</td>
                      <td>{{ $invoice->customer->name ?? 'Walk-in' }}</td>
                      <td>Rs {{ number_format((float) $invoice->total, 2) }}</td>
                      <td>{{ $invoice->items->sum('qty') }}</td>
                    </tr>
                  @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No bills yet.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="d-flex flex-column gap-3">
            <a href="/products" class="btn btn-lg btn-success quick-action"><i class="fa-solid fa-plus me-2"></i>Add Product</a>
            <a href="/companies" class="btn btn-lg btn-outline-primary quick-action"><i class="fa-solid fa-building me-2"></i>Manage Companies</a>
            <a href="/categories" class="btn btn-lg btn-outline-primary quick-action"><i class="fa-solid fa-layer-group me-2"></i>Manage Categories</a>
            <a href="/subcategories" class="btn btn-lg btn-outline-primary quick-action"><i class="fa-solid fa-sitemap me-2"></i>Manage Subcategories</a>
            <a href="{{ route('billing.create') }}" class="btn btn-lg btn-primary quick-action"><i class="fa-solid fa-file-invoice-dollar me-2"></i>Create Invoice</a>
            <a href="{{ route('billing.history') }}" class="btn btn-lg btn-danger quick-action"><i class="fa-solid fa-arrow-trend-down me-2"></i>Stock Out History</a>
            <a href="{{ route('reports.index') }}" class="btn btn-lg btn-outline-success quick-action"><i class="fa-solid fa-chart-line me-2"></i>Reports</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="globalLoader" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background:rgba(255,255,255,0.7);z-index:9999;display:none;">
    <div class="loader"></div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <script>
    window.csrfField = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
    const chartLabels = @json($chartLabels);
    const chartSales = @json($chartSales);
    const chartRevenue = @json($chartRevenue);
  </script>
  <script src="{{ asset('assets/js/components.js')}}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
  <script>
    document.getElementById('sidebar-container').innerHTML = window.renderSidebar('dashboard');
    document.getElementById('navbar-container').innerHTML = window.renderNavbar('Dashboard');

    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: chartLabels,
        datasets: [{
          label: 'Invoices',
          data: chartSales,
          borderColor: '#2563EB',
          backgroundColor: 'rgba(37,99,235,0.08)',
          tension: 0.4,
          fill: true,
          pointRadius: 4,
          pointBackgroundColor: '#2563EB'
        }, {
          label: 'Revenue',
          data: chartRevenue,
          borderColor: '#10B981',
          backgroundColor: 'rgba(16,185,129,0.08)',
          tension: 0.4,
          fill: false,
          yAxisID: 'revenue',
          pointRadius: 4,
          pointBackgroundColor: '#10B981'
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: true } },
        scales: {
          y: { beginAtZero: true, title: { display: true, text: 'Invoices' } },
          revenue: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, title: { display: true, text: 'Revenue' } }
        }
      }
    });
  </script>
</body>
</html>
