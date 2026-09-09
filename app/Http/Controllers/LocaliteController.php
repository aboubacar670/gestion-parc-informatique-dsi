<?php

namespace App\Http\Controllers;

use App\Models\Localite;
use App\Models\TypeLocalite;
use Illuminate\Http\Request;

class LocaliteController extends Controller
{
    public function index(Request $request)
    {
        $query = Localite::with('typeLocalite');

        if ($request->search) {
            $query->where('nom', 'like', '%' . $request->search . '%');
        }

        if ($request->type_localite_id) {
            $query->where('type_localite_id', $request->type_localite_id);
        }
        $query = Localite::with(['typeLocalite', 'parent']);
        $localites = $query->paginate(10);
        $typesLocalites = TypeLocalite::all();

        return view('localites.index', compact('localites', 'typesLocalites'));
    }

    public function create()
    {
        $typesLocalites = TypeLocalite::all();
        $parents = Localite::orderby('nom')->get();
        return view('localites.create', compact('typesLocalites', 'parents'));
    }

  public function store(Request $request)
{
    $request->validate([
        'nom'              => 'required|string|max:100',
        'type_localite_id' => 'required|exists:type_localites,id',
        'parent_id'        => 'nullable|exists:localites,id',
    ]);

    // Générer le code automatiquement
    $derniere = Localite::orderBy('id', 'desc')->first();
    $numero = $derniere ? intval(substr($derniere->code_localite, 4)) + 1 : 1;
    $code = 'LOC-' . str_pad($numero, 3, '0', STR_PAD_LEFT);

    Localite::create([
        'nom'              => $request->nom,
        'type_localite_id' => $request->type_localite_id,
        'code_localite'    => $code,
        'parent_id'        => $request->parent_id ?? null,
    ]);

    return redirect()->route('localites.index')
        ->with('success', 'Localité ajoutée avec succès.');
}

    public function edit($id)
    {
        $localite = Localite::findOrFail($id);
        $typesLocalites = TypeLocalite::all();
        $parents = Localite::where('id', '!=', $id)->orderby('nom')->get(); // Exclure la localité actuelle des parents possibles   
        return view('localites.edit', compact('localite', 'typesLocalites', 'parents'));
    }

    public function update(Request $request, $id)
    {
        $localite = Localite::findOrFail($id);

        $request->validate([
            'nom'              => 'required|string|max:100',
            'type_localite_id' => 'required|exists:type_localites,id',
            'parent_id'        => 'nullable|exists:localites,id',
        ]);

        $localite->update([
            'nom'              => $request->nom,
            'type_localite_id' => $request->type_localite_id,
            'code_localite'    => $localite->code_localite, // Le code reste inchangé
            'parent_id'        => $request->parent_id ?? null,
        ]);

        return redirect()->route('localites.index')
            ->with('success', 'Localité modifiée avec succès.');
    }

    public function destroy($id)
{
    $localite = Localite::findOrFail($id);
    
    try {
                $localite->delete();
                return redirect()->route('localites.index')
                    ->with('success', 'Localité supprimée avec succès.');
        }              catch (\Exception $e)
        {
            return redirect()->route('localites.index')
            ->with('error', 'Impossible de supprimer cette localité car elle est utilisée par une ou plusieurs structures.');
        }
}
    
        
}