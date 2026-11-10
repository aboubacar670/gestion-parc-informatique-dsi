@extends('layouts.app')

@section('title', 'Planifier une intervention')

@section('content')
<div class="top-bar">
    <div>
        <h1 class="page-title">Planifier une Mission</h1>
        <p class="page-subtitle">Créer une nouvelle mission de maintenance / dépannage</p>
    </div>
    <a href="{{ route('interventions.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-xmark"></i> Annuler
    </a>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <h2 class="card-title"><i class="fa-solid fa-screwdriver-wrench" style="color: hsl(var(--primary));"></i> Planification de mission</h2>
    
    @if($errors->any())
        <div class="alert alert-danger">
            <ul style="list-style: none; margin: 0; padding: 0;">
                @foreach($errors->all() as $error)
                    <li><i class="fa-solid fa-circle-exclamation"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('interventions.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="libelle" class="form-label">Libellé / Titre de la mission <span style="color: red;">*</span></label>
            <input type="text" id="libelle" name="libelle" value="{{ old('libelle') }}" class="form-control" placeholder="ex: Réinstallation salle informatique et audit réseau" required>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="type_intervention_id" class="form-label">Type d'intervention <span style="color: red;">*</span></label>
                <select id="type_intervention_id" name="type_intervention_id" class="form-control" required>
                    <option value="">Sélectionner un type</option>
                    @foreach($typesIntervention as $type)
                        <option value="{{ $type->id }}" {{ old('type_intervention_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->libelle }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="statut_intervention_id" class="form-label">Statut initial de la mission <span style="color: red;">*</span></label>
                <select id="statut_intervention_id" name="statut_intervention_id" class="form-control" required>
                    @foreach($statutsIntervention as $statut)
                        <option value="{{ $statut->id }}" {{ old('statut_intervention_id') == $statut->id || ($statut->libelle === 'Planifiée' && !old('statut_intervention_id')) ? 'selected' : '' }}>
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
                    <option value="{{ $struct->id }}" {{ old('structure_id', request('structure_id')) == $struct->id ? 'selected' : '' }}>
                        {{ $struct->nom }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="date_debut" class="form-label">Date de début prévue <span style="color: red;">*</span></label>
                <input type="date" id="date_debut" name="date_debut" value="{{ old('date_debut', date('Y-m-d')) }}" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="date_fin" class="form-label">Date de fin prévue (Optionnelle)</label>
                <input type="date" id="date_fin" name="date_fin" value="{{ old('date_fin') }}" class="form-control">
            </div>
            
        </div>
                <div class="form-group" style="margin-top: 1.5rem;">
            <label class="form-label">Techniciens assignés (optionnel)</label>

            @if($techniciens->isEmpty())
                <p class="page-subtitle">Aucun technicien enregistré pour le moment.</p>
            @else
                <div style="border: 1px solid #e3e6ea; border-radius: 8px; max-height: 320px; overflow-y: auto;">
                    <div style="display: grid; grid-template-columns: 2fr 1.2fr 1fr 1fr; gap: .75rem; padding: .5rem 1rem; background: #f5f6f8; font-size: .8rem; font-weight: 600;">
                        <span>Technicien</span><span>Date</span><span>Début</span><span>Fin</span>
                    </div>

                    @foreach($techniciens as $tech)
                        <div style="display: grid; grid-template-columns: 2fr 1.2fr 1fr 1fr; gap: .75rem; align-items: center; padding: .75rem 1rem; border-bottom: 1px solid #eef0f3;">
                            <label style="display: flex; align-items: center; gap: .6rem; cursor: pointer;">
                                <input type="checkbox" name="techniciens[]" value="{{ $tech->id }}"
                                       {{ in_array($tech->id, old('techniciens', [])) ? 'checked' : '' }}>
                                <span>
                                    <strong>{{ $tech->prenom }} {{ $tech->nom }}</strong><br>
                                    <small>{{ $tech->profil->libelle ?? '' }}</small>
                                </span>
                            </label>
                            <input type="date" name="assign[{{ $tech->id }}][date_intervention]"
                                   value="{{ old('assign.' . $tech->id . '.date_intervention') }}" class="form-control">
                            <input type="time" name="assign[{{ $tech->id }}][heure_debut]"
                                   value="{{ old('assign.' . $tech->id . '.heure_debut') }}" class="form-control">
                            <input type="time" name="assign[{{ $tech->id }}][heure_fin]"
                                   value="{{ old('assign.' . $tech->id . '.heure_fin') }}" class="form-control">
                        </div>
                    @endforeach
                </div>
                <small>Si la date d'un technicien est vide, la date de début de la mission est utilisée.</small>
            @endif
        </div>

        <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
            <a href="{{ route('interventions.index') }}" class="btn btn-secondary">
                Annuler
            </a>
            <button type="submit" class="btn btn-primary">
                Planifier l'Intervention <i class="fa-solid fa-circle-check"></i>
            </button>
        </div>

    </form>
</div>
@endsection
