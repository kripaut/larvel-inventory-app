<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>InventoryPro - Billing</title>
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
        <h3 class="mb-0">Create Invoice</h3>
        <a href="{{ route('billing.index') }}" class="btn btn-outline-primary"><i class="fa-solid fa-clock-rotate-left me-2"></i>Bills</a>
      </div>

      @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
      @endif

      <form method="POST" action="{{ route('billing.store') }}" class="glass-card p-4" id="invoiceForm">
        @csrf
        <div class="row g-3 mb-4">
          <div class="col-md-4">
            <label class="form-label">Customer Name</label>
            <input type="text" name="customer_name" class="form-control" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Phone</label>
            <input type="text" name="customer_phone" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label">Email</label>
            <input type="email" name="customer_email" class="form-control">
          </div>
          <div class="col-md-2">
            <label class="form-label">Payment</label>
            <select name="payment_method" class="form-select">
              <option value="cash">Cash</option>
              <option value="upi">UPI</option>
              <option value="card">Card</option>
              <option value="bank">Bank</option>
            </select>
          </div>
          <div class="col-md-12">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" role="switch" id="gstApplied" name="gst_applied" value="1" checked>
              <label class="form-check-label" for="gstApplied">Apply GST</label>
            </div>
          </div>
        </div>

        <div class="table-responsive mb-3">
          <table class="table align-middle" id="itemsTable">
            <thead>
              <tr>
                <th style="min-width:320px">Product</th>
                <th>Stock</th>
                <th>Qty</th>
                <th>Price</th>
                <th class="gst-col">GST %</th>
                <th>Line Total</th>
                <th></th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>

        <button type="button" class="btn btn-outline-primary mb-4" id="addRow"><i class="fa-solid fa-plus me-2"></i>Add Item</button>

        <div class="row justify-content-end">
          <div class="col-lg-4">
            <div class="border rounded p-3 bg-white">
              <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><strong id="baseTotal">0.00</strong></div>
              <div class="d-flex justify-content-between mb-2 gst-total-row"><span>GST</span><strong id="gstTotal">0.00</strong></div>
              <label class="form-label">Final Total Editable</label>
              <input type="number" step="0.01" min="0" name="final_total" id="finalTotal" class="form-control form-control-lg fw-bold">
              <small class="text-muted">You can change final total before saving invoice.</small>
            </div>
          </div>
        </div>

        <div class="text-end mt-4">
          <button type="submit" class="btn btn-primary btn-lg"><i class="fa-solid fa-file-invoice me-2"></i>Save Invoice</button>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
  <script>
    window.csrfField = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
    const products = @json($productsForBilling);
  </script>
  <script src="{{ asset('assets/js/components.js')}}"></script>
  <script>
    document.getElementById('sidebar-container').innerHTML = window.renderSidebar('billing');
    document.getElementById('navbar-container').innerHTML = window.renderNavbar('Billing');

    const tbody = document.querySelector('#itemsTable tbody');
    const gstSwitch = document.getElementById('gstApplied');
    const finalTotal = document.getElementById('finalTotal');
    let rowIndex = 0;
    let totalEdited = false;

    finalTotal.addEventListener('input', () => totalEdited = true);
    gstSwitch.addEventListener('change', calculateTotals);
    document.getElementById('addRow').addEventListener('click', () => addRow());

    function productOptions() {
      return products.map(product => {
        const searchText = [product.name, product.company, product.category, product.subcategory].join(' ').toLowerCase();
        const label = [product.name, product.company, product.category, product.subcategory].filter(Boolean).join(' - ');
        return `<option value="${product.id}" data-stock="${product.stock}" data-price="${product.price}" data-gst="${product.gst}" data-search="${searchText}">${label}</option>`;
      }).join('');
    }

    function addRow() {
      const index = rowIndex++;
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>
          <input type="text" class="form-control form-control-sm mb-1 product-search" placeholder="Search name, company, category, sub-category">
          <select name="items[${index}][product_id]" class="form-select product-select" required>${productOptions()}</select>
        </td>
        <td class="stock-cell text-muted">0</td>
        <td><input type="number" name="items[${index}][qty]" class="form-control qty-input" min="1" value="1" required></td>
        <td><input type="number" name="items[${index}][price]" class="form-control price-input" min="0" step="0.01" required></td>
        <td class="gst-col gst-cell">0</td>
        <td class="line-total fw-semibold">0.00</td>
        <td><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="fa-solid fa-trash"></i></button></td>
      `;
      tbody.appendChild(tr);
      bindRow(tr);
      syncProduct(tr);
      calculateTotals();
    }

    function bindRow(tr) {
      tr.querySelector('.product-select').addEventListener('change', () => {
        syncProduct(tr);
        calculateTotals();
      });
      tr.querySelector('.product-search').addEventListener('input', () => filterProductOptions(tr));
      tr.querySelector('.qty-input').addEventListener('input', calculateTotals);
      tr.querySelector('.price-input').addEventListener('input', calculateTotals);
      tr.querySelector('.remove-row').addEventListener('click', () => {
        tr.remove();
        calculateTotals();
      });
    }

    function filterProductOptions(tr) {
      const term = tr.querySelector('.product-search').value.trim().toLowerCase();
      const select = tr.querySelector('.product-select');
      let firstVisible = null;

      Array.from(select.options).forEach(option => {
        const match = !term || option.dataset.search.includes(term);
        option.hidden = !match;
        option.disabled = !match;
        if (match && !firstVisible) firstVisible = option;
      });

      if (select.selectedOptions[0]?.disabled && firstVisible) {
        select.value = firstVisible.value;
        syncProduct(tr);
      }

      calculateTotals();
    }

    function syncProduct(tr) {
      const selected = tr.querySelector('.product-select').selectedOptions[0];
      tr.querySelector('.stock-cell').textContent = selected.dataset.stock;
      tr.querySelector('.price-input').value = Number(selected.dataset.price).toFixed(2);
      tr.querySelector('.gst-cell').textContent = Number(selected.dataset.gst).toFixed(2);
    }

    function calculateTotals() {
      let base = 0;
      let gst = 0;
      const gstApplied = gstSwitch.checked;
      document.querySelectorAll('.gst-col, .gst-total-row').forEach(el => el.style.display = gstApplied ? '' : 'none');

      tbody.querySelectorAll('tr').forEach(tr => {
        const selected = tr.querySelector('.product-select').selectedOptions[0];
        const qty = Number(tr.querySelector('.qty-input').value || 0);
        const price = Number(tr.querySelector('.price-input').value || 0);
        const gstRate = gstApplied ? Number(selected.dataset.gst || 0) : 0;
        const lineBase = qty * price;
        const lineGst = lineBase * gstRate / 100;
        const total = lineBase + lineGst;
        base += lineBase;
        gst += lineGst;
        tr.querySelector('.line-total').textContent = total.toFixed(2);
      });

      document.getElementById('baseTotal').textContent = base.toFixed(2);
      document.getElementById('gstTotal').textContent = gst.toFixed(2);
      if (!totalEdited) {
        finalTotal.value = (base + gst).toFixed(2);
      }
    }

    addRow();
  </script>
</body>
</html>
