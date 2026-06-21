// Reusable Components for Inventory System
// Sidebar, Navbar, Footer, etc.

// Sidebar HTML
window.renderSidebar = function(activePage = '') {
  return `
    <nav class="sidebar d-flex flex-column p-2">
      <div class="sidebar-header mb-4 d-flex align-items-center">
        <i class="fa-solid fa-boxes-stacked fa-lg me-2 text-primary"></i>
        <span class="fw-bold">InventoryPro</span>
      </div>
      <ul class="nav nav-pills flex-column mb-auto">
        <li><a href="/dashboard" class="nav-link ${activePage==='dashboard'?'active':''}"><i class="fa-solid fa-gauge-high me-2"></i>Dashboard</a></li>
        <li><a href="/products" class="nav-link ${activePage==='products'?'active':''}"><i class="fa-solid fa-cube me-2"></i>Products</a></li>
        <li><a href="/companies" class="nav-link ${activePage==='companies'?'active':''}"><i class="fa-solid fa-building me-2"></i>Companies</a></li>
        <li><a href="/categories" class="nav-link ${activePage==='categories'?'active':''}"><i class="fa-solid fa-layer-group me-2"></i>Categories</a></li>
        <li><a href="/subcategories" class="nav-link ${activePage==='subcategories'?'active':''}"><i class="fa-solid fa-sitemap me-2"></i>Subcategories</a></li>
        <li><a href="/bills/history" class="nav-link ${activePage==='stock'?'active':''}"><i class="fa-solid fa-warehouse me-2"></i>Stock History</a></li>
        <li><a href="/billing" class="nav-link ${activePage==='billing'?'active':''}"><i class="fa-solid fa-file-invoice-dollar me-2"></i>Billing</a></li>
        <li><a href="/reports" class="nav-link ${activePage==='reports'?'active':''}"><i class="fa-solid fa-chart-line me-2"></i>Reports</a></li>
        <li><a href="notifications.html" class="nav-link ${activePage==='notifications'?'active':''}"><i class="fa-solid fa-bell me-2"></i>Notifications</a></li>
      </ul>
      <div class="mt-auto mb-3 px-2">
        <form action="/logout" method="POST">
          ${window.csrfField || ''}
          <button type="submit" class="btn btn-outline-light w-100"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Logout</button>
        </form>
      </div>
    </nav>
  `;
};

// Top Navbar HTML
window.renderNavbar = function(pageTitle = 'Dashboard') {
  return `
    <nav class="navbar top-navbar navbar-expand navbar-light px-4 py-2">
      <button class="btn btn-link d-lg-none me-3" id="sidebarToggle"><i class="fa-solid fa-bars fa-lg"></i></button>
      <span class="navbar-brand fw-bold text-primary">${pageTitle}</span>
      <div class="ms-auto d-flex align-items-center gap-3">
        <button class="btn btn-link position-relative"><i class="fa-solid fa-bell fa-lg"></i><span class="badge bg-danger position-absolute top-0 start-100 translate-middle p-1 border border-light rounded-circle"></span></button>
        <div class="dropdown">
          <button class="btn btn-link dropdown-toggle" data-bs-toggle="dropdown"><img src="https://lh3.googleusercontent.com/a/default-user=s40" alt="User" class="rounded-circle" width="36" height="36"></button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="settings.html"><i class="fa-solid fa-user me-2"></i>Profile</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <form action="/logout" method="POST" class="px-3">
                ${window.csrfField || ''}
                <button type="submit" class="dropdown-item p-0 border-0 bg-transparent text-start w-100"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Logout</button>
              </form>
            </li>     
          </ul>
        </div>
      </div>
    </nav>
  `;
};
