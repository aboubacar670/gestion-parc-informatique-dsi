@extends('layouts.app')

@section('title', 'Tableau de Bord')

@section('content')
<div class="top-bar">
    <div>
        <h1 class="page-title">Tableau de Bord</h1>
        <p class="page-subtitle">Rapport en temps réel des infrastructures informatiques</p>
    </div>
</div>

<!-- STATS GRID -->
<div class="stats-grid">
    <div class="stat-card">
        <div>
            <div class="stat-value">{{ $stats['structures'] }}</div>
            <div class="stat-label">Structures</div>
        </div>
        <div class="stat-icon-wrapper icon-primary">
            <i class="fa-solid fa-school"></i>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-value">{{ $stats['equipements'] }}</div>
            <div class="stat-label">Équipements</div>
        </div>
        <div class="stat-icon-wrapper icon-success">
            <i class="fa-solid fa-laptop-code"></i>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-value">{{ $stats['incidents_ouverts'] }}</div>
            <div class="stat-label">Incidents Actifs</div>
        </div>
        <div class="stat-icon-wrapper icon-danger">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-value">{{ $stats['interventions_planifiees'] }}</div>
            <div class="stat-label">Interventions</div>
        </div>
        <div class="stat-icon-wrapper icon-warning">
            <i class="fa-solid fa-screwdriver-wrench"></i>
        </div>
    </div>
</div>

