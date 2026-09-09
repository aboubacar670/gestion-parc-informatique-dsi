@extends('layouts.app')

@section('title', $structure->nom)

@section('content')
<div class="top-bar">
    <div>
        <h1 class="page-title">{{ $structure->nom }}</h1>
        <p class="page-subtitle"><i class="fa-solid fa-school"></i> Fiche d'établissement éducatif</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="{{ route('structures.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Retour
        </a>
        @if(Auth::user()->isAdmin())
            <a href="{{ route('structures.edit', $structure->id) }}" class="btn btn-secondary" style="color: hsl(var(--warning));">
                <i class="fa-solid fa-pen-to-square"></i> Modifier
            </a>
        @endif
    </div>
</div>

<div class="detail-grid">
    
    <!-- DETAIL FORM & GEOLOCATION MAP -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        <!-- Fiche Technique -->
        <div class="card" style="margin-bottom: 0;">
            <h2 class="card-title"><i class="fa-solid fa-circle-info" style="color: hsl(var(--primary));"></i> Fiche Technique</h2>
            <ul class="meta-list">
                <li class="meta-item">
                    <span class="meta-label">Nom Complet</span>
                    <span class="meta-value">{{ $structure->nom }}</span>
                </li>
                <li class="meta-item">
                    <span class="meta-label">Acronyme</span>
                    <span class="meta-value">{{ $structure->acronyme ?? '-' }}</span>
                </li>
                <li class="meta-item">
                    <span class="meta-label">Type d'établissement</span>
                    <span class="meta-value">{{ $structure->typeStructure?->libelle }}</span>
                </li>
                <li class="meta-item">
                    <span class="meta-label">Localité / Commune</span>
                    <span class="meta-value">{{ $structure->localite?->nom }}</span>
                </li>
                <li class="meta-item">
                    <span class="meta-label">Structure Parente</span>
                    <span class="meta-value">{{ $structure->parent?->nom ?? 'Aucune (Rattachement DSI direct)' }}</span>
                </li>
                <li class="meta-item">
                    <span class="meta-label">Latitude</span>
                    <span class="meta-value">{{ $structure->latitude ?? 'Non spécifiée' }}</span>
                </li>
                <li class="meta-item">
                    <span class="meta-label">Longitude</span>
                    <span class="meta-value">{{ $structure->longitude ?? 'Non spécifiée' }}</span>
                </li>
            </ul>
        </div>
        
        <!-- Carte Locale -->
        @if($structure->latitude && $structure->longitude)
            <div class="card" style="margin-bottom: 0;">
                <h2 class="card-title"><i class="fa-solid fa-location-crosshairs"></i> Emplacement Géographique</h2>
                <div id="local-map" style="height: 250px; border-radius: 8px; overflow: hidden; border: 1px solid var(--bg-accent);"></div>
            </div>
        @endif
        
    </div>
    
    <!-- EQUIPEMENTS & MATERIELS -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        <div class="card" style="margin-bottom: 0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h2 class="card-title" style="margin-bottom: 0;">
                    <i class="fa-solid fa-laptop-code" style="color: hsl(var(--success));"></i> Parc Informatique
                </h2>
                @if(Auth::user()->isAdmin() || Auth::user()->isAgent())
                    <a href="{{ route('equipements.create', ['structure_id' => $structure->id]) }}" class="btn btn-secondary btn-sm" style="color: hsl(var(--primary));">
                        <i class="fa-solid fa-plus"></i> Ajouter
                    </a>
                @endif
            </div>
            
            @if($structure->equipements->isEmpty())
                <p style="font-size: 0.875rem; color: var(--text-secondary); text-align: center; padding: 2rem 0;">Aucun équipement enregistré pour cette structure.</p>
            @else
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach($structure->equipements as $equip)
                        <div style="background-color: #f8fafc; padding: 0.75rem; border-radius: 8px; border: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="font-weight: 600; font-size: 0.875rem; color: var(--text-primary);">{{ $equip->nom }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                                    {{ $equip->typeEquipement?->libelle }} • Vol. {{ $equip->version ?? 'N/A' }}
                                </div>
                            </div>
                            <span class="badge {{ $equip->etatEquipement?->libelle === 'Fonctionnel' ? 'badge-success' : ($equip->etatEquipement?->libelle === 'En panne' ? 'badge-warning' : 'badge-danger') }}" style="font-size: 0.65rem;">
                                {{ $equip->etatEquipement?->libelle }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        
    </div>
    
</div>

<!-- SECTION DU BAS : INCIDENTS ET INTERVENTIONS -->
<div style="margin-top: 1.5rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
    
    <!-- SIGNALEMENTS D'INCIDENTS -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2 class="card-title" style="margin-bottom: 0;">
                <i class="fa-solid fa-triangle-exclamation" style="color: hsl(var(--danger));"></i> Signaler &amp; Pannes
            </h2>
            <a href="{{ route('incidents.create', ['structure_id' => $structure->id]) }}" class="btn btn-secondary btn-sm" style="color: hsl(var(--danger));">
                <i class="fa-solid fa-plus"></i> Signaler
            </a>
        </div>
        
        @if($structure->incidents->isEmpty())
            <p style="font-size: 0.875rem; color: var(--text-secondary); text-align: center; padding: 2rem 0;">Aucun incident signalé.</p>
        @else
            <div class="table-container">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Description</th>
                            <th>Date</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($structure->incidents as $inc)
                            <tr>
                                <td>
                                    <a href="{{ route('incidents.show', $inc->id) }}" style="font-weight: 600; text-decoration: none; color: hsl(var(--primary));">
                                        {{ $inc->code }}
                                    </a>
                                </td>
                                <td>{{ Str::limit($inc->libelle, 35) }}</td>
                                <td>{{ \Carbon\Carbon::parse($inc->date_intervention)->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge {{ $inc->statutIncident?->libelle === 'Résolu' ? 'badge-success' : ($inc->statutIncident?->libelle === 'En cours de traitement' ? 'badge-warning' : 'badge-danger') }}">
                                        {{ $inc->statutIncident?->libelle }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    
    <!-- MISSIONS DE MAINTENANCE / INTERVENTIONS -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2 class="card-title" style="margin-bottom: 0;">
                <i class="fa-solid fa-screwdriver-wrench" style="color: hsl(var(--warning));"></i> Missions d'Intervention
            </h2>
            @if(Auth::user()->isAdmin() || Auth::user()->isAgent())
                <a href="{{ route('interventions.create', ['structure_id' => $structure->id]) }}" class="btn btn-secondary btn-sm" style="color: hsl(var(--warning));">
                    <i class="fa-solid fa-plus"></i> Planifier
                </a>
            @endif
        </div>
        
        @if($structure->interventions->isEmpty())
            <p style="font-size: 0.875rem; color: var(--text-secondary); text-align: center; padding: 2rem 0;">Aucune intervention enregistrée.</p>
        @else
            <div class="table-container">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Libellé</th>
                            <th>Début</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($structure->interventions as $int)
                            <tr>
                                <td>
                                    <a href="{{ route('interventions.edit', $int->id) }}" style="font-weight: 600; text-decoration: none; color: hsl(var(--primary));">
                                        {{ $int->code }}
                                    </a>
                                </td>
                                <td>{{ Str::limit($int->libelle, 35) }}</td>
                                <td>{{ \Carbon\Carbon::parse($int->date_debut)->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge {{ $int->statutIntervention?->libelle === 'Terminée' ? 'badge-success' : ($int->statutIntervention?->libelle === 'Annulée' ? 'badge-danger' : 'badge-info') }}">
                                        {{ $int->statutIntervention?->libelle }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    
</div>
@endsection

@if($structure->latitude && $structure->longitude)
    @section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var lat = {{ $structure->latitude }};
            var lng = {{ $structure->longitude }};
            
            var map = L.map('local-map').setView([lat, lng], 13);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
            
            var icon = L.divIcon({
                html: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#2563eb" width="32px" height="32px"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>`,
                className: 'custom-leaflet-marker',
                iconSize: [32, 32],
                iconAnchor: [16, 32]
            });
            
            L.marker([lat, lng], { icon: icon }).addTo(map)
                .bindPopup("<strong>{{ $structure->nom }}</strong><br>Coordonnées : " + lat + ", " + lng)
                .openPopup();
        });
    </script>
    @endsection
@endif
