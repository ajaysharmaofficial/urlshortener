<?php

namespace App\Http\Controllers;

use App\Models\Url;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class UrlController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['redirect']);
        $this->middleware('permission:urls.view')->only(['index', 'show']);
        $this->middleware('permission:urls.create')->only(['create', 'store']);
        $this->middleware('permission:urls.update')->only(['edit', 'update']);
        $this->middleware('permission:urls.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $companyFilter = $request->get('company_id');

        $query = Url::with(['creator', 'company'])->latest();

        if ($user->hasRole('SuperAdmin')) {
            if (!empty($companyFilter)) {
                $query->where('company_id', $companyFilter);
            }
            $companies = Company::orderBy('name')->get();

        } elseif ($user->hasRole('Admin')) {
            $query->where('company_id', $user->company_id);
            $companies = collect();

        } elseif ($user->hasRole('Member')) {
            $query->where('created_by', $user->id);
            $companies = collect();

        } else {
            abort(403, 'Unauthorized');
        }
        $urls = $query->paginate(10)->appends($request->query());

        return view('urls.index', compact('urls', 'companies'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasAnyRole(['SuperAdmin', 'Admin', 'Member'])) {
            abort(403, 'Unauthorized to create URLs.');
        }

        $data = $request->validate([
            'original_url' => 'required|url|max:2048',
        ]);

        $companyId = $user->company_id ?? null;

        $shortCode = $this->generateUniqueShortCode();

        $url = Url::create([
            'original_url' => $data['original_url'],
            'short_code' => $shortCode,
            'company_id' => $companyId,
            'created_by' => $user->id,
            'hits' => 0,
        ]);

        return redirect()->route('urls.index')->with('success', 'Short URL created.');
    }


    public function update(Request $request, Url $url)
    {
        $user = Auth::user();

        $allowed = $user->hasRole('SuperAdmin')
            || ($user->hasRole('Admin') && $user->company_id === $url->company_id)
            || ($url->created_by === $user->id);

        if (!$allowed) {
            abort(403, 'Unauthorized to update this URL.');
        }

        $data = $request->validate([
            'original_url' => 'required|url|max:2048',
        ]);

        $url->original_url = $data['original_url'];
        $url->save();

        return redirect()->route('urls.index')->with('success', 'URL updated successfully.');
    }


    public function destroy(Url $url)
    {
        $user = Auth::user();

        $allowed = $user->hasRole('SuperAdmin')
            || ($user->hasRole('Admin') && $user->company_id === $url->company_id)
            || ($url->created_by === $user->id);

        if (!$allowed) {
            abort(403, 'Unauthorized to delete this URL.');
        }

        $url->delete();

        return redirect()->route('urls.index')->with('success', 'URL deleted successfully.');
    }


    public function redirect($shortCode)
    {
        $url = Url::where('short_code', $shortCode)->firstOrFail();

        $url->increment('hits');

        return redirect()->away($url->original_url);
    }


    protected function generateUniqueShortCode(int $length = 6): string
    {
        do {
            $code = Str::random($length);
        } while (Url::where('short_code', $code)->exists());

        return $code;
    }
}