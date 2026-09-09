@extends('layouts.app')

@section('title', 'Créer une nouvelle structure')

@section('content')
<div class="top-bar">
    <div>
        <h1 class="page-title">Nouvelle Structure</h1>
        <p class="page-subtitle">Ajouter un nouvel établissement éducatif à la base de données</p>
    </div>
    <a href="{{ route('structures.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-xmark"></i> Annuler
    </a>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <h2 class="card-title"><i class="fa-solid fa-school" style="color: hsl(var(--primary));"></i> Formulaire de création</h2>
    
    @if($errors->any())
        <div class="alert alert-danger">
            <ul style="list-style: none; margin: 0; padding: 0;">
                @foreach($errors->all() as $error)
                    <li><i class="fa-solid fa-circle-exclamation"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('structures.store') }}" method="POST">
        @csrf
        
        <div class="form-grid">
            <div class="form-group">
                <label for="nom" class="form-label">Nom officiel de la structure <span style="color: red;">*</span></label>
                <input type="text" id="nom" name="nom" value="{{ old('nom') }}" class="form-control" placeholder="ex: École Primaire Mogho Naaba" required>
            </div>
            
            <div class="form-group">
                <label for="acronyme" class="form-label">Acronyme / Sigle</label>
                <input type="text" id="acronyme" name="acronyme" value="{{ old('acronyme') }}" class="form-control" placeholder="ex: EPP Mogho Naaba">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="type_structure_id" class="form-label">Type de structure <span style="color: red;">*</span></label>
                <select id="type_structure_id" name="type_structure_id" class="form-control" required>
                    <option value="">Sélectionner un type</option>
                    @foreach($typesStructure as $type)
                        <option value="{{ $type->id }}" {{ old('type_structure_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->libelle }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="localite_id" class="form-label">Localité / Commune <span style="color: red;">*</span></label>
                <select id="localite_id" name="localite_id" class="form-control" required>
                    <option value="">Sélectionner une localité</option>
                    @foreach($localites as $loc)
                        <option value="{{ $loc->id }}" {{ old('localite_id') == $loc->id ? 'selected' : '' }}>
                            {{ $loc->nom }} ({{ $loc->typeLocalite?->libelle }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="parent_id" class="form-label">Rattachement administratif (Parent)</label>
            <select id="parent_id" name="parent_id" class="form-control">
                <option value="">Aucun (Rattachement direct DSI)</option>
                @foreach($structures as $struct)
                    <option value="{{ $struct->id }}" {{ old('parent_id') == $struct->id ? 'selected' : '' }}>
                        {{ $struct->nom }} ({{ $struct->typeStructure?->libelle }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-grid" style="background-color: #f8fafc; padding: 1rem; border-radius: 8px; margin-top: 1rem; border: 1px dashed var(--bg-accent);">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="latitude" class="form-label">Latitude GPS</label>
                <input type="number" id="latitude" name="latitude" value="{{ old('latitude') }}" step="0.0000001" class="form-control" placeholder="ex: 12.3714">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="longitude" class="form-label">Longitude GPS</label>
                <input type="number" id="longitude" name="longitude" value="{{ old('longitude') }}" step="0.0000001" class="form-control" placeholder="ex: -1.5197">
            </div>
        </div>

        <div class="form-group">
    <label class="form-label">Coordonnées GPS</label>
    <div style="display:flex; gap:0.5rem; align-items:flex-end;">
        <div style="flex:1;">
            <input type="text" name="latitude" id="latitude" 
                class="form-control" placeholder="Latitude"
                value="{{ old('latitude') }}">
        </div>
        <div style="flex:1;">
            <input type="text" name="longitude" id="longitude" 
                class="form-control" placeholder="Longitude"
                value="{{ old('longitude') }}">
        </div>
        <button type="button" id="btnGeo" onclick="geolocalisier()" 
            class="btn btn-secondary" style="white-space:nowrap;">
            <i class="fas fa-map-marker-alt"></i> Géolocaliser
        </button>
    </div>
</div>
        <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
            <a href="{{ route('structures.index') }}" class="btn btn-secondary">
                Annuler
            </a>
            <button type="submit" class="btn btn-primary">
                Enregistrer la Structure <i class="fa-solid fa-circle-check"></i>
            </button>
        </div>

    </form>
</div>

@section('scripts')
<script>
// 1. GÉNÉRATION AUTOMATIQUE DE L'ACRONYME
document.getElementById('nom').addEventListener('input', function() {
    const nom = this.value;
    const acronyme = nom
        .split(' ')
        .filter(word => word.length > 2) // ignore les petits mots (de, la, l', etc.)
        .map(word => word[0].toUpperCase())
        .join('');
    
    document.getElementById('acronyme').value = acronyme;
});

// 2. GÉOLOCALISATION AUTOMATIQUE VIA OPENSTREETMAP
function geolocalisier() {
    const nom = document.getElementById('nom').value;
    if (!nom) {
        alert('Veuillez d\'abord saisir le nom de la structure.');
        return;
    }

    const btn = document.getElementById('btnGeo');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Recherche...';
    btn.disabled = true;

    fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(nom + ' Burkina Faso')}&format=json&limit=1`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                document.getElementById('latitude').value = parseFloat(data[0].lat).toFixed(4);
                document.getElementById('longitude').value = parseFloat(data[0].lon).toFixed(4);
                btn.innerHTML = '<i class="fas fa-check"></i> Localisé !';
                btn.style.background = 'hsl(var(--success))';
            } else {
                btn.innerHTML = '<i class="fas fa-map-marker-alt"></i> Géolocaliser';
                btn.disabled = false;
                alert('Localisation non trouvée. Saisissez les coordonnées manuellement.');
            }
        })
        .catch(() => {
            btn.innerHTML = '<i class="fas fa-map-marker-alt"></i> Géolocaliser';
            btn.disabled = false;
            alert('Erreur de connexion. Saisissez les coordonnées manuellement.');
        });
}
</script>
@endsection
