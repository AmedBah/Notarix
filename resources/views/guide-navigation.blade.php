<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guide de Navigation - Notarix GED</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .guide-card {
            transition: transform 0.2s ease-in-out;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .guide-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }
        .step-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        .feature-icon {
            font-size: 2.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .navbar-brand {
            font-weight: bold;
            color: #667eea !important;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-folder-open me-2"></i>
                Notarix GED
            </a>
            <div class="navbar-nav ms-auto">
                @guest
                    <a class="nav-link" href="{{ route('login') }}">
                        <i class="fas fa-sign-in-alt me-1"></i>
                        Connexion
                    </a>
                @else
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt me-1"></i>
                        Dashboard
                    </a>
                @endguest
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="bg-primary text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">
                        <i class="fas fa-compass me-3"></i>
                        Guide de Navigation
                    </h1>
                    <p class="lead mb-4">
                        Découvrez comment utiliser efficacement votre système de Gestion Électronique de Documents (GED) Notarix
                    </p>
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-light btn-lg">
                            <i class="fas fa-rocket me-2"></i>
                            Commencer maintenant
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Aller au Dashboard
                        </a>
                    @endguest
                </div>
                <div class="col-lg-4 text-center">
                    <i class="fas fa-map-marked-alt" style="font-size: 8rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Guide Steps -->
    <div class="container my-5">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="fw-bold">Comment démarrer avec Notarix GED</h2>
                <p class="text-muted">Suivez ces étapes simples pour maîtriser votre GED</p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Étape 1: Connexion -->
            <div class="col-lg-4">
                <div class="card guide-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="step-number mx-auto mb-3">1</div>
                        <i class="fas fa-sign-in-alt feature-icon mb-3"></i>
                        <h5 class="card-title">Connexion</h5>
                        <p class="card-text text-muted">
                            Connectez-vous avec vos identifiants pour accéder à votre espace personnel sécurisé.
                        </p>
                        @guest
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-right me-1"></i>
                                Se connecter
                            </a>
                        @else
                            <span class="badge bg-success">
                                <i class="fas fa-check me-1"></i>
                                Connecté
                            </span>
                        @endguest
                    </div>
                </div>
            </div>

            <!-- Étape 2: Dashboard -->
            <div class="col-lg-4">
                <div class="card guide-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="step-number mx-auto mb-3">2</div>
                        <i class="fas fa-tachometer-alt feature-icon mb-3"></i>
                        <h5 class="card-title">Dashboard</h5>
                        <p class="card-text text-muted">
                            Visualisez l'aperçu de vos documents, dossiers et activités récentes en un coup d'œil.
                        </p>
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                <i class="fas fa-eye me-1"></i>
                                Voir Dashboard
                            </a>
                        @else
                            <button class="btn btn-secondary" disabled>
                                <i class="fas fa-lock me-1"></i>
                                Connexion requise
                            </button>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Étape 3: Gestion Documents -->
            <div class="col-lg-4">
                <div class="card guide-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="step-number mx-auto mb-3">3</div>
                        <i class="fas fa-file-alt feature-icon mb-3"></i>
                        <h5 class="card-title">Gestion Documents</h5>
                        <p class="card-text text-muted">
                            Ajoutez, organisez et gérez vos documents avec les fonctionnalités d'archivage automatique.
                        </p>
                        @auth
                            <a href="{{ route('documents.index') }}" class="btn btn-primary">
                                <i class="fas fa-folder-open me-1"></i>
                                Mes Documents
                            </a>
                        @else
                            <button class="btn btn-secondary" disabled>
                                <i class="fas fa-lock me-1"></i>
                                Connexion requise
                            </button>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fonctionnalités Principales -->
    <div class="bg-light py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">Fonctionnalités Principales</h2>
                    <p class="text-muted">Explorez toutes les possibilités de votre GED</p>
                </div>
            </div>

            <div class="row g-4">
                <!-- Documents -->
                <div class="col-md-6 col-lg-3">
                    <div class="card guide-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-file-alt feature-icon mb-3"></i>
                            <h6 class="card-title">Documents</h6>
                            <p class="card-text small text-muted">
                                Ajout, modification, archivage automatique, scan
                            </p>
                            @auth
                                <a href="{{ route('documents.index') }}" class="btn btn-sm btn-outline-primary">Accéder</a>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Dossiers -->
                <div class="col-md-6 col-lg-3">
                    <div class="card guide-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-folder feature-icon mb-3"></i>
                            <h6 class="card-title">Dossiers</h6>
                            <p class="card-text small text-muted">
                                Organisation par projets, clients, affaires
                            </p>
                            @auth
                                <a href="{{ route('dossiers.index') }}" class="btn btn-sm btn-outline-primary">Accéder</a>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Personnes -->
                <div class="col-md-6 col-lg-3">
                    <div class="card guide-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-users feature-icon mb-3"></i>
                            <h6 class="card-title">Personnes</h6>
                            <p class="card-text small text-muted">
                                Gestion contacts, clients, intervenants
                            </p>
                            @auth
                                <a href="{{ route('personnes.index') }}" class="btn btn-sm btn-outline-primary">Accéder</a>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Recherche -->
                <div class="col-md-6 col-lg-3">
                    <div class="card guide-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-search feature-icon mb-3"></i>
                            <h6 class="card-title">Recherche</h6>
                            <p class="card-text small text-muted">
                                Recherche multicritères, historique
                            </p>
                            @auth
                                <a href="{{ route('recherche.index') }}" class="btn btn-sm btn-outline-primary">Accéder</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Rapides -->
    @auth
    <div class="container my-5">
        <div class="row">
            <div class="col-12 text-center mb-4">
                <h3 class="fw-bold">Actions Rapides</h3>
                <p class="text-muted">Commencez immédiatement avec ces actions courantes</p>
            </div>
        </div>

        <div class="row justify-content-center g-3">
            <div class="col-md-6 col-lg-3">
                <a href="{{ route('documents.create') }}" class="btn btn-success w-100 p-3">
                    <i class="fas fa-plus-circle me-2"></i>
                    Ajouter un Document
                </a>
            </div>
            <div class="col-md-6 col-lg-3">
                <a href="{{ route('dossiers.create') }}" class="btn btn-info w-100 p-3">
                    <i class="fas fa-folder-plus me-2"></i>
                    Créer un Dossier
                </a>
            </div>
            <div class="col-md-6 col-lg-3">
                <a href="{{ route('personnes.create') }}" class="btn btn-warning w-100 p-3">
                    <i class="fas fa-user-plus me-2"></i>
                    Ajouter une Personne
                </a>
            </div>
            <div class="col-md-6 col-lg-3">
                <a href="{{ route('recherche.index') }}" class="btn btn-primary w-100 p-3">
                    <i class="fas fa-search me-2"></i>
                    Rechercher
                </a>
            </div>
        </div>
    </div>
    @endauth

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <div class="row">
                <div class="col-12">
                    <p class="mb-2">
                        <i class="fas fa-folder-open me-2"></i>
                        <strong>Notarix GED</strong> - Système de Gestion Électronique de Documents
                    </p>
                    <p class="text-muted small mb-0">
                        Architecture mono-entreprise conforme au cahier des charges
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