<!-- CARTOGRAPHIE ET INFOS -->
<div class="detail-grid">
    
    <!-- CARTE INTERACTIVE -->
    <div class="map-card" style="margin-bottom: 0;">
        <div class="map-header">
            <h2 class="card-title" style="margin-bottom: 0;">
                <i class="fa-solid fa-map-location-dot"></i> Cartographie du Burkina Faso
            </h2>
            <div style="font-size: 0.75rem; display: flex; gap: 1rem;">
                <span style="display: flex; align-items: center; gap: 0.25rem;"><span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background-color: #22c55e;"></span> Tout fonctionnel</span>
                <span style="display: flex; align-items: center; gap: 0.25rem;"><span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background-color: #eab308;"></span> Incidents légers</span>
                <span style="display: flex; align-items: center; gap: 0.25rem;"><span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background-color: #ef4444;"></span> Pannes majeures</span>
            </div>
        </div>
        
        <div id="map" class="map-container"></div>
    </div>
    
    <!-- SECTIONS LATÉRALES RAPIDES -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        <!-- DERNIERS INCIDENTS -->
        <div class="card" style="margin-bottom: 0; flex-grow: 1;">
            <h2 class="card-title"><i class="fa-solid fa-triangle-exclamation" style="color: hsl(var(--danger));"></i> Derniers Incidents</h2>
            @if($derniersIncidents->isEmpty())
                <p style="font-size: 0.875rem; color: var(--text-secondary); text-align: center; padding: 2rem 0;">Aucun incident en cours.</p>
            @else
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @foreach($derniersIncidents as $inc)
                        <div style="border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.25rem;">
                                <a href="{{ route('incidents.show', $inc->id) }}" style="font-weight: 600; text-decoration: none; color: var(--text-primary); font-size: 0.9rem;">
                                    {{ $inc->libelle }}
                                </a>
                                <span class="badge {{ $inc->statutIncident?->libelle === 'Résolu' ? 'badge-success' : ($inc->statutIncident?->libelle === 'En cours de traitement' ? 'badge-warning' : 'badge-danger') }}" style="font-size: 0.65rem;">
                                    {{ $inc->statutIncident?->libelle }}
                                </span>
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">
                                <i class="fa-solid fa-school"></i> {{ $inc->structure?->nom }} • <i class="fa-solid fa-calendar"></i> {{ \Carbon\Carbon::parse($inc->date_intervention)->format('d/m/Y') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        <!-- GRAPHIQUES -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            <div class="card">
                        <h2 class="card-title">États des équipements</h2>
                        <canvas id="chartEquipements" height="200"></canvas>
                    </div>
                    <div class="card">
                        <h2 class="card-title">Incidents par mois</h2>
                        <canvas id="chartIncidents" height="200"></canvas>
            </div>
        </div>
        
        <!-- PROCHAINES MISSIONS -->
        <div class="card" style="margin-bottom: 0; flex-grow: 1;">
            <h2 class="card-title"><i class="fa-solid fa-screwdriver-wrench" style="color: hsl(var(--warning));"></i> Missions Actives</h2>
            @if($prochainesInterventions->isEmpty())
                <p style="font-size: 0.875rem; color: var(--text-secondary); text-align: center; padding: 2rem 0;">Aucune intervention planifiée.</p>
            @else
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @foreach($prochainesInterventions as $int)
                        <div style="border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.25rem;">
                                <a href="{{ route('interventions.edit', $int->id) }}" style="font-weight: 600; text-decoration: none; color: var(--text-primary); font-size: 0.9rem;">
                                    {{ $int->libelle }}
                                </a>
                                <span class="badge {{ $int->statutIntervention?->libelle === 'Terminée' ? 'badge-success' : 'badge-info' }}" style="font-size: 0.65rem;">
                                    {{ $int->statutIntervention?->libelle }}
                                </span>
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">
                                <i class="fa-solid fa-school"></i> {{ $int->structure?->nom }} • <i class="fa-solid fa-wrench"></i> {{ $int->typeIntervention?->libelle }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        
    </div>
    
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialiser la carte sur le Burkina Faso (centre : Ouagadougou)
        var map = L.map('map').setView([12.3714, -1.5197], 7);

        // Fonds de carte OpenStreetMap avec un style moderne
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Données des structures injectées de Laravel
        var structures = @json($structuresPourCarte);

        // Définir des couleurs de pins personnalisées via SVG
        function createSvgIcon(color) {
            var hex = "#22c55e"; // Vert
            if (color === 'orange') hex = "#eab308";
            if (color === 'rouge') hex = "#ef4444";
            
            return L.divIcon({
                html: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="${hex}" width="32px" height="32px"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>`,
                className: 'custom-leaflet-marker',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });
        }

        // Ajouter les marqueurs sur la carte
        structures.forEach(function(structure) {
            var marker = L.marker([structure.lat, structure.lng], {
                icon: createSvgIcon(structure.couleur)
            }).addTo(map);

            var popupContent = `
                <div style="font-family: 'Outfit', sans-serif; padding: 0.25rem;">
                    <h4 style="margin: 0 0 0.5rem 0; color: #0f172a; font-weight: 600;">${structure.nom}</h4>
                    <p style="margin: 0 0 0.5rem 0; font-size: 0.8rem; color: #475569;">
                        Incidents actifs : <strong>${structure.incidents}</strong>
                    </p>
                    <a href="${structure.url_fiche}" style="display: inline-block; padding: 0.35rem 0.75rem; background-color: #2563eb; color: white; text-decoration: none; border-radius: 4px; font-size: 0.75rem; font-weight: 500;">
                        Voir la fiche complète
                    </a>
                </div>
            `;
            marker.bindPopup(popupContent);
        });
    });
</script>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Graphique camembert — États des équipements
const ctxEquip = document.getElementById('chartEquipements').getContext('2d');
new Chart(ctxEquip, {
    type: 'doughnut',
    data: {
        labels: @json($graphEquipLabels),
        datasets: [{
            data: @json($graphEquipData),
            backgroundColor: ['#0ff362','#cf0b0b', '#f59e0b', '#445eef'],
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
    }
});

        // Graphique barres — Incidents par mois
        const ctxMois = document.getElementById('chartIncidents').getContext('2d');
        new Chart(ctxMois, {
            type: 'bar',
            data: {
                labels: @json($graphMoisLabels),
                datasets: [{
                    label: 'Incidents',
                    data: @json($graphMoisData),
                    backgroundColor: '#3b82f6',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
});
</script>
@endsection
