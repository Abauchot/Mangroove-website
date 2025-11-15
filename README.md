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

### 3. Charger les données de test (optionnel)

```bash
./load-fixtures.sh
```

> ✅ Le backend Symfony et le frontend Vue s'initialisent automatiquement au premier lancement. Les fixtures ajoutent des données de test cohérentes pour le développement.

* Frontend : [http://localhost:5173](http://localhost:5173)
* Backend : [http://localhost:8000/api](http://localhost:8000/api)

---

## 🛠️ Structure du projet

```text
Mangroove-website/
├── backend/       # Symfony + API Platform (initialisé et versionné)
├── frontend/      # Vue 3 + PrimeVue (initialisé automatiquement)
├── docker-compose.yml
├── load-fixtures.sh  # Script de chargement des données de test
```

---

## � Collection Postman

**Collection prête à l'emploi pour tester l'API :**

1. **Importer** : `Mangroove_API.postman_collection.json`
2. **Environnement** : `Mangroove_Local.postman_environment.json`
3. **Guide complet** : [POSTMAN_GUIDE.md](POSTMAN_GUIDE.md)

> ⚡ **Authentification automatique** : Le token JWT est géré automatiquement !

### 🆕 Nouveautés Forum dans Postman

La collection inclut maintenant **tous les endpoints du forum** :

* **🧵 Forum Threads** : Création, modification, épinglage, verrouillage
* **💬 Forum Posts** : Messages et réponses hiérarchiques  
* **📊 Forum Stats** : Statistiques globales du forum
* **🔄 Variables auto** : `{{forum_thread_id}}` et `{{forum_post_id}}` remplies automatiquement

**Test rapide recommandé :**
1. Login → Threads → Créer post → Répondre → Statistiques

> 📊 **Collection complète** : 49 endpoints au total couvrant tout l'écosystème Mangroove !

---

## 🗃️ Gestion des données de test (Fixtures)

**Système de fixtures Symfony pour peupler la base de données avec des données cohérentes.**

### 🚀 Chargement des fixtures

```bash
# Chargement complet (développement)
./load-fixtures.sh

# Chargement pour tests
./load-fixtures.sh test
```

Le script charge automatiquement les fixtures dans l'ordre correct et affiche un rapport détaillé.

### 📊 Données générées

| Type | Quantité | Description |
|------|----------|-------------|
| **Utilisateurs** | 5 | Admin, modérateur et utilisateurs standard |
| **Jams** | 4 | Différents statuts (draft, published, running, closed) |
| **GameEntries** | 5 | Jeux avec URLs et différents états |
| **Commentaires** | 7 | Commentaires modérés et publics |

### 👥 Comptes de test disponibles

```text
Admin :      admin@mangroove.com / admin123
Modérateur : moderator@mangroove.com / mod123
Utilisateur: alice@example.com / alice123
Utilisateur: bob@example.com / bob123
Utilisateur: charlie@example.com / charlie123
```

### 🏗️ Architecture des fixtures

```text
backend/src/DataFixtures/
├── UserFixtures.php      # Utilisateurs (ordre 1)
├── JamFixtures.php       # Game Jams (ordre 2)  
├── GameEntryFixtures.php # Soumissions (ordre 3)
└── CommentFixtures.php   # Commentaires (ordre 4)
```

Chaque fixture implémente `OrderedFixtureInterface` pour garantir le bon ordre de chargement.

### 🛠️ Développement des fixtures

```bash
# Créer une nouvelle fixture
docker compose exec backend php bin/console make:fixtures

# Vider et recharger la base
docker compose exec backend php bin/console doctrine:fixtures:load --no-interaction
```

---

## 🧵 Système de Forum/Threads

Le projet intègre un système de forum complet pour faciliter les discussions entre développeurs lors des game jams.

### 📋 Entités du Forum

1. **ForumThread** (`forum_thread` table)
   - ID UUID unique, titre du thread
   - Auteur (relation avec User)
   - Jam liée (optionnelle, relation avec Jam)
   - Visibilité publique/privée
   - Statuts: épinglé, verrouillé, annonce
   - Timestamps de création/mise à jour

2. **ForumPost** (`forum_post` table)
   - ID UUID unique, contenu du message
   - Auteur (relation avec User)
   - Thread parent (relation avec ForumThread)
   - Post parent (pour les réponses, auto-référence)
   - Timestamp de création

### 🛠 API Endpoints Forum

#### Endpoints CRUD automatiques (API Platform)

```http
GET    /api/forum_threads     # Lister les threads
POST   /api/forum_threads     # Créer un thread
GET    /api/forum_threads/{id} # Détail d'un thread
PATCH  /api/forum_threads/{id} # Modifier un thread
DELETE /api/forum_threads/{id} # Supprimer un thread

GET    /api/forum_posts       # Lister les posts
POST   /api/forum_posts       # Créer un post
GET    /api/forum_posts/{id}  # Détail d'un post
PATCH  /api/forum_posts/{id}  # Modifier un post
DELETE /api/forum_posts/{id}  # Supprimer un post
```

#### Endpoints personnalisés

```http
GET  /api/forum/threads/{id}/posts # Posts d'un thread avec hiérarchie
POST /api/forum/threads/{id}/pin   # Épingler/désépingler un thread
POST /api/forum/threads/{id}/lock  # Verrouiller/déverrouiller un thread
GET  /api/forum/stats              # Statistiques globales du forum
```

### 🔐 Permissions Forum

| Action | USER | MODERATOR | ADMIN |
|--------|------|-----------|-------|
| Voir threads publics | ✅ | ✅ | ✅ |
| Créer thread | ✅ | ✅ | ✅ |
| Poster message | ✅ | ✅ | ✅ |
| Modifier ses posts | ✅ | ✅ | ✅ |
| Épingler thread | ❌ | ✅ | ✅ |
| Verrouiller thread | ❌ | ✅ | ✅ |
| Supprimer posts autres | ❌ | ✅ | ✅ |
| Créer annonces | ❌ | ✅ | ✅ |

### 📝 Exemples d'utilisation Forum

#### 1. Créer un nouveau thread

```bash
curl -X POST http://localhost:8000/api/forum_threads \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Recherche coéquipiers pour la prochaine jam",
    "author": "/api/users/{USER_ID}",
    "isPublic": true
  }'
```

#### 2. Créer un thread lié à une jam

```bash
curl -X POST http://localhost:8000/api/forum_threads \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Discussion - Stratégies pour la Game Jam",
    "author": "/api/users/{USER_ID}",
    "jam": "/api/jams/{JAM_ID}",
    "isPublic": true
  }'
```

#### 3. Poster un message dans un thread

```bash
curl -X POST http://localhost:8000/api/forum_posts \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "content": "Je suis développeur Unity avec 2 ans d'\''expérience !",
    "thread": "/api/forum_threads/{THREAD_ID}",
    "author": "/api/users/{USER_ID}"
  }'
```

#### 4. Répondre à un post (système hiérarchique)

```bash
curl -X POST http://localhost:8000/api/forum_posts \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "content": "Moi aussi, on peut faire équipe !",
    "thread": "/api/forum_threads/{THREAD_ID}",
    "author": "/api/users/{USER_ID}",
    "parent": "/api/forum_posts/{PARENT_POST_ID}"
  }'
```

#### 5. Récupérer les posts d'un thread avec hiérarchie

```bash
curl -X GET http://localhost:8000/api/forum/threads/{THREAD_ID}/posts \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 🎯 Fonctionnalités Forum

✅ **Threads hiérarchiques** : Threads avec posts et réponses  
✅ **Sécurité** : Authentification JWT requise  
✅ **Permissions** : Contrôle d'accès par rôles  
✅ **Modération** : Épinglage et verrouillage des threads  
✅ **Intégration Jams** : Threads liés aux game jams  
✅ **Réponses imbriquées** : Système de réponses aux posts  
✅ **API REST complète** : CRUD pour threads et posts  
✅ **Statistiques** : Service d'analyse de l'activité du forum

### 📊 Données de test Forum

Le système de forum est initialisé avec :
- **5 threads** de démonstration (annonce, aide technique, discussion jam, showcase, feedback)
- **10 posts** avec système de réponses hiérarchiques
- Threads liés aux jams existantes
- Thread d'annonce épinglé
- Exemples de discussions techniques et de feedback

---

## 📖 Documentation utile

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

## 🎭 Matrice des rôles

Mangroove implémente un système de rôles hiérarchique pour gérer les permissions sur la plateforme de GameJam.

### 📊 Hiérarchie des rôles

```text
ADMIN > MODERATOR > USER
```

### 👥 Gestion des Utilisateurs

| **Action** | **USER** | **MODERATOR** | **ADMIN** |
|------------|----------|---------------|-----------|
| Voir son profil | ✅ | ✅ | ✅ |
| Modifier son profil | ✅ | ✅ | ✅ |
| Supprimer son compte | ✅ | ✅ | ✅ |
| Lister tous les utilisateurs | ❌ | ✅ | ✅ |
| Voir profil d'un autre utilisateur | ❌ | ✅ | ✅ |
| Modifier un autre utilisateur | ❌ | ❌ | ✅ |
| Supprimer un autre utilisateur | ❌ | ❌ | ✅ |
| Promouvoir/Rétrograder | ❌ | ❌ | ✅ |

### 🎯 Gestion des Jams

| **Action** | **USER** | **MODERATOR** | **ADMIN** |
|------------|----------|---------------|-----------|
| Voir toutes les jams | ✅ | ✅ | ✅ |
| Voir détail d'une jam | ✅ | ✅ | ✅ |
| Filtrer par statut/slug | ✅ | ✅ | ✅ |
| **Création** | | | |
| Créer une jam | ❌ | ✅ | ✅ |
| **Modifications** | | | |
| Modifier une jam | ❌ | ✅ (ses jams) | ✅ (toutes) |
| Supprimer une jam | ❌ | ✅ (ses jams) | ✅ (toutes) |
| **Cycle de vie** | | | |
| Publier (`draft` → `published`) | ❌ | ✅ (ses jams) | ✅ (toutes) |
| Démarrer (`published` → `running`) | ❌ | ✅ (ses jams) | ✅ (toutes) |
| Fermer (`running` → `closed`) | ❌ | ✅ (ses jams) | ✅ (toutes) |
| Archiver (`closed` → `archived`) | ❌ | ✅ (ses jams) | ✅ (toutes) |

### 🎮 Gestion des GameEntry (Soumissions)

| **Action** | **USER** | **MODERATOR** | **ADMIN** |
|------------|----------|---------------|-----------|
| Voir soumissions d'une jam | ✅ | ✅ | ✅ |
| Soumettre un jeu | ✅ | ✅ | ✅ |
| Modifier sa soumission | ✅ (pendant `running`) | ✅ | ✅ |
| Supprimer sa soumission | ✅ (pendant `running`) | ✅ | ✅ |
| Modifier soumission d'autrui | ❌ | ✅ | ✅ |
| Supprimer soumission d'autrui | ❌ | ✅ | ✅ |

### 🏆 Gestion des Votes et Évaluations

| **Action** | **USER** | **MODERATOR** | **ADMIN** |
|------------|----------|---------------|-----------|
| Voter pour un jeu | ✅ (pendant `closed`) | ✅ | ✅ |
| Voir résultats | ✅ | ✅ | ✅ |
| Modifier votes d'autrui | ❌ | ❌ | ✅ |
| Publier résultats | ❌ | ✅ (ses jams) | ✅ (toutes) |

### 🛠️ Administration

| **Action** | **USER** | **MODERATOR** | **ADMIN** |
|------------|----------|---------------|-----------|
| Voir logs système | ❌ | ❌ | ✅ |
| Configurer API | ❌ | ❌ | ✅ |
| Gestion base de données | ❌ | ❌ | ✅ |
| Backup/Restore | ❌ | ❌ | ✅ |

### 🔧 Implémentation

#### Rôles dans l'entité User

```php
const ROLE_USER = 'ROLE_USER';
const ROLE_MODERATOR = 'ROLE_MODERATOR';
const ROLE_ADMIN = 'ROLE_ADMIN';

#[ORM\Column(type: 'json')]
private array $roles = ['ROLE_USER'];
```

#### Principes de sécurité

* **Principe du moindre privilège** : Tout est interdit par défaut
* **Escalade progressive** : USER → MODERATOR → ADMIN
* **Propriétaire** : Un modérateur peut gérer ses propres jams
* **Audit trail** : Logs pour toutes les actions sensibles

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
