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

        {{-- ─── Section Techniciens ───────────────────────────────────── --}}
        <div class="form-group" style="margin-top: 1.5rem;">
            <label class="form-label">Techniciens assignés <span style="color: var(--text-muted); font-weight: 400;">(optionnel)</span></label>

            @php
                // Récupère les techniciens déjà assignés avec leurs données pivot
                $assignes = $intervention->techniciens->keyBy('id');
            @endphp

            @if($techniciens->isEmpty())
                <p class="page-subtitle">
                    <i class="fa-solid fa-circle-info"></i>
                    Aucun technicien enregistré. 
                    <a href="{{ route('techniciens.create') }}">Ajouter un technicien</a>
                </p>
            @else
                <div style="border: 1px solid #e3e6ea; border-radius: 8px; overflow: hidden;">
                    <div style="display: grid; grid-template-columns: 2fr 1.2fr 1fr 1fr; gap: .75rem; padding: .6rem 1rem; background: #f5f6f8; font-size: .78rem; font-weight: 600; color: var(--text-secondary);">
                        <span>Technicien</span>
                        <span>Date intervention</span>
                        <span>Heure début</span>
                        <span>Heure fin</span>
                    </div>

                    @foreach($techniciens as $tech)
                        @php
                            $pivot = $assignes->get($tech->id)?->pivot ?? null;
                            $isChecked = $assignes->has($tech->id);
                        @endphp
                        <div class="technicien-row {{ $isChecked ? 'row-checked' : '' }}"
                             style="display: grid; grid-template-columns: 2fr 1.2fr 1fr 1fr; gap: .75rem; align-items: center; padding: .75rem 1rem; border-top: 1px solid #eef0f3; transition: background .15s;">
                            
                            <label style="display: flex; align-items: center; gap: .6rem; cursor: pointer; margin: 0;">
                                <input type="checkbox" name="techniciens[]" value="{{ $tech->id }}"
                                       class="tech-checkbox"
                                       data-row="{{ $tech->id }}"
                                       {{ $isChecked || in_array($tech->id, old('techniciens', [])) ? 'checked' : '' }}>
                                <span>
                                    <strong>{{ $tech->prenom }} {{ $tech->nom }}</strong><br>
                                    <small style="color: var(--text-muted);">{{ $tech->profil->libelle ?? '—' }}</small>
                                </span>
                            </label>

                            <input type="date" name="assign[{{ $tech->id }}][date_intervention]"
                                   value="{{ old('assign.' . $tech->id . '.date_intervention', $pivot?->date_intervention ?? '') }}"
                                   class="form-control tech-field-{{ $tech->id }}"
                                   {{ !$isChecked && !in_array($tech->id, old('techniciens', [])) ? 'disabled' : '' }}>

                            <input type="time" name="assign[{{ $tech->id }}][heure_debut]"
                                   value="{{ old('assign.' . $tech->id . '.heure_debut', $pivot?->heure_debut ?? '') }}"
                                   class="form-control tech-field-{{ $tech->id }}"
                                   {{ !$isChecked && !in_array($tech->id, old('techniciens', [])) ? 'disabled' : '' }}>

                            <input type="time" name="assign[{{ $tech->id }}][heure_fin]"
                                   value="{{ old('assign.' . $tech->id . '.heure_fin', $pivot?->heure_fin ?? '') }}"
                                   class="form-control tech-field-{{ $tech->id }}"
                                   {{ !$isChecked && !in_array($tech->id, old('techniciens', [])) ? 'disabled' : '' }}>
                        </div>
                    @endforeach
                </div>
                <small style="color: var(--text-muted);">
                    <i class="fa-solid fa-circle-info"></i>
                    Cochez un technicien pour activer ses champs. Si la date est vide, la date de début de la mission est utilisée.
                </small>
            @endif
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

@push('scripts')
<style>
    .row-checked { background-color: hsl(var(--primary) / .05); }
    .tech-field-disabled { opacity: .4; pointer-events: none; }
</style>
<script>
document.querySelectorAll('.tech-checkbox').forEach(function(cb) {
    cb.addEventListener('change', function() {
        const id = this.dataset.row;
        const fields = document.querySelectorAll('.tech-field-' + id);
        const row = this.closest('.technicien-row');
        fields.forEach(f => f.disabled = !this.checked);
        if (this.checked) {
            row.style.backgroundColor = 'hsl(var(--primary) / .05)';
        } else {
            row.style.backgroundColor = '';
        }
    });
});
</script>
@endpush
@endsection
