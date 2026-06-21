<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $lowStockLimit = 15;
        $chartLabels = [];
        $chartSales = [];
        $chartRevenue = [];

        for ($i = 7; $i >= 0; $i--) {
            $month = now()->startOfMonth()->subMonths($i);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            $chartLabels[] = $month->format('M Y');
            $chartSales[] = Invoice::whereBetween('created_at', [$start, $end])->count();
            $chartRevenue[] = (float) Invoice::whereBetween('created_at', [$start, $end])->sum('total');
        }

        $stats = [
            'products' => Product::count(),
            'sales' => Invoice::count(),
            'revenue' => (float) Invoice::sum('total'),
            'lowStock' => Product::where('stock', '<', $lowStockLimit)->count(),
            'itemsSold' => InvoiceItem::sum('qty'),
        ];

        $lowStockProducts = Product::with('company')
            ->where('stock', '<', $lowStockLimit)
            ->orderBy('stock')
            ->limit(8)
            ->get();

        $recentInvoices = Invoice::with(['customer', 'items'])
            ->latest()
            ->limit(6)
            ->get();

        return view('dashboard', compact(
            'stats',
            'lowStockProducts',
            'recentInvoices',
            'chartLabels',
            'chartSales',
            'chartRevenue'
        ));
    }
}
