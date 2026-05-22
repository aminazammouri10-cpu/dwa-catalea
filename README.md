# Catalea - Boutique cosmétiques pour bébés

Projet DWA - EPHEC

## Installation

### Base de données
Le répertoire `database` contient le schéma SQL de la base de données.

Importer le fichier `database/catalea.sql` dans phpMyAdmin.

### Configuration
Copier le fichier `config.example.php` en `config.php` et remplir les identifiants :

```php
define('DB_HOST',     'localhost');
define('DB_USER',     'votre_user');
define('DB_PASSWORD', 'votre_password');
define('DB_NAME',     'votre_db');
```

### Structure
- `database/` — schéma SQL
- `public/` — pages PHP du site
- `src/` — fonctions PHP
- `templates/` — header et footer

## Sprints
- Sprint 0 : Modélisation de la base de données
- Sprint 1 : Pages produits statiques
- Sprint 2 : Pages produits dynamiques
- Sprint 3 : Filtres des produits
- Sprint 4 : Panier statique
- Sprint 5 : Ajout au panier
- Sprint 6 : Panier dynamique
- Sprint 7 : Modification du panier
- Sprint 8 : Processus de commande
