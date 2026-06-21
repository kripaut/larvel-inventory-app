<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['customer', 'items'])
            ->latest()
            ->paginate(12);

        return view('billing.index', compact('invoices'));
    }

    public function create()
    {
        $products = Product::with(['company', 'category', 'subcategory'])
            ->orderBy('name')
            ->get();

        $productsForBilling = $products->map(fn ($product) => [
            'id' => $product->id,
            'name' => $product->name,
            'company' => $product->company->name ?? '',
            'category' => $product->category->name ?? '',
            'subcategory' => $product->subcategory->name ?? '',
            'stock' => $product->stock,
            'price' => (float) $product->sell_price,
            'gst' => (float) $product->gst,
        ])->values();

        return view('billing.create', compact('productsForBilling'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'customer_email' => 'nullable|email|max:255',
            'payment_method' => 'required|string|max:50',
            'gst_applied' => 'nullable|boolean',
            'final_total' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $invoice = DB::transaction(function () use ($validated, $request) {
            $gstApplied = $request->boolean('gst_applied');
            $productIds = collect($validated['items'])->pluck('product_id')->unique()->values();
            $products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

            $customer = Customer::create([
                'name' => $validated['customer_name'],
                'phone' => $validated['customer_phone'] ?? null,
                'email' => $validated['customer_email'] ?? null,
            ]);

            $invoice = Invoice::create([
                'invoice_no' => $this->nextInvoiceNo(),
                'customer_id' => $customer->id,
                'payment_method' => $validated['payment_method'],
                'gst_applied' => $gstApplied,
                'total' => 0,
            ]);

            $calculatedTotal = 0;

            foreach ($validated['items'] as $line) {
                $product = $products->get((int) $line['product_id']);
                $qty = (int) $line['qty'];

                if (!$product || $product->stock < $qty) {
                    $productName = $product ? $product->name : 'selected product';
                    abort(422, "Insufficient stock for {$productName}.");
                }

                $price = round((float) $line['price'], 2);
                $baseAmount = round($price * $qty, 2);
                $gstRate = $gstApplied ? (float) $product->gst : 0;
                $gstAmount = round($baseAmount * $gstRate / 100, 2);
                $subtotal = round($baseAmount + $gstAmount, 2);

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $product->id,
                    'qty' => $qty,
                    'price' => $price,
                    'gst' => $gstRate,
                    'subtotal' => $subtotal,
                ]);

                $product->decrement('stock', $qty);
                $calculatedTotal += $subtotal;
            }

            $invoice->update([
                'total' => array_key_exists('final_total', $validated) && $validated['final_total'] !== null
                    ? round((float) $validated['final_total'], 2)
                    : round($calculatedTotal, 2),
            ]);

            return $invoice;
        });

        return redirect()
            ->route('billing.show', $invoice)
            ->with('success', 'Invoice created and stock updated.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'items.product.company', 'items.product.category', 'items.product.subcategory']);

        return view('billing.show', compact('invoice'));
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load(['customer', 'items.product.company']);

        return view('billing.pdf', compact('invoice'));
    }

    public function excel(Invoice $invoice)
    {
        $invoice->load(['customer', 'items.product.company']);
        $filename = $invoice->invoice_no . '.xls';

        return response()
            ->view('billing.excel', compact('invoice'))
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    public function history()
    {
        $items = InvoiceItem::with(['invoice.customer', 'product.company'])
            ->latest()
            ->paginate(20);

        return view('billing.history', compact('items'));
    }

    private function nextInvoiceNo(): string
    {
        do {
            $number = 'INV-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5));
        } while (Invoice::where('invoice_no', $number)->exists());

        return $number;
    }
}
