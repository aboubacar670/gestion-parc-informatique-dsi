@extends('layouts.app')

@section('title', 'Gestion des Équipements')

@section('content')
<div class="top-bar">
    <div>
        <h1 class="page-title">Équipements &amp; Matériels</h1>
        <p class="page-subtitle">Inventaire et suivi technique du parc informatique</p>
    </div>
    <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
        <a href="{{ route('export.equipements') }}" class="btn btn-secondary">
            <i class="fas fa-file-excel"></i> Exporter Excel
        </a>
        @if(Auth::user()->isAdmin() || Auth::user()->isAgent())
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('importModal').style.display='flex'">
            <i class="fas fa-file-import"></i> Importer Excel
        </button>
        @endif
        <button onclick="window.print()" class="btn btn-secondary">
            <i class="fas fa-print"></i> Imprimer / PDF
        </button>
        @if(Auth::user()->isAdmin() || Auth::user()->isAgent())
        <a href="{{ route('equipements.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Nouvel Équipement
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
    <form action="{{ route('equipements.index') }}" method="GET" class="filters-form">
        <div class="form-group">
            <label for="search" class="form-label" style="font-size: 0.75rem;">Rechercher</label>
            <input type="text" id="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Nom, marque, specs...">
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
            <label for="type_equipement_id" class="form-label" style="font-size: 0.75rem;">Type de matériel</label>
            <select id="type_equipement_id" name="type_equipement_id" class="form-control">
                <option value="">Tous</option>
                @foreach($typesEquipement as $type)
                    <option value="{{ $type->id }}" {{ request('type_equipement_id') == $type->id ? 'selected' : '' }}>
                        {{ $type->libelle }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="etat_equipement_id" class="form-label" style="font-size: 0.75rem;">État de fonctionnement</label>
            <select id="etat_equipement_id" name="etat_equipement_id" class="form-control">
                <option value="">Tous</option>
                @foreach($etatsEquipement as $etat)
                    <option value="{{ $etat->id }}" {{ request('etat_equipement_id') == $etat->id ? 'selected' : '' }}>
                        {{ $etat->libelle }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-primary" style="padding: 0.625rem 1rem;">
                <i class="fa-solid fa-filter"></i>
            </button>
            <a href="{{ route('equipements.index') }}" class="btn btn-secondary" style="padding: 0.625rem 1rem;" title="Réinitialiser">
                <i class="fa-solid fa-rotate"></i>
            </a>
        </div>
    </form>
</div>

<!-- EQUIPEMENTS TABLE -->
<div class="card">
    @if($equipements->isEmpty())
        <p style="text-align: center; color: var(--text-secondary); padding: 3rem 0;">Aucun matériel ne correspond à votre recherche.</p>
    @else
        <div class="table-container">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Nom du matériel</th>
                        <th>Type</th>
                        <th>Caractéristiques</th>
                        <th>Structure / Site</th>
                        <th>Modèle/Version</th>
                        <th>État</th>
                        @if(Auth::user()->isAdmin() || Auth::user()->isAgent())
                        @endif
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($equipements as $equip)
                        <tr>
                            <td style="font-weight: 600; color: var(--text-primary);">{{ $equip->nom }}</td>
                            <td>{{ $equip->typeEquipement?->libelle }}</td>
                            <td style="font-size: 0.8rem; color: var(--text-secondary);">
                                {{ Str::limit($equip->caracteristique, 45) }}
                            </td>
                            <td>
                                @if($equip->structure)
                                    <a href="{{ route('structures.show', $equip->structure_id) }}" style="color: inherit; text-decoration: none; font-weight: 500;">
                                        {{ $equip->structure->nom }}
                                    </a>
                                @else
                                    <span style="color: var(--text-muted);">Non affecté</span>
                                @endif
                            </td>
                            <td>{{ $equip->version ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $equip->etatEquipement?->libelle === 'Fonctionnel' ? 'badge-success' : ($equip->etatEquipement?->libelle === 'En panne' ? 'badge-warning' : 'badge-danger') }}">
                                    {{ $equip->etatEquipement?->libelle }}
                                </span>
                            </td>

                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 0.5rem;">
                                    @if(Auth::user()->isAdmin() || Auth::user()->isAgent())
                                        <a href="{{ route('equipements.edit', $equip->id) }}" class="btn btn-secondary btn-sm" title="Modifier" style="color: hsl(var(--warning));">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    @endif
                                    @if(Auth::user()->isAdmin())
                                        <form id="deleteForm-{{ $equip->id }}" 
                                                action="{{ route('equipements.destroy', $equip->id) }}" 
                                                method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            <button type="button" onclick="confirmDelete('deleteForm-{{ $equip->id }}', 
                                                'Êtes-vous sûr de vouloir supprimer l\'équipement {{ addslashes($equip->nom) }} ? Cette action est irréversible.')" 
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
            {{ $equipements->links() }}
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
            <i class="fas fa-file-import"></i> Importer des Équipements (Excel)
        </h3>
        <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1.25rem;">
            Sélectionnez un fichier <code>.xlsx</code> ou <code>.xls</code>. Les colonnes doivent correspondre au format exporté (Nom matériel, Type, Caractéristiques, Structure, Modèle/Version, État).
        </p>
        <form action="{{ route('import.equipements') }}" method="POST" enctype="multipart/form-data">
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
