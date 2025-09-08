# 📮 Collection Postman - Mangroove API

## 🚀 Installation rapide

### 1. Importer la collection dans Postman

**Option A : Importer via fichier**
1. Ouvrir Postman
2. Cliquer sur **"Import"** (en haut à gauche)
3. Glisser-déposer le fichier `Mangroove_API.postman_collection.json`
4. Importer aussi l'environnement `Mangroove_Local.postman_environment.json`

**Option B : Importer via URL** (si la collection est hébergée)
1. Cliquer sur **"Import"** → **"Link"**
2. Coller l'URL de la collection

### 2. Configurer l'environnement

1. Sélectionner l'environnement **"Mangroove Local"** dans le dropdown (en haut à droite)
2. Vérifier que `base_url` = `http://localhost:8000`

## 🔐 Authentification (étapes obligatoires)

### 1. Créer un utilisateur (si nécessaire)
- Utiliser la requête **"Register - Créer un compte"**
- Ou utiliser l'utilisateur existant : `user@mangroove.com` / `password123`

### 2. Se connecter
1. Ouvrir la requête **"Login - Obtenir JWT Token"** 
2. Cliquer **"Send"**
3. ✅ **Le token JWT sera automatiquement sauvegardé** pour toutes les autres requêtes !

## 📋 Utilisation des endpoints

Une fois connecté, tous les endpoints de l'API sont utilisables :

### 👥 **Users API**
- **Get All Users** - Liste des utilisateurs
- **Get User by ID** - Voir un utilisateur spécifique
- **Create User** - Créer un nouvel utilisateur
- **Update User** - Modifier un utilisateur
- **Delete User** - Supprimer un utilisateur

### 📋 **Documentation**
- **API Entrypoint** - Point d'entrée de l'API
- **OpenAPI Documentation** - Spec OpenAPI au format JSON

## ⚡ Fonctionnalités automatiques

- **🔄 Token auto-sauvegardé** : Le token JWT est automatiquement récupéré et utilisé
- **🔒 Auth héritée** : Toute la collection utilise automatiquement le token
- **🌍 Variables d'environnement** : URLs et credentials configurables

## 🛠️ Variables disponibles

| Variable | Description | Valeur par défaut |
|----------|-------------|------------------|
| `base_url` | URL de l'API | `http://localhost:8000` |
| `jwt_token` | Token JWT | *(rempli automatiquement)* |
| `test_email` | Email de test | `user@mangroove.com` |
| `test_password` | Mot de passe de test | `password123` |

## 🔧 Personnalisation

### Changer l'URL de l'API
1. Aller dans **Environments** → **Mangroove Local**
2. Modifier `base_url` (ex: `https://api.mangroove.com`)

### Utiliser d'autres credentials
1. Modifier `test_email` et `test_password` dans l'environnement
2. Ou directement dans la requête de login

## 📱 Interface web alternative

Vous pouvez aussi utiliser l'interface Swagger directement :
- **Swagger UI** : http://localhost:8000/api/docs
- **ReDoc** : http://localhost:8000/api/docs?ui=re_doc

## 🆘 Résolution de problèmes

### Token expiré
- Relancer la requête **"Login"** pour obtenir un nouveau token

### Erreur 401 Unauthorized  
- Vérifier que le token est bien sauvegardé dans les variables
- Se reconnecter avec la requête **"Login"**

### Erreur de connexion
- Vérifier que Docker est lancé : `docker compose up`
- Vérifier l'URL : `http://localhost:8000` (pas de `/` à la fin)
