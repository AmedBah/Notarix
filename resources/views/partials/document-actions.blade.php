{{-- Boutons d'action pour les documents selon le cahier des charges --}}
@if(isset($document))
<div class="btn-group" role="group" aria-label="Actions document">
    
    {{-- Bouton de consultation/vue --}}
    @if(Auth::user()->hasPermission('lecture') || Auth::user()->isAdmin())
        <button type="button" class="btn btn-info btn-sm" onclick="viewDocument({{ $document->id }})" title="Consulter le document">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
            </svg>
        </button>
    @endif

    {{-- Gestion des demandes d'accès et téléchargement --}}
    @if($document->visibility === 'private' && $document->user_id !== Auth::id() && !Auth::user()->isAdmin())
        {{-- Document privé : système de demandes d'accès --}}
        @if(isset($demandes))
            @php
                $demande = $demandes->where('document_id', $document->id)->where('emetteur_id', Auth::id())->first();
            @endphp
            
            @if(!$demande)
                {{-- Pas de demande : bouton pour demander l'accès --}}
                <button type="button" class="btn btn-warning btn-sm" onclick="requestAccess({{ $document->id }})" title="Demander l'accès">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-send-plus-fill" viewBox="0 0 16 16">
                        <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 1.59 2.498C8 14 8 13 8 12.5a4.5 4.5 0 0 1 5.026-4.47L15.964.686Zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471-.47 1.178Z"/>
                        <path d="M16 12.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Zm-3.5-2a.5.5 0 0 0-.5.5v1h-1a.5.5 0 0 0 0 1h1v1a.5.5 0 0 0 1 0v-1h1a.5.5 0 0 0 0-1h-1v-1a.5.5 0 0 0-.5-.5Z"/>
                    </svg>
                </button>
            @elseif($demande->etat == 1)
                {{-- Demande acceptée : bouton de téléchargement --}}
                <a href="/download/{{ $document->id }}" class="btn btn-success btn-sm" title="Télécharger">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                        <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                    </svg>
                </a>
            @elseif($demande->etat == 2)
                {{-- Demande refusée --}}
                <button type="button" class="btn btn-dark btn-sm" title="Votre demande a été refusée" disabled>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-lock2" viewBox="0 0 16 16">
                        <path d="M8 5a1 1 0 0 1 1 1v1H7V6a1 1 0 0 1 1-1zm2 2.076V6a2 2 0 1 0-4 0v1.076c-.54.166-1 .597-1 1.224v2.4c0 .816.781 1.3 1.5 1.3h3c.719 0 1.5-.484 1.5-1.3V8.3c0-.627-.46-1.058-1-1.224z"/>
                        <path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/>
                    </svg>
                </button>
            @else
                {{-- Demande en attente --}}
                <button type="button" class="btn btn-secondary btn-sm" onclick="cancelRequest({{ $document->id }})" title="Annuler la demande">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-stopwatch-fill" viewBox="0 0 16 16">
                        <path d="M6.5 0a.5.5 0 0 0 0 1H7v1.07A7.001 7.001 0 0 0 8 16a7 7 0 0 0 5.29-11.584.531.531 0 0 0 .013-.012l.354-.354.353.354a.5.5 0 1 0 .707-.707l-1.414-1.415a.5.5 0 1 0-.707.707l.354.354-.354.354a.717.717 0 0 0-.012.012A6.973 6.973 0 0 0 9 2.071V1h.5a.5.5 0 0 0 0-1h-3zm2 5.6V9a.5.5 0 0 1-.5.5H4.5a.5.5 0 0 1 0-1h3V5.6a.5.5 0 1 1 1 0z"/>
                    </svg>
                </button>
            @endif
        @endif
    @else
        {{-- Bouton de téléchargement normal --}}
        @if(Auth::user()->hasPermission('telecharger') || Auth::user()->isAdmin())
            @if($document->visibility !== 'admin_only' || Auth::user()->isAdmin())
                <a href="/download/{{ $document->id }}" class="btn btn-success btn-sm" title="Télécharger">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                        <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                    </svg>
                </a>
            @endif
        @endif
    @endif

    {{-- Bouton d'archivage automatique --}}
    @if((Auth::user()->hasPermission('modification') || Auth::user()->isAdmin()) && !$document->is_archived)
        @if($document->user_id === Auth::id() || Auth::user()->isAdmin())
            <form action="/archive/{{ $document->id }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-warning btn-sm" title="Archiver automatiquement" 
                        onclick="return confirm('Voulez-vous archiver ce document ?')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-archive" viewBox="0 0 16 16">
                        <path d="M0 2a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1v7.5a2.5 2.5 0 0 1-2.5 2.5h-9A2.5 2.5 0 0 1 1 12.5V5a1 1 0 0 1-1-1V2zm2 3v7.5A1.5 1.5 0 0 0 3.5 14h9a1.5 1.5 0 0 0 1.5-1.5V5H2zm13-3H1v2h14V2zM5 7.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z"/>
                    </svg>
                </button>
            </form>
        @endif
    @endif

    {{-- Bouton de restauration (si archivé) --}}
    @if($document->is_archived && (Auth::user()->hasPermission('modification') || Auth::user()->isAdmin()))
        @if($document->user_id === Auth::id() || Auth::user()->isAdmin())
            <form action="/restore/{{ $document->id }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm" title="Restaurer le document" 
                        onclick="return confirm('Voulez-vous restaurer ce document ?')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                        <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                    </svg>
                </button>
            </form>
        @endif
    @endif

    {{-- Bouton de marquage pour mise à jour (admin seulement) --}}
    @if(Auth::user()->isAdmin() && !$document->needs_update)
        <form action="/mark-update/{{ $document->id }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-info btn-sm" title="Marquer pour mise à jour">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cloud-plus" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8 5.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V10a.5.5 0 0 1-1 0V8.5H6a.5.5 0 0 1 0-1h1.5V6a.5.5 0 0 1 .5-.5z"/>
                    <path d="M4.406 3.342A5.53 5.53 0 0 1 8 2c2.69 0 4.923 2 5.166 4.579C14.758 6.804 16 8.137 16 9.773 16 11.569 14.502 13 12.687 13H3.781C1.708 13 0 11.366 0 9.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383zm.653.757c-.757.653-1.153 1.44-1.153 2.056v.448l-.445.049C2.064 6.805 1 7.952 1 9.318 1 10.785 2.23 12 3.781 12h8.906C13.98 12 15 10.988 15 9.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 4.825 10.328 3 8 3a4.53 4.53 0 0 0-2.941 1.1z"/>
                </svg>
            </button>
        </form>
    @endif

    {{-- Bouton de suppression --}}
    @if(Auth::user()->hasPermission('suppression') || Auth::user()->isAdmin())
        @if($document->user_id === Auth::id() || Auth::user()->isAdmin())
            <a href="/delete/{{ $document->id }}" class="btn btn-danger btn-sm" title="Supprimer" 
               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce document ?')">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                    <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                </svg>
            </a>
        @endif
    @endif
</div>

{{-- Indicateurs visuels --}}
<div class="mt-2">
    @if($document->is_archived)
        <span class="badge badge-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-archive-fill" viewBox="0 0 16 16">
                <path d="M12.643 15C13.979 15 15 13.845 15 12.5V5H1v7.5C1 13.845 2.021 15 3.357 15h9.286zM5.5 7h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1 0-1zM.8 1a.8.8 0 0 0-.8.8V3a.8.8 0 0 0 .8.8h14.4A.8.8 0 0 0 16 3V1.8a.8.8 0 0 0-.8-.8H.8z"/>
            </svg>
            Archivé
        </span>
    @endif
    
    @if($document->needs_update)
        <span class="badge badge-info">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-cloud" viewBox="0 0 16 16">
                <path d="M4.406 3.342A5.53 5.53 0 0 1 8 2c2.69 0 4.923 2 5.166 4.579C14.758 6.804 16 8.137 16 9.773 16 11.569 14.502 13 12.687 13H3.781C1.708 13 0 11.366 0 9.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383zm.653.757c-.757.653-1.153 1.44-1.153 2.056v.448l-.445.049C2.064 6.805 1 7.952 1 9.318 1 10.785 2.23 12 3.781 12h8.906C13.98 12 15 10.988 15 9.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 4.825 10.328 3 8 3a4.53 4.53 0 0 0-2.941 1.1z"/>
            </svg>
            Mise à jour requise
        </span>
    @endif
    
    @if($document->status === 'valide')
        <span class="badge badge-success">✅ Validé</span>
    @elseif($document->status === 'en_cours')
        <span class="badge badge-warning">⚙️ En cours</span>
    @else
        <span class="badge badge-light">📝 Brouillon</span>
    @endif
    
    @if($document->visibility === 'private')
        <span class="badge badge-dark">🔒 Privé</span>
    @elseif($document->visibility === 'admin_only')
        <span class="badge badge-danger">👑 Admin</span>
    @endif
</div>

{{-- Scripts JavaScript pour les interactions --}}
<script>
function viewDocument(documentId) {
    // Ouvre une fenêtre modale ou une nouvelle page pour consulter le document
    window.open('/view/' + documentId, '_blank');
}

function requestAccess(documentId) {
    if (confirm('Demander l\'accès à ce document ?')) {
        fetch('/request-access/' + documentId, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Demande d\'accès envoyée avec succès');
                location.reload();
            } else {
                alert('Erreur lors de l\'envoi de la demande');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de l\'envoi de la demande');
        });
    }
}

function cancelRequest(documentId) {
    if (confirm('Annuler la demande d\'accès ?')) {
        fetch('/cancel-request/' + documentId, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Demande d\'accès annulée');
                location.reload();
            } else {
                alert('Erreur lors de l\'annulation');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de l\'annulation');
        });
    }
}
</script>

@endif
