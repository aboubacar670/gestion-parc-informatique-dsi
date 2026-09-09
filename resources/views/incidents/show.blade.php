@extends('layouts.app')

@section('title', 'Détails de l\'incident ' . $incident->code)

@section('content')
<div class="top-bar">
    <div>
        <h1 class="page-title">Incident {{ $incident->code }}</h1>
        <p class="page-subtitle"><i class="fa-solid fa-triangle-exclamation"></i> Fiche de suivi d'incident technique</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="{{ route('incidents.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Retour
        </a>
    </div>
</div>

<div class="detail-grid">
    
    <!-- INFOS PRINCIPALES DE L'INCIDENT -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        <div class="card" style="margin-bottom: 0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h2 class="card-title" style="margin-bottom: 0;">{{ $incident->libelle }}</h2>
                <span class="badge {{ $incident->statutIncident?->libelle === 'Résolu' ? 'badge-success' : ($incident->statutIncident?->libelle === 'En cours de traitement' ? 'badge-warning' : 'badge-danger') }}" style="font-size: 0.85rem;">
                    {{ $incident->statutIncident?->libelle }}
                </span>
            </div>
            
            <p style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 1.5rem; background-color: #f8fafc; padding: 1rem; border-radius: 8px; border-left: 4px solid #94a3b8; font-style: italic; white-space: pre-line;">{{ $incident->description ?? 'Aucune description détaillée fournie.' }}</p>
            
            <h3 style="font-size: 0.95rem; font-weight: 600; margin-bottom: 0.75rem; color: var(--text-primary);">
                <i class="fa-solid fa-laptop-code"></i> Matériel(s) concerné(s)
            </h3>
            
            @if($incident->equipements->isEmpty())
                <p style="font-size: 0.8rem; color: var(--text-secondary); font-style: italic;">Aucun matériel informatique spécifique n'a été rattaché à cette panne (signalement général sur le site).</p>
            @else
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    @foreach($incident->equipements as $equip)
                        <div style="background-color: #f8fafc; padding: 0.75rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <div style="font-weight: 600; font-size: 0.85rem;">{{ $equip->nom }}</div>
                            <div style="font-size: 0.7rem; color: var(--text-secondary);">
                                {{ $equip->typeEquipement?->libelle }} • Version {{ $equip->version ?? 'N/A' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        
    </div>
    
    <!-- FICHE STRUCTURE ET STATUT -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        <!-- Fiche Technique Structure -->
        <div class="card" style="margin-bottom: 0;">
            <h2 class="card-title"><i class="fa-solid fa-school"></i> Structure d'Origine</h2>
            <ul class="meta-list">
                <li class="meta-item">
                    <span class="meta-label">Nom</span>
                    <span class="meta-value">
                        <a href="{{ route('structures.show', $incident->structure_id) }}" style="color: hsl(var(--primary)); text-decoration: none;">
                            {{ $incident->structure?->nom }}
                        </a>
                    </span>
                </li>
                <li class="meta-item">
                    <span class="meta-label">Localité</span>
                    <span class="meta-value">{{ $incident->structure?->localite?->nom }}</span>
                </li>
                <li class="meta-item">
                    <span class="meta-label">Date Déclarée</span>
                    <span class="meta-value">{{ \Carbon\Carbon::parse($incident->date_intervention)->format('d/m/Y') }}</span>
                </li>
            </ul>
        </div>
        
        <!-- Actions DSI / Prise en Charge -->
        @if(Auth::user()->isAdmin() || Auth::user()->isAgent())
            <div class="card" style="margin-bottom: 0;">
                <h2 class="card-title"><i class="fa-solid fa-gears" style="color: hsl(var(--primary));"></i> Prise en Charge DSI</h2>
                <form action="{{ route('incidents.update-statut', $incident->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="form-group">
                        <label for="statut_incident_id" class="form-label">Mettre à jour le statut</label>
                        @php
                            $statuts = \App\Models\StatutIncident::orderBy('libelle')->get();
                        @endphp
                        <select id="statut_incident_id" name="statut_incident_id" class="form-control" onchange="this.form.submit()">
                            @foreach($statuts as $statut)
                                <option value="{{ $statut->id }}" {{ $incident->statut_incident_id == $statut->id ? 'selected' : '' }}>
                                    {{ $statut->libelle }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 1rem; border-top: 1px solid #f1f5f9; padding-top: 0.75rem;">
                    💡 Choisir un statut le mettra immédiatement à jour pour l'école et pour la carte sur le tableau de bord.
                </div>
            </div>
        @endif
        
    </div>
    
</div>
@endsection
