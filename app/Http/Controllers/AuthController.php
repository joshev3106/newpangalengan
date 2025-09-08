<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Tampilkan form login
    public function create()
    {
        return view('auth.login');
    }

    // Proses login
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required'],
        ], [], [
            'email'    => 'Email',
            'password' => 'Kata sandi',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            // amankan sesi
            $request->session()->regenerate();

            // redirect ke halaman yang sempat diminta / fallback
            return redirect()->intended(route('stunting.index'));
        }

        return back()
            ->withInput($request->only('email','remember'))
            ->withErrors(['email' => 'Kredensial tidak cocok.']);
    }

    // Logout
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('ok','Berhasil keluar.');
    }
}
