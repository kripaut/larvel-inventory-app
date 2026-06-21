<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>InventoryPro - Products</title>
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .product-img {
      width: 48px;
      height: 48px;
      object-fit: cover;
      border-radius: 0.75rem;
      box-shadow: 0 2px 8px rgba(30,41,59,0.08);
    }
    .table thead th {
      font-weight: 600;
      color: #1E293B;
    }
    .table tbody td {
      vertical-align: middle;
    }
    .badge-category {
      background: #2563EB22;
      color: #2563EB;
      border-radius: 0.5rem;
      font-size: 0.95rem;
      padding: 0.4em 0.8em;
    }
    .badge-stock {
      border-radius: 0.5rem;
      font-size: 0.95rem;
      padding: 0.4em 0.8em;
    }
    .badge-stock.low {
      background: #EF444422;
      color: #EF4444;
    }
    .badge-stock.ok {
      background: #10B98122;
      color: #10B981;
    }
    .custom-pagination .page-link {
      border-radius: 0.5rem;
      margin: 0 2px;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div id="sidebar-container"></div>
  <div class="main-content">
    <div id="navbar-container"></div>
    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Product Management</h3>
        <div class="d-flex gap-2">
          <button class="btn btn-outline-success" id="exportProducts"><i class="fa-solid fa-file-excel me-2"></i>Export</button>
          <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModal"><i class="fa-solid fa-file-import me-2"></i>Import</button>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal"><i class="fa-solid fa-plus me-2"></i>Add Product</button>
        </div>
      </div>
      <div class="glass-card p-4">
        <div class="row mb-3 g-2">
          <div class="col-md-3">
            <input type="text" id="searchProduct" class="form-control" placeholder="Search products...">
          </div>
          <div class="col-md-3">
            <select id="filterCompany" class="form-select">
              <option value="">All Companies</option>
            </select>
          </div>
          <div class="col-md-3">
            <select id="filterCategory" class="form-select">
              <option value="">All Categories</option>
            </select>
          </div>
          <div class="col-md-3">
            <select id="filterSubcategory" class="form-select">
              <option value="">All Sub-categories</option>
            </select>
          </div>
        </div>
        <div class="row mb-3 g-2">
          <div class="col-md-3">
            <label class="form-label small mb-1">From Date</label>
            <input type="date" id="filterDateFrom" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label small mb-1">To Date</label>
            <input type="date" id="filterDateTo" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label small mb-1">Month</label>
            <input type="month" id="filterMonth" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label small mb-1">Year</label>
            <select id="filterYear" class="form-select">
              <option value="">All Years</option>
            </select>
          </div>
        </div>
        <div class="row mb-3 g-2 align-items-end">
          <div class="col-md-3">
            <label class="form-label small mb-1">Per Page</label>
            <select id="perPageSelect" class="form-select">
              <option value="5">5</option>
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
            </select>
          </div>
          <div class="col-md-9 text-end">
            <button id="clearFilters" class="btn btn-sm btn-outline-secondary">Clear Filters</button>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle" id="productsTable">
            <thead>
              <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Company</th>
                <th>Category</th>
                <th>Sub-category</th>
                <th>Stock</th>
                <th>Buy Price</th>
                <th>Sell Price</th>
                <th>GST (%)</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <!-- JS will populate -->
            </tbody>
          </table>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
          <div id="paginationInfo" class="text-muted small"></div>
          <nav>
            <ul class="pagination custom-pagination mb-0" id="paginationControls"></ul>
          </nav>
        </div>
      </div>
    </div>
  </div>
  <!-- Product Modal -->
  <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content glass-card p-3">
        <div class="modal-header">
          <h5 class="modal-title" id="productModalLabel">Add/Edit Product</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="productForm">
            <div class="row g-3">
              <input type="hidden" id="prodId">
              <div class="col-md-6">
                <label class="form-label">Product Name</label>
                <input type="text" class="form-control" id="prodName" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Company</label>
                <select class="form-select" id="prodCompany" required></select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Category</label>
                <select class="form-select" id="prodCategory" required></select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Sub-category</label>
                <select class="form-select" id="prodSubCategory" required></select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Stock Quantity</label>
                <input type="number" class="form-control" id="prodStock" min="0" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Buy Price (₹)</label>
                <input type="number" step="0.01" class="form-control" id="prodBuyPrice" min="0" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Sell Price (₹)</label>
                <input type="number" step="0.01" class="form-control" id="prodSellPrice" min="0" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">GST (%)</label>
                <input type="number" step="0.01" class="form-control" id="prodGST" min="0" max="28" required>
              </div>
              <div class="col-md-8">
                <label class="form-label">Product Image</label>
                <input type="file" class="form-control" id="prodImage" accept="image/*">
              </div>
            </div>
            <div class="mt-4 d-flex justify-content-end gap-2">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Save Product</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <div id="globalLoader" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background:rgba(255,255,255,0.7);z-index:9999;display:none;">
    <div class="loader"></div>
  </div>
  <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content glass-card p-3">
        <div class="modal-header">
          <h5 class="modal-title" id="importModalLabel">Import Products</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="importForm">
          <div class="modal-body">
            <label class="form-label">Excel CSV File</label>
            <input type="file" class="form-control" id="importFile" accept=".csv,.txt" required>
            <div class="form-text">Columns: name, company, category, subcategory, stock, buy_price, sell_price, gst</div>
            <a href="{{ route('products.import-template') }}" class="btn btn-sm btn-link px-0 mt-2">Download import template</a>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Import</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
  <script>
    window.csrfToken = '{{ csrf_token() }}';
    window.csrfField = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
  </script>
  <script src="{{ asset('assets/js/components.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
  <script>
    // Render sidebar and navbar
    document.getElementById('sidebar-container').innerHTML = window.renderSidebar('products');
    document.getElementById('navbar-container').innerHTML = window.renderNavbar('Products');

    const filterCompany = document.getElementById('filterCompany');
    const filterCategory = document.getElementById('filterCategory');
    const filterSubcategory = document.getElementById('filterSubcategory');
    const filterDateFrom = document.getElementById('filterDateFrom');
    const filterDateTo = document.getElementById('filterDateTo');
    const filterMonth = document.getElementById('filterMonth');
    const filterYear = document.getElementById('filterYear');
    const searchProduct = document.getElementById('searchProduct');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const perPageSelect = document.getElementById('perPageSelect');
    const paginationInfo = document.getElementById('paginationInfo');
    const paginationControls = document.getElementById('paginationControls');
    const exportProducts = document.getElementById('exportProducts');
    const importForm = document.getElementById('importForm');
    const importFile = document.getElementById('importFile');

    const prodId = document.getElementById('prodId');
    const prodName = document.getElementById('prodName');
    const prodCompany = document.getElementById('prodCompany');
    const prodCategory = document.getElementById('prodCategory');
    const prodSubCategory = document.getElementById('prodSubCategory');
    const prodStock = document.getElementById('prodStock');
    const prodBuyPrice = document.getElementById('prodBuyPrice');
    const prodSellPrice = document.getElementById('prodSellPrice');
    const prodGST = document.getElementById('prodGST');
    const prodImage = document.getElementById('prodImage');
    const productForm = document.getElementById('productForm');
    const productModalElement = document.getElementById('productModal');
    const productModal = new bootstrap.Modal(productModalElement);
    const importModal = new bootstrap.Modal(document.getElementById('importModal'));
    let editingProductId = null;
    let currentPage = 1;

    let allProducts = [];
    let companyData = []; // [{id, name, categories:[{id,name,subcategories:[{id,name}]}]}]

    function safeToast(message, type) {
      if (typeof window.showToast === 'function') {
        window.showToast(message, type);
      } else {
        console.log(`[${type}] ${message}`);
      }
    }

    function getCompanyByName(name) {
      return companyData.find(c => c.name === name);
    }
    function getCategoryByName(company, name) {
      return company ? company.categories.find(c => c.name === name) : null;
    }
    function getSubcategoryByName(category, name) {
      return category ? category.subcategories.find(s => s.name === name) : null;
    }

    function populateCompanyOptions(selectElement, includeAll = true) {
      selectElement.innerHTML = '';
      if (includeAll) {
        selectElement.innerHTML = '<option value="">All Companies</option>';
      }
      companyData.forEach(c => {
        selectElement.innerHTML += `<option value="${c.id}">${c.name}</option>`;
      });
    }

    function populateCategoryOptionsForCompany(selectElement, companyId) {
      selectElement.innerHTML = '<option value="">Select category</option>';
      const company = companyData.find(c => c.id === Number(companyId));
      if (!company) return;
      company.categories.forEach(cat => {
        selectElement.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
      });
    }

    function populateSubcategoryOptions(companyId, categoryId) {
      prodSubCategory.innerHTML = '<option value="">Select sub-category</option>';
      const company = companyData.find(c => c.id === Number(companyId));
      if (!company) return;
      const category = company.categories.find(cat => cat.id === Number(categoryId));
      if (!category) return;
      category.subcategories.forEach(sub => {
        prodSubCategory.innerHTML += `<option value="${sub.id}">${sub.name}</option>`;
      });
    }

    function populateFilterCategoryOptions() {
      const previousValue = filterCategory.value;
      filterCategory.innerHTML = '<option value="">All Categories</option>';
      const companyId = filterCompany.value;
      const seen = new Set();

      companyData.forEach(c => {
        if (companyId && c.id !== Number(companyId)) return;
        c.categories.forEach(cat => {
          if (!seen.has(cat.id)) {
            seen.add(cat.id);
            filterCategory.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
          }
        });
      });

      if (seen.has(Number(previousValue))) {
        filterCategory.value = previousValue;
      }
    }

    function populateFilterSubcategoryOptions() {
      const previousValue = filterSubcategory.value;
      filterSubcategory.innerHTML = '<option value="">All Sub-categories</option>';
      const companyId = filterCompany.value;
      const categoryId = filterCategory.value;
      const seen = new Set();

      companyData.forEach(c => {
        if (companyId && c.id !== Number(companyId)) return;
        c.categories.forEach(cat => {
          if (categoryId && cat.id !== Number(categoryId)) return;
          cat.subcategories.forEach(sub => {
            if (!seen.has(sub.id)) {
              seen.add(sub.id);
              filterSubcategory.innerHTML += `<option value="${sub.id}">${sub.name}</option>`;
            }
          });
        });
      });

      if (seen.has(Number(previousValue))) {
        filterSubcategory.value = previousValue;
      }
    }

    function populateFilterYearOptions() {
      filterYear.innerHTML = '<option value="">All Years</option>';
      const currentYear = new Date().getFullYear();
      for (let y = currentYear; y >= currentYear - 5; y--) {
        filterYear.innerHTML += `<option value="${y}">${y}</option>`;
      }
    }

    function resetProductForm() {
      editingProductId = null;
      prodId.value = '';
      prodName.value = '';
      prodCompany.value = '';
      prodCategory.innerHTML = '<option value="">Select category</option>';
      prodSubCategory.innerHTML = '<option value="">Select sub-category</option>';
      prodStock.value = '';
      prodBuyPrice.value = '';
      prodSellPrice.value = '';
      prodGST.value = '';
      prodImage.value = '';
      document.getElementById('productModalLabel').textContent = 'Add Product';
    }

    async function fetchCompanyData() {
      const res = await fetch('/api/company-categories');
      companyData = await res.json();
    }

    function buildFilterParams() {
      const params = new URLSearchParams();

      if (searchProduct.value.trim()) params.append('search', searchProduct.value.trim());
      if (filterCompany.value) params.append('company_id', filterCompany.value);
      if (filterCategory.value) params.append('category_id', filterCategory.value);
      if (filterSubcategory.value) params.append('subcategory_id', filterSubcategory.value);
      if (filterDateFrom.value) params.append('date_from', filterDateFrom.value);
      if (filterDateTo.value) params.append('date_to', filterDateTo.value);
      if (filterMonth.value) params.append('month', filterMonth.value);
      if (filterYear.value) params.append('year', filterYear.value);
      params.append('per_page', perPageSelect.value);
      params.append('page', currentPage);

      return params.toString();
    }

    function buildExportParams() {
      const params = new URLSearchParams(buildFilterParams());
      params.delete('page');
      params.delete('per_page');
      return params.toString();
    }

    let paginationMeta = null;

    async function fetchProducts() {
      const qs = buildFilterParams();
      const res = await fetch(`/api/products?${qs}`);
      const json = await res.json();
      allProducts = json.data || [];
      paginationMeta = json.pagination || null;
    }

    function openProductModal(product) {
      editingProductId = product.id;
      prodId.value = product.id;
      prodName.value = product.name;

      const company = getCompanyByName(product.company);
      prodCompany.value = company ? company.id : '';

      populateCategoryOptionsForCompany(prodCategory, prodCompany.value);
      const category = getCategoryByName(company, product.category);
      prodCategory.value = category ? category.id : '';

      populateSubcategoryOptions(prodCompany.value, prodCategory.value);
      const sub = getSubcategoryByName(category, product.subcategory);
      prodSubCategory.value = sub ? sub.id : '';

      prodStock.value = product.stock;
      prodBuyPrice.value = product.buy_price;
      prodSellPrice.value = product.sell_price;
      prodGST.value = product.gst;
      prodImage.value = '';
      document.getElementById('productModalLabel').textContent = 'Edit Product';
      productModal.show();
    }

    function renderProductsTable(products) {
      const tbody = document.querySelector('#productsTable tbody');
      tbody.innerHTML = '';

      if (products.length === 0) {
        tbody.innerHTML = '<tr><td colspan="10" class="text-center text-muted py-4">No products found</td></tr>';
        return;
      }

      products.forEach(prod => {
        tbody.innerHTML += `<tr>
          <td><img src="${prod.image}" class="product-img"></td>
          <td>${prod.name}</td>
          <td>${prod.company || '-'}</td>
          <td><span class='badge-category'>${prod.category}</span></td>
          <td>${prod.subcategory}</td>
          <td><span class='badge-stock ${prod.stock<15?'low':'ok'}'>${prod.stock}</span></td>
          <td>₹${prod.buy_price}</td>
          <td>₹${prod.sell_price}</td>
          <td>${prod.gst}%</td>
          <td>
            <button class="btn btn-sm btn-outline-primary me-1 edit-product" data-product-id="${prod.id}"><i class="fa-solid fa-pen"></i></button>
            <button class="btn btn-sm btn-outline-danger delete-product" data-product-id="${prod.id}"><i class="fa-solid fa-trash"></i></button>
          </td>
        </tr>`;
      });

      attachProductTableEvents();
    }

    function renderPagination() {
      if (!paginationMeta) {
        paginationInfo.textContent = '';
        paginationControls.innerHTML = '';
        return;
      }

      const { current_page, last_page, total, from, to } = paginationMeta;

      paginationInfo.textContent = total > 0
        ? `Showing ${from} to ${to} of ${total} entries`
        : 'No entries';

      paginationControls.innerHTML = '';

      const addPageItem = (label, page, disabled = false, active = false) => {
        const li = document.createElement('li');
        li.className = `page-item ${disabled ? 'disabled' : ''} ${active ? 'active' : ''}`;
        const a = document.createElement('a');
        a.className = 'page-link';
        a.textContent = label;
        if (!disabled && !active) {
          a.addEventListener('click', () => {
            currentPage = page;
            refreshAndRender();
          });
        }
        li.appendChild(a);
        paginationControls.appendChild(li);
      };

      addPageItem('Prev', current_page - 1, current_page <= 1);

      let startPage = Math.max(1, current_page - 2);
      let endPage = Math.min(last_page, current_page + 2);

      if (startPage > 1) {
        addPageItem('1', 1, false, current_page === 1);
        if (startPage > 2) addPageItem('...', current_page, true);
      }

      for (let p = startPage; p <= endPage; p++) {
        addPageItem(String(p), p, false, p === current_page);
      }

      if (endPage < last_page) {
        if (endPage < last_page - 1) addPageItem('...', current_page, true);
        addPageItem(String(last_page), last_page, false, current_page === last_page);
      }

      addPageItem('Next', current_page + 1, current_page >= last_page);
    }

    function attachProductTableEvents() {
      document.querySelectorAll('.edit-product').forEach(btn => {
        btn.addEventListener('click', function() {
          const productId = parseInt(this.dataset.productId, 10);
          const product = allProducts.find(p => p.id === productId);
          if (product) openProductModal(product);
        });
      });
      document.querySelectorAll('.delete-product').forEach(btn => {
        btn.addEventListener('click', async function() {
          const productId = parseInt(this.dataset.productId, 10);
          if (confirm('Delete this product?')) {
            const res = await fetch(`/products/${productId}`, {
              method: 'DELETE',
              headers: { 'X-CSRF-TOKEN': window.csrfToken }
            });
            const result = await res.json();
            if (result.success) {
              await refreshAndRender();
              safeToast('Product deleted successfully', 'success');
            }
          }
        });
      });
    }

    async function refreshAndRender() {
      await fetchProducts();
      renderProductsTable(allProducts);
      renderPagination();
    }

    function resetToFirstPageAndRefresh() {
      currentPage = 1;
      refreshAndRender();
    }

    async function initProductsPage() {
      await fetchCompanyData();
      populateCompanyOptions(filterCompany);
      populateCompanyOptions(prodCompany, false);
      populateFilterCategoryOptions();
      populateFilterSubcategoryOptions();
      populateFilterYearOptions();
      resetProductForm();
      await refreshAndRender();
    }

    filterCompany.addEventListener('change', () => {
      populateFilterCategoryOptions();
      populateFilterSubcategoryOptions();
      resetToFirstPageAndRefresh();
    });

    filterCategory.addEventListener('change', () => {
      populateFilterSubcategoryOptions();
      resetToFirstPageAndRefresh();
    });

    filterSubcategory.addEventListener('change', resetToFirstPageAndRefresh);

    let searchDebounce;
    searchProduct.addEventListener('input', () => {
      clearTimeout(searchDebounce);
      searchDebounce = setTimeout(resetToFirstPageAndRefresh, 350);
    });

    filterDateFrom.addEventListener('change', resetToFirstPageAndRefresh);
    filterDateTo.addEventListener('change', resetToFirstPageAndRefresh);

    filterMonth.addEventListener('change', () => {
      if (filterMonth.value) filterYear.value = '';
      resetToFirstPageAndRefresh();
    });

    filterYear.addEventListener('change', () => {
      if (filterYear.value) filterMonth.value = '';
      resetToFirstPageAndRefresh();
    });

    perPageSelect.addEventListener('change', resetToFirstPageAndRefresh);

    exportProducts.addEventListener('click', () => {
      const qs = buildExportParams();
      window.location.href = `/products/export${qs ? '?' + qs : ''}`;
    });

    importForm.addEventListener('submit', async event => {
      event.preventDefault();

      if (!importFile.files[0]) return;

      const formData = new FormData();
      formData.append('file', importFile.files[0]);

      const res = await fetch('/products/import', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': window.csrfToken },
        body: formData
      });

      const result = await res.json().catch(() => null);

      if (!res.ok || !result?.success) {
        safeToast(result?.message || 'Import failed', 'danger');
        return;
      }

      safeToast(result.message, 'success');
      importForm.reset();
      importModal.hide();
      await fetchCompanyData();
      populateCompanyOptions(filterCompany);
      populateCompanyOptions(prodCompany, false);
      populateFilterCategoryOptions();
      populateFilterSubcategoryOptions();
      resetToFirstPageAndRefresh();
    });

    clearFiltersBtn.addEventListener('click', () => {
      searchProduct.value = '';
      filterCompany.value = '';
      filterCategory.value = '';
      filterSubcategory.value = '';
      filterDateFrom.value = '';
      filterDateTo.value = '';
      filterMonth.value = '';
      filterYear.value = '';
      populateFilterCategoryOptions();
      populateFilterSubcategoryOptions();
      resetToFirstPageAndRefresh();
    });

    prodCompany.addEventListener('change', function() {
      populateCategoryOptionsForCompany(prodCategory, this.value);
      prodSubCategory.innerHTML = '<option value="">Select sub-category</option>';
    });

    prodCategory.addEventListener('change', function() {
      populateSubcategoryOptions(prodCompany.value, prodCategory.value);
    });

    productForm.addEventListener('submit', async function(event) {
      event.preventDefault();

      const formData = new FormData();
      formData.append('name', prodName.value);
      formData.append('company_id', prodCompany.value);
      formData.append('category_id', prodCategory.value);
      formData.append('subcategory_id', prodSubCategory.value);
      formData.append('stock', prodStock.value);
      formData.append('buy_price', prodBuyPrice.value);
      formData.append('sell_price', prodSellPrice.value);
      formData.append('gst', prodGST.value);
      if (prodImage.files[0]) {
        formData.append('image', prodImage.files[0]);
      }

      let url = '/products';
      if (editingProductId) {
        formData.append('_method', 'PUT');
        url = `/products/${editingProductId}`;
      }

      const res = await fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': window.csrfToken },
        body: formData
      });

      if (!res.ok) {
        const errJson = await res.json().catch(() => null);
        const msg = errJson?.message || 'Something went wrong';
        safeToast(msg, 'danger');
        return;
      }

      const result = await res.json();

      if (result.success) {
        safeToast(editingProductId ? 'Product updated successfully' : 'Product added successfully', 'success');
      } else {
        safeToast('Something went wrong', 'danger');
      }

      resetProductForm();
      productModal.hide();
      await refreshAndRender();
    });

    productModalElement.addEventListener('hidden.bs.modal', resetProductForm);
    document.addEventListener('DOMContentLoaded', initProductsPage);
  </script>
</body>
</html>
