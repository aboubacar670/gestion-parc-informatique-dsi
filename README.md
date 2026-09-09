# 🖥️ Application de Gestion du Parc Informatique et des Incidents (DSI - MEBAPLN)

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![Leaflet](https://img.shields.io/badge/Leaflet-1.9-199900?style=for-the-badge&logo=leaflet&logoColor=white)](https://leafletjs.com)
[![License](https://img.shields.io/badge/License-MIT-blue.svg?style=for-the-badge)](LICENSE)

Une solution web complète et centralisée développée pour la **Direction des Services Informatiques (DSI)** du Ministère du Enseignement de Base, de l'Alphabétisation et de la Promotion des Langues Nationales (MEBAPLN). 

Cette application vise à moderniser la gestion du parc informatique, automatiser la traçabilité des incidents et interventions de maintenance, et fournir une **cartographie géographique interactive** des équipements et pannes à l'échelle nationale.

---

## 🎯 Fonctionnalités Clés

### 📊 1. Tableau de Bord & Cartographie Interactive
- Dashboard avec indicateurs clés (KPIs) en temps réel : nombre total d'équipements, incidents ouverts, interventions en cours, taux de résolution.
- **Carte Géographique Interactive (Leaflet.js)** : Visualisation géographique des structures et des équipements avec code couleur selon l'état des incidents (Critique, Élevé, Moyen, Faible).

### 💻 2. Gestion du Parc Informatique (Équipements & Structures)
- Fiche détaillée pour chaque équipement (ordinateurs, imprimantes, serveurs, routeurs, etc.).
- Historique d'affectation par structure et par localité administrative.
- Suivi de la garantie, de la marque, du numéro de série et du statut opérationnel.

### ⚠️ 3. Gestion Automatisée des Incidents
- Signalement et enregistrement des pannes matérielles ou logicielles.
- **Génération automatique de référence unique** (`INC-YYYYMMDD-XXX`).
- Classification par sévérité et attribution prioritaire aux techniciens.

### 🛠️ 4. Suivi des Interventions de Maintenance
- Fiches d'intervention technique avec **code unique automatique** (`INT-YYYYMMDD-XXX`).
- Rapport de diagnostic, pièces remplacées et résolution.
- Mise à jour automatique du statut des incidents à la clôture de l'intervention.

### 📥 5. Importation & Exportation Excel Avancées
- **Exportation en 1 clic** des listes structurées sous format Excel (.xlsx).
- **Importation assistée avec validation des données** (menus déroulants intégrés aux modèles Excel).
- Traitement robuste des erreurs lors de l'importation avec génération de compte-rendu d'erreurs détaillé.

---

## 🛠️ Stack Technique

- **Backend** : PHP 8.2+, Framework Laravel 11.x (Architecture MVC, Eloquent ORM, Artisan CLI)
- **Frontend** : Blade Templates, Bootstrap 5, FontAwesome Icons, JavaScript (ES6)
- **Cartographie** : Leaflet.js, OpenStreetMap API
- **Traitement Excel** : PhpSpreadsheet (`phpoffice/phpspreadsheet`)
- **Base de Données** : MySQL / MariaDB (Compatible SQLite pour le développement local)

---

## 🚀 Installation & Configuration Locale

### Prérequis
- PHP >= 8.2
- Composer
- Node.js & NPM
- Serveur MySQL (ex: XAMPP, WAMP, Laragon) ou SQLite

### Étapes d'installation

1. **Cloner le dépôt**
   ```bash
   git clone https://github.com/VOTRE_NOM_UTILISATEUR/gestion-dsi.git
   cd gestion-dsi
   ```

2. **Installer les dépendances PHP et JavaScript**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Configurer le fichier d'environnement**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configurer la base de données dans `.env`**
   ```env
   DB_CONNECTION=sqlite
   # Ou pour MySQL :
   # DB_CONNECTION=mysql
   # DB_HOST=127.0.0.1
   # DB_PORT=3306
   # DB_DATABASE=gestion_dsi
   # DB_USERNAME=root
   # DB_PASSWORD=
   ```

5. **Exécuter les migrations et insérer les données de démonstration (Seeder)**
   ```bash
   php artisan migrate --seed
   ```

6. **Lancer le serveur de développement**
   ```bash
   php artisan serve
   ```
   Rendez-vous sur `http://127.0.0.1:8000` !

---

## 🔑 Identifiants de Démonstration

| Rôle | Email | Mot de passe |
| :--- | :--- | :--- |
| **Administrateur** | `admin@dsi.gov.bf` | `password` |
| **Technicien** | `technicien@dsi.gov.bf` | `password` |
| **Utilisateur** | `agent@dsi.gov.bf` | `password` |

---

## 👨‍💻 Auteur

Développé par **[Ton Nom / Prénom]** dans le cadre du projet de stage / mémoire de fin d'études à l'**IPAM** pour la **DSI (MEBAPLN)**.

- **LinkedIn** : [Ton Profil LinkedIn](https://linkedin.com)
- **Email** : ton.email@example.com

---
*Projet open-source à des fins de présentation de portfolio.*
