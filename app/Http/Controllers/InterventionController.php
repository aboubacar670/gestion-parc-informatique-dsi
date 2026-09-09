<?php

namespace App\Http\Controllers;

use App\Models\Intervention;
use App\Models\Structure;
use App\Models\TypeIntervention;
use App\Models\StatutIntervention;
use Illuminate\Http\Request;

class InterventionController extends Controller
{
    /** Liste les interventions */
    public function index(Request $request)
    {
        $query = Intervention::with(['structure', 'typeIntervention', 'statutIntervention']);

        if ($request->filled('structure_id')) {
            $query->where('structure_id', $request->structure_id);
        }

        if ($request->filled('type_intervention_id')) {
            $query->where('type_intervention_id', $request->type_intervention_id);
        }

        if ($request->filled('statut_intervention_id')) {
            $query->where('statut_intervention_id', $request->statut_intervention_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('libelle', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $interventions       = $query->latest('date_debut')->paginate(15)->withQueryString();
        $structures         = Structure::orderBy('nom')->get();
        $typesIntervention   = TypeIntervention::orderBy('libelle')->get();
        $statutsIntervention = StatutIntervention::orderBy('libelle')->get();

        return view('interventions.index', compact(
            'interventions', 'structures', 'typesIntervention', 'statutsIntervention'
        ));
    }

    /** Formulaire de création */
    public function create()
    {
        $structures         = Structure::orderBy('nom')->get();
        $typesIntervention   = TypeIntervention::orderBy('libelle')->get();
        $statutsIntervention = StatutIntervention::orderBy('libelle')->get();

        return view('interventions.create', compact('structures', 'typesIntervention', 'statutsIntervention'));
    }

    /** Enregistre une intervention */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'libelle'                => ['required', 'string', 'max:255'],
            'type_intervention_id'   => ['required', 'exists:type_interventions,id'],
            'statut_intervention_id' => ['required', 'exists:statut_interventions,id'],
            'date_debut'             => ['required', 'date'],
            'date_fin'               => ['nullable', 'date', 'after_or_equal:date_debut'],
            'structure_id'           => ['required', 'exists:structures,id'],
        ], [
            'libelle.required'                => 'Le libellé de l\'intervention est obligatoire.',
            'type_intervention_id.required'   => 'Le type d\'intervention est obligatoire.',
            'statut_intervention_id.required' => 'Le statut est obligatoire.',
            'date_debut.required'             => 'La date de début est obligatoire.',
            'structure_id.required'           => 'La structure concernée est obligatoire.',
            'date_fin.after_or_equal'         => 'La date de fin doit être supérieure ou égale à la date de début.',
        ]);

        // Génération automatique du code de l'intervention (ex: INT-2026-001)
        $count = Intervention::count() + 1;
        $validated['code'] = 'INT-' . now()->year . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        Intervention::create($validated);

        return redirect()->route('interventions.index')
            ->with('success', 'Intervention "' . $validated['libelle'] . '" planifiée avec succès !');
    }

    /** Formulaire de modification */
    public function edit(Intervention $intervention)
    {
        $structures         = Structure::orderBy('nom')->get();
        $typesIntervention   = TypeIntervention::orderBy('libelle')->get();
        $statutsIntervention = StatutIntervention::orderBy('libelle')->get();

        return view('interventions.edit', compact('intervention', 'structures', 'typesIntervention', 'statutsIntervention'));
    }

    /** Met à jour une intervention */
    public function update(Request $request, Intervention $intervention)
    {
        $validated = $request->validate([
            'libelle'                => ['required', 'string', 'max:255'],
            'type_intervention_id'   => ['required', 'exists:type_interventions,id'],
            'statut_intervention_id' => ['required', 'exists:statut_interventions,id'],
            'date_debut'             => ['required', 'date'],
            'date_fin'               => ['nullable', 'date', 'after_or_equal:date_debut'],
            'structure_id'           => ['required', 'exists:structures,id'],
        ], [
            'date_fin.after_or_equal' => 'La date de fin doit être supérieure ou égale à la date de début.',
        ]);

        $intervention->update($validated);

        return redirect()->route('interventions.index')
            ->with('success', 'Intervention mise à jour avec succès !');
    }

    /** Supprime une intervention */
    public function destroy(Intervention $intervention)
    {
        $libelle = $intervention->libelle;
        $intervention->delete();

        return redirect()->route('interventions.index')
            ->with('success', 'Intervention "' . $libelle . '" supprimée avec succès.');
    }
}
