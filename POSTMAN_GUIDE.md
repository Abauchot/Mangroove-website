# 📬 Guide d'utilisation Postman - API Mangroove

Ce guide vous explique comment utiliser la collection Postman mise à jour pour tester l'API Mangroove avec les fonctionnalités de gestion des utilisateurs, des jams (game jams) et des soumissions de jeux (GameEntry).

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

## 🔄 Cycle de vie des Jams

Le cycle de vie d'une jam suit cette séquence :

```
DRAFT → PUBLISHED → RUNNING → CLOSED → ARCHIVED
```

### 1. Créer une Jam (statut: draft)
```json
{
  "title": "Ma Super Game Jam 2025",
  "slug": "ma-super-game-jam-2025",
  "startsAt": "2025-12-01T09:00:00+00:00",
  "endsAt": "2025-12-03T23:59:59+00:00",
  "votingEndAt": "2025-12-10T23:59:59+00:00",
  "themeSubmissionEndAt": "2025-11-25T23:59:59+00:00",
  "themeVotingEndAt": "2025-11-30T23:59:59+00:00",
  "theme": "Innovation"
}
```

### 2. Publier la Jam (draft → published)
```http
POST /api/jams/{{jam_id}}/publish
```

### 3. Démarrer la Jam (published → running)
```http
POST /api/jams/{{jam_id}}/start
```
⚡ **Les soumissions de jeux sont maintenant ouvertes !**

### 4. Fermer la Jam (running → closed)
```http
POST /api/jams/{{jam_id}}/close
```
⚡ **Plus de soumissions, les votes commencent !**

### 5. Archiver la Jam (closed → archived)
```http
POST /api/jams/{{jam_id}}/archive
```
⚡ **Jam terminée définitivement**

## 🎮 GameEntry (Soumissions de jeux)

### Créer une soumission
**Prérequis :** La jam doit être en statut `running`

```json
{
  "title": "Mon Super Jeu",
  "description": "Un jeu incroyable créé pour cette jam !",
  "jam": "/api/jams/{{jam_id}}",
  "teamName": "Team Awesome",
  "playUrl": "https://mon-jeu.itch.io",
  "mediaUrls": [
    "https://example.com/screenshot1.png",
    "https://example.com/gameplay.gif"
  ],
  "tags": ["puzzle", "2D", "retro"],
  "isPublic": true
}
```

### Opérations disponibles
- **GET** `/api/game_entries?jam={{jam_id}}` - Lister les soumissions d'une jam
- **POST** `/api/game_entries` - Créer une soumission
- **PUT** `/api/game_entries/{{game_entry_id}}` - Modifier sa soumission
- **DELETE** `/api/game_entries/{{game_entry_id}}` - Supprimer sa soumission

### Règles métier
✅ **Autorisé :** Modifier/supprimer sa soumission quand jam = `running`
❌ **Interdit :** Modifier/supprimer quand jam = `closed` ou `archived`
🔒 **Sécurité :** Seul l'auteur peut modifier sa soumission

## 🛠️ Workflow complet

### Scénario typique
1. **S'inscrire/Se connecter** → Obtenir le token JWT
2. **Créer une jam** → Obtenir `{{jam_id}}`
3. **Publier la jam** → `POST /api/jams/{{jam_id}}/publish`
4. **Démarrer la jam** → `POST /api/jams/{{jam_id}}/start`
5. **Soumettre un jeu** → `POST /api/game_entries`
6. **📝 Commenter un jeu** → `POST /api/comments`
7. **📖 Lire les commentaires** → `GET /api/comments?gameEntry={{game_entry_id}}`
8. **✏️ Modifier son commentaire** → `PATCH /api/comments/{{comment_id}}`
9. **Modifier sa soumission** → `PUT /api/game_entries/{{game_entry_id}}`
10. **Fermer la jam** → `POST /api/jams/{{jam_id}}/close`
11. **Voir les soumissions** → `GET /api/game_entries?jam={{jam_id}}`
12. **Archiver la jam** → `POST /api/jams/{{jam_id}}/archive`

## 💬 API Comments

### Opérations disponibles
- **GET** `/api/comments` - Liste tous les commentaires accessibles
- **GET** `/api/comments?gameEntry={{game_entry_id}}` - Commentaires d'un jeu
- **GET** `/api/comments/{id}` - Récupère un commentaire par ID
- **POST** `/api/comments` - Crée un nouveau commentaire
- **PATCH** `/api/comments/{id}` - Met à jour son commentaire
- **DELETE** `/api/comments/{id}` - Supprime un commentaire

### Permissions
| **Action** | **USER** | **MODERATOR** | **ADMIN** |
|------------|----------|---------------|-----------|
| Voir commentaires | ✅ | ✅ | ✅ |
| Créer commentaire | ✅ | ✅ | ✅ |
| Modifier son commentaire | ✅ (15 min) | ✅ | ✅ |
| Supprimer son commentaire | ✅ | ✅ | ✅ |
| Supprimer commentaire d'autrui | ❌ | ✅ (ses jams) | ✅ (tous) |
| Modérer commentaires | ❌ | ✅ | ✅ |

### Exemple de création
```json
{
  "content": "Super jeu ! J'adore le gameplay. 🎮",
  "gameEntry": "/api/game_entries/{{game_entry_id}}"
}
```

### Règles métier
✅ **Autorisé :** Commenter tout jeu public
❌ **Interdit :** Modifier après 15 minutes (sauf admin/modo)
🔒 **Sécurité :** L'auteur est automatiquement défini

## 🛠️ Workflow complet

**❌ 401 Unauthorized**
- Vérifiez que vous êtes connecté (Login)
- Le token JWT expire après un certain temps

**❌ 404 Not Found**  
- Vérifiez que l'ID UUID est correct
- Utilisez les variables {{user_id}}, {{jam_id}} ou {{game_entry_id}}

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
