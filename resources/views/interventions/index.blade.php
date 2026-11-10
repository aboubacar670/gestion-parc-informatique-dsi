@extends('layouts.app')

@section('title', 'Planification des Interventions')

@section('content')
<div class="top-bar">
    <div>
        <h1 class="page-title">Interventions & Maintenance</h1>
        <p class="page-subtitle">Suivi et planification des missions de support technique</p>
    </div>
    @if(Auth::user()->isAdmin() || Auth::user()->isAgent())
    <a href="{{ route('interventions.create') }}" class="btn btn-primary" style="background-color: hsl(var(--primary));">
        <i class="fa-solid fa-plus"></i> Nouvelle intervention
    </a>
    @endif
</div>

<!-- FILTERS BAR -->
<div class="filters-bar">
    <form action="{{ route('interventions.index') }}" method="GET" class="filters-form">
        <div class="form-group">
            <label for="search" class="form-label" style="font-size: 0.75rem;">Rechercher</label>
            <input type="text" id="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Code ou libellé...">
        </div>
        
        <div class="form-group">
            <label for="structure_id" class="form-label" style="font-size: 0.75rem;">Structure / Site</label>
            <select id="structure_id" name="structure_id" class="form-control">
                <option value="">Toutes</option>
                @foreach($structures as $struct)
                    <option value="{{ $struct->id }}" {{ request('structure_id') == $struct->id ? 'selected' : '' }}>
                        {{ $struct->nom }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="type_intervention_id" class="form-label" style="font-size: 0.75rem;">Type d'intervention</label>
            <select id="type_intervention_id" name="type_intervention_id" class="form-control">
                <option value="">Tous</option>
                @foreach($typesIntervention as $type)
                    <option value="{{ $type->id }}" {{ request('type_intervention_id') == $type->id ? 'selected' : '' }}>
                        {{ $type->libelle }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="statut_intervention_id" class="form-label" style="font-size: 0.75rem;">Statut de la mission</label>
            <select id="statut_intervention_id" name="statut_intervention_id" class="form-control">
                <option value="">Tous</option>
                @foreach($statutsIntervention as $statut)
                    <option value="{{ $statut->id }}" {{ request('statut_intervention_id') == $statut->id ? 'selected' : '' }}>
                        {{ $statut->libelle }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-primary" style="padding: 0.625rem 1rem;">
                <i class="fa-solid fa-filter"></i>
            </button>
            <a href="{{ route('interventions.index') }}" class="btn btn-secondary" style="padding: 0.625rem 1rem;" title="Réinitialiser">
                <i class="fa-solid fa-rotate"></i>
            </a>
        </div>
    </form>
</div>

<!-- INTERVENTIONS TABLE -->
<div class="card">
    @if($interventions->isEmpty())
        <p style="text-align: center; color: var(--text-secondary); padding: 3rem 0;">Aucune mission enregistrée.</p>
    @else
        <div class="table-container">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Libellé / Titre de la mission</th>
                        <th>Type</th>
                        <th>Structure / Site</th>
                        <th>Technicien(s)</th>
                        <th>Date début</th>
                        <th>Date fin</th>
                        <th>Statut</th>
                        @if(Auth::user()->isAdmin() || Auth::user()->isAgent())
                            <th style="text-align: right;">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($interventions as $int)
                        <tr>
                            <td style="font-family: monospace; font-weight: 700; color: hsl(var(--primary));">
                                {{ $int->code }}
                            </td>
                            <td style="font-weight: 600; color: var(--text-primary);">
                                @if(Auth::user()->isAdmin() || Auth::user()->isAgent())
                                    <a href="{{ route('interventions.edit', $int->id) }}" style="color: inherit; text-decoration: none;">
                                        {{ $int->libelle }}
                                    </a>
                                @else
                                    {{ $int->libelle }}
                                @endif
                            </td>
                            <td>{{ $int->typeIntervention?->libelle }}</td>
                            <td>
                                @if($int->structure)
                                    <a href="{{ route('structures.show', $int->structure_id) }}" style="color: inherit; text-decoration: none; font-weight: 500;">
                                        {{ $int->structure->nom }}
                                    </a>
                                @else
                                    <span style="color: var(--text-muted);">Non spécifié</span>
                                @endif
                            </td>

                            {{-- ─── Colonne Techniciens avec popover ─── --}}
                            <td>
                                @if($int->techniciens->isEmpty())
                                    <span style="color: var(--text-muted); font-style: italic; font-size: .85rem;">—</span>
                                @else
                                    <div style="display: flex; flex-wrap: wrap; gap: .3rem;">
                                        @foreach($int->techniciens as $tech)
                                            @php
                                                $pivot = $tech->pivot;
                                                $dateAff = $pivot->date_intervention
                                                    ? \Carbon\Carbon::parse($pivot->date_intervention)->format('d/m/Y')
                                                    : '—';
                                                $hDebut = $pivot->heure_debut ?? '—';
                                                $hFin   = $pivot->heure_fin   ?? '—';
                                                $tooltipText = "{$tech->prenom} {$tech->nom} | {$dateAff} | {$hDebut} → {$hFin}";
                                            @endphp
                                            <span class="badge badge-info tech-badge"
                                                  style="cursor: pointer; font-size: .72rem; position: relative;"
                                                  title="{{ $tooltipText }}"
                                                  data-tooltip="{{ $tooltipText }}">
                                                <i class="fa-solid fa-user-gear"></i>
                                                {{ $tech->prenom }} {{ $tech->nom }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>

                            <td>{{ \Carbon\Carbon::parse($int->date_debut)->format('d/m/Y') }}</td>
                            <td>
                                {{ $int->date_fin ? \Carbon\Carbon::parse($int->date_fin)->format('d/m/Y') : '-' }}
                            </td>
                            <td>
                                <span class="badge {{ $int->statutIntervention?->libelle === 'Terminée' ? 'badge-success' : ($int->statutIntervention?->libelle === 'Annulée' ? 'badge-danger' : 'badge-info') }}">
                                    {{ $int->statutIntervention?->libelle }}
                                </span>
                            </td>
                            @if(Auth::user()->isAdmin() || Auth::user()->isAgent())
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 0.5rem;">
                                    <a href="{{ route('interventions.edit', $int->id) }}" class="btn btn-secondary btn-sm" title="Modifier/Fermer" style="color: hsl(var(--warning));">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    @if(Auth::user()->isAdmin())
                                       <form id="deleteForm-{{ $int->id }}" 
                                            action="{{ route('interventions.destroy', $int->id) }}" 
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                            <button onclick="confirmDelete('deleteForm-{{ $int->id }}', 
                                                'Êtes-vous sûr de vouloir supprimer l\'intervention {{ $int->id }} ? Cette action est irréversible.')" 
                                                class="btn btn-secondary btn-sm" 
                                                title="Supprimer" 
                                                style="color: hsl(var(--danger));">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                    @endif
                                </div>
                            </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="pagination-wrapper">
            {{ $interventions->links() }}
        </div>
    @endif
</div>

{{-- Popover/Tooltip pour les badges techniciens --}}
@push('scripts')
<style>
.tech-badge {
    position: relative;
}
.tech-badge::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: calc(100% + 6px);
    left: 50%;
    transform: translateX(-50%);
    background: #1e2435;
    color: #fff;
    font-size: .75rem;
    white-space: nowrap;
    padding: .35rem .65rem;
    border-radius: 6px;
    pointer-events: none;
    opacity: 0;
    transition: opacity .18s ease;
    z-index: 999;
    box-shadow: 0 4px 12px rgba(0,0,0,.25);
}
.tech-badge::before {
    content: '';
    position: absolute;
    bottom: calc(100% + 1px);
    left: 50%;
    transform: translateX(-50%);
    border: 5px solid transparent;
    border-top-color: #1e2435;
    pointer-events: none;
    opacity: 0;
    transition: opacity .18s ease;
    z-index: 999;
}
.tech-badge:hover::after,
.tech-badge:hover::before {
    opacity: 1;
}
</style>
@endpush
@endsection
