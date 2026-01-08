<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        // Se já estiver logado, redireciona direto
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
{
    $credentials = $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
        $request->session()->regenerate();
        return redirect()->intended('/dashboard');
    }

    return back()->withErrors([
        'email' => 'Email ou senha incorretos.',
    ])->onlyInput('email');
}

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
    public function desvincularMeli(Request $request)
{
    // Limpar os tokens do Mercado Livre
    $user = $request->user();
    $user->token_meli = null;
    $user->refresh_token_meli = null;
    $user->meli_user_id = null;
    $user->save();

    // Redireciona de volta ao dashboard
    return redirect()->route('dashboard')->with('success', 'Conta do Mercado Livre desvinculada com sucesso!');
}
}
