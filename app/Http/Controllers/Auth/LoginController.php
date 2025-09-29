<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Cari user berdasarkan username
        $user = User::where('username', $request->username)->first();

        // Cek password (plain, karena di SQL tidak di-hash)
        if ($user && $user->password === $request->password) {
            Session::put('user', $user);

            // Cek id_ruangan untuk redirect
            if ($user->id_ruangan === 'SP00') {
                // Superadmin
                return redirect()->route('superadmin.dashboard');
            } else {
                // Admin ruangan lain
                return redirect()->route('admin.dashboard');
            }
        }

        return back()->withErrors(['login' => 'Username atau password salah']);
    }

    public function logout()
    {
        Session::forget('user');
        return redirect('/login');
    }
}
