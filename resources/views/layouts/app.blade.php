<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gestion DSI') - Cartographie des Infrastructures</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- CSS Global Premium -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ time() }}">
    
    <!-- FontAwesome Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Leaflet Map CSS CDN (Only load when needed or globally for map widgets) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    
    @yield('styles')
</head>
<body>
    <div class="app-container">
        
        <!-- SIDEBAR -->
        <aside class="sidebar">
            <div class="brand-section">
            <div class="brand-logo">D</div>
                <span class="brand-title">DSI</span>
                <button onclick="toggleSidebar()" style="margin-left:auto; background:none; border:none; color:white; cursor:pointer; font-size:1rem;">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
            <nav class="nav-links">
                @if(!Auth::user()->isResponsable())
                <li class="nav-item {{ Route::is('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Tableau de Bord</span>
                    </a>
                </li>
                <li class="nav-item {{ Route::is('structures.*') ? 'active' : '' }}">
                    <a href="{{ route('structures.index') }}">
                        <i class="fa-solid fa-school"></i>
                        <span>Structures</span>
                    </a>
                </li>
                <li class="nav-item {{ Route::is('equipements.*') ? 'active' : '' }}">
                    <a href="{{ route('equipements.index') }}">
                        <i class="fa-solid fa-laptop-code"></i>
                        <span>Équipements</span>
                    </a>
                </li>
                <li class="nav-item {{ Route::is('incidents.*') ? 'active' : '' }}">
                    <a href="{{ route('incidents.index') }}">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Incidents</span>
                    </a>
                </li>
                <li class="nav-item {{ Route::is('interventions.*') ? 'active' : '' }}">
                    <a href="{{ route('interventions.index') }}">
                        <i class="fa-solid fa-screwdriver-wrench"></i>
                        <span>Interventions</span>
                    </a>
                </li>
                @endif
                @if(Auth::user()->isAdmin())
                    <li class="nav-item {{ Route::is('utilisateurs.*') ? 'active' : '' }}">
                        <a href="{{ route('utilisateurs.index') }}">
                            <i class="fa-solid fa-users"></i>
                            <span>Utilisateurs</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Route::is('referentiels.*') ? 'active' : '' }}">
                        <a href="{{ route('referentiels.index') }}">
                            <i class="fa-solid fa-sliders"></i>
                            <span>Référentiels</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Route::is('localites.*') ? 'active' : '' }}">
                        <a href="{{ route('localites.index') }}">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Localités</span>
                        </a>
                    </li>
                @endif
            </nav>
            
            <!-- Profil Utilisateur en bas -->
            @auth
            <div class="user-profile-section">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->prenom, 0, 1) . substr(Auth::user()->nom, 0, 1)) }}
                </div>
                <div class="user-info">
                    <a href="{{ route('profil.index') }}" class="user-name" style="text-decoration:none; color:inherit;">
                        {{ Auth::user()->prenom }} {{ Auth::user()->nom }}
                    </a>
                    <div class="user-role">{{ Auth::user()->role_libelle }}</div>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="margin: 0; padding: 0;">
                    @csrf
                    <button type="submit" class="logout-btn" title="Se déconnecter">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
            @endauth
        </aside>
        
        <!-- MAIN CONTENT AREA -->
        <main class="main-content">
            @if(!Auth::user()->isResponsable())
            @php $nbIncidents = \App\Models\Incident::whereHas('statutIncident', fn($q) => $q->where('libelle','Signalé'))->count(); @endphp
            @if($nbIncidents > 0)
            <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:8px; padding:0.75rem 1rem; margin-bottom:1rem; display:flex; align-items:center; gap:0.75rem;">
                <i class="fa-solid fa-bell" style="color:#ef4444; font-size:1.1rem;"></i>
                <span style="color:#991b1b; font-weight:500;">
                    {{ $nbIncidents }} incident(s) en attente de traitement
                </span>
                <a href="{{ route('incidents.index') }}" style="margin-left:auto; color:#ef4444; font-weight:600; text-decoration:none;">
                    Voir →
                </a>
            </div>
            @endif
            @endif
            <!-- Alertes Flash de Laravel -->
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}
                </div>
            @endif
            
            @yield('content')
        </main>
        
    </div>
    <!-- MODAL DE CONFIRMATION DE SUPPRESSION -->
<div id="deleteModal" style="
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 9999;
    align-items: center;
    justify-content: center;
">
    <div style="
        background: white;
        border-radius: 12px;
        padding: 2rem;
        max-width: 420px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        text-align: center;
    ">
        <div style="font-size: 3rem; margin-bottom: 1rem;">🗑️</div>
        <h3 style="margin: 0 0 0.5rem; color: var(--text-primary); font-size: 1.25rem;">
            Confirmer la suppression
        </h3>
        <p id="deleteModalMessage" style="color: var(--text-secondary); margin: 0 0 1.5rem;">
            Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center;">
            <button onclick="closeDeleteModal()" style="
                padding: 0.6rem 1.5rem;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                background: white;
                color: var(--text-primary);
                cursor: pointer;
                font-size: 0.95rem;
                font-weight: 500;
            ">Annuler</button>
            <button id="deleteConfirmBtn" style="
                padding: 0.6rem 1.5rem;
                border: none;
                border-radius: 8px;
                background: hsl(var(--danger));
                color: white;
                cursor: pointer;
                font-size: 0.95rem;
                font-weight: 500;
            ">Supprimer</button>
        </div>
    </div>
</div>

    <!-- Leaflet Map JS CDN -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    @yield('scripts')
    <script>
        // ─── Restore Sidebar State On Load ───────────────────────────────────────
        (function() {
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                document.addEventListener('DOMContentLoaded', function() {
                    const appContainer = document.querySelector('.app-container');
                    const sidebar = document.querySelector('.sidebar');
                    const mainContent = document.querySelector('.main-content');
                    if (appContainer) appContainer.classList.add('sidebar-collapsed');
                    if (sidebar) sidebar.classList.add('collapsed');
                    if (mainContent) mainContent.classList.add('expanded');
                });
            }
        })();

        // ─── Toggle Sidebar ───────────────────────────────────────────────────────
        function toggleSidebar() {
            const appContainer = document.querySelector('.app-container');
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');

            if (appContainer) appContainer.classList.toggle('sidebar-collapsed');
            if (sidebar) sidebar.classList.toggle('collapsed');
            if (mainContent) mainContent.classList.toggle('expanded');

            const isCollapsed = appContainer ? appContainer.classList.contains('sidebar-collapsed') : false;
            localStorage.setItem('sidebarCollapsed', isCollapsed ? 'true' : 'false');
        }

        // ─── Modal de Confirmation de Suppression ─────────────────────────────────
        function confirmDelete(formId, message) {
            const modal = document.getElementById('deleteModal');
            const confirmBtn = document.getElementById('deleteConfirmBtn');
            const messageEl = document.getElementById('deleteModalMessage');

            if (message) {
                messageEl.textContent = message;
            }

            modal.style.display = 'flex';

            confirmBtn.onclick = function() {
                document.getElementById(formId).submit();
            };
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Fermer le modal si on clique en dehors
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) closeDeleteModal();
        });
    </script>
</body>
</html>
