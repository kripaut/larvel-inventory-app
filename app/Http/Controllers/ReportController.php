<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->buildReportData($request);

        return view('reports.index', $data);
    }

    public function export(Request $request)
    {
        $data = $this->buildReportData($request);
        $filename = 'reports-' . now()->format('Ymd-His') . '.xls';

        return response()
            ->view('reports.export', $data)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    private function buildReportData(Request $request): array
    {
        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input('to', now()->format('Y-m-d'));
        $start = $from . ' 00:00:00';
        $end = $to . ' 23:59:59';

        $invoiceQuery = Invoice::with(['customer', 'items.product'])
            ->whereBetween('created_at', [$start, $end]);

        $invoices = (clone $invoiceQuery)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $allInvoices = (clone $invoiceQuery)->get();

        $itemQuery = InvoiceItem::with(['invoice.customer', 'product.company', 'product.category', 'product.subcategory'])
            ->whereHas('invoice', fn ($query) => $query->whereBetween('created_at', [$start, $end]));

        $items = (clone $itemQuery)->get();
        $lowStockProducts = Product::with(['company', 'category', 'subcategory'])
            ->where('stock', '<', 15)
            ->orderBy('stock')
            ->get();

        $topProducts = $items
            ->groupBy('product_id')
            ->map(function ($rows) {
                $first = $rows->first();

                return [
                    'name' => $first->product->name ?? 'Deleted product',
                    'company' => $first->product->company->name ?? '',
                    'qty' => $rows->sum('qty'),
                    'total' => $rows->sum(fn ($item) => (float) $item->subtotal),
                ];
            })
            ->sortByDesc('qty')
            ->values()
            ->take(10);

        $summary = [
            'invoiceCount' => $allInvoices->count(),
            'revenue' => $allInvoices->sum(fn ($invoice) => (float) $invoice->total),
            'gstInvoices' => $allInvoices->where('gst_applied', true)->count(),
            'nonGstInvoices' => $allInvoices->where('gst_applied', false)->count(),
            'itemsSold' => $items->sum('qty'),
            'stockValue' => Product::all()->sum(fn ($product) => (float) $product->stock * (float) $product->buy_price),
            'lowStockCount' => $lowStockProducts->count(),
        ];

        return compact('from', 'to', 'summary', 'invoices', 'items', 'topProducts', 'lowStockProducts');
    }
}
