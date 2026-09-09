<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\TypeStructure;
use App\Models\TypeLocalite;
use App\Models\TypeEquipement;
use App\Models\EtatEquipement;
use App\Models\StatutIncident;
use App\Models\StatutIntervention;
use App\Models\TypeIntervention;
use App\Models\Localite;
use App\Models\Structure;
use App\Models\Equipement;
use App\Models\Incident;
use App\Models\Intervention;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── 1. Rôles ──────────────────────────────────────────────────────────
        $roleAdmin  = Role::create(['libelle' => 'Administrateur DSI']);
        $roleAgent  = Role::create(['libelle' => 'Agent DSI']);
        $roleResp   = Role::create(['libelle' => 'Responsable de structure']);

        // ─── 2. Types de localité ──────────────────────────────────────────────
        $typeRegion   = TypeLocalite::create(['libelle' => 'Région']);
        $typeProvince = TypeLocalite::create(['libelle' => 'Province']);
        $typeCommune  = TypeLocalite::create(['libelle' => 'Commune']);
        $typeVillage  = TypeLocalite::create(['libelle' => 'Village/Secteur']);

        // ─── 3. Types de structure ────────────────────────────────────────────
        $typePrimaire   = TypeStructure::create(['libelle' => 'École primaire publique']);
        $typeSecondaire = TypeStructure::create(['libelle' => 'Lycée/Collège public']);
        $typeDPEP       = TypeStructure::create(['libelle' => 'Direction Provinciale de l\'EPNL']);
        $typeDREP       = TypeStructure::create(['libelle' => 'Direction Régionale de l\'EPNL']);
        $typeDSI        = TypeStructure::create(['libelle' => 'Direction des Systèmes d\'Information']);

        // ─── 4. Types d'équipement ────────────────────────────────────────────
        $typePC         = TypeEquipement::create(['libelle' => 'Ordinateur de bureau']);
        $typeLaptop     = TypeEquipement::create(['libelle' => 'Ordinateur portable']);
        $typeImprimante = TypeEquipement::create(['libelle' => 'Imprimante']);
        $typeRouteur    = TypeEquipement::create(['libelle' => 'Routeur/Modem']);
        $typeSwitch     = TypeEquipement::create(['libelle' => 'Switch réseau']);
        $typeServeur    = TypeEquipement::create(['libelle' => 'Serveur']);
        $typeVideo      = TypeEquipement::create(['libelle' => 'Vidéoprojecteur']);
        $typeTablette   = TypeEquipement::create(['libelle' => 'Tablette numérique']);

        // ─── 5. États d'équipement ────────────────────────────────────────────
        $etatFonctionnel  = EtatEquipement::create(['libelle' => 'Fonctionnel']);
        $etatPanne        = EtatEquipement::create(['libelle' => 'En panne']);
        $etatHorsSvc      = EtatEquipement::create(['libelle' => 'Hors service']);
        $etatMaintenance  = EtatEquipement::create(['libelle' => 'En maintenance']);

        // ─── 6. Statuts des incidents ─────────────────────────────────────────
        $statutSignale   = StatutIncident::create(['libelle' => 'Signalé']);
        $statutEnCours   = StatutIncident::create(['libelle' => 'En cours de traitement']);
        $statutResolu    = StatutIncident::create(['libelle' => 'Résolu']);
        $statutFerme     = StatutIncident::create(['libelle' => 'Fermé']);

        // ─── 7. Statuts des interventions ────────────────────────────────────
        $ivPlanifiee  = StatutIntervention::create(['libelle' => 'Planifiée']);
        $ivEnCours    = StatutIntervention::create(['libelle' => 'En cours']);
        $ivTerminee   = StatutIntervention::create(['libelle' => 'Terminée']);
        $ivAnnulee    = StatutIntervention::create(['libelle' => 'Annulée']);

        // ─── 8. Types d'intervention ─────────────────────────────────────────
        $tiMaintenance = TypeIntervention::create(['libelle' => 'Maintenance préventive']);
        $tiReparation  = TypeIntervention::create(['libelle' => 'Réparation']);
        $tiInstall     = TypeIntervention::create(['libelle' => 'Installation']);
        $tiFormation   = TypeIntervention::create(['libelle' => 'Formation utilisateurs']);
        $tiAudit       = TypeIntervention::create(['libelle' => 'Audit du parc informatique']);

        // ─── 9. Localités (Régions, Provinces, Communes) ─────────────────────

        // Région Centre (Ouagadougou)
        $regCentre = Localite::create([
            'code_localite' => 'REG-CTR',
            'nom'           => 'Centre',
            'type_localite_id' => $typeRegion->id,
            'longitude'     => -1.5197,
            'latitude'      => 12.3714,
        ]);
        $provKadiogo = Localite::create([
            'code_localite' => 'PROV-KAD',
            'nom'           => 'Kadiogo',
            'type_localite_id' => $typeProvince->id,
            'parent_id'     => $regCentre->id,
            'longitude'     => -1.5197,
            'latitude'      => 12.3714,
        ]);
        $commOuaga = Localite::create([
            'code_localite' => 'COM-OUAGA',
            'nom'           => 'Ouagadougou',
            'type_localite_id' => $typeCommune->id,
            'parent_id'     => $provKadiogo->id,
            'longitude'     => -1.5197,
            'latitude'      => 12.3714,
        ]);

        // Région Hauts-Bassins (Bobo-Dioulasso)
        $regHautsBassins = Localite::create([
            'code_localite' => 'REG-HB',
            'nom'           => 'Hauts-Bassins',
            'type_localite_id' => $typeRegion->id,
            'longitude'     => -4.2979,
            'latitude'      => 11.1770,
        ]);
        $provHouet = Localite::create([
            'code_localite' => 'PROV-HOUET',
            'nom'           => 'Houet',
            'type_localite_id' => $typeProvince->id,
            'parent_id'     => $regHautsBassins->id,
            'longitude'     => -4.2979,
            'latitude'      => 11.1770,
        ]);
        $commBobo = Localite::create([
            'code_localite' => 'COM-BOBO',
            'nom'           => 'Bobo-Dioulasso',
            'type_localite_id' => $typeCommune->id,
            'parent_id'     => $provHouet->id,
            'longitude'     => -4.2979,
            'latitude'      => 11.1770,
        ]);

        // Région Nord (Ouahigouya)
        $regNord = Localite::create([
            'code_localite' => 'REG-NORD',
            'nom'           => 'Nord',
            'type_localite_id' => $typeRegion->id,
            'longitude'     => -2.4200,
            'latitude'      => 13.5771,
        ]);
        $provYatenga = Localite::create([
            'code_localite' => 'PROV-YATENGA',
            'nom'           => 'Yatenga',
            'type_localite_id' => $typeProvince->id,
            'parent_id'     => $regNord->id,
            'longitude'     => -2.4200,
            'latitude'      => 13.5771,
        ]);
        $commOuahig = Localite::create([
            'code_localite' => 'COM-OUAHIG',
            'nom'           => 'Ouahigouya',
            'type_localite_id' => $typeCommune->id,
            'parent_id'     => $provYatenga->id,
            'longitude'     => -2.4200,
            'latitude'      => 13.5771,
        ]);

        // ─── 10. Structures / Sites éducatifs ────────────────────────────────

        $dsi = Structure::create([
            'nom'              => 'DSI - Ministère de l\'Enseignement de Base',
            'acronyme'         => 'DSI/MEBAPLN',
            'type_structure_id' => $typeDSI->id,
            'localite_id'      => $commOuaga->id,
            'longitude'        => -1.5197,
            'latitude'         => 12.3714,
        ]);

        $ecoleMogho = Structure::create([
            'nom'              => 'Structure Primaire Publique Mogho Naaba',
            'acronyme'         => 'EPP Mogho Naaba',
            'type_structure_id' => $typePrimaire->id,
            'localite_id'      => $commOuaga->id,
            'longitude'        => -1.5345,
            'latitude'         => 12.3845,
        ]);

        $ecoleKamgho = Structure::create([
            'nom'              => 'Structure Primaire Publique Kamgho',
            'acronyme'         => 'EPP Kamgho',
            'type_structure_id' => $typePrimaire->id,
            'localite_id'      => $commOuaga->id,
            'longitude'        => -1.5120,
            'latitude'         => 12.3600,
        ]);

        $lyceeBobo = Structure::create([
            'nom'              => 'Lycée Technique et Professionnel de Bobo-Dioulasso',
            'acronyme'         => 'LTPB',
            'type_structure_id' => $typeSecondaire->id,
            'localite_id'      => $commBobo->id,
            'longitude'        => -4.3012,
            'latitude'         => 11.1820,
        ]);

        $ecoleOuahig = Structure::create([
            'nom'              => 'Structure Primaire Publique Secteur 5 Ouahigouya',
            'acronyme'         => 'EPP S5 Ouahig.',
            'type_structure_id' => $typePrimaire->id,
            'localite_id'      => $commOuahig->id,
            'longitude'        => -2.4241,
            'latitude'         => 13.5810,
        ]);

        // ─── 11. Équipements ─────────────────────────────────────────────────

        $pc1 = Equipement::create([
            'nom' => 'DELL OptiPlex 3050',
            'version' => '2019',
            'caracteristique' => 'Intel Core i3 – 8 Go RAM – 256 Go SSD',
            'type_equipement_id' => $typePC->id,
            'etat_equipement_id' => $etatFonctionnel->id,
            'structure_id' => $ecoleMogho->id,
            'description' => 'Salle informatique principale',
        ]);

        $pc2 = Equipement::create([
            'nom' => 'HP EliteDesk 800',
            'version' => '2020',
            'caracteristique' => 'Intel Core i5 – 8 Go RAM – 500 Go HDD',
            'type_equipement_id' => $typePC->id,
            'etat_equipement_id' => $etatPanne->id,
            'structure_id' => $ecoleMogho->id,
            'description' => 'Écran cassé, en attente de remplacement',
        ]);

        $routeur1 = Equipement::create([
            'nom' => 'TP-Link Archer C60',
            'version' => 'v3.0',
            'caracteristique' => 'Wi-Fi AC1350 Dual Band',
            'type_equipement_id' => $typeRouteur->id,
            'etat_equipement_id' => $etatFonctionnel->id,
            'structure_id' => $ecoleMogho->id,
            'description' => 'Connexion internet principale',
        ]);

        $pcBobo = Equipement::create([
            'nom' => 'Lenovo ThinkCentre M720',
            'version' => '2021',
            'caracteristique' => 'Intel Core i5 – 16 Go RAM – 512 Go SSD',
            'type_equipement_id' => $typePC->id,
            'etat_equipement_id' => $etatMaintenance->id,
            'structure_id' => $lyceeBobo->id,
            'description' => 'Labo numérique – salle 3',
        ]);

        $videoBobo = Equipement::create([
            'nom' => 'EPSON EB-X41',
            'version' => '2020',
            'caracteristique' => '3600 lumens – HDMI – VGA',
            'type_equipement_id' => $typeVideo->id,
            'etat_equipement_id' => $etatHorsSvc->id,
            'structure_id' => $lyceeBobo->id,
            'description' => 'Lampe défectueuse, hors service',
        ]);

        // ─── 12. Incidents ───────────────────────────────────────────────────

        $inc1 = Incident::create([
            'code'              => 'INC-2026-001',
            'libelle'           => 'Écran d\'ordinateur cassé',
            'structure_id'      => $ecoleMogho->id,
            'statut_incident_id' => $statutEnCours->id,
            'description'       => 'L\'écran du HP EliteDesk 800 est cassé suite à une chute accidentelle. L\'ordinateur reste inutilisable.',
            'date_intervention' => '2026-06-10',
        ]);
        $inc1->equipements()->attach($pc2->id);

        $inc2 = Incident::create([
            'code'              => 'INC-2026-002',
            'libelle'           => 'Vidéoprojecteur hors service',
            'structure_id'      => $lyceeBobo->id,
            'statut_incident_id' => $statutSignale->id,
            'description'       => 'Le vidéoprojecteur EPSON EB-X41 ne s\'allume plus. Lampe probablement défectueuse.',
            'date_intervention' => '2026-07-01',
        ]);
        $inc2->equipements()->attach($videoBobo->id);

        // ─── 13. Interventions ───────────────────────────────────────────────

        Intervention::create([
            'code'                   => 'INT-2026-001',
            'libelle'                => 'Remplacement écran – EPP Mogho Naaba',
            'type_intervention_id'   => $tiReparation->id,
            'statut_intervention_id' => $ivEnCours->id,
            'date_debut'             => '2026-06-15',
            'date_fin'               => null,
            'structure_id'           => $ecoleMogho->id,
        ]);

        Intervention::create([
            'code'                   => 'INT-2026-002',
            'libelle'                => 'Maintenance préventive – LTPB',
            'type_intervention_id'   => $tiMaintenance->id,
            'statut_intervention_id' => $ivPlanifiee->id,
            'date_debut'             => '2026-07-20',
            'date_fin'               => '2026-07-22',
            'structure_id'           => $lyceeBobo->id,
        ]);

        // ─── 14. Utilisateurs de test ─────────────────────────────────────────

        User::create([
            'role_id'       => $roleAdmin->id,
            'structure_id'  => $dsi->id,
            'matricule'     => 'ADM-001',
            'nom'           => 'KOAMA',
            'prenom'        => 'Aboubacar',
            'telephone'     => '+226 70 00 00 01',
            'email'         => 'admin@dsi.gov.bf',
            'mots_de_passe' => Hash::make('Admin@2026'),
        ]);

        User::create([
            'role_id'       => $roleAgent->id,
            'structure_id'  => $dsi->id,
            'matricule'     => 'AGT-001',
            'nom'           => 'SAWADOGO',
            'prenom'        => 'Ibrahim',
            'telephone'     => '+226 70 00 00 02',
            'email'         => 'agent@dsi.gov.bf',
            'mots_de_passe' => Hash::make('Agent@2026'),
        ]);

        User::create([
            'role_id'       => $roleResp->id,
            'structure_id'  => $ecoleMogho->id,
            'matricule'     => 'RSP-001',
            'nom'           => 'OUEDRAOGO',
            'prenom'        => 'Fatimata',
            'telephone'     => '+226 70 00 00 03',
            'email'         => 'responsable@structure-mogho.bf',
            'mots_de_passe' => Hash::make('Resp@2026'),
        ]);

        $this->command->info('✅ Seeder terminé avec succès !');
        $this->command->table(
            ['Compte', 'Email', 'Mot de passe'],
            [
                ['Administrateur DSI', 'admin@dsi.gov.bf', 'Admin@2026'],
                ['Agent DSI',          'agent@dsi.gov.bf', 'Agent@2026'],
                ['Responsable de structure',  'responsable@structure-mogho.bf', 'Resp@2026'],
                
            ]
        );
    }
}
