<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StructureController;
use App\Http\Controllers\EquipementController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\InterventionController;
use App\Http\Controllers\UtilisateurControler;
use App\Http\Controllers\ReferentielController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\LocaliteController;
use App\Http\Controllers\TechnicienController;

// ─── ROUTES D'AUTHENTIFICATION ──────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ─── ROUTES PROTÉGÉES (AUTH REQUIRED) ───────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Redirection de la racine vers le tableau de bord
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // Tableau de bord
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Structures / Sites éducatifs
    Route::resource('structures', StructureController::class);

    // Équipements
    Route::resource('equipements', EquipementController::class);
    Route::patch('equipements/{equipement}/etat', [EquipementController::class, 'updateEtat'])->name('equipements.update-etat');

    // Incidents / Pannes
    Route::resource('incidents', IncidentController::class)->except(['edit', 'update', 'destroy']);
    Route::patch('incidents/{incident}/statut', [IncidentController::class, 'updateStatut'])->name('incidents.update-statut');

    // Interventions / Missions
    Route::resource('interventions', InterventionController::class);

    // Techniciens (Admin + Agent DSI seulement)
    Route::resource('techniciens', TechnicienController::class);

    // Utilisateurs
    Route::resource('utilisateurs', UtilisateurControler::class);

     //localités
    Route::resource('localites', LocaliteController::class);
    
    // Référentiels
    Route::get('/referentiels', [ReferentielController::class, 'index'])->name('referentiels.index');
    Route::post('/referentiels', [ReferentielController::class, 'store'])->name('referentiels.store');
    Route::put('/referentiels/{type}/{id}', [ReferentielController::class, 'update'])->name('referentiels.update');
    Route::delete('/referentiels/{type}/{id}', [ReferentielController::class, 'destroy'])->name('referentiels.destroy');

    //localités
    Route::resource('localites', LocaliteController::class);

    // Profil
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
    Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');

    // Export Excel
    Route::get('/export/structures', [ExportController::class, 'exportStructures'])->name('export.structures');
    Route::get('/export/equipements', [ExportController::class, 'exportEquipements'])->name('export.equipements');
    Route::get('/export/localites', [ExportController::class, 'exportLocalites'])->name('export.localites');
    Route::get('/export/incidents/modele', [ExportController::class, 'exportIncidentTemplate'])->name('export.incidents.modele');

    // Import Excel
    Route::post('/import/structures', [ImportController::class, 'importStructures'])->name('import.structures');
    Route::post('/import/equipements', [ImportController::class, 'importEquipements'])->name('import.equipements');
    Route::post('/import/localites', [ImportController::class, 'importLocalites'])->name('import.localites');
});