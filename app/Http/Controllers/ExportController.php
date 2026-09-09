<?php

namespace App\Http\Controllers;

use App\Models\Structure;
use App\Models\Localite;
use App\Models\Equipement;
use App\Models\TypeStructure;
use App\Models\TypeLocalite;
use App\Models\TypeEquipement;
use App\Models\EtatEquipement;
use App\Models\StatutIncident;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ExportController extends Controller
{
    public function exportStructures()
    {
        $structures = Structure::with(['typeStructure', 'localite'])->get();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Structures DSI');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A8A']], // Bleu DSI
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];

        $headers = ['Nom de la Structure', 'Acronyme', 'Type de Structure', 'Localité', 'Latitude', 'Longitude', 'État Parc'];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);

        // Données existantes
        $row = 2;
        foreach ($structures as $s) {
            $sheet->setCellValue('A'.$row, $s->nom);
            $sheet->setCellValue('B'.$row, $s->acronyme ?? '');
            $sheet->setCellValue('C'.$row, $s->typeStructure?->libelle ?? '');
            $sheet->setCellValue('D'.$row, $s->localite?->nom ?? '');
            $sheet->setCellValue('E'.$row, $s->latitude ?? '');
            $sheet->setCellValue('F'.$row, $s->longitude ?? '');
            $sheet->setCellValue('G'.$row, 'Tout fonctionnel');
            $row++;
        }

        // Feuille cachée de Références
        $dataSheet = $spreadsheet->createSheet();
        $dataSheet->setTitle('Donnees_Ref');
        $dataSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

        $types = TypeStructure::pluck('libelle')->toArray();
        $localites = Localite::pluck('nom')->toArray();

        if (!empty($types)) {
            $dataSheet->fromArray(array_chunk($types, 1), null, 'A2');
        }
        $dataSheet->setCellValue('A1', 'TypesStructure');

        if (!empty($localites)) {
            $dataSheet->fromArray(array_chunk($localites, 1), null, 'B2');
        }
        $dataSheet->setCellValue('B1', 'Localites');

        $lastRowTypes = count($types) + 1;
        $lastRowLocs = count($localites) + 1;

        // Validation tolérante (permets la saisie libre tout en offrant la liste déroulante)
        for ($i = 2; $i <= 100; $i++) {
            if ($lastRowTypes > 1) {
                $valType = $sheet->getCell('C' . $i)->getDataValidation();
                $valType->setType(DataValidation::TYPE_LIST)
                        ->setAllowBlank(true)
                        ->setShowDropDown(true)
                        ->setShowErrorMessage(false)
                        ->setShowInputMessage(false)
                        ->setFormula1('Donnees_Ref!$A$2:$A$' . $lastRowTypes);
            }

            if ($lastRowLocs > 1) {
                $valLoc = $sheet->getCell('D' . $i)->getDataValidation();
                $valLoc->setType(DataValidation::TYPE_LIST)
                       ->setAllowBlank(true)
                       ->setShowDropDown(true)
                       ->setShowErrorMessage(false)
                       ->setShowInputMessage(false)
                       ->setFormula1('Donnees_Ref!$B$2:$B$' . $lastRowLocs);
            }
        }

        $spreadsheet->setActiveSheetIndex(0);
        foreach(range('A','G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $filename = 'Structures_DSI_' . date('Y-m-d') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'. $filename .'"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportEquipements()
    {
        $equipements = Equipement::with(['typeEquipement', 'etatEquipement', 'structure'])->get();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Équipements');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A8A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];

        $headers = ['Nom du matériel', 'Type de matériel', 'Caractéristiques', 'Structure / Site (Affectation)', 'Modèle / Version', 'État de fonctionnement'];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

        $row = 2;
        foreach ($equipements as $e) {
            $sheet->setCellValue('A'.$row, $e->nom);
            $sheet->setCellValue('B'.$row, $e->typeEquipement?->libelle ?? '');
            $sheet->setCellValue('C'.$row, $e->caracteristique ?? '');
            $sheet->setCellValue('D'.$row, $e->structure?->nom ?? 'Non affecté');
            $sheet->setCellValue('E'.$row, $e->version ?? '');
            $sheet->setCellValue('F'.$row, $e->etatEquipement?->libelle ?? '');
            $row++;
        }

        // Feuille de références pré-remplies par la SEST / DSI
        $dataSheet = $spreadsheet->createSheet();
        $dataSheet->setTitle('Donnees_Ref');
        $dataSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

        $structures = Structure::pluck('nom')->toArray();
        $typesEquip = TypeEquipement::pluck('libelle')->toArray();
        $etatsEquip = EtatEquipement::pluck('libelle')->toArray();

        if (!empty($structures)) {
            $dataSheet->fromArray(array_chunk($structures, 1), null, 'A2');
        }
        $dataSheet->setCellValue('A1', 'Structures');

        if (!empty($typesEquip)) {
            $dataSheet->fromArray(array_chunk($typesEquip, 1), null, 'B2');
        }
        $dataSheet->setCellValue('B1', 'TypesEquipement');

        if (!empty($etatsEquip)) {
            $dataSheet->fromArray(array_chunk($etatsEquip, 1), null, 'C2');
        }
        $dataSheet->setCellValue('C1', 'EtatsEquipement');

        $lastRowStruct = count($structures) + 1;
        $lastRowType = count($typesEquip) + 1;
        $lastRowEtat = count($etatsEquip) + 1;

        // Listes déroulantes ouvertes (n'interdisent pas la saisie manuelle de nouveau matériel)
        for ($i = 2; $i <= 100; $i++) {
            if ($lastRowType > 1) {
                $valType = $sheet->getCell('B' . $i)->getDataValidation();
                $valType->setType(DataValidation::TYPE_LIST)
                        ->setAllowBlank(true)
                        ->setShowDropDown(true)
                        ->setShowErrorMessage(false)
                        ->setShowInputMessage(false)
                        ->setFormula1('Donnees_Ref!$B$2:$B$' . $lastRowType);
            }

            if ($lastRowStruct > 1) {
                $valStruct = $sheet->getCell('D' . $i)->getDataValidation();
                $valStruct->setType(DataValidation::TYPE_LIST)
                          ->setAllowBlank(true)
                          ->setShowDropDown(true)
                          ->setShowErrorMessage(false)
                          ->setShowInputMessage(false)
                          ->setFormula1('Donnees_Ref!$A$2:$A$' . $lastRowStruct);
            }

            if ($lastRowEtat > 1) {
                $valEtat = $sheet->getCell('F' . $i)->getDataValidation();
                $valEtat->setType(DataValidation::TYPE_LIST)
                        ->setAllowBlank(true)
                        ->setShowDropDown(true)
                        ->setShowErrorMessage(false)
                        ->setShowInputMessage(false)
                        ->setFormula1('Donnees_Ref!$C$2:$C$' . $lastRowEtat);
            }
        }

        $spreadsheet->setActiveSheetIndex(0);
        foreach(range('A','F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Equipements_DSI_' . date('Y-m-d') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'. $filename .'"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    
    public function exportLocalites()
    {
        $localites = Localite::with('typeLocalite')->get();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Localités');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A8A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];

        $sheet->fromArray(['Nom de la Localité', 'Type de Localité'], null, 'A1');
        $sheet->getStyle('A1:B1')->applyFromArray($headerStyle);

        $row = 2;
        foreach ($localites as $l) {
            $sheet->setCellValue('A'.$row, $l->nom);
            $sheet->setCellValue('B'.$row, $l->typeLocalite?->libelle ?? '');
            $row++;
        }

        // Feuille de références
        $dataSheet = $spreadsheet->createSheet();
        $dataSheet->setTitle('Donnees_Ref');
        $dataSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

        $typesLocalite = TypeLocalite::pluck('libelle')->toArray();
        if (!empty($typesLocalite)) {
            $dataSheet->fromArray(array_chunk($typesLocalite, 1), null, 'A2');
        }
        $dataSheet->setCellValue('A1', 'TypesLocalite');

        $lastRowType = count($typesLocalite) + 1;
        for ($i = 2; $i <= 100; $i++) {
            if ($lastRowType > 1) {
                $valType = $sheet->getCell('B' . $i)->getDataValidation();
                $valType->setType(DataValidation::TYPE_LIST)
                        ->setAllowBlank(true)
                        ->setShowDropDown(true)
                        ->setShowErrorMessage(false)
                        ->setShowInputMessage(false)
                        ->setFormula1('Donnees_Ref!$A$2:$A$' . $lastRowType);
            }
        }

        $spreadsheet->setActiveSheetIndex(0);
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);

        $filename = 'Localites_' . date('Y-m-d') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'. $filename .'"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportIncidentTemplate()
    {
        $spreadsheet = new Spreadsheet();
        
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Déclaration d\'Incidents');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFDC2626']], // Rouge Panne
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];

        $headers = ['Structure / Site', 'Appareil / Équipement', 'Date du Constat (AAAA-MM-JJ)', 'Statut Initial', 'Titre de la panne', 'Description détaillée'];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);
        $sheet->getStyle('A1:F1')->getAlignment()->setWrapText(true);

        foreach(range('A','F') as $col) {
            $sheet->getColumnDimension($col)->setWidth(30);
        }

        $dataSheet = $spreadsheet->createSheet();
        $dataSheet->setTitle('Donnees');
        $dataSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

        $structures = Structure::pluck('nom')->toArray();
        if (!empty($structures)) {
            $dataSheet->fromArray(array_chunk($structures, 1), null, 'A2');
        }
        $dataSheet->setCellValue('A1', 'Structures');

        $equipements = Equipement::with(['typeEquipement', 'structure'])->get()->map(function($e) {
            $structNom = $e->structure ? $e->structure->nom : 'Général';
            return $e->nom . ' [' . $structNom . ']';
        })->toArray();

        if (!empty($equipements)) {
            $dataSheet->fromArray(array_chunk($equipements, 1), null, 'B2');
        }
        $dataSheet->setCellValue('B1', 'Equipements');

        $statuts = StatutIncident::pluck('libelle')->toArray();
        if (!empty($statuts)) {
            $dataSheet->fromArray(array_chunk($statuts, 1), null, 'C2');
        }
        $dataSheet->setCellValue('C1', 'Statuts');

        $lastRowStruct = count($structures) + 1;
        $lastRowEquip = count($equipements) + 1;
        $lastRowStatut = count($statuts) + 1;

        for ($i = 2; $i <= 100; $i++) {
            if ($lastRowStruct > 1) {
                $valStruct = $sheet->getCell('A' . $i)->getDataValidation();
                $valStruct->setType(DataValidation::TYPE_LIST)
                          ->setAllowBlank(true)
                          ->setShowDropDown(true)
                          ->setShowErrorMessage(false)
                          ->setShowInputMessage(false)
                          ->setFormula1('Donnees!$A$2:$A$' . $lastRowStruct);
            }

            if ($lastRowEquip > 1) {
                $valEquip = $sheet->getCell('B' . $i)->getDataValidation();
                $valEquip->setType(DataValidation::TYPE_LIST)
                          ->setAllowBlank(true)
                          ->setShowDropDown(true)
                          ->setShowErrorMessage(false)
                          ->setShowInputMessage(false)
                          ->setFormula1('Donnees!$B$2:$B$' . $lastRowEquip);
            }

            if ($lastRowStatut > 1) {
                $valStatut = $sheet->getCell('D' . $i)->getDataValidation();
                $valStatut->setType(DataValidation::TYPE_LIST)
                          ->setAllowBlank(true)
                          ->setShowDropDown(true)
                          ->setShowErrorMessage(false)
                          ->setShowInputMessage(false)
                          ->setFormula1('Donnees!$C$2:$C$' . $lastRowStatut);
            }

            $sheet->setCellValue('D' . $i, 'Signalé');
        }

        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'Modele_Declaration_Incidents_' . date('Y-m-d') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'. $filename .'"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
