@extends('layouts.app')

@section('title', 'Mon Profil')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Mon Profil</h1>
        <p class="page-subtitle">Gérer vos informations personnelles</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card" style="max-width:700px">
    <form method="POST" action="{{ route('profil.update') }}">
        @csrf @method('PUT')

        <div class="form-grid">
            <div class="form-group">
                <label>Prénom</label>
                <input type="text" name="prenom" class="form-control"
                    value="{{ old('prenom', $user->prenom) }}" required>
            </div>
            <div class="form-group">
                <label>Nom</label>
                <input type="text" name="nom" class="form-control"
                    value="{{ old('nom', $user->nom) }}" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control"
                    value="{{ $user->email }}" disabled>
            </div>
            <div class="form-group">
                <label>Téléphone</label>
                <input type="text" name="telephone" class="form-control"
                    value="{{ old('telephone', $user->telephone) }}">
            </div>
            <div class="form-group">
                <label>Rôle</label>
                <input type="text" class="form-control"
                    value="{{ $user->role->libelle }}" disabled>
            </div>
            <div class="form-group">
                <label>Structure</label>
                <input type="text" class="form-control"
                    value="{{ $user->structure->nom ?? '—' }}" disabled>
            </div>
        </div>

        <hr style="margin: 1.5rem 0; border-color: #e2e8f0">
        <h3 style="font-size:1rem; font-weight:600; margin-bottom:1rem">
            Changer le mot de passe <small style="font-weight:400; color:#64748b">(laisser vide pour ne pas changer)</small>
        </h3>

        <div class="form-grid">
            <div class="form-group">
                <label>Nouveau mot de passe</label>
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
                <i class="fas fa-save"></i> Enregistrer
            </button>
        </div>
    </form>
</div>
@endsection