<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Company;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        return view('products');
    }

    public function export(Request $request)
    {
        $query = Product::with('company', 'category', 'subcategory');
        $this->applyFilters($query, $request);

        $products = $query->orderBy('id', 'desc')->get();
        $filename = 'products-' . now()->format('Ymd-His') . '.xls';

        return response()
            ->view('products-export', compact('products'))
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    public function importTemplate()
    {
        $csv = "name,company,category,subcategory,stock,buy_price,sell_price,gst\n";
        $csv .= "Sample Product,Ceramic World,Tiles,300x300,10,100,120,18\n";

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="products-import-template.csv"');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:4096',
        ]);

        $handle = fopen($request->file('file')->getRealPath(), 'r');
        $header = fgetcsv($handle);

        if (!$header) {
            return response()->json(['success' => false, 'message' => 'Import file is empty.'], 422);
        }

        $header = array_map(fn ($value) => strtolower(trim($value)), $header);
        $required = ['name', 'company', 'category', 'subcategory', 'stock', 'buy_price', 'sell_price', 'gst'];
        $missing = array_diff($required, $header);

        if ($missing) {
            return response()->json([
                'success' => false,
                'message' => 'Missing columns: ' . implode(', ', $missing),
            ], 422);
        }

        $created = 0;

        DB::transaction(function () use ($handle, $header, &$created) {
            while (($row = fgetcsv($handle)) !== false) {
                if (count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0) {
                    continue;
                }

                $data = array_combine($header, array_slice(array_pad($row, count($header), null), 0, count($header)));

                $company = Company::firstOrCreate(['name' => trim($data['company'])]);
                $category = Category::firstOrCreate([
                    'company_id' => $company->id,
                    'name' => trim($data['category']),
                ]);
                $subcategory = Subcategory::firstOrCreate([
                    'category_id' => $category->id,
                    'name' => trim($data['subcategory']),
                ]);

                Product::create([
                    'name' => trim($data['name']),
                    'company_id' => $company->id,
                    'category_id' => $category->id,
                    'subcategory_id' => $subcategory->id,
                    'stock' => max(0, (int) $data['stock']),
                    'buy_price' => max(0, (float) $data['buy_price']),
                    'sell_price' => max(0, (float) $data['sell_price']),
                    'gst' => min(28, max(0, (float) $data['gst'])),
                ]);

                $created++;
            }
        });

        fclose($handle);

        return response()->json([
            'success' => true,
            'message' => "{$created} products imported successfully.",
        ]);
    }

    // GET /api/products - paginated JSON list with filters
    public function apiIndex(Request $request)
    {
        $query = Product::with('company', 'category', 'subcategory');

        $this->applyFilters($query, $request);

        $query->orderBy('id', 'desc');

        $perPage = (int) $request->input('per_page', 5);
        if ($perPage <= 0) {
            $perPage = 5;
        }

        $paginated = $query->paginate($perPage)->withQueryString();

        $data = collect($paginated->items())->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'company' => $p->company->name ?? null,
                'company_id' => $p->company_id,
                'category' => $p->category->name ?? null,
                'category_id' => $p->category_id,
                'subcategory' => $p->subcategory->name ?? null,
                'subcategory_id' => $p->subcategory_id,
                'stock' => $p->stock,
                'buy_price' => $p->buy_price,
                'sell_price' => $p->sell_price,
                'gst' => $p->gst,
                'image' => $p->image ? asset('storage/' . $p->image) : 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85',
                'created_at' => $p->created_at->format('Y-m-d'),
            ];
        });

        return response()->json([
            'data' => $data,
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'from' => $paginated->firstItem(),
                'to' => $paginated->lastItem(),
            ],
        ]);
    }

    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('company', fn ($c) => $c->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('subcategory', fn ($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->input('company_id'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('subcategory_id')) {
            $query->where('subcategory_id', $request->input('subcategory_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        if ($request->filled('month')) {
            $month = $request->input('month');
            $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$month]);
        }

        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->input('year'));
        }
    }

    // GET /api/company-categories - hierarchy for dropdowns
    public function companyCategoryData()
    {
        $companies = Company::with('categories.subcategories')->orderBy('name')->get();

        $data = $companies->map(function ($c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'categories' => $c->categories->map(function ($cat) {
                    return [
                        'id' => $cat->id,
                        'name' => $cat->name,
                        'subcategories' => $cat->subcategories->map(fn($s) => [
                            'id' => $s->id,
                            'name' => $s->name,
                        ]),
                    ];
                }),
            ];
        });

        return response()->json($data);
    }

    // POST /products - create
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
            'stock' => 'required|integer|min:0',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'gst' => 'required|numeric|min:0|max:28',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($validated);

        return response()->json(['success' => true, 'product' => $product]);
    }

    // PUT /products/{product} - update
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
            'stock' => 'required|integer|min:0',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'gst' => 'required|numeric|min:0|max:28',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return response()->json(['success' => true, 'product' => $product]);
    }

    // DELETE /products/{product}
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['success' => true]);
    }
}
