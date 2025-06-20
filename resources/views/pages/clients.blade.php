@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 sidebar">
            @include('layouts.sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-md-10 main-content">
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-users"></i> Gestion des Clients
                </h1>
                <div class="page-actions">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#createClientModal">
                        <i class="fas fa-plus"></i> Nouveau Client
                    </button>
                </div>
            </div>

            <!-- Barre de recherche -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('clients.search') }}" class="row">
                        <div class="col-md-8">
                            <input type="text" name="q" class="form-control" 
                                   placeholder="Rechercher un client (nom, prénom, email...)" 
                                   value="{{ request('q') }}">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-outline-primary mr-2">
                                <i class="fas fa-search"></i> Rechercher
                            </button>
                            <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card bg-primary">
                        <div class="stats-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stats-content">
                            <h3>{{ count($clients) }}</h3>
                            <p>Total Clients</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card bg-success">
                        <div class="stats-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="stats-content">
                            <h3>{{ $clients->where('status', 'active')->count() }}</h3>
                            <p>Clients Actifs</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card bg-warning">
                        <div class="stats-icon">
                            <i class="fas fa-user-clock"></i>
                        </div>
                        <div class="stats-content">
                            <h3>{{ $clients->where('statut', 'inactif')->count() }}</h3>
                            <p>Clients Inactifs</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card bg-info">
                        <div class="stats-icon">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <div class="stats-content">
                            <h3>{{ $clients->where('created_at', '>=', now()->startOfMonth())->count() }}</h3>
                            <p>Ce Mois</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des clients -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list"></i> Liste des Clients
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($clients->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Client</th>
                                        <th>Email</th>
                                        <th>Téléphone</th>
                                        <th>Statut</th>
                                        <th>Date Création</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($clients as $client)
                                    <tr>
                                        <td>
                                            <div class="user-info">
                                                <div class="avatar">
                                                    <i class="fas fa-user-circle"></i>
                                                </div>
                                                <div class="details">
                                                    <strong>{{ $client->nom }} {{ $client->prenom }}</strong>
                                                    <small class="d-block text-muted">ID: #{{ $client->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $client->email }}</td>
                                        <td>{{ $client->telephone ?? 'Non renseigné' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $client->statut === 'actif' ? 'success' : 'warning' }}">
                                                {{ ucfirst($client->statut) }}
                                            </span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($client->created_at)->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="{{ route('clients.show', $client->id) }}" 
                                                   class="btn btn-sm btn-outline-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('clients.edit', $client->id) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-sm btn-outline-{{ $client->statut === 'actif' ? 'warning' : 'success' }}"
                                                        onclick="toggleStatus({{ $client->id }})" 
                                                        title="{{ $client->statut === 'actif' ? 'Désactiver' : 'Activer' }}">
                                                    <i class="fas fa-{{ $client->statut === 'actif' ? 'user-times' : 'user-check' }}"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteClient({{ $client->id }})" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h5>Aucun client trouvé</h5>
                            <p class="text-muted">
                                @if(request('q'))
                                    Aucun client ne correspond à votre recherche.
                                @else
                                    Commencez par créer votre premier client.
                                @endif
                            </p>
                            @if(!request('q'))
                                <button class="btn btn-primary" data-toggle="modal" data-target="#createClientModal">
                                    <i class="fas fa-plus"></i> Créer un Client
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Création Client -->
<div class="modal fade" id="createClientModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('clients.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus"></i> Nouveau Client
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nom">Nom *</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="prenom">Prénom *</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telephone">Téléphone</label>
                                <input type="tel" class="form-control" id="telephone" name="telephone">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="adresse">Adresse</label>
                        <textarea class="form-control" id="adresse" name="adresse" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Mot de passe *</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation">Confirmer le mot de passe *</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Créer le Client
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleStatus(clientId) {
    if (confirm('Êtes-vous sûr de vouloir changer le statut de ce client ?')) {
        window.location.href = `/clients/${clientId}/toggle-status`;
    }
}

function deleteClient(clientId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce client ? Cette action est irréversible.')) {
        // Créer un formulaire pour la suppression
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/clients/${clientId}`;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = '{{ csrf_token() }}';
        
        form.appendChild(methodInput);
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
