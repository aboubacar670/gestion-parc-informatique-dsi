<?php

namespace App\Http\Controllers;

use App\Models\Structure;
use App\Models\Equipement;
use App\Models\Incident;
use App\Models\Intervention;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->isResponsable()) {
            return redirect()->route('incidents.create');
        }

        // ── Statistiques globales ─────────────────────────────────────────────
        $stats = [
            'structures'    => Structure::count(),
            'equipements'   => Equipement::count(),
            'incidents'     => Incident::count(),
            'interventions' => Intervention::count(),
        ];

        $stats['incidents_ouverts'] = Incident::whereHas('statutIncident', function ($q) {
            $q->whereIn('libelle', ['Signalé', 'En cours de traitement']);
        })->count();

        $stats['interventions_planifiees'] = Intervention::whereHas('statutIntervention', function ($q) {
            $q->whereIn('libelle', ['Planifiée', 'En cours']);
        })->count();

        // ── Données pour la carte Leaflet ─────────────────────────────────────
        // Chaque structure avec coordonnées + état calculé (couleur marqueur)
        $structuresPourCarte = Structure::with([
            'localite',
            'incidents.statutIncident',
        ])
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->get()
        ->map(function ($structure) {
            $nbIncidentsOuverts = $structure->incidents->filter(function ($inc) {
                return in_array($inc->statutIncident?->libelle, ['Signalé', 'En cours de traitement']);
            })->count();

            return [
                'id'        => $structure->id,
                'nom'       => $structure->nom,
                'lat'       => (float) $structure->latitude,
                'lng'       => (float) $structure->longitude,
                'incidents' => $nbIncidentsOuverts,
                'couleur'   => $nbIncidentsOuverts === 0 ? 'vert' : ($nbIncidentsOuverts <= 2 ? 'orange' : 'rouge'),
                'url_fiche' => route('structures.show', $structure->id),
            ];
        });

        // ── Derniers incidents ────────────────────────────────────────────────
        $derniersIncidents = Incident::with(['structure', 'statutIncident'])
            ->latest()
            ->take(5)
            ->get();

        // ── Prochaines interventions ──────────────────────────────────────────
        $prochainesInterventions = Intervention::with(['structure', 'statutIntervention', 'typeIntervention'])
            ->whereHas('statutIntervention', function ($q) {
                $q->whereIn('libelle', ['Planifiée', 'En cours']);
            })
            ->orderBy('date_debut')
            ->take(5)
            ->get();
            // — Données graphique : états des équipements
            $etatsEquipements = \App\Models\EtatEquipement::withCount('equipements')->get();
            $graphEquipLabels = $etatsEquipements->pluck('libelle');
            $graphEquipData = $etatsEquipements->pluck('equipements_count');

            // — Données graphique : incidents par mois (6 derniers mois)
            $incidentsParMois = \App\Models\Incident::selectRaw('MONTH(created_at) as mois, COUNT(*) as total')
                ->whereYear('created_at', date('Y'))
                ->groupBy('mois')
                ->orderBy('mois')
                ->get();
            $graphMoisLabels = $incidentsParMois->map(fn($i) => date('M', mktime(0,0,0,$i->mois,1)));
            $graphMoisData = $incidentsParMois->pluck('total');

        return view('dashboard', compact(
            'stats',
            'structuresPourCarte',
            'derniersIncidents',
            'prochainesInterventions',
            'user',
            'graphEquipLabels',
            'graphEquipData',
            'graphMoisLabels',
            'graphMoisData',
        ));
    }
}
