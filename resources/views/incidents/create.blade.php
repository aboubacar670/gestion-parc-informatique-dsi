@extends('layouts.app')

@section('title', 'Signaler une panne / incident')

@section('content')
<div class="top-bar">
    <div>
        <h1 class="page-title">Signaler un Incident</h1>
        <p class="page-subtitle">Déclarer un dysfonctionnement sur le parc informatique</p>
    </div>
    <a href="{{ route('incidents.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-xmark"></i> Annuler
    </a>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <h2 class="card-title"><i class="fa-solid fa-triangle-exclamation" style="color: hsl(var(--danger));"></i> Déclaration d'incident</h2>
    
    @if($errors->any())
        <div class="alert alert-danger">
            <ul style="list-style: none; margin: 0; padding: 0;">
                @foreach($errors->all() as $error)
                    <li><i class="fa-solid fa-circle-exclamation"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('incidents.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="libelle" class="form-label">Titre court / Libellé de la panne <span style="color: red;">*</span></label>
            <input type="text" id="libelle" name="libelle" value="{{ old('libelle') }}" class="form-control" placeholder="ex: Routeur Wi-Fi ne s'allume plus" required>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="structure_id" class="form-label">Structure concernée <span style="color: red;">*</span></label>
                <select id="structure_id" name="structure_id" class="form-control" required>
                    <option value="">Sélectionner une structure</option>
                    @foreach($structures as $struct)
                        <option value="{{ $struct->id }}" {{ old('structure_id', request('structure_id')) == $struct->id ? 'selected' : '' }}>
                            {{ $struct->nom }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="date_intervention" class="form-label">Date du constat <span style="color: red;">*</span></label>
                <input type="date" id="date_intervention" name="date_intervention" value="{{ old('date_intervention', date('Y-m-d')) }}" class="form-control" required>
            </div>
        </div>

        <div class="form-group">
            <label for="statut_incident_id" class="form-label">Statut initial <span style="color: red;">*</span></label>
            <select id="statut_incident_id" name="statut_incident_id" class="form-control" required>
                @foreach($statuts as $statut)
                    <option value="{{ $statut->id }}" {{ old('statut_incident_id') == $statut->id || ($statut->libelle === 'Signalé' && !old('statut_incident_id')) ? 'selected' : '' }}>
                        {{ $statut->libelle }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- SELECTION DES EQUIPEMENTS CONCERNES -->
        @php
            $equipements = \App\Models\Equipement::with('typeEquipement')->get();
        @endphp
        <div class="form-group" id="equipement_selection_wrapper" style="display: none;">
            <label class="form-label">Matériels informatiques impactés (Cochez le ou les équipements)</label>
            <div id="equipements_list" style="max-height: 180px; overflow-y: auto; background-color: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0.75rem; display: flex; flex-direction: column; gap: 0.5rem;">
                @foreach($equipements as $equip)
                    <label class="equip-checkbox-item" data-structure="{{ $equip->structure_id }}" style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; cursor: pointer; padding: 0.25rem 0;">
                        <input type="checkbox" name="equipement_ids[]" value="{{ $equip->id }}" style="accent-color: hsl(var(--danger));">
                        <span><strong>{{ $equip->nom }}</strong> ({{ $equip->typeEquipement?->libelle }})</span>
                    </label>
                @endforeach
            </div>
            <p id="no_equipments_text" style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem; font-style: italic; display: none;">
                Aucun équipement enregistré pour cette structure. L'incident sera enregistré globalement.
            </p>
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Description détaillée de la panne / Symptômes</label>
            <textarea id="description" name="description" rows="4" class="form-control" placeholder="Décrivez le problème précisément (voyants allumés, messages d'erreurs, actions effectuées...)"></textarea>
        </div>

        <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
            <a href="{{ route('incidents.index') }}" class="btn btn-secondary">
                Annuler
            </a>
            <button type="submit" class="btn btn-danger" style="background-color: hsl(var(--danger)); border-color: hsl(var(--danger));">
                Déclarer l'Incident <i class="fa-solid fa-triangle-exclamation"></i>
            </button>
        </div>

    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var structureSelect = document.getElementById('structure_id');
        var wrapper = document.getElementById('equipement_selection_wrapper');
        var checkboxItems = document.querySelectorAll('.equip-checkbox-item');
        var noEquipmentsText = document.getElementById('no_equipments_text');
        var listContainer = document.getElementById('equipements_list');
        
        function filterEquipments() {
            var selectedStruct = structureSelect.value;
            if (!selectedStruct) {
                wrapper.style.display = 'none';
                return;
            }
            
            wrapper.style.display = 'block';
            var count = 0;
            
            checkboxItems.forEach(function(item) {
                var structId = item.getAttribute('data-structure');
                if (structId === selectedStruct) {
                    item.style.display = 'flex';
                    count++;
                } else {
                    item.style.display = 'none';
                    // Décocher si masqué pour éviter d'envoyer des données incohérentes
                    item.querySelector('input').checked = false;
                }
            });
            
            if (count === 0) {
                listContainer.style.display = 'none';
                noEquipmentsText.style.display = 'block';
            } else {
                listContainer.style.display = 'flex';
                noEquipmentsText.style.display = 'none';
            }
        }
        
        structureSelect.addEventListener('change', filterEquipments);
        
        // Lancer une fois au chargement (si structure déjà pré-sélectionnée)
        filterEquipments();
    });
</script>
@endsection
