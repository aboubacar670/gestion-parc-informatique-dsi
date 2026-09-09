@extends('layouts.app')

@section('title', 'Gestion des Structures')

@section('content')
<div class="top-bar">
    <div>
        <h1 class="page-title">Structures &amp; Sites Éducatifs</h1>
        <p class="page-subtitle">Liste et filtres géographiques des établissements scolaires</p>
    </div>
    <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
        <a href="{{ route('export.structures') }}" class="btn btn-secondary">
            <i class="fas fa-file-excel"></i> Exporter Excel
        </a>
        @if(Auth::user()->isAdmin())
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('importModal').style.display='flex'">
            <i class="fas fa-file-import"></i> Importer Excel
        </button>
        @endif
        <button onclick="window.print()" class="btn btn-secondary">
            <i class="fas fa-print"></i> Imprimer / PDF
        </button>
        @if(Auth::user()->isAdmin())
        <a href="{{ route('structures.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Nouvelle Structure
        </a>
        @endif
    </div>
</div>

@if(session('import_warnings') && count(session('import_warnings')) > 0)
<div class="alert alert-warning" style="margin-bottom: 1rem;">
    <strong>Avertissements d'importation :</strong>
    <ul style="margin: 0.5rem 0 0 1.25rem; padding: 0;">
        @foreach(session('import_warnings') as $warn)
            <li>{{ $warn }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- FILTERS BAR -->
<div class="filters-bar">
    <form action="{{ route('structures.index') }}" method="GET" class="filters-form">
        <div class="form-group">
            <label for="search" class="form-label" style="font-size: 0.75rem;">Rechercher</label>
            <input type="text" id="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Nom ou acronyme...">
        </div>
        
        <div class="form-group">
            <label for="localite_id" class="form-label" style="font-size: 0.75rem;">Localité</label>
            <select id="localite_id" name="localite_id" class="form-control">
                <option value="">Toutes</option>
                @foreach($localites as $loc)
                    <option value="{{ $loc->id }}" {{ request('localite_id') == $loc->id ? 'selected' : '' }}>
                        {{ $loc->nom }} ({{ $loc->typeLocalite?->libelle }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="type_structure_id" class="form-label" style="font-size: 0.75rem;">Type de structure</label>
            <select id="type_structure_id" name="type_structure_id" class="form-control">
                <option value="">Tous</option>
                @foreach($typesStructure as $type)
                    <option value="{{ $type->id }}" {{ request('type_structure_id') == $type->id ? 'selected' : '' }}>
                        {{ $type->libelle }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-primary" style="padding: 0.625rem 1rem;">
                <i class="fa-solid fa-filter"></i>
            </button>
            <a href="{{ route('structures.index') }}" class="btn btn-secondary" style="padding: 0.625rem 1rem;" title="Réinitialiser">
                <i class="fa-solid fa-rotate"></i>
            </a>
        </div>
    </form>
</div>

<!-- STRUCTURES TABLE -->
<div class="card">
    @if($structures->isEmpty())
        <p style="text-align: center; color: var(--text-secondary); padding: 3rem 0;">Aucune structure ne correspond à votre recherche.</p>
    @else
        <div class="table-container" style="overflow-x: auto;">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Nom de l'établissement</th>
                        <th class="col-secondary">Acronyme</th>
                        <th>Type</th>
                        <th>Localité</th>
                        <th class="col-secondary">Coordonnées</th>
                        <th>État parc</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($structures as $struct)
                        <tr>
                            <td style="font-weight: 600; color: var(--text-primary);">
                                <a href="{{ route('structures.show', $struct->id) }}" style="color: inherit; text-decoration: none;">
                                    {{ $struct->nom }}
                                </a>
                            </td>
                            <td class="col-secondary">{{ $struct->acronyme ?? '-' }}</td>
                            <td>{{ $struct->typeStructure?->libelle }}</td>
                            <td>{{ $struct->localite?->nom }}</td>
                            <td class="col-secondary">
                                @if($struct->latitude && $struct->longitude)
                                    <span style="font-family: monospace; font-size: 0.75rem;">{{ number_format($struct->latitude, 4) }}, {{ number_format($struct->longitude, 4) }}</span>
                                @else
                                    <span style="color: var(--text-muted); font-style: italic;">Non géolocalisé</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $incOuverts = $struct->incidents->filter(function($inc) {
                                        return in_array($inc->statutIncident?->libelle, ['Signalé', 'En cours de traitement']);
                                    })->count();
                                @endphp
                                @if($incOuverts === 0)
                                    <span class="badge badge-success">Tout fonctionnel</span>
                                @elseif($incOuverts <= 2)
                                    <span class="badge badge-warning">{{ $incOuverts }} incident(s)</span>
                                @else
                                    <span class="badge badge-danger">{{ $incOuverts }} pannes</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 0.5rem;">
                                    <a href="{{ route('structures.show', $struct->id) }}" class="btn btn-secondary btn-sm" title="Consulter la fiche">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    @if(Auth::user()->isAdmin())
                                        <a href="{{ route('structures.edit', $struct->id) }}" class="btn btn-secondary btn-sm" title="Modifier" style="color: hsl(var(--warning));">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form id="deleteForm-{{ $struct->id }}" 
                                            action="{{ route('structures.destroy', $struct->id) }}" 
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <button type="button" onclick="confirmDelete('deleteForm-{{ $struct->id }}', 
                                            'Êtes-vous sûr de vouloir supprimer la structure {{ addslashes($struct->nom) }} ? Cette action est irréversible.')" 
                                            class="btn btn-secondary btn-sm" 
                                            title="Supprimer" 
                                            style="color: hsl(var(--danger));">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="pagination-wrapper">
            {{ $structures->links() }}
        </div>
    @endif
</div>

<style>
@media print {
    .sidebar, .filters-bar, .btn, .top-bar { display: none !important; }
    .main-content { margin: 0 !important; padding: 0 !important; }
    .app-table { width: 100%; font-size: 11px; }
}
</style>

<!-- MODAL IMPORT EXCEL -->
<div id="importModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; padding: 2rem; max-width: 480px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
        <h3 style="margin: 0 0 1rem; color: var(--text-primary); font-size: 1.25rem;">
            <i class="fas fa-file-import"></i> Importer des Structures (Excel)
        </h3>
        <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1.25rem;">
            Sélectionnez un fichier <code>.xlsx</code> ou <code>.xls</code>. Les colonnes doivent correspondre au format exporté (Nom, Acronyme, Type, Localité, Latitude, Longitude).
        </p>
        <form action="{{ route('import.structures') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 1.5rem;">
                <input type="file" name="fichier_excel" accept=".xlsx,.xls" required class="form-control">
            </div>
            <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('importModal').style.display='none'" class="btn btn-secondary">
                    Annuler
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Importer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
