<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function loginAdmin() {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // coba login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // kalau role-nya admin (opsional, kalau kamu punya kolom role di tabel users)
            if (Auth::user()->role === 'admin') {
                return redirect()->intended('/');
            } else {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun ini bukan admin.',
                ]);
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function logout()
    {
        Auth::logout(); // keluarin user
        return redirect('/'); // arahkan ke home atau login
    }
}
