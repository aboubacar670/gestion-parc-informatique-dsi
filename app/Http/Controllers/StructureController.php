<?php

namespace App\Http\Controllers;

use App\Models\Structure;
use App\Models\Localite;
use App\Models\TypeStructure;
use Illuminate\Http\Request;

class StructureController extends Controller
{
    /** Liste des structures avec filtres */
    public function index(Request $request)
    {
        $query = Structure::with(['typeStructure', 'localite', 'incidents']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('acronyme', 'like', "%{$search}%");
            });
        }

        if ($request->filled('localite_id')) {
            $query->where('localite_id', $request->localite_id);
        }

        if ($request->filled('type_structure_id')) {
            $query->where('type_structure_id', $request->type_structure_id);
        }

        $structures = $query->orderBy('nom')->paginate(10)->withQueryString();
        $localites = Localite::orderBy('nom')->get();
        $typesStructure = TypeStructure::all();

        return view('structures.index', compact('structures', 'localites', 'typesStructure'));
    }

    /** Formulaire de création */
    public function create()
    {
        $typesStructure = TypeStructure::all();
        $localites      = Localite::orderBy('nom')->get();
        $structures     = Structure::orderBy('nom')->get();

        return view('structures.create', compact('typesStructure', 'localites', 'structures'));
    }

    /** Enregistre une nouvelle structure */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'               => ['required', 'string', 'max:255'],
            'acronyme'          => ['nullable', 'string', 'max:20'],
            'type_structure_id' => ['required', 'exists:type_structures,id'],
            'localite_id'       => ['required', 'exists:localites,id'],
            'parent_id'         => ['nullable', 'exists:structures,id'],
            'latitude'          => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'         => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        Structure::create($validated);

        return redirect()->route('structures.index')
            ->with('success', 'Structure enregistrée avec succès !');
    }

    /** Fiche détaillée d'une structure */
    public function show(Structure $structure)
    {
        $structure->load([
            'typeStructure',
            'localite',
            'equipements.typeEquipement',
            'equipements.etatEquipement',
            'incidents.statutIncident',
            'utilisateurs.role',
            'enfants',
        ]);

        return view('structures.show', compact('structure'));
    }

    /** Formulaire d'édition */
    public function edit(Structure $structure)
    {
        $typesStructure = TypeStructure::all();
        $localites      = Localite::orderBy('nom')->get();
        $parents    = Structure::where('id', '!=', $structure->id)->orderBy('nom')->get();

        return view('structures.edit', compact('structure', 'typesStructure', 'localites', 'parents',));
    }

    /** Met à jour une structure */
    public function update(Request $request, Structure $structure)
    {
        $validated = $request->validate([
            'nom'               => ['required', 'string', 'max:255'],
            'acronyme'          => ['nullable', 'string', 'max:20'],
            'type_structure_id' => ['required', 'exists:type_structures,id'],
            'localite_id'       => ['required', 'exists:localites,id'],
            'parent_id'         => ['nullable', 'exists:structures,id'],
            'latitude'          => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'         => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $structure->update($validated);

        return redirect()->route('structures.show', $structure)
            ->with('success', 'Structure mise à jour avec succès !');
    }

    /** Supprime une structure */
    public function destroy(Structure $structure)
    {
        try {
            $nom = $structure->nom;
            
            // Dissocier les équipements et supprimer les incidents pour éviter les erreurs de clés étrangères
            $structure->equipements()->update(['structure_id' => null]);
            foreach ($structure->incidents as $inc) {
                $inc->equipements()->detach();
                $inc->delete();
            }
            $structure->delete();

            return redirect()->route('structures.index')
                ->with('success', 'Structure "' . $nom . '" supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('structures.index')
                ->with('error', 'Impossible de supprimer la structure : des éléments y sont rattachés.');
        }
    }
}
