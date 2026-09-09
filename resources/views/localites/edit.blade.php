@extends('layouts.app')
@section('title', 'Modifier la Localité')

@section('content')
<div class="top-bar">
    <div>
        <h1 class="page-title">Modifier la Localité</h1>
        <p class="page-subtitle">Édition : {{ $localite->nom }}</p>
    </div>
    <a href="{{ route('localites.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

<div class="card" style="max-width:600px; margin:0 auto;">
    @if($errors->any())
        <div class="alert alert-danger">
            <ul style="list-style:none; margin:0; padding:0;">
                @foreach($errors->all() as $error)
                    <li><i class="fas fa-circle-exclamation"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('localites.update', $localite->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">Nom de la localité <span style="color:red;">*</span></label>
            <input type="text" name="nom" class="form-control"
                value="{{ old('nom', $localite->nom) }}"
                placeholder="ex: Ouagadougou" required>
        </div>

        <div class="form-group">
            <label class="form-label">Type de localité <span style="color:red;">*</span></label>
            <select name="type_localite_id" class="form-control" required>
                <option value="">Sélectionner un type</option>
                @foreach($typesLocalites as $type)
                    <option value="{{ $type->id }}"
                        {{ old('type_localite_id', $localite->type_localite_id) == $type->id ? 'selected' : '' }}>
                        {{ $type->libelle }}
                    </option>
                @endforeach
            </select>
        </div>

                <div class="form-group">
            <label class="form-label">Localité parente (optionnel)</label>
            <select name="parent_id" class="form-control">
                <option value="">Aucune (localité racine)</option>
                @foreach($parents as $parent)
                    <option value="{{ $parent->id }}" 
                        {{ old('parent_id', $localite->parent_id) == $parent->id ? 'selected' : '' }}>
                        {{ $parent->nom }} ({{ $parent->typeLocalite?->libelle }})
                    </option>
                @endforeach
            </select>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:1rem; margin-top:2rem;">
            <a href="{{ route('localites.index') }}" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-circle-check"></i> Mettre à jour
            </button>
        </div>
    </form>
</div>
@endsection