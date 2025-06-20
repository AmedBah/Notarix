<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/pages.css">
  <script src="/js/jquery-3.6.0.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
  <script src="/js/modal.js"></script>

  <title>Notarix GED - Architecture Centralisée</title>
</head>

<body>
  <!-- Navigation principale pour architecture mono-entreprise -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
    <div class="container-fluid">
      <!-- Logo et nom de l'application -->
      <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
        <i class="fas fa-building mr-2"></i>
        <span class="font-weight-bold">Notarix GED</span>
        <small class="ml-2 text-light opacity-75">Architecture Centralisée</small>
      </a>

      <!-- Bouton de menu mobile -->
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Menu principal -->
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto">
          <!-- Dashboard -->
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
               href="{{ route('dashboard') }}">
              <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
          </li>

          <!-- Gestion des clients -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle {{ request()->routeIs('clients.*') ? 'active' : '' }}" 
               href="#" id="clientsDropdown" role="button" data-toggle="dropdown">
              <i class="fas fa-users"></i> Clients
            </a>
            <div class="dropdown-menu">
              <a class="dropdown-item" href="{{ route('clients.index') }}">
                <i class="fas fa-list"></i> Liste des Clients
              </a>
              <a class="dropdown-item" href="{{ route('clients.create') }}">
                <i class="fas fa-user-plus"></i> Nouveau Client
              </a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="{{ route('clients.search') }}">
                <i class="fas fa-search"></i> Rechercher
              </a>
            </div>
          </li>

          <!-- Gestion documentaire -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="documentsDropdown" role="button" data-toggle="dropdown">
              <i class="fas fa-folder-open"></i> Documents
            </a>
            <div class="dropdown-menu">
              <a class="dropdown-item" href="/documents">
                <i class="fas fa-file-alt"></i> Tous les Documents
              </a>
              <a class="dropdown-item" href="/dossiers">
                <i class="fas fa-folder"></i> Dossiers
              </a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="/uploader">
                <i class="fas fa-upload"></i> Téléverser
              </a>
              <a class="dropdown-item" href="/recherche-avancee">
                <i class="fas fa-search-plus"></i> Recherche Avancée
              </a>
            </div>
          </li>

          <!-- Champs d'activité -->
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('champs-activite.*') ? 'active' : '' }}" 
               href="{{ route('champs-activite.index') }}">
              <i class="fas fa-briefcase"></i> Champs d'Activité
            </a>
          </li>

          <!-- Templates -->
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('templates.*') ? 'active' : '' }}" 
               href="{{ route('templates.index') }}">
              <i class="fas fa-file-contract"></i> Templates
            </a>
          </li>

          <!-- Annuaire de contacts -->
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('contacts.*') ? 'active' : '' }}" 
               href="{{ route('contacts.index') }}">
              <i class="fas fa-address-book"></i> Annuaire
            </a>
          </li>

          <!-- Utilisateurs (Admin uniquement) -->
          @if(Auth::user()->est_admin || (Auth::user()->role ?? 'utilisateur') === 'admin')
          <li class="nav-item">
            <a class="nav-link" href="/users">
              <i class="fas fa-user-cog"></i> Utilisateurs
            </a>
          </li>
          @endif
        </ul>

        <!-- Menu utilisateur -->
        <ul class="navbar-nav ml-auto">
          <!-- Notifications -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button" data-toggle="dropdown">
              <i class="fas fa-bell"></i>
              <span class="badge badge-danger badge-sm">3</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
              <h6 class="dropdown-header">Notifications</h6>
              <a class="dropdown-item" href="#">
                <i class="fas fa-file-upload text-info"></i>
                <small>Nouveau document téléversé</small>
              </a>
              <a class="dropdown-item" href="#">
                <i class="fas fa-user-plus text-success"></i>
                <small>Nouveau client créé</small>
              </a>
              <a class="dropdown-item" href="#">
                <i class="fas fa-calendar text-warning"></i>
                <small>Rappel: Échéance contrat</small>
              </a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item text-center" href="/notifications">Voir toutes</a>
            </div>
          </li>

          <!-- Recherche rapide -->
          <li class="nav-item">
            <form class="form-inline ml-2" method="GET" action="/recherche">
              <div class="input-group">
                <input class="form-control form-control-sm" type="search" 
                       placeholder="Recherche rapide..." name="q">
                <div class="input-group-append">
                  <button class="btn btn-outline-light btn-sm" type="submit">
                    <i class="fas fa-search"></i>
                  </button>
                </div>
              </div>
            </form>
          </li>

          <!-- Profil utilisateur -->
          <li class="nav-item dropdown ml-3">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" 
               id="userDropdown" role="button" data-toggle="dropdown">
              <img src="{{ Auth::user()->logo_path ?? '/images/default-avatar.png' }}" 
                   alt="Avatar" class="rounded-circle mr-2" width="32" height="32"
                   onerror="this.src='/images/default-avatar.png'">
              <span>{{ Auth::user()->prenom ?? 'Admin' }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
              <div class="dropdown-header">
                <strong>{{ Auth::user()->nom ?? 'Administrateur' }} {{ Auth::user()->prenom ?? 'Principal' }}</strong>
                <small class="d-block text-muted">{{ Auth::user()->email }}</small>
                <small class="d-block text-info">{{ Auth::user()->role ?? 'Admin' }}</small>
              </div>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="/users">
                <i class="fas fa-user"></i> Mon Profil
              </a>
              @if(Auth::user()->est_admin || (Auth::user()->role ?? 'utilisateur') === 'admin')
              <a class="dropdown-item" href="/settings">
                <i class="fas fa-cog"></i> Administration
              </a>
              @endif
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="/aide">
                <i class="fas fa-question-circle"></i> Aide
              </a>
              <a class="dropdown-item" href="/changelog">
                <i class="fas fa-info-circle"></i> Nouveautés
              </a>
              <div class="dropdown-divider"></div>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a class="dropdown-item text-danger" href="#" 
                   onclick="event.preventDefault(); this.closest('form').submit();">
                  <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
              </form>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Breadcrumb pour navigation contextuelle -->
  @if(!request()->routeIs('dashboard'))
  <nav aria-label="breadcrumb" class="bg-light border-bottom" style="margin-top: 70px; padding-top: 10px;">
    <div class="container-fluid">
      <ol class="breadcrumb mb-0 py-2">
        <li class="breadcrumb-item">
          <a href="{{ route('dashboard') }}">
            <i class="fas fa-home"></i> Dashboard
          </a>
        </li>
        @if(request()->routeIs('clients.*'))
          <li class="breadcrumb-item active">
            <i class="fas fa-users"></i> Clients
          </li>
        @elseif(request()->routeIs('champs-activite.*'))
          <li class="breadcrumb-item active">
            <i class="fas fa-briefcase"></i> Champs d'Activité
          </li>
        @elseif(request()->routeIs('templates.*'))
          <li class="breadcrumb-item active">
            <i class="fas fa-file-contract"></i> Templates
          </li>
        @elseif(request()->routeIs('contacts.*'))
          <li class="breadcrumb-item active">
            <i class="fas fa-address-book"></i> Annuaire
          </li>
        @endif
      </ol>
    </div>
  </nav>
  @endif

  <!-- Contenu principal -->
  <div class="container-fluid" style="margin-top: {{ request()->routeIs('dashboard') ? '70' : '120' }}px;">
    @yield('content')
  </div>

  <!-- Scripts Bootstrap -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

  <style>
  .navbar-brand small {
    font-size: 0.7rem;
  }

  .nav-link.active {
    background-color: rgba(255,255,255,0.1);
    border-radius: 4px;
  }

  .dropdown-menu {
    border: none;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  }

  .dropdown-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    margin-bottom: 0;
  }

  .badge-sm {
    font-size: 0.75rem;
    padding: 0.25rem 0.4rem;
  }

  .hover-underline-animation {
    position: relative;
    text-decoration: none;
  }

  .hover-underline-animation:after {
    content: '';
    position: absolute;
    width: 100%;
    transform: scaleX(0);
    height: 2px;
    bottom: 0;
    left: 0;
    background-color: #fff;
    transform-origin: bottom right;
    transition: transform 0.25s ease-out;
  }

  .hover-underline-animation:hover:after {
    transform: scaleX(1);
    transform-origin: bottom left;
  }

  @media (max-width: 768px) {
    .form-inline {
      flex-direction: column;
      align-items: stretch;
    }
    
    .input-group {
      width: 100%;
      margin-top: 0.5rem;
    }
  }
  </style>
</body>
</html>
