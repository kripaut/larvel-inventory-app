<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>InventoryPro - Bills</title>
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <div id="sidebar-container"></div>
  <div class="main-content">
    <div id="navbar-container"></div>
    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Bills</h3>
        <div class="d-flex gap-2">
          <a href="{{ route('billing.history') }}" class="btn btn-outline-primary"><i class="fa-solid fa-list me-2"></i>Item History</a>
          <a href="{{ route('billing.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i>Create Invoice</a>
        </div>
      </div>

      @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      <div class="glass-card p-4">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Invoice</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Items</th>
                <th>GST</th>
                <th>Total</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($invoices as $invoice)
                <tr>
                  <td class="fw-semibold">{{ $invoice->invoice_no }}</td>
                  <td>{{ $invoice->created_at->format('d M Y') }}</td>
                  <td>{{ $invoice->customer->name ?? 'Walk-in' }}</td>
                  <td>{{ $invoice->items->sum('qty') }}</td>
                  <td>
                    <span class="badge {{ $invoice->gst_applied ? 'bg-success' : 'bg-secondary' }}">{{ $invoice->gst_applied ? 'Applied' : 'No GST' }}</span>
                  </td>
                  <td>Rs {{ number_format((float) $invoice->total, 2) }}</td>
                  <td>
                    <div class="btn-group btn-group-sm">
                      <a href="{{ route('billing.show', $invoice) }}" class="btn btn-outline-primary">View</a>
                      <a href="{{ route('billing.pdf', $invoice) }}" class="btn btn-outline-secondary">PDF</a>
                      <a href="{{ route('billing.excel', $invoice) }}" class="btn btn-outline-success">Excel</a>
                    </div>
                  </td>
                </tr>
              @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No bills yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        {{ $invoices->links() }}
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
  <script>window.csrfField = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';</script>
  <script src="{{ asset('assets/js/components.js')}}"></script>
  <script>
    document.getElementById('sidebar-container').innerHTML = window.renderSidebar('billing');
    document.getElementById('navbar-container').innerHTML = window.renderNavbar('Bills');
  </script>
</body>
</html>
