<?php

namespace App\Http\Controllers;

use App\Models\Technicien;
use App\Models\ProfilTechnicien;
use Illuminate\Http\Request;

class TechnicienController extends Controller
{
    /** Liste des techniciens */
    public function index(Request $request)
    {
        $query = Technicien::with('profil')
            ->withCount(['interventions', 'incidents']);

        if ($request->filled('profil_id')) {
            $query->where('profil_technicien_id', $request->profil_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%");
            });
        }

        $techniciens = $query->orderBy('nom')->paginate(15)->withQueryString();
        $profils     = ProfilTechnicien::orderBy('libelle')->get();

        return view('techniciens.index', compact('techniciens', 'profils'));
    }

    /** Formulaire de création */
    public function create()
    {
        $profils = ProfilTechnicien::orderBy('libelle')->get();
        return view('techniciens.create', compact('profils'));
    }

    /** Enregistre un technicien */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'                   => ['required', 'string', 'max:100'],
            'prenom'                => ['required', 'string', 'max:100'],
            'telephone'             => ['nullable', 'string', 'max:30'],
            'profil_technicien_id'  => ['required', 'exists:profil_techniciens,id'],
        ], [
            'nom.required'                  => 'Le nom est obligatoire.',
            'prenom.required'               => 'Le prénom est obligatoire.',
            'profil_technicien_id.required' => 'Le profil est obligatoire.',
        ]);

        Technicien::create($validated);

        return redirect()->route('techniciens.index')
            ->with('success', $validated['prenom'] . ' ' . $validated['nom'] . ' ajouté avec succès !');
    }

    /** Formulaire de modification */
    public function edit(Technicien $technicien)
    {
        $profils = ProfilTechnicien::orderBy('libelle')->get();
        return view('techniciens.edit', compact('technicien', 'profils'));
    }

    /** Met à jour un technicien */
    public function update(Request $request, Technicien $technicien)
    {
        $validated = $request->validate([
            'nom'                   => ['required', 'string', 'max:100'],
            'prenom'                => ['required', 'string', 'max:100'],
            'telephone'             => ['nullable', 'string', 'max:30'],
            'profil_technicien_id'  => ['required', 'exists:profil_techniciens,id'],
        ]);

        $technicien->update($validated);

        return redirect()->route('techniciens.index')
            ->with('success', 'Technicien mis à jour avec succès !');
    }

    /** Supprime un technicien — bloqué s'il est assigné */
    public function destroy(Technicien $technicien)
    {
        // Vérifier si le technicien est assigné à des interventions ou incidents
        if ($technicien->interventions()->count() > 0 || $technicien->incidents()->count() > 0) {
            return redirect()->route('techniciens.index')
                ->with('error', 'Impossible de supprimer ' . $technicien->prenom . ' ' . $technicien->nom
                    . ' : ce technicien est assigné à ' . $technicien->interventions()->count()
                    . ' intervention(s) et ' . $technicien->incidents()->count() . ' incident(s).');
        }

        $nom = $technicien->prenom . ' ' . $technicien->nom;
        $technicien->delete();

        return redirect()->route('techniciens.index')
            ->with('success', 'Technicien "' . $nom . '" supprimé avec succès.');
    }
}
