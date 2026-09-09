<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /** Affiche le formulaire de connexion */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /** Traite la soumission du formulaire de connexion */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'email.required'    => 'L\'adresse email est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        $credentials = [
            'email'    => $request->email,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->route('dashboard')->with('success', 'Bienvenue, ' . Auth::user()->prenom . ' !');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Ces identifiants ne correspondent à aucun compte.']);
    }

    /** Déconnecte l'utilisateur */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Vous avez été déconnecté avec succès.');
    }
}
