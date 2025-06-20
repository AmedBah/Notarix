# Documentation des Accès - GED Laravel

## Table des matières
- [Informations générales](#informations-générales)
- [Accès par défaut](#accès-par-défaut)
- [Structure des rôles](#structure-des-rôles)
- [Points d'entrée principaux](#points-dentrée-principaux)
- [Accès au système de fichiers](#accès-au-système-de-fichiers)
- [Gestion des utilisateurs](#gestion-des-utilisateurs)
- [Résolution des problèmes courants](#résolution-des-problèmes-courants)
- [Plan de Navigation - Tutoriel Complet](#-plan-de-navigation---tutoriel-complet)

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

# 📘 Plan de Navigation - Tutoriel Complet

## 🚀 Guide de Démarrage Rapide

### Étape 1: Premier Accès
1. **Accéder à l'application** : `http://localhost:8000`
2. **Se connecter** avec le compte admin : `admin@Notarix.com` / `password`
3. **Découvrir le Dashboard** : Vue d'ensemble de l'activité

### Étape 2: Navigation Principale
- **Dashboard** (`/dashboard`) : Page d'accueil après connexion
- **Documents** (`/documents`) : Gestion complète des documents
- **Dossiers** (`/dossiers`) : Organisation par projets/clients
- **Personnes** (`/personnes`) : Carnet d'adresses intégré
- **Recherche** (`/recherche`) : Moteur de recherche multicritères

## 🗂️ Modules Principaux

### 📄 Module Documents
**URL de base :** `/documents`

| Action | Route | Bouton/Lien | Description |
|--------|-------|-------------|-------------|
| **Lister** | `GET /documents` | "Mes Documents" | Affiche tous les documents |
| **Créer** | `GET /documents/create` | "➕ Nouveau Document" | Formulaire d'ajout |
| **Enregistrer** | `POST /documents` | "💾 Enregistrer" | Sauvegarde le document |
| **Consulter** | `GET /documents/{id}` | "👁️ Voir" | Détails du document |
| **Télécharger** | `GET /documents/{id}/download` | "⬇️ Télécharger" | Download du fichier |
| **Archiver Auto** | `POST /documents/{id}/archiver-auto` | "📦 Archiver" | Archivage automatique |
| **Archiver Manuel** | `POST /documents/{id}/archiver-manuel` | "📦 Archiver manuellement" | Archivage manuel |
| **Restaurer** | `POST /documents/{id}/restore` | "♻️ Restaurer" | Remettre actif |
| **Supprimer** | `DELETE /documents/{id}` | "🗑️ Supprimer" | Suppression définitive |
| **Scanner** | `GET /documents/scan/interface` | "📷 Scanner" | Interface de scan |

**🔗 Boutons Validés :**
- ✅ "Nouveau Document" → `documents.create`
- ✅ "Voir Document" → `documents.show`
- ✅ "Télécharger" → `documents.download`
- ✅ "Archiver" → `documents.archiver-auto` ou `documents.archiver-manuel`

### 📁 Module Dossiers
**URL de base :** `/dossiers`

| Action | Route | Bouton/Lien | Description |
|--------|-------|-------------|-------------|
| **Lister** | `GET /dossiers` | "Mes Dossiers" | Affiche tous les dossiers |
| **Créer** | `GET /dossiers/create` | "➕ Nouveau Dossier" | Formulaire de création |
| **Enregistrer** | `POST /dossiers` | "💾 Créer" | Sauvegarde le dossier |
| **Consulter** | `GET /dossiers/{id}` | "👁️ Ouvrir" | Contenu du dossier |
| **Modifier** | `GET /dossiers/{id}/edit` | "✏️ Modifier" | Formulaire d'édition |
| **Mettre à jour** | `PUT /dossiers/{id}` | "💾 Sauvegarder" | Mise à jour |
| **Fermer** | `POST /dossiers/{id}/fermer` | "🔒 Fermer" | Clôture du dossier |
| **Archiver** | `POST /dossiers/{id}/archiver` | "📦 Archiver" | Archivage complet |

**🔗 Boutons Validés :**
- ✅ "Nouveau Dossier" → `dossiers.create`
- ✅ "Ouvrir Dossier" → `dossiers.show`
- ✅ "Modifier" → `dossiers.edit`
- ✅ "Fermer" → `dossiers.fermer`

### 👥 Module Personnes
**URL de base :** `/personnes`

| Action | Route | Bouton/Lien | Description |
|--------|-------|-------------|-------------|
| **Lister** | `GET /personnes` | "Annuaire" | Liste des contacts |
| **Créer** | `GET /personnes/create` | "➕ Nouvelle Personne" | Formulaire d'ajout |
| **Enregistrer** | `POST /personnes` | "💾 Enregistrer" | Sauvegarde du contact |
| **Consulter** | `GET /personnes/{id}` | "👁️ Voir Profil" | Fiche complète |
| **Modifier** | `GET /personnes/{id}/edit` | "✏️ Modifier" | Formulaire d'édition |
| **Rechercher** | `GET /personnes/recherche/annuaire` | "🔍 Rechercher" | Moteur de recherche |

### 🔍 Module Recherche
**URL de base :** `/recherche`

| Action | Route | Bouton/Lien | Description |
|--------|-------|-------------|-------------|
| **Interface** | `GET /recherche` | "🔍 Recherche Avancée" | Page principale |
| **Documents** | `POST /recherche/documents` | "📄 Chercher Documents" | Recherche dans les docs |
| **Dossiers** | `POST /recherche/dossiers` | "📁 Chercher Dossiers" | Recherche dans les dossiers |
| **Personnes** | `POST /recherche/personnes` | "👥 Chercher Personnes" | Recherche contacts |
| **Globale** | `POST /recherche/globale` | "🌐 Recherche Globale" | Recherche générale |
| **Historique** | `GET /recherche/historique` | "📜 Historique" | Historique des recherches |

## 👤 Module Utilisateurs

### Profil Utilisateur
| Action | Route | Bouton/Lien | Description |
|--------|-------|-------------|-------------|
| **Mon Profil** | `GET /users/profile` | "👤 Mon Profil" | Informations personnelles |
| **Modifier Profil** | `GET /users/profile/edit` | "✏️ Modifier Profil" | Formulaire d'édition |
| **Changer Mot de Passe** | `GET /users/password/change` | "🔑 Changer Mot de Passe" | Sécurité |

### Administration (Admin uniquement)
| Action | Route | Bouton/Lien | Description |
|--------|-------|-------------|-------------|
| **Gestion Utilisateurs** | `GET /users` | "👥 Utilisateurs" | Liste complète |
| **Créer Utilisateur** | `GET /users/create` | "➕ Nouvel Utilisateur" | Formulaire création |
| **Activer/Désactiver** | `POST /users/{id}/toggle-status` | "🔄 Basculer Statut" | Gestion statut |

## 📊 Module Traçabilité
**URL de base :** `/tracabilite`

| Action | Route | Bouton/Lien | Description |
|--------|-------|-------------|-------------|
| **Journal Global** | `GET /tracabilite` | "📊 Journal d'Activité" | Vue d'ensemble |
| **Par Utilisateur** | `GET /tracabilite/utilisateur/{id}` | "👤 Activités User" | Filtrage utilisateur |
| **Par Document** | `GET /tracabilite/document/{id}` | "📄 Historique Doc" | Suivi document |
| **Export** | `GET /tracabilite/export` | "📥 Exporter" | Téléchargement données |

## 🏷️ Module Champs d'Activité
**URL de base :** `/champs-activite`

| Action | Route | Bouton/Lien | Description |
|--------|-------|-------------|-------------|
| **Lister** | `GET /champs-activite` | "🏷️ Champs d'Activité" | Gestion catégories |
| **Créer** | `GET /champs-activite/create` | "➕ Nouveau Champ" | Ajout catégorie |
| **Activer/Désactiver** | `POST /champs-activite/{id}/toggle` | "🔄 Activer/Désactiver" | Gestion statut |

## 📝 Module Actes et Courriers
**URL de base :** `/actes`

| Action | Route | Bouton/Lien | Description |
|--------|-------|-------------|-------------|
| **Lister** | `GET /actes` | "📝 Rédaction d'Actes" | Documents rédigés |
| **Créer** | `GET /actes/create` | "➕ Nouvel Acte" | Formulaire rédaction |
| **Prévisualiser** | `GET /actes/{id}/preview` | "👁️ Aperçu" | Prévisualisation |
| **Générer PDF** | `GET /actes/{id}/pdf` | "📄 Générer PDF" | Export PDF |

## 📋 Module Templates
**URL de base :** `/templates`

| Action | Route | Bouton/Lien | Description |
|--------|-------|-------------|-------------|
| **Lister** | `GET /templates` | "📋 Modèles" | Bibliothèque templates |
| **Créer** | `GET /templates/create` | "➕ Nouveau Modèle" | Création template |
| **Dupliquer** | `POST /templates/{id}/duplicate` | "📋 Dupliquer" | Copie template |

## 📞 Module Contacts
**URL de base :** `/contacts`

| Action | Route | Bouton/Lien | Description |
|--------|-------|-------------|-------------|
| **Annuaire** | `GET /contacts` | "📞 Contacts Pro" | Contacts professionnels |
| **Ajouter** | `GET /contacts/create` | "➕ Nouveau Contact" | Formulaire contact |
| **Basculer Visibilité** | `POST /contacts/{id}/toggle-visibility` | "👁️ Visibilité" | Public/Privé |
| **Export CSV** | `GET /contacts/export/csv` | "📥 Export CSV" | Téléchargement |
| **Import CSV** | `POST /contacts/import/csv` | "📤 Import CSV" | Upload fichier |

## 🎯 Actions Rapides Recommandées

### Pour commencer rapidement :
1. **📄 Ajouter un document** : `/documents/create`
2. **📁 Créer un dossier** : `/dossiers/create`
3. **👤 Ajouter une personne** : `/personnes/create`
4. **🔍 Rechercher** : `/recherche`

### Raccourcis navigation :
- **Ctrl + D** : Aller aux Documents
- **Ctrl + F** : Aller aux Dossiers  
- **Ctrl + P** : Aller aux Personnes
- **Ctrl + S** : Recherche rapide

## ⚠️ Validation des Boutons

### Boutons Principaux Testés :
- ✅ **Navigation principale** : Tous les liens du menu fonctionnent
- ✅ **Actions CRUD** : Create, Read, Update, Delete opérationnels
- ✅ **Formulaires** : Validation et soumission OK
- ✅ **Téléchargements** : Liens de download actifs
- ✅ **Redirections** : Retours après actions appropriés

### Points d'attention :
- 🔍 Vérifier que les middlewares d'authentification sont actifs
- 🔒 S'assurer que les permissions admin sont respectées
- 📱 Tester la responsivité sur mobile
- 🌐 Valider les routes dans différents navigateurs

---

*Documentation mise à jour le 20 Juin 2025 - Version Complète*
