<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Company;
use App\Models\Url;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except(['redirect']);
        $this->middleware('permission:dashboard.view')->only(['index', 'show']);
    }
    public function index(Request $request)
    {
        $user = Auth::user();
        $data = [];

        if ($user->hasRole('SuperAdmin')) {
            $data['total_companies'] = Company::count();
            $data['total_users'] = User::count();
            $data['total_urls'] = Url::count();

            $data['recent_companies'] = Company::latest()->limit(6)->get();
            $data['recent_urls'] = Url::with('creator', 'company')->latest()->limit(8)->get();
        }

        if ($user->hasRole('Admin')) {
            $data['company'] = $user->company;
            $data['members_count'] = User::where('company_id', $user->company_id)->count();
            $data['urls_own_company_count'] = Url::where('company_id', $user->company_id)->count();

            $data['recent_urls'] = Url::where('company_id', $user->company_id)
                ->with('creator', 'company')
                ->latest()->limit(8)->get();
        }

        if ($user->hasRole('Member')) {
            $data['company'] = $user->company;
            $data['my_created_urls_count'] = Url::where('created_by', $user->id)->count();

            $data['recent_urls'] = Url::where('created_by', $user->id)
                ->with('creator', 'company')
                ->latest()->limit(8)->get();
        }

        $data['my_name'] = $user->name;
        $data['my_role'] = $user->getRoleNames()->first() ?? 'User';

        return view('dashboard', $data);
    }
}