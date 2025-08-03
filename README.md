# Mangroove Website

Environnement de développement Dockerisé pour le projet Mangroove (Symfony + API Platform + Vue + PrimeVue).

---

## 🚀 Démarrage rapide

### 1. Cloner le dépôt

```bash
git clone git@github.com:G8ite/Mangroove-website.git
cd Mangroove-website
```

### 2. Initialiser les projets

> Cette étape n'est à faire qu'une seule fois par machine ou après suppression des dossiers `backend/` ou `frontend/`.

#### Backend (Symfony)

```bash
rm -rf backend/*
docker run --rm -it -v $(pwd)/backend:/app -w /app composer create-project symfony/skeleton .
```

#### Frontend (Vue + PrimeVue)

```bash
rm -rf frontend/*
docker compose run --rm frontend
```

Cela va créer un projet Vue automatiquement.

### 3. Lancer l'environnement

```bash
docker compose up --build
```

* Frontend : [http://localhost:5173](http://localhost:5173)
* Backend : [http://localhost:8000/api](http://localhost:8000/api)

---

## 🛠️ Structure du projet

```
Mangroove-website/
├── backend/       # Symfony + API Platform
├── frontend/      # Vue 3 + PrimeVue
├── docker-compose.yml
```

---

## 📖 Documentation utile

* Symfony : [https://symfony.com/doc](https://symfony.com/doc)
* API Platform : [https://api-platform.com/docs/](https://api-platform.com/docs/)
* Vue.js : [https://vuejs.org/](https://vuejs.org/)
* PrimeVue : [https://www.primefaces.org/primevue/](https://www.primefaces.org/primevue/)

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

A réfléchir