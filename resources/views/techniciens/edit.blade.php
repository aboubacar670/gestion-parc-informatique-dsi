@extends('layouts.app')

@section('title', 'Modifier le technicien ' . $technicien->prenom . ' ' . $technicien->nom)

@section('content')
<div class="top-bar">
    <div>
        <h1 class="page-title">Modifier un Technicien</h1>
        <p class="page-subtitle">Édition du profil de {{ $technicien->prenom }} {{ $technicien->nom }}</p>
    </div>
    <a href="{{ route('techniciens.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-xmark"></i> Annuler
    </a>
</div>

<div class="card" style="max-width:600px; margin:0 auto;">
    <h2 class="card-title">
        <i class="fa-solid fa-pen-to-square" style="color:hsl(var(--warning));"></i> Modification du technicien
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

    <form action="{{ route('techniciens.update', $technicien->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="form-group">
                <label for="prenom" class="form-label">Prénom <span style="color:red;">*</span></label>
                <input type="text" id="prenom" name="prenom" value="{{ old('prenom', $technicien->prenom) }}"
                       class="form-control" required>
            </div>
            <div class="form-group">
                <label for="nom" class="form-label">Nom <span style="color:red;">*</span></label>
                <input type="text" id="nom" name="nom" value="{{ old('nom', $technicien->nom) }}"
                       class="form-control" required>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="profil_technicien_id" class="form-label">Profil <span style="color:red;">*</span></label>
                <select id="profil_technicien_id" name="profil_technicien_id" class="form-control" required>
                    <option value="">Sélectionner un profil</option>
                    @foreach($profils as $profil)
                        <option value="{{ $profil->id }}" {{ old('profil_technicien_id', $technicien->profil_technicien_id) == $profil->id ? 'selected' : '' }}>
                            {{ $profil->libelle }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="telephone" class="form-label">Téléphone</label>
                <input type="text" id="telephone" name="telephone" value="{{ old('telephone', $technicien->telephone) }}"
                       class="form-control" placeholder="ex: +226 70 00 00 10">
            </div>
        </div>

        <div style="margin-top:2rem; display:flex; justify-content:flex-end; gap:1rem;">
            <a href="{{ route('techniciens.index') }}" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary" style="background-color:hsl(var(--warning)); border-color:hsl(var(--warning));">
                Mettre à jour <i class="fa-solid fa-circle-check"></i>
            </button>
        </div>
    </form>
</div>
@endsection
