<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvitationMail;
use DB;

class UserInviteController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except(['accept', 'acceptSubmit']);
        $this->middleware('permission:invites.view')->only(['index', 'show']);
        $this->middleware('permission:invites.create')->only(['create', 'store']);
        $this->middleware('permission:invites.update')->only(['edit', 'update']);
        $this->middleware('permission:invites.delete')->only(['destroy']);
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('SuperAdmin')) {
            $invitations = Invitation::with(['company', 'inviter'])->latest()->paginate(10);
            $companies = Company::orderBy('name')->get();
        } elseif ($user->hasRole('Admin')) {
            $invitations = Invitation::where('invite_by', $user->id)
                ->with(['company', 'inviter'])
                ->latest()
                ->paginate(10);
            $companies = Company::where('id', $user->company_id)->get();
        } else {
            abort(403, 'You are not allowed to view invitations.');
        }
        return view('invite.index', compact('invitations', 'companies'));
    }
    public function create()
    {

    }
    public function store(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'email' => 'required|email',
            'role' => 'required|in:Admin,Member',
            'company_id' => 'required|exists:companies,id',
            '__form' => 'nullable|string',
        ]);

        $email = strtolower($validated['email']);
        $role = $validated['role'];
        $companyId = (int) $validated['company_id'];

        if (User::where('email', $email)->exists()) {
            return back()->withInput()->with('error', 'A user with that email already exists.');
        }
        $pending = Invitation::where('email', $email)
            ->where('company_id', $companyId)
            ->where('role', $role)
            ->exists();

        if ($pending) {
            return back()->withInput()->with('error', 'An invitation for this email, company and role already exists.');
        }

        if ($role === 'Admin') {
            if (!$user->hasRole('SuperAdmin')) {
                abort(403, 'Only SuperAdmin can invite an Admin.');
            }
        }

        if ($role === 'Member') {
            if ($user->hasRole('Admin')) {
                if ($user->company_id !== $companyId) {
                    abort(403, 'Admin can only invite Members within their own company.');
                }
            } elseif (!$user->hasRole('SuperAdmin')) {
                abort(403, 'You are not allowed to send invites.');
            }
        }
        $token = (string) Str::uuid();

        $invite = Invitation::create([
            'email' => $email,
            'role' => $role,
            'company_id' => $companyId,
            'token' => $token,
            'invite_by' => $user->id,
        ]);

        return redirect()->route('invite.index')->with('success', 'Invitation created successfully.');
    }

    public function destroy(Invitation $invite)
    {
        $user = Auth::user();

        if ($user->hasRole('SuperAdmin')) {
        } elseif ($user->hasRole('Admin')) {
            if ($invite->company_id !== $user->company_id) {
                abort(403, 'You are not allowed to delete this invitation.');
            }
        } elseif ($invite->invited_by === $user->id) {
        } else {
            abort(403, 'You are not allowed to delete this invitation.');
        }

        $invite->delete();

        return back()->with('success', 'Invitation deleted.');
    }

    public function accept($token)
    {
        $invite = Invitation::where('token', $token)->first();

        if (!$invite) {
            return view('invite.expired')->with('message', 'Invalid or expired invitation link.');
        }

        $expiresAt = $invite->created_at->addHours(24);

        if (now()->greaterThan($expiresAt)) {
            return view('invite.expired')->with('message', 'This invitation link has expired. It was valid for 24 hours only.');
        }

        if (!empty($invite->accepted_at)) {
            return view('invite.expired')->with('message', 'This invitation link has already been used.');
        }

        return view('invite.accept', compact('invite'));
    }


    public function acceptSubmit(Request $request, $token)
    {
        $invite = Invitation::where('token', $token)->firstOrFail();

        if (!auth()->check() && User::where('email', $invite->email)->exists()) {
            return redirect()->route('login')
                ->with('error', 'A user with this invited email already exists. Please login to accept the invitation.');
        }

        if (auth()->check() && auth()->user()->email !== $invite->email) {
            return back()->with('error', 'You are logged in with a different email. Please logout and accept using the invited email.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:191',
            'password' => 'required|confirmed|min:6',
        ]);
        \DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $invite->email,
                'password' => \Hash::make($data['password']),
                'company_id' => $invite->company_id ?? null,
                'email_verified_at' => now(),
            ]);
            $roleName = $invite->role;
            if (method_exists($user, 'assignRole')) {
                $user->assignRole($roleName);
            } elseif (method_exists($user, 'syncRoles')) {
                $user->syncRoles([$roleName]);
            } else {
                if (property_exists($user, 'role') || in_array('role', $user->getFillable())) {
                    $user->role = $roleName;
                    $user->save();
                }
            }
            auth()->login($user);
            $invite->accepted_at = now();
            $invite->save();

            \DB::commit();

            return redirect()->route('dashboard')->with('success', 'Invitation accepted. Welcome!');
        } catch (\Throwable $e) {
            \DB::rollBack();
            \Log::error('Invite accept failed: ' . $e->getMessage(), [
                'token' => $token,
                'exception' => $e,
            ]);

            return back()->withInput()->with('error', 'Something went wrong while accepting the invitation. Please try again or contact support.');
        }
    }

}