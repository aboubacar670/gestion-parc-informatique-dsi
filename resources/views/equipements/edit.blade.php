@extends('layouts.app')

@section('title', 'Modifier l\'équipement ' . $equipement->nom)

@section('content')
<div class="top-bar">
    <div>
        <h1 class="page-title">Modifier l'Équipement</h1>
        <p class="page-subtitle">Édition des informations du matériel : {{ $equipement->nom }}</p>
    </div>
    <a href="{{ route('equipements.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-xmark"></i> Annuler
    </a>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <h2 class="card-title"><i class="fa-solid fa-pen-to-square" style="color: hsl(var(--warning));"></i> Édition d'équipement</h2>
    
    @if($errors->any())
        <div class="alert alert-danger">
            <ul style="list-style: none; margin: 0; padding: 0;">
                @foreach($errors->all() as $error)
                    <li><i class="fa-solid fa-circle-exclamation"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('equipements.update', $equipement->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-grid">
            <div class="form-group">
                <label for="nom" class="form-label">Nom du matériel <span style="color: red;">*</span></label>
                <input type="text" id="nom" name="nom" value="{{ old('nom', $equipement->nom) }}" class="form-control" placeholder="ex: HP ProDesk 400 G6" required>
            </div>
            
            <div class="form-group">
                <label for="version" class="form-label">Modèle / Version / Année</label>
                <input type="text" id="version" name="version" value="{{ old('version', $equipement->version) }}" class="form-control" placeholder="ex: Intel i5 2021">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="type_equipement_id" class="form-label">Type de matériel <span style="color: red;">*</span></label>
                <select id="type_equipement_id" name="type_equipement_id" class="form-control" required>
                    <option value="">Sélectionner un type</option>
                    @foreach($typesEquipement as $type)
                        <option value="{{ $type->id }}" {{ old('type_equipement_id', $equipement->type_equipement_id) == $type->id ? 'selected' : '' }}>
                            {{ $type->libelle }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="etat_equipement_id" class="form-label">État de fonctionnement <span style="color: red;">*</span></label>
                <select id="etat_equipement_id" name="etat_equipement_id" class="form-control" required>
                    @foreach($etatsEquipement as $etat)
                        <option value="{{ $etat->id }}" {{ old('etat_equipement_id', $equipement->etat_equipement_id) == $etat->id ? 'selected' : '' }}>
                            {{ $etat->libelle }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="structure_id" class="form-label">Affectation (Structure) <span style="color: red;">*</span></label>
            <select id="structure_id" name="structure_id" class="form-control" required>
                <option value="">Sélectionner une structure</option>
                @foreach($structures as $struct)
                    <option value="{{ $struct->id }}" {{ old('structure_id', $equipement->structure_id) == $struct->id ? 'selected' : '' }}>
                        {{ $struct->nom }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="caracteristique" class="form-label">Fiche Technique / Caractéristiques détaillées</label>
            <textarea id="caracteristique" name="caracteristique" rows="3" class="form-control" placeholder="Processeur, RAM, disque dur, connectique...">{{ old('caracteristique', $equipement->caracteristique) }}</textarea>
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Notes complémentaires / Emplacement physique</label>
            <textarea id="description" name="description" rows="2" class="form-control" placeholder="ex: Bureau du proviseur, salle info 2...">{{ old('description', $equipement->description) }}</textarea>
        </div>

        <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
            <a href="{{ route('equipements.index') }}" class="btn btn-secondary">
                Annuler
            </a>
            <button type="submit" class="btn btn-primary" style="background-color: hsl(var(--warning)); border-color: hsl(var(--warning));">
                Mettre à jour <i class="fa-solid fa-circle-check"></i>
            </button>
        </div>

    </form>
</div>
@endsection
