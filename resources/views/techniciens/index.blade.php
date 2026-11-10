@extends('layouts.app')

@section('title', 'Gestion des Techniciens')

@section('content')
<div class="top-bar">
    <div>
        <h1 class="page-title">Techniciens</h1>
        <p class="page-subtitle">Gestion de l'équipe technique de la DSI</p>
    </div>
    @if(Auth::user()->isAdmin() || Auth::user()->isAgent())
    <a href="{{ route('techniciens.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Nouveau technicien
    </a>
    @endif
</div>

<!-- FILTRES -->
<div class="filters-bar">
    <form action="{{ route('techniciens.index') }}" method="GET" class="filters-form">
        <div class="form-group">
            <label class="form-label" style="font-size:.75rem;">Rechercher</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Nom ou prénom...">
        </div>
        <div class="form-group">
            <label class="form-label" style="font-size:.75rem;">Profil</label>
            <select name="profil_id" class="form-control">
                <option value="">Tous les profils</option>
                @foreach($profils as $profil)
                    <option value="{{ $profil->id }}" {{ request('profil_id') == $profil->id ? 'selected' : '' }}>
                        {{ $profil->libelle }}
                    </option>
                @endforeach
            </select>
        </div>
        <div style="display:flex; gap:.5rem;">
            <button type="submit" class="btn btn-primary" style="padding:.625rem 1rem;"><i class="fa-solid fa-filter"></i></button>
            <a href="{{ route('techniciens.index') }}" class="btn btn-secondary" style="padding:.625rem 1rem;" title="Réinitialiser"><i class="fa-solid fa-rotate"></i></a>
        </div>
    </form>
</div>

<!-- TABLEAU -->
<div class="card">
    @if($techniciens->isEmpty())
        <div style="text-align:center; padding:3rem 0; color:var(--text-secondary);">
            <i class="fa-solid fa-users-slash" style="font-size:2.5rem; margin-bottom:1rem; display:block; opacity:.4;"></i>
            <p>Aucun technicien enregistré.</p>
            @if(Auth::user()->isAdmin() || Auth::user()->isAgent())
                <a href="{{ route('techniciens.create') }}" class="btn btn-primary" style="margin-top:.5rem;">
                    <i class="fa-solid fa-plus"></i> Ajouter le premier technicien
                </a>
            @endif
        </div>
    @else
        <div class="table-container">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Technicien</th>
                        <th>Profil</th>
                        <th>Téléphone</th>
                        <th style="text-align:center;">Interventions</th>
                        <th style="text-align:center;">Incidents</th>
                        @if(Auth::user()->isAdmin() || Auth::user()->isAgent())
                            <th style="text-align:right;">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($techniciens as $tech)
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:.75rem;">
                                    <div style="width:36px; height:36px; border-radius:50%; background:hsl(var(--primary)/0.12); color:hsl(var(--primary)); font-weight:700; display:flex; align-items:center; justify-content:center; font-size:.85rem; flex-shrink:0;">
                                        {{ strtoupper(substr($tech->prenom,0,1) . substr($tech->nom,0,1)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight:600; color:var(--text-primary);">{{ $tech->prenom }} {{ $tech->nom }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $tech->profil->libelle ?? '—' }}</span>
                            </td>
                            <td>
                                @if($tech->telephone)
                                    <a href="tel:{{ $tech->telephone }}" style="color:inherit; text-decoration:none;">
                                        <i class="fa-solid fa-phone" style="font-size:.8rem; color:hsl(var(--success));"></i>
                                        {{ $tech->telephone }}
                                    </a>
                                @else
                                    <span style="color:var(--text-muted);">—</span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                <span class="badge {{ $tech->interventions_count > 0 ? 'badge-info' : '' }}" style="{{ $tech->interventions_count == 0 ? 'color:var(--text-muted);' : '' }}">
                                    {{ $tech->interventions_count }}
                                </span>
                            </td>
                            <td style="text-align:center;">
                                <span class="badge {{ $tech->incidents_count > 0 ? 'badge-danger' : '' }}" style="{{ $tech->incidents_count == 0 ? 'color:var(--text-muted);' : '' }}">
                                    {{ $tech->incidents_count }}
                                </span>
                            </td>
                            @if(Auth::user()->isAdmin() || Auth::user()->isAgent())
                            <td style="text-align:right;">
                                <div style="display:inline-flex; gap:.5rem;">
                                    <a href="{{ route('techniciens.edit', $tech->id) }}" class="btn btn-secondary btn-sm" title="Modifier" style="color:hsl(var(--warning));">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    @if(Auth::user()->isAdmin())
                                        @if($tech->interventions_count > 0 || $tech->incidents_count > 0)
                                            <button class="btn btn-secondary btn-sm" title="Impossible de supprimer : technicien assigné"
                                                style="color:var(--text-muted); cursor:not-allowed; opacity:.5;" disabled>
                                                <i class="fa-solid fa-lock"></i>
                                            </button>
                                        @else
                                            <form id="deleteForm-{{ $tech->id }}" action="{{ route('techniciens.destroy', $tech->id) }}" method="POST" style="display:inline;">
                                                @csrf @method('DELETE')
                                            </form>
                                            <button onclick="confirmDelete('deleteForm-{{ $tech->id }}', 'Supprimer {{ $tech->prenom }} {{ $tech->nom }} ? Cette action est irréversible.')"
                                                class="btn btn-secondary btn-sm" title="Supprimer" style="color:hsl(var(--danger));">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper">{{ $techniciens->links() }}</div>
    @endif
</div>
@endsection
