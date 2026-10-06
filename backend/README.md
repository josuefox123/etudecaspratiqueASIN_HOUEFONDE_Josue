# Suivi des Demandes d'Actes Administratifs - ASIN Bénin (Backend)

Solution REST API (Laravel 11) & Frontend (Vue.js 3) conçue selon les normes et le cahier des charges de l'**ASIN (Agence des Services et Systèmes d'Information du Bénin)**.

---

## Fonctionnalités & Spécifications

### 1. Backend REST API (Laravel 11)
- **Table `demandes`** :
  - `id` : Identifiant unique auto-incrémenté.
  - `reference` : UUID unique généré automatiquement à la création.
  - `npi` : Numéro Personnel d'Identification (exactement 10 chiffres numériques).
  - `type_acte` : Enum (`acte de naissance`, `casier judiciaire`, `certificat de résidence`).
  - `nombre_copies` : Entier compris entre 1 et 5.
  - `statut` : Enum (`déposée`, `en cours de traitement`, `validée`, `rejetée`). Statut initial forcé à `déposée`.
  - `motif_rejet` : Texte nullable, **obligatoire** (min 5 caractères) si le statut devient `rejetée`.
  - `timestamps` : `created_at` et `updated_at`.

- **Cycle de Vie Strict** :
  - `déposée` -> uniquement vers `en cours de traitement`.
  - `en cours de traitement` -> uniquement vers `validée` ou `rejetée`.
  - `validée` et `rejetée` sont des **états finaux immuables** (toute modification ultérieure est rejetée avec HTTP 422).

### 2. Frontend (Vue.js 3)
- Interface de recherche par **NPI (10 chiffres)** avec messages d'erreurs clairs en français.
- Filtres dynamiques par statut (`déposée`, `en cours de traitement`, `validée`, `rejetée`).
- Badges de statut colorés et affichage clair du motif de rejet le cas échéant.
- **Accessibilité Vocale Locale** : Bouton de lecture vocale utilisant l'API native `window.speechSynthesis` (Web Speech API) sans aucune clé ou API externe.
- **Formulaire de dépôt** de nouvelle demande.
- **Console Agent de traitement** pour la gestion de l'instruction et transitions de statut.
- **Tableau de Bord** avec vue synthétique des statistiques globales.

---

## Routes API (v1)

| Méthode | Endpoint | Description |
| :--- | :--- | :--- |
| `POST` | `/api/v1/demandes` | Dépôt d'une demande (statut initial forcé à `déposée`). |
| `GET` | `/api/v1/usagers/{npi}/demandes` | Liste paginée (max 20) des demandes d'un NPI, triée par date desc, avec `?statut=` optionnel. |
| `PATCH` | `/api/v1/demandes/{reference}/statut` | Transition de statut avec validation stricte du cycle de vie et du motif. |
| `GET` | `/api/v1/demandes/stats` | Compte des demandes regroupées par statut. |
| `GET` | `/api/v1/demandes` | Liste globale des demandes pour la console agent d'instruction. |

---

## Exécution des Tests Automatisés

Les règles de gestion (validation NPI, cycle de vie, immuabilité, motif de rejet) sont couvertes par des tests automatisés PHPUnit/Pest.

```bash
php artisan test --filter=DemandeGestionTest
```

---

## Installation & Démarrage Rapide

### 1. Cloner et Installer les dépendances Composer
```bash
composer install
```

### 2. Configuration de l'environnement `.env`
Copier le fichier d'exemple et générer la clé d'application :
```bash
cp .env.example .env
php artisan key:generate
```

Pour SQLite (prêt à l'emploi) :
```bash
touch database/database.sqlite
```

Ou configurer les accès MySQL dans le fichier `.env` :
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=asin_demandes
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Migrations
Exécuter les migrations de base de données :
```bash
php artisan migrate
```

### 4. Démarrer le serveur local
```bash
php artisan serve
```

API disponible sur : **`http://127.0.0.1:8000/api/v1`** — documentation : **`http://127.0.0.1:8000/docs/api`**

---

## Structure du Projet

```
.
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/
│   │   │   └── DemandeController.php       # Endpoints REST API v1 & Règles de gestion
│   │   └── Requests/
│   │       ├── StoreDemandeRequest.php     # Validation dépôt demande (NPI 10 chiffres, etc.)
│   │       └── UpdateStatutDemandeRequest.php # Validation transition statut & motif rejet
│   └── Models/
│       └── Demande.php                     # Modèle Eloquent & auto-génération UUID
├── database/
│   └── migrations/
│       └── 2026_10_06_000000_create_demandes_table.php
├── routes/
│   ├── api.php                             # Enregistrement des routes API /api/v1/
│   └── web.php
└── tests/
    └── Feature/
        └── DemandeGestionTest.php          # 8 tests automatisés (26 assertions)
```
