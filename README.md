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

* Frontend : [http://localhost:5173](http://localhost:5173)
* Backend : [http://localhost:8000/api](http://localhost:8000/api)

---

## 🛠️ Structure du projet

```
Mangroove-website/
├── backend/       # Symfony + API Platform (initialisé automatiquement)
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