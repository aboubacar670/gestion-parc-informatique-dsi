@extends('layouts.app')

@section('title', 'Modifier l\'intervention ' . $intervention->code)

@section('content')
<div class="top-bar">
    <div>
        <h1 class="page-title">Modifier l'Intervention</h1>
        <p class="page-subtitle">Édition des détails de la mission : {{ $intervention->code }}</p>
    </div>
    <a href="{{ route('interventions.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-xmark"></i> Annuler
    </a>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <h2 class="card-title"><i class="fa-solid fa-pen-to-square" style="color: hsl(var(--warning));"></i> Édition de mission</h2>
    
    @if($errors->any())
        <div class="alert alert-danger">
            <ul style="list-style: none; margin: 0; padding: 0;">
                @foreach($errors->all() as $error)
                    <li><i class="fa-solid fa-circle-exclamation"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('interventions.update', $intervention->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="libelle" class="form-label">Libellé / Titre de la mission <span style="color: red;">*</span></label>
            <input type="text" id="libelle" name="libelle" value="{{ old('libelle', $intervention->libelle) }}" class="form-control" placeholder="ex: Réinstallation salle informatique et audit réseau" required>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="type_intervention_id" class="form-label">Type d'intervention <span style="color: red;">*</span></label>
                <select id="type_intervention_id" name="type_intervention_id" class="form-control" required>
                    <option value="">Sélectionner un type</option>
                    @foreach($typesIntervention as $type)
                        <option value="{{ $type->id }}" {{ old('type_intervention_id', $intervention->type_intervention_id) == $type->id ? 'selected' : '' }}>
                            {{ $type->libelle }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="statut_intervention_id" class="form-label">Statut de la mission <span style="color: red;">*</span></label>
                <select id="statut_intervention_id" name="statut_intervention_id" class="form-control" required>
                    @foreach($statutsIntervention as $statut)
                        <option value="{{ $statut->id }}" {{ old('statut_intervention_id', $intervention->statut_intervention_id) == $statut->id ? 'selected' : '' }}>
                            {{ $statut->libelle }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="structure_id" class="form-label">Structure / Établissement cible <span style="color: red;">*</span></label>
            <select id="structure_id" name="structure_id" class="form-control" required>
                <option value="">Sélectionner une structure</option>
                @foreach($structures as $struct)
                    <option value="{{ $struct->id }}" {{ old('structure_id', $intervention->structure_id) == $struct->id ? 'selected' : '' }}>
                        {{ $struct->nom }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="date_debut" class="form-label">Date de début réelle/prévue <span style="color: red;">*</span></label>
                <input type="date" id="date_debut" name="date_debut" value="{{ old('date_debut', $intervention->date_debut) }}" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="date_fin" class="form-label">Date de fin réelle/prévue (Optionnelle)</label>
                <input type="date" id="date_fin" name="date_fin" value="{{ old('date_fin', $intervention->date_fin) }}" class="form-control">
            </div>
        </div>

        <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
            <a href="{{ route('interventions.index') }}" class="btn btn-secondary">
                Annuler
            </a>
            <button type="submit" class="btn btn-primary" style="background-color: hsl(var(--warning)); border-color: hsl(var(--warning));">
                Mettre à jour <i class="fa-solid fa-circle-check"></i>
            </button>
        </div>

    </form>
</div>
@endsection
