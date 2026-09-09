<?php

namespace App\Http\Controllers;

use App\Models\Structure;
use App\Models\Equipement;
use App\Models\Localite;
use App\Models\TypeStructure;
use App\Models\TypeLocalite;
use App\Models\TypeEquipement;
use App\Models\EtatEquipement;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportController extends Controller
{
    // ─── IMPORT STRUCTURES ────────────────────────────────────────────────────

    public function importStructures(Request $request)
    {
        $request->validate([
            'fichier_excel' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ], [
            'fichier_excel.required' => 'Veuillez sélectionner un fichier Excel.',
            'fichier_excel.mimes'    => 'Le fichier doit être au format .xlsx ou .xls.',
            'fichier_excel.max'      => 'Le fichier ne doit pas dépasser 5 Mo.',
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('fichier_excel')->getPathname());
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray(null, true, true, true);

            // Charger les référentiels en mémoire (évite N+1)
            $typesStructure = TypeStructure::pluck('id', 'libelle');
            $localites      = Localite::pluck('id', 'nom');

            $importes  = 0;
            $ignores   = 0;
            $erreurs   = [];

            // La ligne 1 = en-têtes, on commence à la ligne 2
            foreach ($rows as $numLigne => $row) {
                if ($numLigne === 1) continue; // skip header

                // Colonnes : A=Nom, B=Acronyme, C=Type, D=Localité, E=Latitude, F=Longitude
                $nom         = trim($row['A'] ?? '');
                $acronyme    = trim($row['B'] ?? '');
                $typeLibelle = trim($row['C'] ?? '');
                $locNom      = trim($row['D'] ?? '');
                $latitude    = $row['E'] ?? null;
                $longitude   = $row['F'] ?? null;

                // Ignorer les lignes vides
                if (empty($nom)) continue;

                // Résolution du type de structure
                if (empty($typeLibelle) || !$typesStructure->has($typeLibelle)) {
                    $erreurs[] = "Ligne {$numLigne} — Type de structure introuvable : \"{$typeLibelle}\" (structure \"{$nom}\" ignorée)";
                    $ignores++;
                    continue;
                }

                // Résolution de la localité
                if (empty($locNom) || !$localites->has($locNom)) {
                    $erreurs[] = "Ligne {$numLigne} — Localité introuvable : \"{$locNom}\" (structure \"{$nom}\" ignorée)";
                    $ignores++;
                    continue;
                }

                // Insérer ou mettre à jour (basé sur le nom)
                Structure::updateOrCreate(
                    ['nom' => $nom],
                    [
                        'acronyme'          => $acronyme ?: null,
                        'type_structure_id' => $typesStructure[$typeLibelle],
                        'localite_id'       => $localites[$locNom],
                        'latitude'          => is_numeric($latitude) ? (float)$latitude : null,
                        'longitude'         => is_numeric($longitude) ? (float)$longitude : null,
                    ]
                );
                $importes++;
            }

            $message = "{$importes} structure(s) importée(s) avec succès.";
            if ($ignores > 0) {
                $message .= " {$ignores} ligne(s) ignorée(s) (voir détails).";
            }

            return redirect()->route('structures.index')
                ->with('success', $message)
                ->with('import_warnings', $erreurs);

        } catch (\Exception $e) {
            return redirect()->route('structures.index')
                ->with('error', 'Erreur lors de la lecture du fichier : ' . $e->getMessage());
        }
    }

    // ─── IMPORT ÉQUIPEMENTS ───────────────────────────────────────────────────

    public function importEquipements(Request $request)
    {
        $request->validate([
            'fichier_excel' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ], [
            'fichier_excel.required' => 'Veuillez sélectionner un fichier Excel.',
            'fichier_excel.mimes'    => 'Le fichier doit être au format .xlsx ou .xls.',
            'fichier_excel.max'      => 'Le fichier ne doit pas dépasser 5 Mo.',
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('fichier_excel')->getPathname());
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray(null, true, true, true);

            $typesEquipement = TypeEquipement::pluck('id', 'libelle');
            $etatsEquipement = EtatEquipement::pluck('id', 'libelle');
            $structures      = Structure::pluck('id', 'nom');

            $importes = 0;
            $ignores  = 0;
            $erreurs  = [];

            foreach ($rows as $numLigne => $row) {
                if ($numLigne === 1) continue;

                // Colonnes : A=Nom, B=Type, C=Caractéristiques, D=Structure, E=Modèle/Version, F=État
                $nom          = trim($row['A'] ?? '');
                $typeLibelle  = trim($row['B'] ?? '');
                $caract       = trim($row['C'] ?? '');
                $structNom    = trim($row['D'] ?? '');
                $version      = trim($row['E'] ?? '');
                $etatLibelle  = trim($row['F'] ?? '');

                if (empty($nom)) continue;

                // Résolution type équipement
                if (empty($typeLibelle) || !$typesEquipement->has($typeLibelle)) {
                    $erreurs[] = "Ligne {$numLigne} — Type d'équipement introuvable : \"{$typeLibelle}\" (équipement \"{$nom}\" ignoré)";
                    $ignores++;
                    continue;
                }

                // Résolution état équipement (optionnel — défaut : Fonctionnel)
                $etatId = null;
                if (!empty($etatLibelle) && $etatsEquipement->has($etatLibelle)) {
                    $etatId = $etatsEquipement[$etatLibelle];
                } else {
                    $etatId = $etatsEquipement->first(); // premier état disponible
                }

                // Résolution structure (optionnelle)
                $structureId = null;
                if (!empty($structNom)) {
                    if ($structures->has($structNom)) {
                        $structureId = $structures[$structNom];
                    } else {
                        $erreurs[] = "Ligne {$numLigne} — Structure introuvable : \"{$structNom}\" (équipement \"{$nom}\" importé sans affectation)";
                    }
                }

                Equipement::updateOrCreate(
                    ['nom' => $nom],
                    [
                        'type_equipement_id' => $typesEquipement[$typeLibelle],
                        'etat_equipement_id' => $etatId,
                        'structure_id'       => $structureId,
                        'caracteristique'    => $caract ?: null,
                        'version'            => $version ?: null,
                    ]
                );
                $importes++;
            }

            $message = "{$importes} équipement(s) importé(s) avec succès.";
            if ($ignores > 0) {
                $message .= " {$ignores} ligne(s) ignorée(s).";
            }

            return redirect()->route('equipements.index')
                ->with('success', $message)
                ->with('import_warnings', $erreurs);

        } catch (\Exception $e) {
            return redirect()->route('equipements.index')
                ->with('error', 'Erreur lors de la lecture du fichier : ' . $e->getMessage());
        }
    }

    // ─── IMPORT LOCALITÉS ─────────────────────────────────────────────────────

    public function importLocalites(Request $request)
    {
        $request->validate([
            'fichier_excel' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ], [
            'fichier_excel.required' => 'Veuillez sélectionner un fichier Excel.',
            'fichier_excel.mimes'    => 'Le fichier doit être au format .xlsx ou .xls.',
            'fichier_excel.max'      => 'Le fichier ne doit pas dépasser 5 Mo.',
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('fichier_excel')->getPathname());
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray(null, true, true, true);

            $typesLocalite = TypeLocalite::pluck('id', 'libelle');

            $importes = 0;
            $ignores  = 0;
            $erreurs  = [];

            foreach ($rows as $numLigne => $row) {
                if ($numLigne === 1) continue;

                // Colonnes : A=Nom, B=Type
                $nom         = trim($row['A'] ?? '');
                $typeLibelle = trim($row['B'] ?? '');

                if (empty($nom)) continue;

                // Résolution du type de localité
                if (empty($typeLibelle) || !$typesLocalite->has($typeLibelle)) {
                    $erreurs[] = "Ligne {$numLigne} — Type de localité introuvable : \"{$typeLibelle}\" (localité \"{$nom}\" ignorée)";
                    $ignores++;
                    continue;
                }

                Localite::updateOrCreate(
                    ['nom' => $nom],
                    [
                        'type_localite_id' => $typesLocalite[$typeLibelle],
                        'code_localite'    => 'IMP-' . strtoupper(substr(preg_replace('/\s+/', '', $nom), 0, 8)),
                    ]
                );
                $importes++;
            }

            $message = "{$importes} localité(s) importée(s) avec succès.";
            if ($ignores > 0) {
                $message .= " {$ignores} ligne(s) ignorée(s).";
            }

            return redirect()->route('localites.index')
                ->with('success', $message)
                ->with('import_warnings', $erreurs);

        } catch (\Exception $e) {
            return redirect()->route('localites.index')
                ->with('error', 'Erreur lors de la lecture du fichier : ' . $e->getMessage());
        }
    }
}
