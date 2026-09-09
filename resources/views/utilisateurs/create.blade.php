@extends('layouts.app')

@section('title', 'Nouvel Utilisateur')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Nouvel Utilisateur</h1>
        <p class="page-subtitle">Créer un nouveau compte d'accès</p>
    </div>
    <a href="{{ route('utilisateurs.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

<div class="card" style="max-width:700px">
    <form method="POST" action="{{ route('utilisateurs.store') }}">
        @csrf

        <div class="form-grid">
            <div class="form-group">
                <label>Prénom</label>
                <input type="text" name="prenom" class="form-control"
                    value="{{ old('prenom') }}" required>
            </div>
            <div class="form-group">
                <label>Nom</label>
                <input type="text" name="nom" class="form-control"
                    value="{{ old('nom') }}" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control"
                    value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <label>Téléphone</label>
                <input type="text" name="telephone" class="form-control"
                    value="{{ old('telephone') }}">
            </div>
            <div class="form-group">
                <label>Rôle</label>
                <select name="role_id" class="form-control" required>
                    <option value="">-- Choisir un rôle --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}"
                            {{ old('role_id') == $role->id ? 'selected' : '' }}>
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
                            {{ old('structure_id') == $structure->id ? 'selected' : '' }}>
                            {{ $structure->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="mots_de_passe" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Confirmer le mot de passe</label>
                <input type="password" name="mots_de_passe_confirmation" class="form-control" required>
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
                <i class="fas fa-save"></i> Créer l'utilisateur
            </button>
        </div>
    </form>
</div>
@endsection