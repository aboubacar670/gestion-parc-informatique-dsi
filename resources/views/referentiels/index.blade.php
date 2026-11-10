@extends('layouts.app')

@section('title', 'Référentiels')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Référentiels</h1>
        <p class="page-subtitle">Gestion des types, états et statuts</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@php
$sections = [
    ['label' => "Types d'équipements",    'type' => 'type_equipement',     'items' => $typesEquipements],
    ['label' => "États d'équipements",    'type' => 'etat_equipement',     'items' => $etatsEquipements],
    ['label' => "Types de structures",    'type' => 'type_structure',      'items' => $typesStructures],
    ['label' => "Types de localités",     'type' => 'type_localite',       'items' => $typesLocalites],
    ['label' => "Types d'interventions",  'type' => 'type_intervention',   'items' => $typesInterventions],
    ['label' => "Statuts d'incidents",    'type' => 'statut_incident',     'items' => $statutsIncidents],
    ['label' => "Statuts d'interventions",'type' => 'statut_intervention', 'items' => $statutsInterventions],
    ['label' => "Profils Techniciens",    'type' => 'profil_technicien',   'items' => $profilsTechniciens],
];
@endphp

@foreach($sections as $section)
<div class="card" style="margin-bottom:1.5rem">
    <div style="display:flex; justify-content:space-between; align-items:center; padding:1rem 1.5rem; border-bottom:1px solid #e2e8f0">
        <div style="display:flex; justify-content:space-between; align-items:center;">
    <h3 style="margin:0;">{{ $section['label'] }}</h3>
    @if($section['type'] === 'localite')
        <a href="{{ route('export.localites') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-file-excel"></i> Exporter Excel
        </a>
    @endif
</div>

    <div style="padding:1rem 1.5rem">
        {{-- Formulaire ajout --}}
        <form method="POST" action="{{ route('referentiels.store') }}" style="display:flex; gap:0.5rem; margin-bottom:1rem">
            @csrf
            <input type="hidden" name="type" value="{{ $section['type'] }}">
            <input type="text" name="libelle" class="form-control" placeholder="Nouveau libellé..." required style="max-width:300px">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter
            </button>
        </form>

        {{-- Liste --}}
        <table class="app-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Libellé</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($section['items'] as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>
                        <form method="POST" action="{{ route('referentiels.update', [$section['type'], $item->id]) }}"
                              style="display:flex; gap:0.5rem">
                            @csrf @method('PUT')
                            <input type="text" name="libelle" class="form-control"
                                   value="{{ $item->libelle }}" required style="max-width:300px">
                            <button type="submit" class="btn btn-secondary btn-sm">
                                <i class="fas fa-save"></i>
                            </button>
                        </form>
                    </td>
                    <td>
                        <form id="deleteForm-{{ $item->id }}" 
                            action="{{ route('referentiels.destroy', [$section['type'], $item->id]) }}" 
                            method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                        </form>
                        <button onclick="confirmDelete('deleteForm-{{ $item->id }}', 
                            'Êtes-vous sûr de vouloir supprimer l\'élément {{ $item->libelle }} ? Cette action est irréversible.')" 
                            class="btn btn-secondary btn-sm" 
                            title="Supprimer" 
                            style="color: hsl(var(--danger));">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align:center">Aucun élément.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endforeach
@endsection