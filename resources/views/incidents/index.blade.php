@extends('layouts.app')

@section('title', 'Suivi des Incidents')

@section('content')
<div class="top-bar">
    <div>
        <h1 class="page-title">Incidents &amp; Pannes Signalées</h1>
        <p class="page-subtitle">Déclaration et suivi de résolution des incidents techniques</p>
    </div>
    <div style="display: flex; gap: 10px;">
        
        <a href="{{ route('incidents.create') }}" class="btn btn-danger">
            <i class="fa-solid fa-triangle-exclamation"></i> Déclarer une Panne
        </a>
    </div>
</div>

<!-- FILTERS BAR -->
<div class="filters-bar">
    <form action="{{ route('incidents.index') }}" method="GET" class="filters-form">
        <div class="form-group">
            <label for="search" class="form-label" style="font-size: 0.75rem;">Rechercher</label>
            <input type="text" id="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Code ou libellé...">
        </div>
        
        @if(!Auth::user()->isResponsable())
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
        @endif

        <div class="form-group">
            <label for="statut_incident_id" class="form-label" style="font-size: 0.75rem;">Statut de l'incident</label>
            <select id="statut_incident_id" name="statut_incident_id" class="form-control">
                <option value="">Tous</option>
                @foreach($statuts as $statut)
                    <option value="{{ $statut->id }}" {{ request('statut_incident_id') == $statut->id ? 'selected' : '' }}>
                        {{ $statut->libelle }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-primary" style="padding: 0.625rem 1rem;">
                <i class="fa-solid fa-filter"></i>
            </button>
            <a href="{{ route('incidents.index') }}" class="btn btn-secondary" style="padding: 0.625rem 1rem;" title="Réinitialiser">
                <i class="fa-solid fa-rotate"></i>
            </a>
        </div>
    </form>
</div>

<!-- INCIDENTS TABLE -->
<div class="card">
    @if($incidents->isEmpty())
        <p style="text-align: center; color: var(--text-secondary); padding: 3rem 0;">Aucun incident à afficher.</p>
    @else
        <div class="table-container">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Libellé / Titre de la panne</th>
                        <th>Structure / Site</th>
                        <th>Date de signalement</th>
                        <th>Statut</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($incidents as $inc)
                        <tr>
                            <td style="font-family: monospace; font-weight: 700; color: hsl(var(--danger));">
                                {{ $inc->code }}
                            </td>
                            <td style="font-weight: 600; color: var(--text-primary);">
                                <a href="{{ route('incidents.show', $inc->id) }}" style="color: inherit; text-decoration: none;">
                                    {{ $inc->libelle }}
                                </a>
                            </td>
                            <td>
                                @if($inc->structure)
                                    <a href="{{ route('structures.show', $inc->structure_id) }}" style="color: inherit; text-decoration: none; font-weight: 500;">
                                        {{ $inc->structure->nom }}
                                    </a>
                                @else
                                    <span style="color: var(--text-muted);">Non spécifié</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($inc->date_intervention)->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge {{ $inc->statutIncident?->libelle === 'Résolu' ? 'badge-success' : ($inc->statutIncident?->libelle === 'En cours de traitement' ? 'badge-warning' : 'badge-danger') }}">
                                    {{ $inc->statutIncident?->libelle }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('incidents.show', $inc->id) }}" class="btn btn-secondary btn-sm" title="Détails &amp; Résolution">
                                    <i class="fa-solid fa-eye"></i> Gérer
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="pagination-wrapper">
            {{ $incidents->links() }}
        </div>
    @endif
</div>
@endsection
