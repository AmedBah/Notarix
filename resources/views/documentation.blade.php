<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Documentation' }} - Notarix GED</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .documentation-content {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.7;
        }
        .documentation-content h1,
        .documentation-content h2,
        .documentation-content h3 {
            color: #2c3e50;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }
        .documentation-content h1 {
            border-bottom: 3px solid #3498db;
            padding-bottom: 0.5rem;
        }
        .documentation-content h2 {
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 0.3rem;
        }
        .documentation-content code {
            background-color: #f8f9fa;
            padding: 0.2rem 0.4rem;
            border-radius: 0.25rem;
            font-size: 0.9em;
        }
        .documentation-content blockquote {
            border-left: 4px solid #3498db;
            padding-left: 1rem;
            margin: 1rem 0;
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.25rem;
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
                <a class="nav-link" href="{{ route('guide.navigation') }}">
                    <i class="fas fa-compass me-1"></i>
                    Guide Navigation
                </a>
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

    <!-- Header -->
    <div class="bg-primary text-white py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="mb-0">
                        <i class="fas fa-book me-2"></i>
                        {{ $title ?? 'Documentation' }}
                    </h1>
                </div>
                <div class="col-auto">
                    <a href="{{ route('home') }}" class="btn btn-light">
                        <i class="fas fa-home me-1"></i>
                        Accueil
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Documentation Content -->
    <div class="container my-5">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="card shadow-sm">
                    <div class="card-body p-5">
                        <div class="documentation-content">
                            {!! \Illuminate\Support\Str::markdown($content ?? 'Aucun contenu disponible.') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
