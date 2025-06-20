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
                    <i class="fas fa-address-book"></i> Annuaire Téléphonique
                </h1>
                <div class="page-actions">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#createContactModal">
                        <i class="fas fa-plus"></i> Nouveau Contact
                    </button>
                    <div class="btn-group ml-2" role="group">
                        <button class="btn btn-outline-secondary dropdown-toggle" 
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-download"></i> Exporter
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ route('contacts.export', 'csv') }}">
                                <i class="fas fa-file-csv"></i> Format CSV
                            </a>
                            <a class="dropdown-item" href="{{ route('contacts.export', 'pdf') }}">
                                <i class="fas fa-file-pdf"></i> Format PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtres et recherche -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('contacts.search') }}" class="row">
                        <div class="col-md-6">
                            <input type="text" name="q" class="form-control" 
                                   placeholder="Rechercher un contact (nom, téléphone, email...)" 
                                   value="{{ request('q') }}">
                        </div>
                        <div class="col-md-4">
                            <select name="categorie" class="form-control">
                                <option value="">Toutes les catégories</option>
                                <option value="Justice" {{ request('categorie') === 'Justice' ? 'selected' : '' }}>Justice</option>
                                <option value="Administration" {{ request('categorie') === 'Administration' ? 'selected' : '' }}>Administration</option>
                                <option value="Professionnel" {{ request('categorie') === 'Professionnel' ? 'selected' : '' }}>Professionnel</option>
                                <option value="Expert" {{ request('categorie') === 'Expert' ? 'selected' : '' }}>Expert</option>
                                <option value="Immobilier" {{ request('categorie') === 'Immobilier' ? 'selected' : '' }}>Immobilier</option>
                                <option value="Banque" {{ request('categorie') === 'Banque' ? 'selected' : '' }}>Banque</option>
                                <option value="Assurance" {{ request('categorie') === 'Assurance' ? 'selected' : '' }}>Assurance</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-search"></i> Rechercher
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
                            <i class="fas fa-address-book"></i>
                        </div>
                        <div class="stats-content">
                            <h3>{{ count($contacts) }}</h3>
                            <p>Total Contacts</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card bg-success">
                        <div class="stats-icon">
                            <i class="fas fa-gavel"></i>
                        </div>
                        <div class="stats-content">
                            <h3>{{ collect($contacts)->where('categorie', 'Justice')->count() }}</h3>
                            <p>Justice</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card bg-info">
                        <div class="stats-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="stats-content">
                            <h3>{{ collect($contacts)->where('categorie', 'Administration')->count() }}</h3>
                            <p>Administration</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card bg-warning">
                        <div class="stats-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="stats-content">
                            <h3>{{ collect($contacts)->where('categorie', 'Professionnel')->count() }}</h3>
                            <p>Professionnels</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grille des contacts par catégorie -->
            @php
                $categoriesGrouped = collect($contacts)->groupBy('categorie');
            @endphp

            @foreach($categoriesGrouped as $categorie => $contactsCategorie)
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-{{ 
                            $categorie === 'Justice' ? 'gavel' : 
                            ($categorie === 'Administration' ? 'building' : 
                            ($categorie === 'Expert' ? 'user-graduate' : 
                            ($categorie === 'Immobilier' ? 'home' : 'users'))) 
                        }}"></i>
                        {{ $categorie }} ({{ count($contactsCategorie) }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="row no-gutters">
                        @foreach($contactsCategorie as $contact)
                        <div class="col-md-6 col-lg-4">
                            <div class="contact-card">
                                <div class="contact-header">
                                    <h6 class="contact-name">{{ $contact['nom'] }}</h6>
                                    <span class="badge badge-outline-{{ 
                                        $categorie === 'Justice' ? 'primary' : 
                                        ($categorie === 'Administration' ? 'info' : 
                                        ($categorie === 'Expert' ? 'success' : 'secondary')) 
                                    }}">
                                        {{ $categorie }}
                                    </span>
                                </div>
                                <div class="contact-info">
                                    <div class="contact-item">
                                        <i class="fas fa-phone text-success"></i>
                                        <a href="tel:{{ $contact['telephone'] }}">{{ $contact['telephone'] }}</a>
                                    </div>
                                    @if($contact['email'])
                                    <div class="contact-item">
                                        <i class="fas fa-envelope text-info"></i>
                                        <a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a>
                                    </div>
                                    @endif
                                    @if($contact['adresse'])
                                    <div class="contact-item">
                                        <i class="fas fa-map-marker-alt text-danger"></i>
                                        <small>{{ $contact['adresse'] }}</small>
                                    </div>
                                    @endif
                                    @if($contact['notes'])
                                    <div class="contact-notes">
                                        <small class="text-muted">{{ $contact['notes'] }}</small>
                                    </div>
                                    @endif
                                </div>
                                <div class="contact-actions">
                                    <a href="{{ route('contacts.show', $contact['id']) }}" 
                                       class="btn btn-sm btn-outline-info" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('contacts.edit', $contact['id']) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger" 
                                            onclick="deleteContact({{ $contact['id'] }})" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach

            @if(empty($contacts))
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-address-book"></i>
                </div>
                <h5>Aucun contact trouvé</h5>
                <p class="text-muted">
                    @if(request('q') || request('categorie'))
                        Aucun contact ne correspond aux critères de recherche.
                    @else
                        Commencez par créer votre premier contact.
                    @endif
                </p>
                @if(!request('q') && !request('categorie'))
                    <button class="btn btn-primary" data-toggle="modal" data-target="#createContactModal">
                        <i class="fas fa-plus"></i> Créer un Contact
                    </button>
                @endif
            </div>
            @endif

            <!-- Vue liste détaillée -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list"></i> Vue Liste Complète
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Contact</th>
                                    <th>Catégorie</th>
                                    <th>Téléphone</th>
                                    <th>Email</th>
                                    <th>Adresse</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contacts as $contact)
                                <tr>
                                    <td>
                                        <div class="contact-info">
                                            <strong>{{ $contact['nom'] }}</strong>
                                            @if($contact['notes'])
                                                <br><small class="text-muted">{{ Str::limit($contact['notes'], 50) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ 
                                            $contact['categorie'] === 'Justice' ? 'primary' : 
                                            ($contact['categorie'] === 'Administration' ? 'info' : 
                                            ($contact['categorie'] === 'Expert' ? 'success' : 'secondary')) 
                                        }}">
                                            {{ $contact['categorie'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="tel:{{ $contact['telephone'] }}" class="text-success">
                                            <i class="fas fa-phone"></i> {{ $contact['telephone'] }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($contact['email'])
                                            <a href="mailto:{{ $contact['email'] }}" class="text-info">
                                                <i class="fas fa-envelope"></i> {{ $contact['email'] }}
                                            </a>
                                        @else
                                            <span class="text-muted">Non renseigné</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($contact['adresse'])
                                            <small>{{ Str::limit($contact['adresse'], 30) }}</small>
                                        @else
                                            <span class="text-muted">Non renseignée</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('contacts.show', $contact['id']) }}" 
                                               class="btn btn-sm btn-outline-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('contacts.edit', $contact['id']) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteContact({{ $contact['id'] }})" title="Supprimer">
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

<!-- Modal Création Contact -->
<div class="modal fade" id="createContactModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('contacts.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus"></i> Nouveau Contact
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="nom">Nom du Contact *</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="categorie">Catégorie *</label>
                                <select class="form-control" id="categorie" name="categorie" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="Justice">Justice</option>
                                    <option value="Administration">Administration</option>
                                    <option value="Professionnel">Professionnel</option>
                                    <option value="Expert">Expert</option>
                                    <option value="Immobilier">Immobilier</option>
                                    <option value="Banque">Banque</option>
                                    <option value="Assurance">Assurance</option>
                                    <option value="Autres">Autres</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telephone">Téléphone *</label>
                                <input type="tel" class="form-control" id="telephone" name="telephone" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="adresse">Adresse</label>
                        <textarea class="form-control" id="adresse" name="adresse" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"
                                  placeholder="Informations complémentaires, spécialités, horaires..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Créer le Contact
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function deleteContact(contactId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce contact ? Cette action est irréversible.')) {
        // Créer un formulaire pour la suppression
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/contacts/${contactId}`;
        
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
.contact-card {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    margin: 10px;
    transition: transform 0.2s;
}

.contact-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.contact-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.contact-name {
    margin: 0;
    color: #333;
    font-weight: 600;
}

.contact-info {
    margin-bottom: 15px;
}

.contact-item {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
}

.contact-item i {
    width: 16px;
    margin-right: 8px;
}

.contact-item a {
    text-decoration: none;
    color: inherit;
}

.contact-item a:hover {
    text-decoration: underline;
}

.contact-notes {
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #eee;
}

.contact-actions {
    display: flex;
    gap: 5px;
}

.badge-outline-primary {
    border: 1px solid #007bff;
    color: #007bff;
    background: transparent;
}

.badge-outline-info {
    border: 1px solid #17a2b8;
    color: #17a2b8;
    background: transparent;
}

.badge-outline-success {
    border: 1px solid #28a745;
    color: #28a745;
    background: transparent;
}

.badge-outline-secondary {
    border: 1px solid #6c757d;
    color: #6c757d;
    background: transparent;
}
</style>
@endsection
