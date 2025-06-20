# Documentation des Accès - GED Laravel

## Table des matières
- [Informations générales](#informations-générales)
- [Accès par défaut](#accès-par-défaut)
- [Structure des rôles](#structure-des-rôles)
- [Points d'entrée principaux](#points-dentrée-principaux)
- [Accès au système de fichiers](#accès-au-système-de-fichiers)
- [Gestion des utilisateurs](#gestion-des-utilisateurs)
- [Résolution des problèmes courants](#résolution-des-problèmes-courants)

## Informations générales

**Nom du Projet:** GED Laravel (Gestion Électronique de Documents)  
**Version:** 1.0  
**Date de documentation:** 20 juin 2025  
**Environnement de développement:** Laravel 8.x, PHP 7.3+/8.0+, MySQL

## Accès par défaut

### Compte administrateur

Pour accéder à l'application en tant qu'administrateur:

- **Email:** admin@Notarix.com
- **Mot de passe:** password
- **Rôle:** Administrateur

### URL d'accès

- **Développement local:** http://localhost:8000
- **Serveur de développement (via PHP artisan):** php artisan serve
- **Environnement de production:** À configurer selon votre hébergement

## Structure des rôles

L'application comprend trois niveaux d'accès:

1. **Admin** (`est_admin = 1`)
   - Accès complet à toutes les fonctionnalités
   - Création/modification des utilisateurs
   - Gestion des entreprises
   - Configuration du système
   - Gestion des sections et des dossiers

2. **Utilisateur** (`est_admin = 0`)
   - Consultation des documents associés
   - Téléchargement et manipulation des documents selon les droits attribués
   - Gestion de son profil

3. **Visiteur** (accès limité via demandes)
   - Consultation des documents partagés uniquement
   - Aucun droit de modification

## Points d'entrée principaux

### Routes principales

| Route | Méthode | Description | Accès requis |
|-------|---------|-------------|-------------|
| `/` | GET | Page d'accueil | Public |
| `/login` | GET/POST | Authentification | Public |
| `/register` | GET/POST | Enregistrement | Public |
| `/sections` | GET | Liste des sections | Authentifié |
| `/sections/{section_id}` | GET | Afficher une section spécifique | Authentifié |
| `/sections/{section_id}/{dossier_id}` | GET | Afficher un dossier spécifique | Authentifié |
| `/documents/{employee_id}` | GET | Documents d'un employé | Admin/Propriétaire |
| `/demandes` | GET | Liste des demandes | Authentifié |
| `/compte` | GET | Gestion du compte | Authentifié |
| `/liste` | GET | Liste des utilisateurs | Admin |

### Opérations de gestion

| Route | Méthode | Description | Accès requis |
|-------|---------|-------------|-------------|
| `/creerSection` | POST | Créer une nouvelle section | Admin |
| `/creerDossier/{section_id}` | POST | Créer un nouveau dossier | Admin |
| `/uploader/{section_id}` | POST | Téléverser un document dans une section | Authentifié |
| `/uploader/{section_id}/{dossier_id}` | POST | Téléverser un document dans un dossier | Authentifié |
| `/download/{id_doc}` | GET | Télécharger un document | Authentifié + Droits |
| `/delete/{id_doc}` | GET | Supprimer un document | Admin/Propriétaire |
| `/deleteFolder/{id_folder}` | GET | Supprimer un dossier | Admin |
| `/deleteSection/{section_id}` | GET | Supprimer une section | Admin |

## Accès au système de fichiers

Les documents sont stockés dans le système de fichiers avec la structure suivante:

- **Chemin de base:** `storage/app/public/`
  - **Documents:** `documents/{section_id}/{document_name}`
  - **Documents dans dossiers:** `documents/{section_id}/{dossier_id}/{document_name}`
  - **Images utilisateurs:** `users/{user_id}/profile.{extension}`
  - **Images par défaut:** `default/user.png`

Pour que les fichiers soient accessibles publiquement, assurez-vous d'avoir exécuté:
```bash
php artisan storage:link
```

## Gestion des utilisateurs

### Création d'un utilisateur

Pour créer un utilisateur via Tinker:

```php
// Vérifier si l'entreprise existe
$enterprise = \App\Models\Entreprise::find(1);

// Créer l'utilisateur
$user = new \App\Models\User();
$user->nom = 'Nom Utilisateur';
$user->telephone = '0123456789';
$user->email = 'utilisateur@example.com';
$user->est_admin = 0; // 0 pour utilisateur standard, 1 pour admin
$user->password = \Hash::make('mot_de_passe');
$user->entreprise_id = $enterprise->id;
$user->save();
```

### Modification des droits d'accès

Les droits d'accès sont principalement gérés via le champ `est_admin` dans la table `users`. Des contrôles d'accès supplémentaires sont implémentés au niveau des contrôleurs.

## Résolution des problèmes courants

### Erreur d'accès refusé aux fichiers

- Vérifiez que les permissions sont correctement définies sur le dossier `storage`
- Assurez-vous que le lien symbolique est créé avec `php artisan storage:link`

### Erreur de champ manquant dans la base de données

Si vous rencontrez des erreurs liées aux champs manquants ou sans valeur par défaut:

1. Modifiez la migration correspondante pour rendre le champ nullable ou ajouter une valeur par défaut
2. Ou modifiez le modèle pour définir une valeur par défaut lors de la création (comme pour `logo_path`)

### Problème de démarrage du serveur

- Utiliser le serveur de développement PHP intégré:
  ```bash
  php -S localhost:8000 -t public
  ```
- Vérifier que le fichier `server.php` existe à la racine du projet
- Vérifier que le répertoire `public` est bien défini comme point d'entrée

---

*Documentation générée le 20 Juin 2025*
