<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReportController;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::view('/companies', 'master-data', ['activeTab' => 'companies'])->name('companies.index');
    Route::view('/categories', 'master-data', ['activeTab' => 'categories'])->name('categories.index');
    Route::view('/subcategories', 'master-data', ['activeTab' => 'subcategories'])->name('subcategories.index');

    // Products page + actions
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/export', [ProductController::class, 'export'])->name('products.export');
    Route::get('/products/import-template', [ProductController::class, 'importTemplate'])->name('products.import-template');
    Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);

    // Billing
    Route::get('/billing', [InvoiceController::class, 'create'])->name('billing.create');
    Route::post('/billing', [InvoiceController::class, 'store'])->name('billing.store');
    Route::get('/bills', [InvoiceController::class, 'index'])->name('billing.index');
    Route::get('/bills/history', [InvoiceController::class, 'history'])->name('billing.history');
    Route::get('/bills/{invoice}', [InvoiceController::class, 'show'])->name('billing.show');
    Route::get('/bills/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('billing.pdf');
    Route::get('/bills/{invoice}/excel', [InvoiceController::class, 'excel'])->name('billing.excel');

    // Product APIs
    Route::get('/api/products', [ProductController::class, 'apiIndex']);
    Route::get('/api/company-categories', [ProductController::class, 'companyCategoryData']);

    // Company CRUD API
    Route::get('/api/companies', [CompanyController::class, 'index']);
    Route::post('/api/companies', [CompanyController::class, 'store']);
    Route::put('/api/companies/{company}', [CompanyController::class, 'update']);
    Route::delete('/api/companies/{company}', [CompanyController::class, 'destroy']);

    // Category CRUD API
    Route::get('/api/categories', [CategoryController::class, 'index']);
    Route::post('/api/categories', [CategoryController::class, 'store']);
    Route::put('/api/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/api/categories/{category}', [CategoryController::class, 'destroy']);

    // Subcategory CRUD API
    Route::get('/api/subcategories', [SubcategoryController::class, 'index']);
    Route::post('/api/subcategories', [SubcategoryController::class, 'store']);
    Route::put('/api/subcategories/{subcategory}', [SubcategoryController::class, 'update']);
    Route::delete('/api/subcategories/{subcategory}', [SubcategoryController::class, 'destroy']);
});
