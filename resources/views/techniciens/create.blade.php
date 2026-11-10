@extends('layouts.app')

@section('title', 'Ajouter un technicien')

@section('content')
<div class="top-bar">
    <div>
        <h1 class="page-title">Nouveau Technicien</h1>
        <p class="page-subtitle">Ajouter un membre à l'équipe technique</p>
    </div>
    <a href="{{ route('techniciens.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-xmark"></i> Annuler
    </a>
</div>

<div class="card" style="max-width:600px; margin:0 auto;">
    <h2 class="card-title">
        <i class="fa-solid fa-user-plus" style="color:hsl(var(--primary));"></i> Informations du technicien
    </h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul style="list-style:none; margin:0; padding:0;">
                @foreach($errors->all() as $error)
                    <li><i class="fa-solid fa-circle-exclamation"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('techniciens.store') }}" method="POST">
        @csrf

        <div class="form-grid">
            <div class="form-group">
                <label for="prenom" class="form-label">Prénom <span style="color:red;">*</span></label>
                <input type="text" id="prenom" name="prenom" value="{{ old('prenom') }}"
                       class="form-control" placeholder="ex: Idrissa" required>
            </div>
            <div class="form-group">
                <label for="nom" class="form-label">Nom <span style="color:red;">*</span></label>
                <input type="text" id="nom" name="nom" value="{{ old('nom') }}"
                       class="form-control" placeholder="ex: OUEDRAOGO" required>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="profil_technicien_id" class="form-label">Profil <span style="color:red;">*</span></label>
                <select id="profil_technicien_id" name="profil_technicien_id" class="form-control" required>
                    <option value="">Sélectionner un profil</option>
                    @foreach($profils as $profil)
                        <option value="{{ $profil->id }}" {{ old('profil_technicien_id') == $profil->id ? 'selected' : '' }}>
                            {{ $profil->libelle }}
                        </option>
                    @endforeach
                </select>
                @if($profils->isEmpty())
                    <small style="color:hsl(var(--warning));">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        Aucun profil disponible. 
                        <a href="{{ route('referentiels.index') }}">Créer un profil dans les Référentiels</a>
                    </small>
                @endif
            </div>
            <div class="form-group">
                <label for="telephone" class="form-label">Téléphone</label>
                <input type="text" id="telephone" name="telephone" value="{{ old('telephone') }}"
                       class="form-control" placeholder="ex: +226 70 00 00 10">
            </div>
        </div>

        <div style="margin-top:2rem; display:flex; justify-content:flex-end; gap:1rem;">
            <a href="{{ route('techniciens.index') }}" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">
                Enregistrer <i class="fa-solid fa-circle-check"></i>
            </button>
        </div>
    </form>
</div>
@endsection
