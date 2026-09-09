@extends('layouts.app')
@section('title', 'Localités')

@section('content')
    <div class="page-header">
        <div class="top-bar">
            <div>
                <h1 class="page-title">Localités</h1>
                <p class="page-subtitle">Gestion des localités du territoire</p>
            </div>
            <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
                <a href="{{ route('export.localites') }}" class="btn btn-secondary">
                    <i class="fas fa-file-excel"></i> Exporter Excel
                </a>
                @if(Auth::user()->isAdmin())
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('importModal').style.display='flex'">
                    <i class="fas fa-file-import"></i> Importer Excel
                </button>
                <a href="{{ route('localites.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nouvelle Localité
                </a>
                @endif
            </div>
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

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="filters-bar">
            <form method="GET" action="{{ route('localites.index') }}">
                <div style="display:flex; gap:1rem; align-items:flex-end; flex-wrap:wrap;">
                    <div class="filter-group" style="flex:1; min-width:200px;">
                        <input type="text" name="search" class="form-control"
                            placeholder="Rechercher une localité..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="filter-group" style="min-width:200px;">
                        <select name="type_localite_id" class="form-control">
                            <option value="">Tous les types</option>
                            @foreach($typesLocalites as $type)
                                <option value="{{ $type->id }}" {{ request('type_localite_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->libelle }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display:flex; gap:0.5rem;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i>
                        </button>
                        <a href="{{ route('localites.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        @if($localites->isEmpty())
            <p style="text-align:center; color:var(--text-secondary); padding:3rem 0;">
                Aucune localité trouvée.
            </p>
        @else
            <div class="table-container" style="overflow-x:auto;">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Type</th>
                            <th>Code</th>
                            <th>Localité parente</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($localites as $localite)
                        <tr>
                            
                            <td style="font-weight:600;">{{ $localite->nom }}</td>
                            <td>{{ $localite->typeLocalite?->libelle ?? '-' }}</td>
                            <td>{{ $localite->code_localite }}</td>
                            <td>{{ $localite->parent?->nom ?? '-' }}</td>
                            <td style="text-align:right;">
                                <div style="display:inline-flex; gap:0.5rem;">
                                    @if(Auth::user()->isAdmin())
                                    <a href="{{ route('localites.edit', $localite->id) }}"
                                        class="btn btn-secondary btn-sm"
                                        title="Modifier">
                                        <i class="fas fa-pen-to-square"></i>
                                    </a>
                                    <form id="deleteForm-{{ $localite->id }}"
                                        action="{{ route('localites.destroy', $localite->id) }}"
                                        method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button onclick="confirmDelete('deleteForm-{{ $localite->id }}',
                                        'Êtes-vous sûr de vouloir supprimer la localité {{ $localite->nom }} ?')"
                                        class="btn btn-secondary btn-sm"
                                        title="Supprimer"
                                        style="color:hsl(var(--danger));">
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
                {{ $localites->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL IMPORT EXCEL -->
    <div id="importModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 12px; padding: 2rem; max-width: 480px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
            <h3 style="margin: 0 0 1rem; color: var(--text-primary); font-size: 1.25rem;">
                <i class="fas fa-file-import"></i> Importer des Localités (Excel)
            </h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1.25rem;">
                Sélectionnez un fichier <code>.xlsx</code> ou <code>.xls</code>. Les colonnes doivent correspondre au format exporté (Nom de la Localité, Type de Localité).
            </p>
            <form action="{{ route('import.localites') }}" method="POST" enctype="multipart/form-data">
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