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

## 📖 Documentation utile

* Symfony : [https://symfony.com/doc](https://symfony.com/doc)
* API Platform : [https://api-platform.com/docs/](https://api-platform.com/docs/)
* Vue.js : [https://vuejs.org/](https://vuejs.org/)
* PrimeVue : [https://www.primefaces.org/primevue/](https://www.primefaces.org/primevue/)

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