<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>InventoryPro - Master Management</title>
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .management-tabs .nav-link {
      border-radius: 0.75rem;
      color: #475569;
      font-weight: 600;
    }
    .management-tabs .nav-link.active {
      background: #2563EB;
      color: #fff;
    }
    .empty-state {
      color: #64748B;
      padding: 2rem;
      text-align: center;
    }
    .badge-soft {
      background: #2563EB22;
      color: #2563EB;
      border-radius: 0.5rem;
      padding: 0.35rem 0.65rem;
    }
  </style>
</head>
<body>
  <div id="sidebar-container"></div>
  <div class="main-content">
    <div id="navbar-container"></div>
    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 class="mb-1">Master Management</h3>
          <div class="text-muted">Manage companies, categories, and subcategories used by products.</div>
        </div>
      </div>

      <div class="glass-card p-4">
        <ul class="nav nav-pills management-tabs gap-2 mb-4" id="managementTabs">
          <li class="nav-item">
            <button class="nav-link" data-tab="companies" type="button"><i class="fa-solid fa-building me-2"></i>Companies</button>
          </li>
          <li class="nav-item">
            <button class="nav-link" data-tab="categories" type="button"><i class="fa-solid fa-layer-group me-2"></i>Categories</button>
          </li>
          <li class="nav-item">
            <button class="nav-link" data-tab="subcategories" type="button"><i class="fa-solid fa-sitemap me-2"></i>Subcategories</button>
          </li>
        </ul>

        <div class="row g-4">
          <div class="col-lg-4">
            <form id="managementForm" class="glass-card p-3">
              <h5 class="mb-3" id="formTitle">Add Company</h5>
              <input type="hidden" id="editingId">

              <div class="mb-3 d-none" id="companyGroup">
                <label class="form-label">Company</label>
                <select class="form-select" id="companySelect"></select>
              </div>

              <div class="mb-3 d-none" id="categoryGroup">
                <label class="form-label">Category</label>
                <select class="form-select" id="categorySelect"></select>
              </div>

              <div class="mb-3">
                <label class="form-label" id="nameLabel">Name</label>
                <input type="text" class="form-control" id="nameInput" required maxlength="255">
              </div>

              <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-outline-secondary" id="resetFormBtn">Clear</button>
                <button type="submit" class="btn btn-primary" id="saveBtn">Save</button>
              </div>
            </form>
          </div>

          <div class="col-lg-8">
            <div class="d-flex gap-2 mb-3">
              <input type="text" class="form-control" id="searchInput" placeholder="Search...">
              <button type="button" class="btn btn-outline-secondary" id="refreshBtn"><i class="fa-solid fa-rotate"></i></button>
            </div>
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead id="tableHead"></thead>
                <tbody id="tableBody"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
  <script>
    window.csrfToken = '{{ csrf_token() }}';
    window.csrfField = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
    window.initialManagementTab = @json($activeTab ?? 'companies');
  </script>
  <script src="{{ asset('assets/js/components.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
  <script>
    const tabs = {
      companies: {
        title: 'Companies',
        singular: 'Company',
        endpoint: '/api/companies',
        pageUrl: '/companies',
        sidebarKey: 'companies',
      },
      categories: {
        title: 'Categories',
        singular: 'Category',
        endpoint: '/api/categories',
        pageUrl: '/categories',
        sidebarKey: 'categories',
      },
      subcategories: {
        title: 'Subcategories',
        singular: 'Subcategory',
        endpoint: '/api/subcategories',
        pageUrl: '/subcategories',
        sidebarKey: 'subcategories',
      }
    };

    let activeTab = tabs[window.initialManagementTab] ? window.initialManagementTab : 'companies';
    let companies = [];
    let categories = [];
    let subcategories = [];
    let rows = [];

    const managementTabs = document.getElementById('managementTabs');
    const managementForm = document.getElementById('managementForm');
    const formTitle = document.getElementById('formTitle');
    const editingId = document.getElementById('editingId');
    const companyGroup = document.getElementById('companyGroup');
    const categoryGroup = document.getElementById('categoryGroup');
    const companySelect = document.getElementById('companySelect');
    const categorySelect = document.getElementById('categorySelect');
    const nameInput = document.getElementById('nameInput');
    const nameLabel = document.getElementById('nameLabel');
    const searchInput = document.getElementById('searchInput');
    const tableHead = document.getElementById('tableHead');
    const tableBody = document.getElementById('tableBody');
    const resetFormBtn = document.getElementById('resetFormBtn');
    const refreshBtn = document.getElementById('refreshBtn');

    function toast(message, type = 'success') {
      if (typeof window.showToast === 'function') {
        window.showToast(message, type);
      }
    }

    function escapeHtml(value) {
      return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
      }[char]));
    }

    function renderShell() {
      document.getElementById('sidebar-container').innerHTML = window.renderSidebar(tabs[activeTab].sidebarKey);
      document.getElementById('navbar-container').innerHTML = window.renderNavbar(tabs[activeTab].title);
    }

    function setActiveTab(nextTab, updateUrl = true) {
      activeTab = nextTab;
      if (updateUrl) history.pushState({}, '', tabs[activeTab].pageUrl);
      document.querySelectorAll('#managementTabs .nav-link').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.tab === activeTab);
      });
      renderShell();
      resetForm();
      renderTable();
    }

    function fillCompanyOptions(selectedId = '') {
      companySelect.innerHTML = '<option value="">Select company</option>';
      companies.forEach(company => {
        companySelect.innerHTML += `<option value="${company.id}">${escapeHtml(company.name)}</option>`;
      });
      companySelect.value = selectedId ? String(selectedId) : '';
    }

    function fillCategoryOptions(selectedId = '', companyId = '') {
      categorySelect.innerHTML = '<option value="">Select category</option>';
      categories
        .filter(category => !companyId || Number(category.company_id) === Number(companyId))
        .forEach(category => {
          categorySelect.innerHTML += `<option value="${category.id}">${escapeHtml(category.name)}</option>`;
        });
      categorySelect.value = selectedId ? String(selectedId) : '';
    }

    function resetForm() {
      editingId.value = '';
      nameInput.value = '';
      companyGroup.classList.toggle('d-none', activeTab === 'companies');
      categoryGroup.classList.toggle('d-none', activeTab !== 'subcategories');
      nameLabel.textContent = `${tabs[activeTab].singular} Name`;
      formTitle.textContent = `Add ${tabs[activeTab].singular}`;
      fillCompanyOptions();
      fillCategoryOptions();
    }

    function currentDataset() {
      if (activeTab === 'companies') return companies;
      if (activeTab === 'categories') return categories;
      return subcategories;
    }

    function renderTable() {
      rows = currentDataset();
      const search = searchInput.value.trim().toLowerCase();
      const filtered = rows.filter(row => {
        const text = [
          row.name,
          row.company_name,
          row.category_name
        ].filter(Boolean).join(' ').toLowerCase();
        return text.includes(search);
      });

      if (activeTab === 'companies') {
        tableHead.innerHTML = '<tr><th>Name</th><th class="text-end">Actions</th></tr>';
      } else if (activeTab === 'categories') {
        tableHead.innerHTML = '<tr><th>Name</th><th>Company</th><th class="text-end">Actions</th></tr>';
      } else {
        tableHead.innerHTML = '<tr><th>Name</th><th>Company</th><th>Category</th><th class="text-end">Actions</th></tr>';
      }

      if (!filtered.length) {
        tableBody.innerHTML = `<tr><td colspan="4" class="empty-state">No ${tabs[activeTab].title.toLowerCase()} found</td></tr>`;
        return;
      }

      tableBody.innerHTML = filtered.map(row => {
        const commonActions = `
          <td class="text-end">
            <button class="btn btn-sm btn-outline-primary me-1 edit-row" data-id="${row.id}"><i class="fa-solid fa-pen"></i></button>
            <button class="btn btn-sm btn-outline-danger delete-row" data-id="${row.id}"><i class="fa-solid fa-trash"></i></button>
          </td>`;

        if (activeTab === 'companies') {
          return `<tr><td>${escapeHtml(row.name)}</td>${commonActions}</tr>`;
        }

        if (activeTab === 'categories') {
          return `<tr><td>${escapeHtml(row.name)}</td><td><span class="badge-soft">${escapeHtml(row.company_name || '-')}</span></td>${commonActions}</tr>`;
        }

        return `<tr><td>${escapeHtml(row.name)}</td><td>${escapeHtml(row.company_name || '-')}</td><td><span class="badge-soft">${escapeHtml(row.category_name || '-')}</span></td>${commonActions}</tr>`;
      }).join('');

      bindRowActions();
    }

    function bindRowActions() {
      document.querySelectorAll('.edit-row').forEach(button => {
        button.addEventListener('click', () => {
          const row = rows.find(item => Number(item.id) === Number(button.dataset.id));
          if (!row) return;
          editingId.value = row.id;
          nameInput.value = row.name;
          formTitle.textContent = `Edit ${tabs[activeTab].singular}`;

          if (activeTab === 'categories') {
            fillCompanyOptions(row.company_id);
          }

          if (activeTab === 'subcategories') {
            fillCompanyOptions(row.company_id);
            fillCategoryOptions(row.category_id, row.company_id);
          }
        });
      });

      document.querySelectorAll('.delete-row').forEach(button => {
        button.addEventListener('click', async () => {
          if (!confirm(`Delete this ${tabs[activeTab].singular.toLowerCase()}? Linked records may also be removed.`)) return;
          const response = await fetch(`${tabs[activeTab].endpoint}/${button.dataset.id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': window.csrfToken }
          });
          if (!response.ok) {
            toast('Delete failed', 'danger');
            return;
          }
          toast(`${tabs[activeTab].singular} deleted`);
          await loadAll();
          resetForm();
          renderTable();
        });
      });
    }

    async function loadAll() {
      const [companiesRes, categoriesRes, subcategoriesRes] = await Promise.all([
        fetch('/api/companies'),
        fetch('/api/categories'),
        fetch('/api/subcategories')
      ]);
      companies = await companiesRes.json();
      categories = await categoriesRes.json();
      subcategories = await subcategoriesRes.json();
    }

    managementTabs.addEventListener('click', event => {
      const button = event.target.closest('[data-tab]');
      if (!button) return;
      setActiveTab(button.dataset.tab);
    });

    companySelect.addEventListener('change', () => {
      fillCategoryOptions('', companySelect.value);
    });

    searchInput.addEventListener('input', renderTable);
    resetFormBtn.addEventListener('click', resetForm);
    refreshBtn.addEventListener('click', async () => {
      await loadAll();
      resetForm();
      renderTable();
      toast('List refreshed');
    });

    managementForm.addEventListener('submit', async event => {
      event.preventDefault();
      const payload = { name: nameInput.value.trim() };

      if (!payload.name) return;
      if (activeTab === 'categories') payload.company_id = companySelect.value;
      if (activeTab === 'subcategories') payload.category_id = categorySelect.value;

      const isEditing = Boolean(editingId.value);
      const response = await fetch(isEditing ? `${tabs[activeTab].endpoint}/${editingId.value}` : tabs[activeTab].endpoint, {
        method: isEditing ? 'PUT' : 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': window.csrfToken
        },
        body: JSON.stringify(payload)
      });

      if (!response.ok) {
        const error = await response.json().catch(() => null);
        toast(error?.message || 'Save failed', 'danger');
        return;
      }

      toast(`${tabs[activeTab].singular} ${isEditing ? 'updated' : 'added'} successfully`);
      await loadAll();
      resetForm();
      renderTable();
    });

    document.addEventListener('DOMContentLoaded', async () => {
      renderShell();
      await loadAll();
      setActiveTab(activeTab, false);
    });
  </script>
</body>
</html>
