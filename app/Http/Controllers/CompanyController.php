<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    // GET /api/companies
    public function index()
    {
        return response()->json(Company::orderBy('name')->get());
    }

    // POST /api/companies
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:companies,name',
        ]);

        $company = Company::create($validated);

        return response()->json(['success' => true, 'company' => $company]);
    }

    // PUT /api/companies/{company}
    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:companies,name,' . $company->id,
        ]);

        $company->update($validated);

        return response()->json(['success' => true, 'company' => $company]);
    }

    // DELETE /api/companies/{company}
    public function destroy(Company $company)
    {
        $company->delete();
        return response()->json(['success' => true]);
    }
}