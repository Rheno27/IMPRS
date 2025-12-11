<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return Auth::user()->isSuperadmin()
                ? redirect()->route('superadmin.dashboard')
                : redirect()->route('admin.dashboard');
        }
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $request->username)->first();

        if ($user && $user->password === $request->password) {

            Auth::login($user);
            $request->session()->regenerate();

            if ($user->isSuperadmin()) {
                return redirect()->intended(route('superadmin.dashboard'));
            }
            return redirect()->intended(route('admin.dashboard'));
        }
        return back()->withErrors(['login' => 'Username atau password salah']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}