@extends('layouts.app')

@section('title', 'Modifier la structure ' . $structure->nom)

@section('content')
<div class="top-bar">
    <div>
        <h1 class="page-title">Modifier la Structure</h1>
        <p class="page-subtitle">Mettre à jour les informations de la structure : {{ $structure->nom }}</p>
    </div>
    <a href="{{ route('structures.show', $structure->id) }}" class="btn btn-secondary">
        <i class="fa-solid fa-xmark"></i> Annuler
    </a>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <h2 class="card-title"><i class="fa-solid fa-pen-to-square" style="color: hsl(var(--warning));"></i> Édition de structure</h2>
    
    @if($errors->any())
        <div class="alert alert-danger">
            <ul style="list-style: none; margin: 0; padding: 0;">
                @foreach($errors->all() as $error)
                    <li><i class="fa-solid fa-circle-exclamation"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('structures.update', $structure->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-grid">
            <div class="form-group">
                <label for="nom" class="form-label">Nom officiel de la structure <span style="color: red;">*</span></label>
                <input type="text" id="nom" name="nom" value="{{ old('nom', $structure->nom) }}" class="form-control" placeholder="ex: École Primaire Mogho Naaba" required>
            </div>
            
            <div class="form-group">
                <label for="acronyme" class="form-label">Acronyme / Sigle</label>
                <input type="text" id="acronyme" name="acronyme" value="{{ old('acronyme', $structure->acronyme) }}" class="form-control" placeholder="ex: EPP Mogho Naaba">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="type_structure_id" class="form-label">Type de structure <span style="color: red;">*</span></label>
                <select id="type_structure_id" name="type_structure_id" class="form-control" required>
                    <option value="">Sélectionner un type</option>
                    @foreach($typesStructure as $type)
                        <option value="{{ $type->id }}" {{ old('type_structure_id', $structure->type_structure_id) == $type->id ? 'selected' : '' }}>
                            {{ $type->libelle }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="localite_id" class="form-label">Localité / Commune <span style="color: red;">*</span></label>
                <select id="localite_id" name="localite_id" class="form-control" required>
                    <option value="">Sélectionner une localité</option>
                    @foreach($localites as $loc)
                        <option value="{{ $loc->id }}" {{ old('localite_id', $structure->localite_id) == $loc->id ? 'selected' : '' }}>
                            {{ $loc->nom }} ({{ $loc->typeLocalite?->libelle }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="parent_id" class="form-label">Rattachement administratif (Parent)</label>
            <select id="parent_id" name="parent_id" class="form-control">
                <option value="">Aucun (Rattachement direct DSI)</option>
                @foreach($parents as $parent)
                    <option value="{{ $parent->id }}" {{ old('parent_id', $structure->parent_id) == $parent->id ? 'selected' : '' }}>
                        {{ $parent->nom }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-grid" style="background-color: #f8fafc; padding: 1rem; border-radius: 8px; margin-top: 1rem; border: 1px dashed var(--bg-accent);">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="latitude" class="form-label">Latitude GPS</label>
                <input type="number" id="latitude" name="latitude" value="{{ old('latitude', $structure->latitude) }}" step="0.0000001" class="form-control" placeholder="ex: 12.3714">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="longitude" class="form-label">Longitude GPS</label>
                <input type="number" id="longitude" name="longitude" value="{{ old('longitude', $structure->longitude) }}" step="0.0000001" class="form-control" placeholder="ex: -1.5197">
            </div>
        </div>

        <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
            <a href="{{ route('structures.show', $structure->id) }}" class="btn btn-secondary">
                Annuler
            </a>
            <button type="submit" class="btn btn-primary" style="background-color: hsl(var(--warning)); border-color: hsl(var(--warning));">
                Mettre à jour <i class="fa-solid fa-circle-check"></i>
            </button>
        </div>

    </form>
</div>
@endsection
