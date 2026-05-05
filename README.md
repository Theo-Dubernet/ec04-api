# EC04 - API CityLunch

## 📌 Description

API REST développée avec Symfony permettant de gérer :
- les utilisateurs (création, modification, suppression)
- les produits
- les sacs des livreurs (ajout / retrait de produits)
- consultation des données via endpoints JSON

---

# 🗂️ MCD (Modèle Conceptuel de Données)
![MCD Looping](mcd.png).

# Import dans Postman
import json présent à la racine du fichier (CityLunch Api.postman_collection.json)
- File → Import → sélection du fichier JSON

# ⚙️ Stack technique
- Symphony
- PostgresSQL

# Initialisation de l'application
- Télécharger le vendor du projet
```bash
composer install
```
- Lancer le conteneur docker
```bash
docker composer up -d
```

- Dans le fichier d'environnement, définissez le path vers la base de donnée dans le conteneur docker :

```env
DATABASE_URL="postgresql://symfony:symfony@127.0.0.1:5432/symfony?serverVersion=16&charset=utf8"
```

Lancer l'api via le serveur symfony
```
symfony server:start
```

# Migration des données avec l'ORM
php bin/console doctrine:database:create # pour créer la bdd mais normale le conteneur l'a déjà créer
php bin/console make:migration # Créer une migration
php bin/console doctrine:migrations:migrate # pour migré les données via la migration

# Exécuter les tests

Par manque de temps je n'ai pas pu passer assez de temps sur cette partie. J'ai créer un test mais j'ai pas réussis à le faire marché.
Le test se trouve dans le répertoire /test/Controller/ dans le fichier ProduitControllerTest.php

on lance les tests avec la commande suivante :

```bash
php bin/phpunit
```

# Souci rencontré

- Avec le module lexit, j'ai réussi à créer mes clef privé et publique mais je n'ai pas réussi à atteindre la route de connexion ce qui ma
empêcher de tester la protection des routes au endpoint lié au Sac des livreurs.