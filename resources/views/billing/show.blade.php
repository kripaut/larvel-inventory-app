@php
  $baseTotal = $invoice->items->sum(fn($item) => (float) $item->price * $item->qty);
  $lineTotal = $invoice->items->sum(fn($item) => (float) $item->subtotal);
  $gstTotal = $lineTotal - $baseTotal;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $invoice->invoice_no }}</title>
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
        <h3 class="mb-0">Invoice {{ $invoice->invoice_no }}</h3>
        <div class="d-flex gap-2">
          <a href="{{ route('billing.pdf', $invoice) }}" class="btn btn-outline-secondary"><i class="fa-solid fa-file-pdf me-2"></i>PDF</a>
          <a href="{{ route('billing.excel', $invoice) }}" class="btn btn-outline-success"><i class="fa-solid fa-file-excel me-2"></i>Excel</a>
          <a href="{{ route('billing.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i>New</a>
        </div>
      </div>

      <div class="glass-card p-4">
        <div class="row mb-4">
          <div class="col-md-6">
            <h5>Customer</h5>
            <div>{{ $invoice->customer->name ?? 'Walk-in' }}</div>
            <div class="text-muted">{{ $invoice->customer->phone ?? '' }}</div>
            <div class="text-muted">{{ $invoice->customer->email ?? '' }}</div>
          </div>
          <div class="col-md-6 text-md-end">
            <div><strong>Date:</strong> {{ $invoice->created_at->format('d M Y, h:i A') }}</div>
            <div><strong>Payment:</strong> {{ ucfirst($invoice->payment_method) }}</div>
            <div><strong>GST:</strong> {{ $invoice->gst_applied ? 'Applied' : 'Not applied' }}</div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                @if ($invoice->gst_applied)<th>GST %</th>@endif
                <th class="text-end">Total</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($invoice->items as $item)
                <tr>
                  <td>{{ $item->product->name ?? 'Deleted product' }}</td>
                  <td>{{ $item->qty }}</td>
                  <td>Rs {{ number_format((float) $item->price, 2) }}</td>
                  @if ($invoice->gst_applied)<td>{{ number_format((float) $item->gst, 2) }}</td>@endif
                  <td class="text-end">Rs {{ number_format((float) $item->subtotal, 2) }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="row justify-content-end">
          <div class="col-md-4">
            <div class="d-flex justify-content-between"><span>Subtotal</span><strong>Rs {{ number_format($baseTotal, 2) }}</strong></div>
            @if ($invoice->gst_applied)
              <div class="d-flex justify-content-between"><span>GST</span><strong>Rs {{ number_format($gstTotal, 2) }}</strong></div>
            @endif
            <hr>
            <div class="d-flex justify-content-between fs-4"><span>Total</span><strong>Rs {{ number_format((float) $invoice->total, 2) }}</strong></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
  <script>window.csrfField = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';</script>
  <script src="{{ asset('assets/js/components.js')}}"></script>
  <script>
    document.getElementById('sidebar-container').innerHTML = window.renderSidebar('billing');
    document.getElementById('navbar-container').innerHTML = window.renderNavbar('Invoice');
  </script>
</body>
</html>
