# ASIN Bénin — Suivi des Demandes d'Actes Administratifs

**Candidat :** HOUEFONDE Josué  
**Dépôt GitHub officiel :** [https://github.com/josuefox123/etudecaspratiqueASIN_HOUEFONDE_Josue.git](https://github.com/josuefox123/etudecaspratiqueASIN_HOUEFONDE_Josue.git)

Solution numérique complète d'instruction et de suivi des demandes d'actes administratifs (actes de naissance, casiers judiciaires, certificats de résidence), développée selon les standards d'ingénierie logicielle et de sécurité de l'**Agence des Systèmes d'Information et du Numérique (ASIN)** de la République du Bénin.

---

## 🏛️ Vue d'ensemble du Projet

Le projet est structuré sous forme de **Monorepo** comprenant :
- **`/backend`** : API REST sous **Laravel 11** alimentée par une base de données **SQLite** prête à l'emploi et documentée automatiquement avec **Dedoc Scramble (OpenAPI/Swagger)**.
- **`/frontend`** : Application Monopage (SPA) sous **Vue.js 3 (Composition API / `<script setup>`)**, propulsée par **Vite** et stylisée avec **Tailwind CSS**. Elle intègre le portail citoyen (carrousel béninois, tableau de bord NPI, récépissé officiel, impression PDF, assistance vocale) et la console agent **Soft UI Dashboard PRO**. Le projet est 100% responsive sur mobile, tablette et desktop.

---

## 📁 Architecture du Monorepo

```
etudecaspratiqueASIN_HOUEFONDE_Josue/
├── backend/                              # API REST Laravel 11 (SQLite)
│   ├── app/
│   │   ├── Http/Controllers/Api/
│   │   │   └── DemandeController.php     # Endpoints v1 & Contrôle du cycle de vie
│   │   ├── Http/Requests/
│   │   │   ├── StoreDemandeRequest.php   # Validation NPI (10 chiffres), acte, copies
│   │   │   └── UpdateStatutDemandeRequest.php # Validation transition & motif de rejet
│   │   └── Models/Demande.php            # Modèle Eloquent & auto-génération UUID
│   ├── config/cors.php                   # Configuration CORS pour http://localhost:5173
│   ├── database/
│   │   └── database.sqlite               # Base de données SQLite pré-configurée
│   ├── routes/api.php                    # Endpoints /api/v1/
│   └── tests/Feature/DemandeGestionTest.php # Suite de 8 tests automatisés (26 assertions)
│
├── frontend/                             # SPA Vue.js 3 + Vite + Tailwind CSS
│   ├── .env                              # VITE_API_BASE_URL=http://localhost:8000/api/v1
│   ├── src/
│   │   ├── App.vue                       # Portail citoyen & Console Agent Soft UI PRO
│   │   └── main.js
│   └── vite.config.js
│
├── .gitignore                            # Exclusion vendor/, node_modules/, .env, dist/
└── README.md                             # Guide d'évaluation du jury
```

---

## 📥 Guide de Clonage & Lancement Rapide (Pour le Jury)

### Préréquis
- **Git**
- **PHP** >= 8.2 (avec extension PDO SQLite)
- **Node.js** >= 18.x et **npm**

---

### Étape 1 : Cloner le dépôt GitHub

```bash
git clone https://github.com/josuefox123/etudecaspratiqueASIN_HOUEFONDE_Josue.git
cd etudecaspratiqueASIN_HOUEFONDE_Josue
```

---

### Étape 2 : Lancer le Backend (`/backend`)

Dans un premier terminal :

```bash
cd backend
composer install
php artisan key:generate
php artisan migrate --force
php artisan serve --port=8000
```
*(Le serveur API REST écoute sur `http://localhost:8000`)*

---

### Étape 3 : Lancer le Frontend (`/frontend`)

Dans un second terminal :

```bash
cd frontend
npm install
npm run dev
```
*(L'application Web tourne sur `http://localhost:5173`)*

---

## 🔑 URLs d'Accès & Identifiants du Dashboard Agent

| Espace | URL d'Accès | Identifiants / Code d'accès | Description |
| :--- | :--- | :--- | :--- |
| **Portail Citoyen (Public)** | [`http://localhost:5173`](http://localhost:5173) | *Accès libre usager* | Recherche par NPI, dépôt de demande, assistance vocale. Aucun bouton d'agent visible. |
| **Espace Agent (Dashboard PRO)** | [`http://localhost:5173/#/agent`](http://localhost:5173/#/agent) | **Code Agent : `AGENT2026`** | Console d'instruction sécurisée (Soft UI Dashboard PRO) pour traiter les demandes. |
| **Documentation API (OpenAPI)** | [`http://localhost:8000/docs/api`](http://localhost:8000/docs/api) | *Accès libre* | Interface interactive Dedoc Scramble documentant les 5 endpoints REST. |
| **Endpoint Base API REST v1** | [`http://localhost:8000/api/v1`](http://localhost:8000/api/v1) | *Accès API* | Racine des endpoints JSON. |

---

## 📋 Règles Métiers & Spécifications Implémentées

### 1. Modèle de Données `demandes`
- `reference` : UUID v4 unique généré automatiquement à la création.
- `npi` : Numéro Personnel d'Identification (strictement 10 chiffres numériques).
- `type_acte` : Enum (`acte de naissance`, `casier judiciaire`, `certificat de résidence`).
- `nombre_copies` : Entier compris entre 1 et 5.
- `statut` : Enum (`déposée`, `en cours de traitement`, `validée`, `rejetée`). Statut initial forcé à `déposée`.
- `motif_rejet` : Texte nullable. **Obligatoire (min 5 caractères)** si le statut passe à `rejetée`.

### 2. Cycle de Vie Strict & Immuabilité
- `déposée` ➔ uniquement vers `en cours de traitement`.
- `en cours de traitement` ➔ uniquement vers `validée` ou `rejetée`.
- **États Finaux Immuables** : Toute tentative de modification d'une demande aux statuts `validée` ou `rejetée` est immédiatement bloquée avec un code d'erreur `HTTP 422`.

### 3. Endpoints API v1 REST
- `POST /api/v1/demandes` : Création d'une nouvelle demande (statut forcé à `déposée`).
- `GET /api/v1/usagers/{npi}/demandes` : Consultation des demandes d'un usager, paginée (max 20), triée par date décroissante, avec filtre optionnel `?statut=`.
- `PATCH /api/v1/demandes/{reference}/statut` : Transition de statut sécurisée avec validation des transitions autorisées.
- `GET /api/v1/demandes/stats` : Dénombrement synthétique par statut.
- `GET /api/v1/demandes` : Consultation globale pour la console agent d'instruction.

---

## 🛡️ Sécurité & Ingénierie Logicielle (OWASP Top 10)

1. **Prévention Injections SQL** : Utilisation exclusive des requêtes paramétrées de l'ORM Eloquent.
2. **Protection Mass Assignment** : Encapsulation `$fillable` stricte sur le modèle `Demande`.
3. **Protection Anti-DDoS / Saturation** : Pagination bornée à 20 éléments par page sur l'ensemble des endpoints de recherche.
4. **Validation Stricte par FormRequests** : Isolation complète de la validation d'entrée (`StoreDemandeRequest` et `UpdateStatutDemandeRequest`) avec messages d'erreurs clairs en français.
5. **Accessibilité & Souveraineté Numérique** : Assistance vocale 100% locale via la **Web Speech API (`window.speechSynthesis`)**, sans aucune dépendance externe ni fuite de données nominatives vers des tiers.
6. **Design Système Soigné (Sans Emojis)** : Utilisation d'icônes vectorielles SVG (Heroicons), typographie institutionnelle, palettes aux couleurs nationales du Bénin et tableau de bord de type Soft UI Dashboard PRO.

---

## 🎨 Caractéristiques UX/UI & Fonctionnalités Bonus

1. **Carrousel Hero béninois** : 3 visuels haute définition intégrant les thèmes e-Gouvernement Bénin, Cotonou Smart City et Citoyenneté numérique.
2. **Tableau de Bord Citoyen par NPI** : Cartes synthétiques (Total, Déposées, En cours, Validées, Rejetées) et barre de progression dynamique.
3. **Imprimante & Récépissé Officiel A4 (Bonus)** : Génération d'une **Attestation de Dépôt officielle** avec en-tête de la République du Bénin, bloc NPI, référence UUID, QR code de vérification et impression/génération PDF via `window.print()`.
4. **Copie rapide UUID (Bonus)** : Bouton interactif de copie de la référence avec feedback instantané.
5. **Responsivité Intégrale (100%)** : Adaptation fluide sur smartphones, tablettes et écrans desktop pour tous les tableaux, formulaires et carrousels.

---

## 🧪 Lancement de la Suite de Tests Automatisés

Les 8 tests d'intégration et de validation des règles métiers peuvent être exécutés à tout moment :

```bash
cd backend
php artisan test --filter=DemandeGestionTest
```

**Résultat d'exécution (8/8 passés avec succès) :**
```bash
  PASS  Tests\Feature\DemandeGestionTest
  ✓ creation demande force statut deposee                                0.35s  
  ✓ validation champs creation demande                                   0.02s  
  ✓ recuperation demandes par npi avec filtre                            0.02s  
  ✓ transition deposee vers en cours                                     0.02s  
  ✓ interdiction transition directe deposee vers validee                 0.02s  
  ✓ motif rejet obligatoire pour statut rejete                           0.02s  
  ✓ immuabilite etats finaux                                             0.02s  
  ✓ statistiques demandes                                                0.02s  

  Tests: 8 passed (26 assertions)
  Duration: 0.64s
```
