<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use App\Models\Category;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    // GET /api/subcategories?category_id=1
    public function index(Request $request)
    {
        $query = Subcategory::with('category.company');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $subcategories = $query->orderBy('name')->get();

        $data = $subcategories->map(fn($s) => [
            'id' => $s->id,
            'name' => $s->name,
            'category_id' => $s->category_id,
            'category_name' => $s->category->name ?? null,
            'company_id' => $s->category->company_id ?? null,
            'company_name' => $s->category->company->name ?? null,
        ]);

        return response()->json($data);
    }

    // POST /api/subcategories
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        $subcategory = Subcategory::create($validated);

        return response()->json(['success' => true, 'subcategory' => $subcategory]);
    }

    // PUT /api/subcategories/{subcategory}
    public function update(Request $request, Subcategory $subcategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        $subcategory->update($validated);

        return response()->json(['success' => true, 'subcategory' => $subcategory]);
    }

    // DELETE /api/subcategories/{subcategory}
    public function destroy(Subcategory $subcategory)
    {
        $subcategory->delete();
        return response()->json(['success' => true]);
    }
}