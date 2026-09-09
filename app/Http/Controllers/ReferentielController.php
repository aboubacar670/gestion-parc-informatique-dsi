<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TypeEquipement;
use App\Models\EtatEquipement;
use App\Models\TypeStructure;
use App\Models\TypeLocalite;
use App\Models\TypeIntervention;
use App\Models\StatutIncident;
use App\Models\StatutIntervention;
use App\Models\Localite;
class ReferentielController extends Controller
{
    public function index()
    {
        return view('referentiels.index', [
            'typesEquipements'     => TypeEquipement::all(),
            'etatsEquipements'     => EtatEquipement::all(),
            'typesStructures'      => TypeStructure::all(),
            'typesLocalites'       => TypeLocalite::all(),
            'localites'            => Localite::all(),
            'typesInterventions'   => TypeIntervention::all(),
            'statutsIncidents'     => StatutIncident::all(),
            'statutsInterventions' => StatutIntervention::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['libelle' => 'required|string|max:100']);
        $model = $this->getModel($request->type);
        $model::create(['libelle' => $request->libelle]);
        return back()->with('success', 'Ajouté avec succès.');
    }

    public function update(Request $request, $type, $id)
    {
        $request->validate(['libelle' => 'required|string|max:100']);
        $model = $this->getModel($type);
        $model::findOrFail($id)->update(['libelle' => $request->libelle]);
        return back()->with('success', 'Modifié avec succès.');
    }

    public function destroy($type, $id)
    {
        $model = $this->getModel($type);
        $model::findOrFail($id)->delete();
        return back()->with('success', 'Supprimé avec succès.');
    }

    private function getModel($type)
    {
        return match($type) {
            'type_equipement'      => TypeEquipement::class,
            'etat_equipement'      => EtatEquipement::class,
            'type_structure'       => TypeStructure::class,
            'type_localite'        => TypeLocalite::class,
            'localite'             => Localite::class,
            'type_intervention'    => TypeIntervention::class,
            'statut_incident'      => StatutIncident::class,
            'statut_intervention'  => StatutIntervention::class,
            default => abort(404),
        };
    }
}
