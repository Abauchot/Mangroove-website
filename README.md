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

1. Ouvrir un shell dans le container backend :

```bash
docker compose exec backend bash
```

2. Utiliser les commandes Symfony :

* Générer une entité :

  ```bash
  php bin/console make:entity
  ```
* Générer une migration Doctrine :

  ```bash
  php bin/console make:migration
  ```
* Appliquer les migrations :

  ```bash
  php bin/console doctrine:migrations:migrate
  ```
* Autres commandes utiles :

  ```bash
  php bin/console make:controller
  php bin/console make:user
  php bin/console make:form
  ```

> ℹ️ Toutes les modifications faites dans le container seront bien enregistrées dans `backend/` et visibles dans Git.

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