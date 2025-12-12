<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:SuperAdmin']);
        $this->middleware('permission:companies.view')->only(['index', 'show']);
        $this->middleware('permission:companies.create')->only(['create', 'store']);
        $this->middleware('permission:companies.update')->only(['edit', 'update']);
        $this->middleware('permission:companies.delete')->only(['destroy']);
    }

    public function index()
    {
        $companies = Company::latest()->paginate(10);

        return view('companies.index', compact('companies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:companies,name',
            'domain' => 'nullable|string',
        ]);

        Company::create([
            'name' => $data['name'],
            'domain' => $data['domain'] ?? null,
        ]);

        return redirect()->route('companies.index')
            ->with('success', 'Company created successfully.');
    }

    public function update(Request $request, Company $company)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:companies,name,' . $company->id,
            'domain' => 'nullable|string',
        ]);

        try {
            $company->update([
                'name' => $data['name'],
                'domain' => $data['domain'] ?? null,
            ]);
        } catch (\Throwable $e) {
            return redirect()->back()
                ->withInput()
                ->with('open_edit_id', $company->id)
                ->with('error', 'Unable to update company.');
        }

        return redirect()->route('companies.index')
            ->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
        $hasUsers = $company->users()->exists();
        $hasUrls = class_exists(Url::class) ? Url::where('company_id', $company->id)->exists() : false;

        if ($hasUsers || $hasUrls) {
            return redirect()->route('companies.index')
                ->with('error', 'Cannot delete — company has users or URLs.');
        }

        $company->delete();
        return redirect()->route('companies.index')
            ->with('success', 'Company deleted successfully.');
    }
}