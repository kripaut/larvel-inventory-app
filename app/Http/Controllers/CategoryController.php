<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Company;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // GET /api/categories?company_id=1
    public function index(Request $request)
    {
        $query = Category::with('company');

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->input('company_id'));
        }

        $categories = $query->orderBy('name')->get();

        $data = $categories->map(fn($c) => [
            'id' => $c->id,
            'name' => $c->name,
            'company_id' => $c->company_id,
            'company_name' => $c->company->name ?? null,
        ]);

        return response()->json($data);
    }

    // POST /api/categories
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
        ]);

        $category = Category::create($validated);

        return response()->json(['success' => true, 'category' => $category]);
    }

    // PUT /api/categories/{category}
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
        ]);

        $category->update($validated);

        return response()->json(['success' => true, 'category' => $category]);
    }

    // DELETE /api/categories/{category}
    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(['success' => true]);
    }
}