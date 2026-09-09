@extends('layouts.app')

@section('title', 'Gestion des Utilisateurs')

@section('content')
<div class="page-header">
    <div class="top-bar">
        <div>
            <h1 class="page-title">Utilisateurs & Accès</h1>
            <p class="page-subtitle">Gestion des comptes et des droits d'accès</p>
        </div>
        <a href="{{ route('utilisateurs.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Nouvel Utilisateur
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="filters-bar">
        <form method="GET" action="{{ route('utilisateurs.index') }}">
            <div style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
                <div class="filter-group" style="flex: 1; min-width: 200px;">
                    <input type="text" name="search" class="form-control"
                        placeholder="Nom, prénom, email..."
                        value="{{ request('search') }}">
                </div>
                <div class="filter-group" style="min-width: 200px;">
                    <select name="role_id" class="form-control">
                        <option value="">Tous les rôles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->libelle }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i>
                    </button>
                    <a href="{{ route('utilisateurs.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
    </div>

    <table class="app-table">
        <thead>
            <tr>
                <th>Nom complet</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Rôle</th>
                <th>Structure</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($utilisateurs as $user)
            <tr>
                <td>{{ $user->prenom }} {{ $user->nom }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->telephone ?? '—' }}</td>
                <td>
                    <span class="badge badge-info">{{ $user->role->libelle }}</span>
                </td>
                <td>{{ $user->structure->nom ?? '—' }}</td>
                <td>
                    <a href="{{ route('utilisateurs.edit', $user) }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-edit"></i>
                    </a>
                   <form id="deleteForm-{{ $user->id }}" 
                        action="{{ route('utilisateurs.destroy', $user->id) }}" 
                        method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                    </form>
                    <button onclick="confirmDelete('deleteForm-{{ $user->id }}', 
                        'Êtes-vous sûr de vouloir supprimer l\'utilisateur {{ $user->prenom }} {{ $user->nom }} ? Cette action est irréversible.')" 
                        class="btn btn-secondary btn-sm" 
                        title="Supprimer" 
                        style="color: hsl(var(--danger));">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center">Aucun utilisateur trouvé.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding:1rem">{{ $utilisateurs->links() }}</div>
</div>
@endsection