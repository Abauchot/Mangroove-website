# 📬 Guide d'utilisation Postman - API Mangroove

Ce guide vous explique comment utiliser la collection Postman mise à jour pour tester l'API Mangroove avec les fonctionnalités de gestion des utilisateurs et des jams (game jams).

## 🚀 Configuration initiale

### 1. Importer les fichiers
1. **Collection** : Importez `Mangroove_API.postman_collection.json`
2. **Environnement** : Importez `Mangroove_Local.postman_environment.json`

### 2. Configurer l'environnement
1. Sélectionnez l'environnement **"Mangroove Local"**
2. Vérifiez que l'URL de base pointe vers `http://localhost:8000`

### 3. Démarrer l'API
Assurez-vous que votre API Symfony est démarrée :
```bash
docker-compose up -d
```

## 🔐 Authentification

### Première connexion
1. **Créer un compte** : Utilisez "Register - Créer un compte"
   ```json
   {
     "email": "votre@email.com",
     "password": "motdepasse123"
   }
   ```

2. **Se connecter** : Utilisez "Login - Obtenir JWT Token"
   ```json
   {
     "email": "votre@email.com", 
     "password": "motdepasse123"
   }
   ```

✅ **Le token JWT sera automatiquement sauvegardé** et utilisé pour toutes les autres requêtes.

## 👥 API Users

### Opérations disponibles
- **GET** `/api/users` - Liste tous les utilisateurs
- **GET** `/api/users/{id}` - Récupère un utilisateur par ID
- **POST** `/api/users` - Crée un nouvel utilisateur
- **PATCH** `/api/users/{id}` - Met à jour un utilisateur
- **DELETE** `/api/users/{id}` - Supprime un utilisateur

### Points importants
- ✅ Les IDs sont des **UUIDs** (ex: `a1b2c3d4-e5f6-7890-abcd-ef1234567890`)
- ✅ L'ID utilisateur est **automatiquement sauvegardé** après création
- 🔒 Toutes les opérations nécessitent une **authentification JWT**

## 🎯 API Jams

### Opérations CRUD
- **GET** `/api/jams` - Liste toutes les jams
- **GET** `/api/jams/{id}` - Récupère une jam par ID UUID
- **POST** `/api/jams` - Crée une nouvelle jam
- **PATCH** `/api/jams/{id}` - Met à jour une jam
- **DELETE** `/api/jams/{id}` - Supprime une jam

### Opérations de filtrage
- **GET** `/api/jams?status=published` - Filtre par statut
- **GET** `/api/jams?slug=ma-jam` - Recherche par slug

### Structure d'une Jam
```json
{
  "title": "Nom de la Game Jam",
  "slug": "nom-game-jam-2025",
  "startsAt": "2025-12-01T09:00:00+00:00",
  "endsAt": "2025-12-03T23:59:59+00:00",
  "votingEndAt": "2025-12-10T23:59:59+00:00",
  "themeSubmissionEndAt": "2025-11-25T23:59:59+00:00",
  "themeVotingEndAt": "2025-11-30T23:59:59+00:00",
  "theme": "Innovation",
  "status": "draft"
}
```

### Statuts disponibles
- `draft` - Brouillon
- `published` - Publiée  
- `active` - En cours
- `completed` - Terminée
- `cancelled` - Annulée

### Points importants
- ✅ Les IDs sont des **UUIDs**
- ✅ L'ID de jam est **automatiquement sauvegardé** après création
- ✅ Le **slug doit être unique**
- 📅 Les dates sont au **format ISO 8601**
- 🔒 Toutes les opérations nécessitent une **authentification JWT**

## 🔧 Variables automatiques

La collection gère automatiquement ces variables :

| Variable | Description | Auto-remplie |
|----------|-------------|--------------|
| `jwt_token` | Token d'authentification | ✅ Après login |
| `user_id` | ID du dernier utilisateur créé | ✅ Après création |
| `jam_id` | ID de la dernière jam créée | ✅ Après création |
| `base_url` | URL de l'API | ❌ Manuel |

## 🎯 Workflow recommandé

### Pour tester les Users
1. **Login** → Token sauvegardé automatiquement
2. **Create User** → ID utilisateur sauvegardé
3. **Get User by ID** → Utilise l'ID sauvegardé
4. **Update User** → Modifie l'utilisateur
5. **Get All Users** → Vérifier la liste

### Pour tester les Jams
1. **Login** → Token sauvegardé automatiquement
2. **Create Jam** → ID jam sauvegardé
3. **Get Jam by ID** → Utilise l'ID sauvegardé
4. **Get Jams by Status** → Filtre par statut
5. **Update Jam** → Change le statut en "published"
6. **Get All Jams** → Vérifier la liste

## 🐛 Dépannage

### Problèmes courants

**❌ 401 Unauthorized**
- Vérifiez que vous êtes connecté (Login)
- Le token JWT expire après un certain temps

**❌ 404 Not Found**  
- Vérifiez que l'ID UUID est correct
- Utilisez les variables {{user_id}} ou {{jam_id}}

**❌ 422 Validation Error**
- Vérifiez le format des données JSON
- Respectez les contraintes (email unique, slug unique, etc.)

**❌ 500 Internal Server Error**
- Vérifiez que la base de données est accessible
- Consultez les logs Symfony

### Vérifications
1. **API en ligne** : `GET /api` doit retourner la documentation
2. **Base de données** : Vérifiez les migrations Doctrine
3. **JWT configuré** : Vérifiez les clés dans `config/jwt/`

## 📚 Ressources supplémentaires

- **Documentation API** : `GET /api/docs.json`
- **Interface Swagger** : Disponible via l'API Platform
- **Logs Symfony** : Consultez `var/log/dev.log`

---

💡 **Conseil** : Utilisez les scripts de test automatiques intégrés pour une expérience optimale !
