<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>InventoryPro - Bill Item History</title>
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
        <h3 class="mb-0">Bill Item Transaction History</h3>
        <a href="{{ route('billing.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i>Create Invoice</a>
      </div>
      <div class="glass-card p-4">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Date</th>
                <th>Invoice</th>
                <th>Customer</th>
                <th>Product</th>
                <th>Qty Out</th>
                <th>Price</th>
                <th>GST</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($items as $item)
                <tr>
                  <td>{{ $item->created_at->format('d M Y') }}</td>
                  <td><a href="{{ route('billing.show', $item->invoice) }}">{{ $item->invoice->invoice_no }}</a></td>
                  <td>{{ $item->invoice->customer->name ?? 'Walk-in' }}</td>
                  <td>{{ $item->product->name ?? 'Deleted product' }}</td>
                  <td><span class="badge bg-danger">-{{ $item->qty }}</span></td>
                  <td>Rs {{ number_format((float) $item->price, 2) }}</td>
                  <td>{{ $item->invoice->gst_applied ? number_format((float) $item->gst, 2) . '%' : 'No GST' }}</td>
                  <td>Rs {{ number_format((float) $item->subtotal, 2) }}</td>
                </tr>
              @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No bill transactions yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        {{ $items->links() }}
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
  <script>window.csrfField = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';</script>
  <script src="{{ asset('assets/js/components.js')}}"></script>
  <script>
    document.getElementById('sidebar-container').innerHTML = window.renderSidebar('stock');
    document.getElementById('navbar-container').innerHTML = window.renderNavbar('Bill History');
  </script>
</body>
</html>
