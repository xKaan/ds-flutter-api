# API de connexion – DS Flutter

API REST en PHP qui authentifie les utilisateurs de l'application Flutter de connexion. Elle reçoit un email et un mot de passe, les vérifie en base et renvoie une réponse JSON avec un token d'authentification. Ce token permet ensuite de récupérer le profil et de se déconnecter.

## Stack

- PHP 8.3 + Apache
- MariaDB 11
- Docker / Docker Compose

## Structure

```
.
├── Dockerfile              
├── docker-compose.yml      
├── php.ini                 
├── .env.example            
├── sql/
│   └── init.sql            
└── src/
    ├── index.php
    ├── login.php
    ├── me.php
    ├── logout.php
    └── config/
        ├── http.php
        ├── db.php
        ├── auth.php
        └── .htaccess
```

## Installation

Prérequis : Docker / Docker Desktop

1. Cloner le dépôt puis se placer à la racine.

2. Créer le fichier `.env` à partir du modèle (et changer les mots de passe si besoin) :

   ```bash
   cp .env.example .env
   ```

3. Lancer les conteneurs :

   ```bash
   docker compose up -d --build
   ```

4. Vérifier que l'API répond sur http://localhost:8080 :

   ```json
   {"status":200,"message":"API en ligne"}
   ```

Au premier démarrage, MariaDB exécute `sql/init.sql` : les tables `users` et `tokens` ainsi que les utilisateurs de test sont créés automatiquement.

Ce script n'est exécuté que si la base est vide. Pour repartir de zéro (par exemple après une modification de `init.sql`) :

```bash
docker compose down -v
docker compose up -d --build
```

## Utilisateurs de test

| Email | Mot de passe | Rôle |
|---|---|---|
| jean@example.com | Jean2026! | 2 |
| marie@example.com | Marie2026! | 1 |

## Endpoints

| Méthode | Endpoint | Rôle | Token requis |
|---|---|---|---|
| `POST` | `/login.php` | Connexion, renvoie le profil et un token | Non |
| `GET` | `/me.php` | Renvoie le profil lié au token | Oui |
| `POST` | `/logout.php` | Supprime le token | Oui |

Le token s'envoie dans l'en-tête `Authorization: Bearer <token>`. Il est valable 7 jours.

### `POST /login.php`

Paramètres (JSON ou formulaire) : `email`, `password`.

```bash
curl -X POST http://localhost:8080/login.php \
  -H "Content-Type: application/json" \
  -d '{"email":"jean@example.com","password":"Jean2026!"}'
```

Succès (200) :

```json
{
  "status": 200,
  "message": "Success",
  "id": 1,
  "firstName": "Jean",
  "lastName": "Dupont",
  "email": "jean@example.com",
  "role": 2,
  "token": "3f9c2a...(64 caractères hexadécimaux)"
}
```

Erreur (400) : identifiants incorrects ou champs invalides.

```json
{
  "status": 400,
  "message": "Error"
}
```

### `GET /me.php`

```bash
curl http://localhost:8080/me.php -H "Authorization: Bearer <token>"
```

Succès (200) : même format que le login, sans le token.

Erreur (401) : token absent, invalide ou expiré.

### `POST /logout.php`

```bash
curl -X POST http://localhost:8080/logout.php -H "Authorization: Bearer <token>"
```

Succès (200) : le token est supprimé et ne peut plus être utilisé.

Erreur (401) : token absent ou mal formé.

### Codes de réponse

| Code | Signification |
|---|---|
| 200 | Succès |
| 400 | Identifiants incorrects ou données invalides |
| 401 | Token absent, invalide ou expiré |
| 405 | Mauvaise méthode HTTP |
| 500 | Erreur serveur (base de données) |

## Sécurité

- **Hachage des mots de passe** : bcrypt. Aucun mot de passe n'est stocké en clair, la vérification se fait avec `password_verify`.
- **Tokens** : générés avec `random_bytes` (256 bits). Seul leur hash SHA-256 est stocké en base, avec une date d'expiration. Une fuite de la base ne permet donc pas de réutiliser les tokens.
- **Injections SQL** : requêtes préparées PDO, avec la préparation faite par MariaDB.
- **Validation des entrées** : méthode POST uniquement, champs obligatoires de type texte, format d'email vérifié, mot de passe limité à 72 octets (limite de bcrypt).
- **Erreurs masquées** : `display_errors` est désactivé, les erreurs partent dans les logs Docker et le client reçoit seulement un JSON générique.
- **Secrets** : les identifiants de la base sont dans `.env`, exclu du dépôt par `.gitignore`.
- **Fichiers de configuration** : le dossier `src/config` n'est pas accessible depuis le web (HTTP 403).
