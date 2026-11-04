# API de connexion – DS Flutter

API REST en PHP qui authentifie les utilisateurs de l'application Flutter de connexion. Elle reçoit un email et un mot de passe, les vérifie en base et renvoie une réponse JSON.

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
    └── config/
        ├── db.php          
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

Au premier démarrage, MariaDB exécute `sql/init.sql` : la table et les utilisateurs de test sont créés automatiquement.

## Utilisateurs de test

| Email | Mot de passe | Rôle |
|---|---|---|
| jean@example.com | Jean2026! | 2 |
| marie@example.com | Marie2026! | 1 |

## Endpoint

### `POST /login.php`

## Sécurité

- **Hachage des mots de passe** : bcrypt. Aucun mot de passe n'est stocké en clair, la vérification se fait avec `password_verify`.
- **Injections SQL** : requêtes préparées PDO, avec la préparation faite par MariaDB.
- **Validation des entrées** : méthode POST uniquement, champs obligatoires de type texte, format d'email vérifié, mot de passe limité à 72 octets (limite de bcrypt).
- **Erreurs masquées** : `display_errors` est désactivé, les erreurs partent dans les logs Docker et le client reçoit seulement un JSON générique.
- **Secrets** : les identifiants de la base sont dans `.env`, exclu du dépôt par `.gitignore`.
- **Fichiers de configuration** : le dossier `src/config` n'est pas accessible depuis le web (HTTP 403).
