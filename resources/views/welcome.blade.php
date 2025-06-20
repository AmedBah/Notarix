<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue - Notarix GED</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .feature-card {
            transition: transform 0.3s ease;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        .feature-icon {
            font-size: 3rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .navbar-brand {
            font-weight: bold;
            color: #667eea !important;
        }
        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }
        .stats-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-folder-open me-2"></i>
                <strong>Notarix GED</strong>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="{{ route('guide.navigation') }}">
                        <i class="fas fa-compass me-1"></i>
                        Guide Navigation
                    </a>
                    <a class="nav-link" href="{{ route('documentation.acces') }}">
                        <i class="fas fa-book me-1"></i>
                        Documentation
                    </a>
                    @guest
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-1"></i>
                            Connexion
                        </a>
                        <a class="nav-link" href="{{ route('register') }}">
                            <i class="fas fa-user-plus me-1"></i>
                            Inscription
                        </a>
                    @else
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt me-1"></i>
                            Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link text-decoration-none">
                                <i class="fas fa-sign-out-alt me-1"></i>
                                Déconnexion
                            </button>
                        </form>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-3 fw-bold mb-4">
                        Bienvenue sur
                        <span class="d-block">Notarix GED</span>
                    </h1>
                    <p class="lead mb-4">
                        Votre système de Gestion Électronique de Documents professionnel. 
                        Organisez, archivez et retrouvez vos documents en toute simplicité.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        @guest
                            <a href="{{ route('login') }}" class="btn btn-light btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Se connecter
                            </a>
                            <a href="{{ route('guide.navigation') }}" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-compass me-2"></i>
                                Découvrir
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Accéder au Dashboard
                            </a>
                        @endguest
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="stats-card p-4 text-center">
                                <i class="fas fa-file-alt fa-3x mb-3"></i>
                                <h4 class="fw-bold">Documents</h4>
                                <p class="small opacity-75">Gestion complète</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-card p-4 text-center">
                                <i class="fas fa-folder fa-3x mb-3"></i>
                                <h4 class="fw-bold">Dossiers</h4>
                                <p class="small opacity-75">Organisation parfaite</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-card p-4 text-center">
                                <i class="fas fa-users fa-3x mb-3"></i>
                                <h4 class="fw-bold">Contacts</h4>
                                <p class="small opacity-75">Carnet d'adresses</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-card p-4 text-center">
                                <i class="fas fa-search fa-3x mb-3"></i>
                                <h4 class="fw-bold">Recherche</h4>
                                <p class="small opacity-75">Retrouvez tout</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">Fonctionnalités Principales</h2>
                    <p class="text-muted">Tout ce dont vous avez besoin pour gérer vos documents professionnels</p>
                </div>
            </div>

            <div class="row g-4">
                <!-- Gestion Documents -->
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-file-alt feature-icon mb-3"></i>
                            <h5 class="card-title">Gestion des Documents</h5>
                            <p class="card-text text-muted">
                                Ajoutez, modifiez et organisez vos documents avec archivage automatique 
                                et fonctionnalités de scan intégrées.
                            </p>
                            <ul class="list-unstyled small text-start">
                                <li><i class="fas fa-check text-success me-2"></i>Upload multiple formats</li>
                                <li><i class="fas fa-check text-success me-2"></i>Archivage automatique</li>
                                <li><i class="fas fa-check text-success me-2"></i>Scan intégré</li>
                                <li><i class="fas fa-check text-success me-2"></i>Gestion des versions</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Organisation Dossiers -->
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-folder-open feature-icon mb-3"></i>
                            <h5 class="card-title">Organisation par Dossiers</h5>
                            <p class="card-text text-muted">
                                Structurez vos documents par projets, clients ou affaires. 
                                Gestion du cycle de vie complet des dossiers.
                            </p>
                            <ul class="list-unstyled small text-start">
                                <li><i class="fas fa-check text-success me-2"></i>Arborescence flexible</li>
                                <li><i class="fas fa-check text-success me-2"></i>Statuts de dossiers</li>
                                <li><i class="fas fa-check text-success me-2"></i>Liaison documents</li>
                                <li><i class="fas fa-check text-success me-2"></i>Archivage automatique</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Recherche Avancée -->
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-search feature-icon mb-3"></i>
                            <h5 class="card-title">Recherche Multicritères</h5>
                            <p class="card-text text-muted">
                                Moteur de recherche puissant avec filtres avancés et 
                                historique des recherches pour retrouver vos documents.
                            </p>
                            <ul class="list-unstyled small text-start">
                                <li><i class="fas fa-check text-success me-2"></i>Recherche textuelle</li>
                                <li><i class="fas fa-check text-success me-2"></i>Filtres multiples</li>
                                <li><i class="fas fa-check text-success me-2"></i>Historique sauvegardé</li>
                                <li><i class="fas fa-check text-success me-2"></i>Recherche globale</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Gestion Contacts -->
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-users feature-icon mb-3"></i>
                            <h5 class="card-title">Gestion des Personnes</h5>
                            <p class="card-text text-muted">
                                Carnet d'adresses intégré avec gestion des contacts professionnels, 
                                clients et intervenants.
                            </p>
                            <ul class="list-unstyled small text-start">
                                <li><i class="fas fa-check text-success me-2"></i>Fiches complètes</li>
                                <li><i class="fas fa-check text-success me-2"></i>Catégorisation</li>
                                <li><i class="fas fa-check text-success me-2"></i>Liaison aux dossiers</li>
                                <li><i class="fas fa-check text-success me-2"></i>Export/Import</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Traçabilité -->
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-history feature-icon mb-3"></i>
                            <h5 class="card-title">Traçabilité Complète</h5>
                            <p class="card-text text-muted">
                                Journalisation de toutes les actions avec suivi complet 
                                des modifications et accès aux documents.
                            </p>
                            <ul class="list-unstyled small text-start">
                                <li><i class="fas fa-check text-success me-2"></i>Log des actions</li>
                                <li><i class="fas fa-check text-success me-2"></i>Suivi utilisateurs</li>
                                <li><i class="fas fa-check text-success me-2"></i>Horodatage précis</li>
                                <li><i class="fas fa-check text-success me-2"></i>Rapports d'audit</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Sécurité -->
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-shield-alt feature-icon mb-3"></i>
                            <h5 class="card-title">Sécurité Renforcée</h5>
                            <p class="card-text text-muted">
                                Gestion des permissions, authentification sécurisée et 
                                niveaux de confidentialité pour vos documents.
                            </p>
                            <ul class="list-unstyled small text-start">
                                <li><i class="fas fa-check text-success me-2"></i>Authentification</li>
                                <li><i class="fas fa-check text-success me-2"></i>Rôles utilisateurs</li>
                                <li><i class="fas fa-check text-success me-2"></i>Confidentialité</li>
                                <li><i class="fas fa-check text-success me-2"></i>Chiffrement</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-light">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h2 class="fw-bold mb-4">Prêt à commencer ?</h2>
                    <p class="lead text-muted mb-4">
                        Découvrez comment Notarix GED peut transformer votre gestion documentaire
                    </p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        @guest
                            <a href="{{ route('login') }}" class="btn btn-gradient btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Se connecter
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn btn-gradient btn-lg">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Accéder au Dashboard
                            </a>
                        @endguest
                        <a href="{{ route('guide.navigation') }}" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-compass me-2"></i>
                            Guide de Navigation
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-folder-open me-2"></i>
                        Notarix GED
                    </h5>
                    <p class="text-muted">
                        Système professionnel de Gestion Électronique de Documents 
                        conçu pour optimiser votre organisation documentaire.
                    </p>
                </div>
                <div class="col-lg-4">
                    <h6 class="fw-bold mb-3">Navigation</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('home') }}" class="text-muted text-decoration-none">Accueil</a></li>
                        <li><a href="{{ route('guide.navigation') }}" class="text-muted text-decoration-none">Guide Navigation</a></li>
                        <li><a href="{{ route('documentation.acces') }}" class="text-muted text-decoration-none">Documentation</a></li>
                        @auth
                            <li><a href="{{ route('dashboard') }}" class="text-muted text-decoration-none">Dashboard</a></li>
                        @endauth
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6 class="fw-bold mb-3">Caractéristiques</h6>
                    <ul class="list-unstyled text-muted">
                        <li><i class="fas fa-check me-2"></i>Architecture mono-entreprise</li>
                        <li><i class="fas fa-check me-2"></i>Conforme au cahier des charges</li>
                        <li><i class="fas fa-check me-2"></i>Interface moderne et intuitive</li>
                        <li><i class="fas fa-check me-2"></i>Sécurité renforcée</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted small mb-0">
                        © {{ date('Y') }} Notarix GED. Tous droits réservés.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted small mb-0">
                        Version {{ config('app.version', '1.0.0') }} - Laravel {{ app()->version() }}
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
