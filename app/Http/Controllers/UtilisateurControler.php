<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Structure;
use Illuminate\Support\Facades\Hash;

class UtilisateurControler extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['role', 'structure']);

        if ($request->search) {
            $query->where('nom', 'like', '%'.$request->search.'%')
                  ->orWhere('prenom', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%');
        }

        if ($request->role_id) {
            $query->where('role_id', $request->role_id);
        }

        $utilisateurs = $query->paginate(15);
        $roles = Role::all();
        return view('utilisateurs.index', compact('utilisateurs', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        $structures = Structure::all();
        return view('utilisateurs.create', compact('roles', 'structures'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'          => 'required|string|max:100',
            'prenom'       => 'required|string|max:100',
            'email'        => 'required|email|unique:utilisateurs,email',
            'telephone'    => 'nullable|string|max:20',
            'role_id'      => 'required|exists:roles,id',
            'structure_id' => 'nullable|exists:structures,id',
            'mots_de_passe'=> 'required|min:6|confirmed',
        ]);

       User::create([
    'nom'          => $request->nom,
    'prenom'       => $request->prenom,
    'email'        => $request->email,
    'telephone'    => $request->telephone,
    'role_id'      => $request->role_id,
    'structure_id' => $request->structure_id,
    'mots_de_passe'=> Hash::make($request->mots_de_passe),
    'matricule'    => $request->matricule ?? 'USR-' . time(),
]);

        return redirect()->route('utilisateurs.index')
                         ->with('success', 'Utilisateur créé avec succès.');
    }

    public function edit(User $utilisateur)
    {
        $roles = Role::all();
        $structures = Structure::all();
        return view('utilisateurs.edit', compact('utilisateur', 'roles', 'structures'));
    }

    public function update(Request $request, User $utilisateur)
    {
        $request->validate([
            'nom'          => 'required|string|max:100',
            'prenom'       => 'required|string|max:100',
            'email'        => 'required|email|unique:utilisateurs,email,'.$utilisateur->id,
            'telephone'    => 'nullable|string|max:20',
            'role_id'      => 'required|exists:roles,id',
            'structure_id' => 'nullable|exists:structures,id',
        ]);

        $data = $request->only(['nom','prenom','email','telephone','role_id','structure_id']);

        if ($request->filled('mots_de_passe')) {
            $data['mots_de_passe'] = Hash::make($request->mots_de_passe);
        }

        $utilisateur->update($data);

        return redirect()->route('utilisateurs.index')
                         ->with('success', 'Utilisateur modifié avec succès.');
    }

    public function destroy(User $utilisateur)
    {
        $utilisateur->delete();
        return redirect()->route('utilisateurs.index')
                         ->with('success', 'Utilisateur supprimé.');
    }
}
