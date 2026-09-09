<?php

namespace App\Http\Controllers;

use App\Models\Equipement;
use App\Models\TypeEquipement;
use App\Models\EtatEquipement;
use App\Models\Structure;
use Illuminate\Http\Request;

class EquipementController extends Controller
{
    /** Liste des équipements avec filtres */
    public function index(Request $request)
    {
        $query = Equipement::with(['typeEquipement', 'etatEquipement', 'structure']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('caracteristique', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('structure_id')) {
            $query->where('structure_id', $request->structure_id);
        }

        if ($request->filled('type_equipement_id')) {
            $query->where('type_equipement_id', $request->type_equipement_id);
        }

        if ($request->filled('etat_equipement_id')) {
            $query->where('etat_equipement_id', $request->etat_equipement_id);
        }

        $equipements     = $query->orderBy('nom')->paginate(15)->withQueryString();
        $structures      = Structure::orderBy('nom')->get();
        $typesEquipement = TypeEquipement::all();
        $etatsEquipement = EtatEquipement::all();

        return view('equipements.index', compact('equipements', 'structures', 'typesEquipement', 'etatsEquipement'));
    }

    /** Formulaire de création */
    public function create()
    {
        $typesEquipement = TypeEquipement::all();
        $etatsEquipement = EtatEquipement::all();
        $structures      = Structure::orderBy('nom')->get();

        return view('equipements.create', compact('typesEquipement', 'etatsEquipement', 'structures'));
    }

    /** Enregistre un nouvel équipement */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'                => ['required', 'string', 'max:255'],
            'version'            => ['nullable', 'string', 'max:50'],
            'caracteristique'    => ['nullable', 'string'],
            'type_equipement_id' => ['required', 'exists:type_equipements,id'],
            'etat_equipement_id' => ['required', 'exists:etat_equipements,id'],
            'structure_id'       => ['nullable', 'exists:structures,id'],
            'description'        => ['nullable', 'string'],
        ]);

        Equipement::create($validated);

        return redirect()->route('equipements.index')
            ->with('success', 'Équipement enregistré avec succès !');
    }

    /** Fiche détaillée d'un équipement */
    public function show(Equipement $equipement)
    {
        $equipement->load([
            'typeEquipement',
            'etatEquipement',
            'structure.localite',
            'incidents.statutIncident',
            'interventions.statutIntervention',
        ]);

        return view('equipements.show', compact('equipement'));
    }

    /** Formulaire d'édition */
    public function edit(Equipement $equipement)
    {
        $typesEquipement = TypeEquipement::all();
        $etatsEquipement = EtatEquipement::all();
        $structures      = Structure::orderBy('nom')->get();

        return view('equipements.edit', compact('equipement', 'typesEquipement', 'etatsEquipement', 'structures'));
    }

    /** Met à jour un équipement */
    public function update(Request $request, Equipement $equipement)
    {
        $validated = $request->validate([
            'nom'                => ['required', 'string', 'max:255'],
            'version'            => ['nullable', 'string', 'max:50'],
            'caracteristique'    => ['nullable', 'string'],
            'type_equipement_id' => ['required', 'exists:type_equipements,id'],
            'etat_equipement_id' => ['required', 'exists:etat_equipements,id'],
            'structure_id'       => ['nullable', 'exists:structures,id'],
            'description'        => ['nullable', 'string'],
        ]);

        $equipement->update($validated);

        return redirect()->route('equipements.index')
            ->with('success', 'Équipement mis à jour avec succès !');
    }

    /** Mise à jour rapide de l'état d'un équipement */
    public function updateEtat(Request $request, Equipement $equipement)
    {
        $request->validate([
            'etat_equipement_id' => ['required', 'exists:etat_equipements,id'],
        ]);

        $equipement->update(['etat_equipement_id' => $request->etat_equipement_id]);

        return redirect()->back()->with('success', 'État de l\'équipement mis à jour !');
    }

    /** Supprime un équipement */
    public function destroy(Equipement $equipement)
    {
        try {
            $nom = $equipement->nom;
            $equipement->incidents()->detach();
            $equipement->delete();

            return redirect()->route('equipements.index')
                ->with('success', 'Équipement "' . $nom . '" supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('equipements.index')
                ->with('error', 'Impossible de supprimer cet équipement.');
        }
    }
}
