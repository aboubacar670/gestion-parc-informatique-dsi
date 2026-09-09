@extends('layouts.app')

@section('title', 'Modifier Utilisateur')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Modifier Utilisateur</h1>
        <p class="page-subtitle">{{ $utilisateur->prenom }} {{ $utilisateur->nom }}</p>
    </div>
    <a href="{{ route('utilisateurs.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

<div class="card" style="max-width:700px">
    <form method="POST" action="{{ route('utilisateurs.update', $utilisateur) }}">
        @csrf @method('PUT')

        <div class="form-grid">
            <div class="form-group">
                <label>Prénom</label>
                <input type="text" name="prenom" class="form-control"
                    value="{{ old('prenom', $utilisateur->prenom) }}" required>
            </div>
            <div class="form-group">
                <label>Nom</label>
                <input type="text" name="nom" class="form-control"
                    value="{{ old('nom', $utilisateur->nom) }}" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control"
                    value="{{ old('email', $utilisateur->email) }}" required>
            </div>
            <div class="form-group">
                <label>Téléphone</label>
                <input type="text" name="telephone" class="form-control"
                    value="{{ old('telephone', $utilisateur->telephone) }}">
            </div>
            <div class="form-group">
                <label>Rôle</label>
                <select name="role_id" class="form-control" required>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}"
                            {{ old('role_id', $utilisateur->role_id) == $role->id ? 'selected' : '' }}>
                            {{ $role->libelle }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Structure (optionnel)</label>
                <select name="structure_id" class="form-control">
                    <option value="">-- Aucune --</option>
                    @foreach($structures as $structure)
                        <option value="{{ $structure->id }}"
                            {{ old('structure_id', $utilisateur->structure_id) == $structure->id ? 'selected' : '' }}>
                            {{ $structure->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Nouveau mot de passe <small>(laisser vide pour ne pas changer)</small></label>
                <input type="password" name="mots_de_passe" class="form-control">
            </div>
            <div class="form-group">
                <label>Confirmer le mot de passe</label>
                <input type="password" name="mots_de_passe_confirmation" class="form-control">
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div style="margin-top:1.5rem">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
@endsection