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
                    <i class="fas fa-briefcase"></i> Champs d'Activité
                </h1>
                <div class="page-actions">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#createChampModal">
                        <i class="fas fa-plus"></i> Nouveau Champ
                    </button>
                </div>
            </div>

            <!-- Barre de recherche -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('champs-activite.search') }}" class="row">
                        <div class="col-md-8">
                            <input type="text" name="q" class="form-control" 
                                   placeholder="Rechercher un champ d'activité..." 
                                   value="{{ request('q') }}">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-outline-primary mr-2">
                                <i class="fas fa-search"></i> Rechercher
                            </button>
                            <a href="{{ route('champs-activite.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="stats-overview">
                        <div class="stats-card bg-info">
                            <div class="stats-icon">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <div class="stats-content">
                                <h3>{{ count($champsActivite) }}</h3>
                                <p>Champs d'Activité</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grille des champs d'activité -->
            <div class="row">
                @forelse($champsActivite as $champ)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="activity-card">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-tag text-primary"></i>
                                    {{ $champ['nom'] }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text">{{ $champ['description'] }}</p>
                                
                                <div class="activity-stats">
                                    <small class="text-muted">
                                        <i class="fas fa-folder"></i> Documents associés: 
                                        <span class="badge badge-info">{{ rand(5, 50) }}</span>
                                    </small>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="btn-group w-100" role="group">
                                    <a href="{{ route('champs-activite.show', $champ['id']) }}" 
                                       class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-eye"></i> Voir
                                    </a>
                                    <a href="{{ route('champs-activite.edit', $champ['id']) }}" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-edit"></i> Modifier
                                    </a>
                                    <button class="btn btn-outline-danger btn-sm" 
                                            onclick="deleteChamp({{ $champ['id'] }})">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <h5>Aucun champ d'activité trouvé</h5>
                        <p class="text-muted">
                            @if(request('q'))
                                Aucun champ d'activité ne correspond à votre recherche.
                            @else
                                Commencez par créer votre premier champ d'activité.
                            @endif
                        </p>
                        @if(!request('q'))
                            <button class="btn btn-primary" data-toggle="modal" data-target="#createChampModal">
                                <i class="fas fa-plus"></i> Créer un Champ d'Activité
                            </button>
                        @endif
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Vue liste alternative -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list"></i> Vue Liste Détaillée
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Champ d'Activité</th>
                                    <th>Description</th>
                                    <th>Documents</th>
                                    <th>Dernière Activité</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($champsActivite as $champ)
                                <tr>
                                    <td>
                                        <div class="activity-info">
                                            <i class="fas fa-briefcase text-primary"></i>
                                            <strong>{{ $champ['nom'] }}</strong>
                                        </div>
                                    </td>
                                    <td>{{ $champ['description'] }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ rand(5, 50) }} docs</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ now()->subDays(rand(1, 30))->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('champs-activite.show', $champ['id']) }}" 
                                               class="btn btn-sm btn-outline-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('champs-activite.edit', $champ['id']) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteChamp({{ $champ['id'] }})" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Création Champ d'Activité -->
<div class="modal fade" id="createChampModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('champs-activite.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus"></i> Nouveau Champ d'Activité
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nom">Nom du Champ d'Activité *</label>
                        <input type="text" class="form-control" id="nom" name="nom" 
                               placeholder="Ex: Droit Civil, Droit Commercial..." required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                                  placeholder="Description détaillée du champ d'activité..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Créer le Champ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function deleteChamp(champId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce champ d\'activité ? Cette action est irréversible.')) {
        // Créer un formulaire pour la suppression
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/champs-activite/${champId}`;
        
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

<style>
.activity-card .card {
    transition: transform 0.2s;
}

.activity-card .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.activity-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.activity-stats {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #eee;
}

.stats-overview {
    display: flex;
    justify-content: center;
}
</style>
@endsection
