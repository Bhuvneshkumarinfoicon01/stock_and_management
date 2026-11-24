<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login_view()
    {
        if (auth()->check()) {
            // Redirect based on role
            return match (auth()->user()->role_id) {
                1 => redirect()->route('admin.superadmin_dashboard'),
                2 => redirect()->route('admin.dashboard'),
                3 => redirect()->route('admin.storemanager_dashboard'),
                5 => redirect()->route('admin.clerk_dashboard'),
                default => redirect()->route('login_view'),
            };
        }

        return view('login');
    }

    public function login(Request $request)
    {
        // Validate Login Data first
        $request->validate([
            'email' => 'required|max:50|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if user exists and is disabled (status == 0)
        if ($user && (int) $user->status === 0) {
            return view('admin.partials.authorisation');
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->boolean('remember'))) {
            session()->flash('success', 'Successfully Logged in!');
            return $this->redirectUser();
        }

        // Use query param so error still shows if session is lost (e.g. subdirectory cookie path)
        return redirect()->route('login_view', ['invalid' => 1])
            ->withErrors(['error' => 'Invalid email or password'])
            ->withInput(['email' => $request->email]);
    }

    private function redirectUser()
    {
        $user = Auth::user();

        // For store manager
        if ($user->role_id == 3) {
            if ($user->assigned_store) {
                // Update DB
                $user->selected_store_id = $user->assigned_store;
                $user->save();

                session(['selected_store_id' => $user->assigned_store]);
            }
            return redirect()->route('admin.storemanager_dashboard');
        }
        // For clerk
        if ($user->role_id == 5) {
            if ($user->assigned_store) {
                // Update DB
                $user->selected_store_id = $user->assigned_store;
                $user->save();

                session(['selected_store_id' => $user->assigned_store]);
            }
            return redirect()->route('admin.clerk_dashboard');
        }

        if ($user->selected_store_id) {
            session(['selected_store_id' => $user->selected_store_id]);
        }

        return match ($user->role_id) {
            1 => redirect()->route('admin.superadmin_dashboard'),
            2 => redirect()->route('admin.dashboard'),
            default => redirect('/'),
        };
    }

    public function authenticated(Request $request, $user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('home');  // Ensure a default route exists for other users
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login_view');
    }
}
