<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Structure;
use App\Models\StatutIncident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncidentController extends Controller
{
    public function index(Request $request)
    {
        $query = Incident::with(['structure', 'statutIncident']);

        if ($request->filled('structure_id')) {
            $query->where('structure_id', $request->structure_id);
        }

        if ($request->filled('statut_incident_id')) {
            $query->where('statut_incident_id', $request->statut_incident_id);
        }

        // Les responsables de structure ne voient que leur propre structure
        $user = Auth::user();
        if ($user->isResponsable() && $user->structure_id) {
            $query->where('structure_id', $user->structure_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('libelle', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $incidents      = $query->latest()->paginate(15)->withQueryString();
        $structures     = Structure::orderBy('nom')->get();
        $statuts        = StatutIncident::orderBy('libelle')->get();

        return view('incidents.index', compact('incidents', 'structures', 'statuts'));
    }

    public function create()
    {
        $user = Auth::user();
        // Si responsable, il ne peut signaler que pour sa structure
        if ($user->isResponsable()) {
            $structures = Structure::where('id', $user->structure_id)->get();
        } else {
            $structures = Structure::orderBy('nom')->get();
        }
        $statuts = StatutIncident::orderBy('libelle')->get();

        return view('incidents.create', compact('structures', 'statuts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'libelle'            => ['required', 'string', 'max:255'],
            'structure_id'       => ['required', 'exists:structures,id'],
            'statut_incident_id' => ['required', 'exists:statut_incidents,id'],
            'description'        => ['nullable', 'string'],
            'date_intervention'  => ['required', 'date'],
            'equipement_ids'     => ['nullable', 'array'],
            'equipement_ids.*'   => ['exists:equipements,id'],
        ], [
            'libelle.required'            => 'Le libellé de l\'incident est obligatoire.',
            'structure_id.required'       => 'La structure concernée est obligatoire.',
            'statut_incident_id.required' => 'Le statut est obligatoire.',
            'date_intervention.required'  => 'La date est obligatoire.',
        ]);

        // Génération automatique du code
        $count = Incident::count() + 1;
        $validated['code'] = 'INC-' . now()->year . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        $incident = Incident::create($validated);

        if ($request->filled('equipement_ids')) {
            $incident->equipements()->attach($request->equipement_ids);
        }

        return redirect()->route('incidents.index')
            ->with('success', 'Incident "' . $validated['libelle'] . '" signalé avec succès !');
    }

    public function show(Incident $incident)
    {
        $incident->load(['structure', 'statutIncident', 'equipements.typeEquipement']);
        return view('incidents.show', compact('incident'));
    }

    /** Mise à jour du statut uniquement */
    public function updateStatut(Request $request, Incident $incident)
    {
        $request->validate([
            'statut_incident_id' => ['required', 'exists:statut_incidents,id'],
        ]);

        $incident->update(['statut_incident_id' => $request->statut_incident_id]);

        return redirect()->back()->with('success', 'Statut de l\'incident mis à jour !');
    }
}
