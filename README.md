# Mangroove Website

Environnement de développement Dockerisé pour le projet Mangroove (Symfony + API Platform + Vue + PrimeVue).

---

## 🚀 Démarrage rapide

### 1. Cloner le dépôt

```bash
git clone git@github.com:G8ite/Mangroove-website.git
cd Mangroove-website
```

### 2. Lancer l'environnement (build + initialisation automatique)

```bash
docker compose up --build
```

> ✅ Le backend Symfony et le frontend Vue s'initialisent automatiquement au premier lancement (plus besoin de vider de dossier ou d'exécuter une commande à la main).

* Frontend : [http://localhost:5173](http://localhost:5173)
* Backend : [http://localhost:8000/api](http://localhost:8000/api)

---

## 🛠️ Structure du projet

```
Mangroove-website/
├── backend/       # Symfony + API Platform (initialisé et versionné)
├── frontend/      # Vue 3 + PrimeVue (initialisé automatiquement)
├── docker-compose.yml
```

---

## � Collection Postman

**Collection prête à l'emploi pour tester l'API :**

1. **Importer** : `Mangroove_API.postman_collection.json`
2. **Environnement** : `Mangroove_Local.postman_environment.json`
3. **Guide complet** : [POSTMAN_GUIDE.md](POSTMAN_GUIDE.md)

> ⚡ **Authentification automatique** : Le token JWT est géré automatiquement !

---

## �📖 Documentation utile

* Symfony : [https://symfony.com/doc](https://symfony.com/doc)
* API Platform : [https://api-platform.com/docs/](https://api-platform.com/docs/)
* Vue.js : [https://vuejs.org/](https://vuejs.org/)
* PrimeVue : [https://www.primefaces.org/primevue/](https://www.primefaces.org/primevue/)

---

## 🎮 Cycle de vie d'une GameJam

Mangroove gère le cycle de vie complet d'une GameJam à travers 5 statuts distincts et des endpoints API dédiés pour chaque transition.

### 📊 Statuts disponibles

| Statut | Description | Actions possibles |
|--------|-------------|-------------------|
| `draft` | Brouillon - Jam en cours de création | Modification, Publication |
| `published` | Publié - Jam annoncée publiquement | Démarrage, Retour en brouillon |
| `running` | En cours - Soumissions ouvertes | Fermeture |
| `closed` | Fermé - Votes en cours, soumissions fermées | Archivage |
| `archived` | Archivé - Résultats publiés, jam terminée | Aucune (état final) |

### 🔄 Flux de transitions

```text
DRAFT → PUBLISHED → RUNNING → CLOSED → ARCHIVED
  ↓         ↓          ↓         ↓         ↓
Création  Annonce   Soumissions  Votes   Résultats
         publique   ouvertes   ouverts  publiés
```

### 🛠️ Endpoints API

#### 1. Publier une Jam

```http
POST /api/jams/{id}/publish
```

* **Transition :** `draft` → `published`
* **Description :** Publie la jam et la rend visible au public

#### 2. Démarrer une Jam

```http
POST /api/jams/{id}/start
```

* **Transition :** `published` → `running`
* **Description :** Ouvre officiellement les soumissions

#### 3. Fermer une Jam

```http
POST /api/jams/{id}/close
```

* **Transition :** `running` → `closed`
* **Description :** Ferme les soumissions et ouvre les votes

#### 4. Archiver une Jam

```http
POST /api/jams/{id}/archive
```

* **Transition :** `closed` → `archived`
* **Description :** Archive définitivement la jam après publication des résultats

### ✅ Validation des transitions

Chaque endpoint valide que la jam est dans le bon état avant d'effectuer la transition :

* ❌ **Erreur 400** : Transition invalide (mauvais statut actuel)
* ❌ **Erreur 404** : Jam inexistante
* ✅ **Succès 200** : Transition effectuée

### 🧪 Tests unitaires

Tous les contrôleurs sont testés avec PHPUnit :

```bash
# Tester tous les contrôleurs de cycle de vie
docker compose exec -e APP_ENV=test backend vendor/bin/phpunit --filter "JamPublishingControllerTest|JamStartControllerTest|JamCloseControllerTest|JamArchiveControllerTest"
```

---

## 🔧 Pour les développeurs backend

Le backend Symfony est prêt à l'emploi avec l'authentification par JWT déjà en place (inscription, login). Tous les fichiers Symfony sont versionnés, y compris :

* Entités (ex : `User.php`)
* Contrôleurs (ex : `RegisterController`)
* Clés JWT
* Migrations Doctrine

### Modifier le code Symfony

#### 🖥️ Génération de fichiers Symfony (en local uniquement)

Les commandes suivantes doivent être exécutées **en local**, pour garantir que les fichiers générés (entités, contrôleurs, etc.) soient bien présents dans le dossier `backend/` et versionnables :

```bash
php bin/console make:entity
php bin/console make:controller
php bin/console make:user
php bin/console make:form
php bin/console make:migration
```

#### 🐘 Commandes liées à la base de données (à exécuter dans Docker)

Ces commandes doivent être exécutées **dans le container**, car la base PostgreSQL n’est accessible que dans le réseau Docker (host `db`) :

```bash
docker compose exec backend bash
php bin/console doctrine:migrations:migrate
```

> ⚠️ Ne pas générer de fichiers dans le container (sinon ils ne seront pas visibles depuis le repo Git).

### ⚠️ Ne pas recréer Symfony dans Docker

Le projet Symfony a été initialisé une fois en local (PHP + Composer), puis versionné.

✅ Aucun `composer create-project` n'est requis dans le `Dockerfile`.

---

## 🧪 Tests

Le projet est configuré avec PHPUnit et prêt pour les tests. L'environnement de test est préparé automatiquement lors de l'exécution des tests.

### Exécuter les tests

#### Option 1 : Script rapide (recommandé)

```bash
./run-tests.sh
```

#### Option 2 : Commande Docker directe

```bash
# Tous les tests
docker compose exec -e APP_ENV=test backend vendor/bin/phpunit

# Tests spécifiques
docker compose exec -e APP_ENV=test backend vendor/bin/phpunit tests/Controller/RegisterControllerTest.php

# Tests avec détails
docker compose exec -e APP_ENV=test backend vendor/bin/phpunit --verbose
```

### Configuration automatique des tests

Le script `./run-tests.sh` configure automatiquement :

* ✅ Base de données de test (`mangroove_test`)
* ✅ Schéma de base de données pour les tests
* ✅ Environnement de test PHPUnit

### Structure des tests

```text
backend/tests/
├── Controller/
│   └── RegisterControllerTest.php  # Tests d'API
└── bootstrap.php                   # Configuration PHPUnit
```

### Développement de nouveaux tests

```bash
# Accéder au container pour développer
docker compose exec backend bash

# Créer un nouveau test
vendor/bin/phpunit --generate-test src/Controller/MonController.php
```

---

## ⚡ Tips

* Pour reconstruire un service :

  ```bash
  docker compose build frontend
  docker compose build backend
  ```

* Pour voir les logs d'un service :

  ```bash
  docker compose logs -f backend
  docker compose logs -f frontend
  ```

* Pour accéder à un container :

  ```bash
  docker exec -it mangroove_backend bash
  ```

---

## ✅ Prérequis

* Docker Desktop (WSL2 si vous êtes sous Windows)
* Git

---

## ⚖️ Licences

à réfléchir
