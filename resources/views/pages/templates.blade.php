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
                    <i class="fas fa-file-contract"></i> Templates d'Actes et Courriers
                </h1>
                <div class="page-actions">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#createTemplateModal">
                        <i class="fas fa-plus"></i> Nouveau Template
                    </button>
                </div>
            </div>

            <!-- Filtres et recherche -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('templates.search') }}" class="row">
                        <div class="col-md-4">
                            <input type="text" name="q" class="form-control" 
                                   placeholder="Rechercher un template..." 
                                   value="{{ request('q') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="type" class="form-control">
                                <option value="">Tous les types</option>
                                <option value="acte" {{ request('type') === 'acte' ? 'selected' : '' }}>Actes</option>
                                <option value="courrier" {{ request('type') === 'courrier' ? 'selected' : '' }}>Courriers</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="categorie" class="form-control">
                                <option value="">Toutes les catégories</option>
                                <option value="Droit Civil" {{ request('categorie') === 'Droit Civil' ? 'selected' : '' }}>Droit Civil</option>
                                <option value="Droit Commercial" {{ request('categorie') === 'Droit Commercial' ? 'selected' : '' }}>Droit Commercial</option>
                                <option value="Droit Immobilier" {{ request('categorie') === 'Droit Immobilier' ? 'selected' : '' }}>Droit Immobilier</option>
                                <option value="Administration" {{ request('categorie') === 'Administration' ? 'selected' : '' }}>Administration</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-search"></i> Filtrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card bg-primary">
                        <div class="stats-icon">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <div class="stats-content">
                            <h3>{{ count($templates) }}</h3>
                            <p>Templates Total</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card bg-success">
                        <div class="stats-icon">
                            <i class="fas fa-stamp"></i>
                        </div>
                        <div class="stats-content">
                            <h3>{{ collect($templates)->where('type', 'acte')->count() }}</h3>
                            <p>Actes</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card bg-info">
                        <div class="stats-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="stats-content">
                            <h3>{{ collect($templates)->where('type', 'courrier')->count() }}</h3>
                            <p>Courriers</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card bg-warning">
                        <div class="stats-icon">
                            <i class="fas fa-download"></i>
                        </div>
                        <div class="stats-content">
                            <h3>{{ rand(50, 200) }}</h3>
                            <p>Téléchargements</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grille des templates -->
            <div class="row">
                @forelse($templates as $template)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="template-card">
                        <div class="card h-100">
                            <div class="card-header">
                                <div class="template-header">
                                    <div class="template-type">
                                        <span class="badge badge-{{ $template['type'] === 'acte' ? 'primary' : 'info' }}">
                                            <i class="fas fa-{{ $template['type'] === 'acte' ? 'stamp' : 'envelope' }}"></i>
                                            {{ ucfirst($template['type']) }}
                                        </span>
                                    </div>
                                    <div class="template-category">
                                        <small class="text-muted">{{ $template['categorie'] }}</small>
                                    </div>
                                </div>
                                <h5 class="card-title mb-0">{{ $template['nom'] }}</h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text">{{ $template['description'] }}</p>
                                
                                <div class="template-info">
                                    <small class="text-muted">
                                        <i class="fas fa-file"></i> {{ $template['fichier'] }}
                                    </small>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($template['created_at'])->format('d/m/Y') }}
                                    </small>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="btn-group w-100" role="group">
                                    <a href="{{ route('templates.show', $template['id']) }}" 
                                       class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-eye"></i> Voir
                                    </a>
                                    <a href="{{ route('templates.download', $template['id']) }}" 
                                       class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-download"></i> Télécharger
                                    </a>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" 
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('templates.edit', $template['id']) }}">
                                                <i class="fas fa-edit"></i> Modifier
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item text-danger" href="#" 
                                               onclick="deleteTemplate({{ $template['id'] }})">
                                                <i class="fas fa-trash"></i> Supprimer
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <h5>Aucun template trouvé</h5>
                        <p class="text-muted">
                            @if(request('q') || request('type') || request('categorie'))
                                Aucun template ne correspond aux critères de recherche.
                            @else
                                Commencez par créer votre premier template.
                            @endif
                        </p>
                        @if(!request('q') && !request('type') && !request('categorie'))
                            <button class="btn btn-primary" data-toggle="modal" data-target="#createTemplateModal">
                                <i class="fas fa-plus"></i> Créer un Template
                            </button>
                        @endif
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Vue liste détaillée -->
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
                                    <th>Template</th>
                                    <th>Type</th>
                                    <th>Catégorie</th>
                                    <th>Fichier</th>
                                    <th>Date Création</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($templates as $template)
                                <tr>
                                    <td>
                                        <div class="template-info">
                                            <i class="fas fa-{{ $template['type'] === 'acte' ? 'stamp' : 'envelope' }} text-primary"></i>
                                            <strong>{{ $template['nom'] }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $template['description'] }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $template['type'] === 'acte' ? 'primary' : 'info' }}">
                                            {{ ucfirst($template['type']) }}
                                        </span>
                                    </td>
                                    <td>{{ $template['categorie'] }}</td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="fas fa-file"></i> {{ $template['fichier'] }}
                                        </small>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($template['created_at'])->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('templates.show', $template['id']) }}" 
                                               class="btn btn-sm btn-outline-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('templates.download', $template['id']) }}" 
                                               class="btn btn-sm btn-outline-success" title="Télécharger">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <a href="{{ route('templates.edit', $template['id']) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteTemplate({{ $template['id'] }})" title="Supprimer">
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

<!-- Modal Création Template -->
<div class="modal fade" id="createTemplateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('templates.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus"></i> Nouveau Template
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="nom">Nom du Template *</label>
                                <input type="text" class="form-control" id="nom" name="nom" 
                                       placeholder="Ex: Acte de vente immobilière..." required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="type">Type *</label>
                                <select class="form-control" id="type" name="type" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="acte">Acte</option>
                                    <option value="courrier">Courrier</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="categorie">Catégorie *</label>
                        <select class="form-control" id="categorie" name="categorie" required>
                            <option value="">Sélectionner...</option>
                            <option value="Droit Civil">Droit Civil</option>
                            <option value="Droit Commercial">Droit Commercial</option>
                            <option value="Droit Immobilier">Droit Immobilier</option>
                            <option value="Droit des Affaires">Droit des Affaires</option>
                            <option value="Administration">Administration</option>
                            <option value="Authentifications">Authentifications</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                                  placeholder="Description du template..."></textarea>
                    </div>
                    <div class="form-group">
                        <label for="fichier">Fichier Template *</label>
                        <input type="file" class="form-control-file" id="fichier" name="fichier" 
                               accept=".doc,.docx,.pdf" required>
                        <small class="form-text text-muted">Formats acceptés: DOC, DOCX, PDF (max 10MB)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Créer le Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function deleteTemplate(templateId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce template ? Cette action est irréversible.')) {
        // Créer un formulaire pour la suppression
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/templates/${templateId}`;
        
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
.template-card .card {
    transition: transform 0.2s;
    border: 1px solid #e9ecef;
}

.template-card .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.template-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.template-info {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #eee;
}
</style>
@endsection
