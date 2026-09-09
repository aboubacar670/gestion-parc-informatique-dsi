<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfilController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('profil.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nom'       => 'required|string|max:100',
            'prenom'    => 'required|string|max:100',
            'telephone' => 'nullable|string|max:20',
        ]);

        $user->nom       = $request->nom;
        $user->prenom    = $request->prenom;
        $user->telephone = $request->telephone;

        if ($request->filled('mots_de_passe')) {
            $request->validate([
                'mots_de_passe' => 'min:6|confirmed',
            ]);
            $user->mots_de_passe = Hash::make($request->mots_de_passe);
        }

        $user->save();

        return back()->with('success', 'Profil mis à jour avec succès.');
    }
}