@extends('layouts.navbar')



@section('content')


  <!-- Modal upload fichier  -->
  
<!-- Modal -->
<div class="modal fade" id="uploaderDocument" tabindex="-1" role="dialog" aria-labelledby="uploaderDocumentTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="uploaderDocumentTitle">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
            <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
          </svg>
          Uploader un Document
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>  @if(isset($section))
  @if(isset($dossier_id))
  <form action="/uploader/{{ $section->id }}/{{ $dossier_id }}" method="POST" enctype="multipart/form-data">
  @else
  <form action="/uploader/{{ $section->id }}" method="POST" enctype="multipart/form-data">
  @endif
    @csrf
      <div class="modal-body">
        
        <div align="center">
            <p class="card-text">Une nouvelle manière de gérer vos documents.</p>
        </div>
        
        <!-- Sélection de fichier -->
        <div class="mb-3" align="center">
            <label for="formFileMultiple" class="custom-file-upload">Sélectionner un document<br>
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                </svg>
            </label>
            <input class="form-control my-2" name="document" type="file" id="formFileMultiple" required 
                   style="background-color: #c2c4c7;color:rgb(44, 40, 40)"
                   accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.txt,.ppt,.pptx">
        </div>
        
        <!-- Informations du client (selon cahier des charges) -->
        <div class="mb-3">
            <label for="client_name" class="form-label">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                </svg>
                Nom du client
            </label>
            <input type="text" class="form-control" id="client_name" name="client_name" 
                   placeholder="Nom du client pour ce document" style="background-color:white;color:black;">
        </div>
        
        <!-- Numéro de dossier (selon cahier des charges) -->
        <div class="mb-3">
            <label for="dossier_number" class="form-label">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-folder2" viewBox="0 0 16 16">
                    <path d="M1 3.5A1.5 1.5 0 0 1 2.5 2h2.764c.958 0 1.76.56 2.311 1.184C7.985 3.648 8.48 4 9.5 4h4A1.5 1.5 0 0 1 15 5.5v7a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 12.5v-9zM2.5 3a.5.5 0 0 0-.5.5V6h12v-.5a.5.5 0 0 0-.5-.5H9.5c-.964 0-1.612-.453-2.11-1.09C6.984 3.482 6.364 3 5.5 3H2.5z"/>
                </svg>
                Numéro de dossier
            </label>
            <input type="text" class="form-control" id="dossier_number" name="dossier_number" 
                   placeholder="Ex: 2025-VP-001" style="background-color:white;color:black;">
        </div>
        
        <!-- Statut du document -->
        <div class="mb-3">
            <label for="status" class="form-label">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-flag" viewBox="0 0 16 16">
                    <path d="M14.778.085A.5.5 0 0 1 15 .5V8a.5.5 0 0 1-.314.464L9.5 10.5v.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-.5L1.314 8.464A.5.5 0 0 1 1 8V.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5v.5L9.186 2.536A.5.5 0 0 1 9.5 3v5.464L14.186.536a.5.5 0 0 1 .592-.451z"/>
                </svg>
                Statut
            </label>
            <select class="form-control" name="status" style="background-color:white;color:black;">
                <option value="brouillon">📝 Brouillon</option>
                <option value="en_cours">⚙️ En cours</option>
                <option value="valide">✅ Validé</option>
            </select>
        </div>
        
        <!-- Visibilité avec permissions -->
        <div class="mb-3">
            <label for="visibility" class="form-label">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                </svg>
                Visibilité
            </label>
            <select class="form-control" name="visibility" style="background-color:white;color:black;">
                <option value="public">🌐 Public (tous les collègues)</option>
                <option value="private">🔒 Privé (vous et l'administrateur)</option>
                @if(Auth::user()->isAdmin())
                <option value="admin_only">👑 Administrateur uniquement</option>
                @endif
            </select>
        </div>

        <div class="card-footer text-muted">
            <div align="center" id="showhere">
                <small>Formats acceptés: PDF, Word, Excel, Images, PowerPoint, Texte</small>
            </div>
        </div>
        
        <script>
            $(document).ready(function() {
                // Amélioration de l'aperçu du fichier sélectionné
                $('input[type="file"]').change(function() {
                    var file = document.getElementById('formFileMultiple').files[0];
                    var showhere = document.getElementById('showhere');
                    
                    if (file) {
                        var fileName = file.name;
                        var fileSize = (file.size / 1024).toFixed(2) + ' Ko';
                        var fileType = file.type;
                        
                        var icon = '';
                        if (fileType.includes('pdf')) {
                            icon = '📄';
                        } else if (fileType.includes('word') || fileType.includes('document')) {
                            icon = '📝';
                        } else if (fileType.includes('sheet') || fileType.includes('excel')) {
                            icon = '📊';
                        } else if (fileType.includes('image')) {
                            icon = '🖼️';
                        } else if (fileType.includes('powerpoint') || fileType.includes('presentation')) {
                            icon = '📊';
                        } else if (fileType.includes('text')) {
                            icon = '📄';
                        } else {
                            icon = '📎';
                        }
                        
                        showhere.innerHTML = `
                            <div class="file-preview">
                                <span class="file-icon">${icon}</span>
                                <div class="file-info">
                                    <div class="file-name">${fileName}</div>
                                    <div class="file-size">${fileSize}</div>
                                </div>
                            </div>
                        `;
                    } else {
                        showhere.innerHTML = '<small>Formats acceptés: PDF, Word, Excel, Images, PowerPoint, Texte</small>';
                    }
                });
                
                // Validation dynamique du formulaire
                $('form').on('submit', function(e) {
                    var file = document.getElementById('formFileMultiple').files[0];
                    var clientName = document.getElementById('client_name').value;
                    
                    if (!file) {
                        e.preventDefault();
                        alert('Veuillez sélectionner un fichier');
                        return false;
                    }
                    
                    // Validation de la taille (max 50MB)
                    if (file.size > 50 * 1024 * 1024) {
                        e.preventDefault();
                        alert('Le fichier est trop volumineux (max 50MB)');
                        return false;
                    }
                    
                    // Validation du nom du client
                    if (!clientName || clientName.trim() === '') {
                        if (!confirm('Continuer sans nom de client ?')) {
                            e.preventDefault();
                            return false;
                        }
                    }
                    
                    // Affichage de l'indicateur de chargement
                    $(this).find('button[type="submit"]').prop('disabled', true).html(`
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Envoi en cours...
                    `);
                });
            });
        </script>
        
        <style>
            .file-preview {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px;
                background: #f8f9fa;
                border-radius: 5px;
                margin-top: 10px;
            }
            
            .file-icon {
                font-size: 24px;
            }
            
            .file-info {
                flex: 1;
                text-align: left;
            }
            
            .file-name {
                font-weight: bold;
                color: #333;
            }
            
            .file-size {
                font-size: 0.9em;
                color: #666;
            }
            
            .custom-file-upload {
                border: 2px dashed #007bff;
                border-radius: 10px;
                padding: 20px;
                text-align: center;
                cursor: pointer;
                transition: all 0.3s ease;
                background: #f8f9fa;
            }
            
            .custom-file-upload:hover {
                background: #e9ecef;
                border-color: #0056b3;
            }
        </style>

      </div>      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">
          <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/>
          </svg>
          Annuler
        </button>
        
        @if(Auth::user()->hasPermission('modification') || Auth::user()->isAdmin())
        <button type="submit" class="btn btn-success">
          <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
            <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
          </svg> 
          📤 Uploader Document
        </button>
        @else
        <button type="button" class="btn btn-secondary" disabled title="Vous n'avez pas les permissions pour uploader">
          <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" fill="currentColor" class="bi bi-lock" viewBox="0 0 16 16">
            <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
          </svg>
          Accès restreint
        </button>
        @endif
      </div>
   </form>

   @endif
     <!-------------------  ###  end form  ###------------------------>

    </div>
  </div>
</div>


{{-- Bouton d'upload flottant --}}
@if(Auth::user()->hasPermission('modification') || Auth::user()->isAdmin())
<div class="floating-upload-btn">
  <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#uploaderDocument" 
          title="Uploader un nouveau document">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-plus-circle-fill" viewBox="0 0 16 16">
      <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
    </svg>
    <span class="d-none d-md-inline">Nouveau document</span>
  </button>
</div>

<style>
.floating-upload-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 1000;
}

.floating-upload-btn .btn {
    border-radius: 50px;
    box-shadow: 0 4px 12px rgba(0,123,255,0.4);
    transition: all 0.3s ease;
}

.floating-upload-btn .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,123,255,0.6);
}
</style>
@endif

<div class="card text-center" style="width: 77%">
  <div class="card-header nombre-elements" style="text-align: center;">

    {{-- Breadcrumb navigation --}}
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0" style="background: none; padding: 0;">
        <li class="breadcrumb-item">
          <a href="/compte" style="text-decoration: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-house" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M2 13.5V7h1v6.5a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5V7h1v6.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5zM7.293 1.5a1 1 0 0 1 1.414 0L15 8l-1 1L8 3.293 2 9 1 8 7.293 1.5z"/>
            </svg>
            {{ $entreprise->nom }}
          </a>
        </li>
        @if(isset($section))
        <li class="breadcrumb-item">
          <a href="/sections/{{ $section->id }}" style="text-decoration: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-folder" viewBox="0 0 16 16">
              <path d="M.54 3.87.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.826a2 2 0 0 1-1.991-1.819l-.637-7a1.99 1.99 0 0 1 .342-1.31zM2.19 4a1 1 0 0 0-.996 1.09l.637 7a1 1 0 0 0 .995.91h10.348a1 1 0 0 0 .995-.91l.637-7A1 1 0 0 0 13.81 4H2.19z"/>
            </svg>
            {{ $section->nom }}
          </a>
        </li>
        @endif
        @isset($folder)
        <li class="breadcrumb-item active" aria-current="page">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-folder-open" viewBox="0 0 16 16">
            <path d="M1.175 3.362c.176-.585.585-.85 1.051-.85h3.536c.203 0 .375.066.507.189L7.175 3.8c.132.123.175.284.175.453v.262h3.5c.466 0 .875.265 1.051.85l.637 2.124c.062.208.062.433 0 .641l-.637 2.124c-.176.585-.585.85-1.051.85H5.175c-.466 0-.875-.265-1.051-.85L3.487 7.641c-.062-.208-.062-.433 0-.641l.637-2.124zM2.5 6.5l.5 1.667v.833a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-.833L12.5 6.5H2.5z"/>
          </svg>
          {{ $folder->nom }}
        </li>
        @endisset
      </ol>
    </nav>
    
    {{-- Bouton d'upload dans le header --}}
    @if(Auth::user()->hasPermission('modification') || Auth::user()->isAdmin())
    <div class="mt-2">
      <button type="button" class="btn btn-success" data-toggle="modal" data-target="#uploaderDocument">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
          <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
          <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
        </svg>
        Uploader un document
      </button>
    </div>
    @endif
  </div>
  <div class="card-body search-list">
    <table class="table">
      <caption>Documents</caption>      <thead style="border-top : 0px ">
        <tr>
          <th scope="col" style="text-align: left" >
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark" viewBox="0 0 16 16">
              <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/>
            </svg>
            Nom
          </th>
          <th scope="col" style="text-align: left">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tag" viewBox="0 0 16 16">
              <path d="M6 4.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm-1 0a.5.5 0 1 0-1 0 .5.5 0 0 0 1 0z"/>
              <path fill-rule="evenodd" d="M2 7.5a1 1 0 0 1 1-1h4.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 0 1.414l-3.5 3.5a1 1 0 0 1-1.414 0L2.293 11.707A1 1 0 0 1 2 11V7.5z"/>
            </svg>
            Type
          </th>
          <th scope="col" style="text-align: left">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hdd" viewBox="0 0 16 16">
              <path d="M0 10a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-3zM5 11.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0zm-1.5-.5a.5.5 0 1 1 0 1 .5.5 0 0 1 0-1zm7.5.5a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1zm1.5-.5a.5.5 0 1 1 0 1 .5.5 0 0 1 0-1z"/>
            </svg>
            Taille
          </th>
          <th scope="col" style="text-align: center">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
              <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z"/>
              <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a.873.873 0 0 1 1.255-.52l.094.319z"/>
            </svg>
            Actions
          </th>
        </tr>
      </thead>
      <tbody>
        
       <!--------------------------Affichage des dossiers ------------------------->
          @isset($dossiers)
           
              @foreach($dossiers as $dossier)
              @isset($section)
              <tr>
                 <!------------------ Nom du dossier --------------------->
                <td style="text-align: left">
                  <a href="/sections/{{$section->id}}/{{$dossier->id}}" style="text-decoration: none">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-folder" viewBox="0 0 16 16">
                    <path d="M.54 3.87.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.826a2 2 0 0 1-1.991-1.819l-.637-7a1.99 1.99 0 0 1 .342-1.31zM2.19 4a1 1 0 0 0-.996 1.09l.637 7a1 1 0 0 0 .995.91h10.348a1 1 0 0 0 .995-.91l.637-7A1 1 0 0 0 13.81 4H2.19zm4.69-1.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981l.006.139C1.72 3.042 1.95 3 2.19 3h5.396l-.707-.707z"/>
                  </svg> {{ $dossier->nom }} </a>
                </td> 
                <!------------------ Type : (Dossier) --------------------->
                <td style="text-align: left">
                  <a href="/sections/{{$section->id}}/{{$dossier->id}}" style="text-decoration: none">
                  Dossier
                  </a>
                </td>
                <td style="text-align: left">
                  <a href="/sections/{{$section->id}}/{{$dossier->id}}" style="text-decoration: none">
               
               @isset($taille)
               @isset($i)
                 {{$taille[$i++]}} ko
               @endisset
               @endisset
                 
                  </a>
                </td>
                <td>
                  @if(Auth::user()->est_admin==1)
                    <!--   Supprimer Dossier-->
                    <button type="button" class="btn btn-danger" onclick="window.location.href='/deleteFolder/{{ $dossier->id }}'">  
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                      </svg>
                    </button>
                  @endif
                 <!--    Deatails Dossier -->
                 <button type="button" title="voire plus" class="btn btn-info" onclick="window.location.href='/settings'">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-square" viewBox="0 0 16 16">
                    <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
                    <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                  </svg>
                </button>
        
                </td>  
              </tr>  
              @endisset
              @endforeach
          @endisset
       <!------------------------ End Affichage des dossiers ----------------------->

      
       <!------------------------ Affichage des documents--------------------------->
        @isset($documents)
  
        @foreach ($documents as $doc)
        @isset($section )    
        @if($doc->section_id==$section->id)
        <tr>
         
          <td style="text-align: left">
            
            @if($doc->type=="Fichier PDF")
            <!------------------   pdf icon ------------------->
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filetype-pdf" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5ZM1.6 11.85H0v3.999h.791v-1.342h.803c.287 0 .531-.057.732-.173.203-.117.358-.275.463-.474a1.42 1.42 0 0 0 .161-.677c0-.25-.053-.476-.158-.677a1.176 1.176 0 0 0-.46-.477c-.2-.12-.443-.179-.732-.179Zm.545 1.333a.795.795 0 0 1-.085.38.574.574 0 0 1-.238.241.794.794 0 0 1-.375.082H.788V12.48h.66c.218 0 .389.06.512.181.123.122.185.296.185.522Zm1.217-1.333v3.999h1.46c.958 0 1.76-.56 2.311-1.184C7.985 10.648 8.48 10 9.5 10h4a1.5 1.5 0 0 1 1.5 1.5v7a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 18.5v-9zM2.19 4a1 1 0 0 0-.996 1.09l.637 7a1 1 0 0 0 .995.91h10.348a1 1 0 0 0 .995-.91l.637-7A1 1 0 0 0 13.81 4H2.19z"/>
            </svg>
            <!------------------------------------------------->
            @elseif($doc->type=="fichier Word")
          
            <!-------------------word icon -------------------->
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-word-fill" viewBox="0 0 16 16">
              <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0zM9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1zM5.485 6.879l1.036 4.144.997-3.655a.5.5 0 0 1 .964 0l.997 3.655 1.036-4.144a.5.5 0 0 1 .97.242l-1.5 6a.5.5 0 0 1-.967.01L8 9.402l-1.018 3.73a.5.5 0 0 1-.967-.01l-1.5-6a.5.5 0 1 1 .97-.242z"/>
            </svg>
            <!------------------------------------------------->
            @elseif($doc->type=="fichier Excel")
            <!-------------------- excel icon ------------------>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-excel-fill" viewBox="0 0 16 16">
              <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0zM9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1zM5.884 6.68 8 9.219l2.116-2.54a.5.5 0 1 1 .768.641L8.651 10l2.233 2.68a.5.5 0 0 1-.768.64L8 10.781l-2.116 2.54a.5.5 0 0 1-.768-.641L7.349 10 5.116 7.32a.5.5 0 1 1 .768-.64z"/>
            </svg>
            <!--------------------------------------------------->
            @elseif($doc->type=="Fichier exécutable")

            <!------------------ exe icon --------------------------->
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filetype-exe" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5ZM2.575 15.202H.785v-1.073H2.47v-.606H.785v-1.025h1.79v-.648H0v3.999h2.575v-.647ZM6.31 11.85h-.893l-.823 1.439h-.036l-.832-1.439h-.931l1.227 1.983-1.239 2.016h.861l.853-1.415h.035l.85 1.415h.908l-1.254-1.992L6.31 11.85Zm1.025 3.352h1.79v.647H6.548V11.85h2.576v.648h-1.79v1.025h1.684v.606H7.334v1.073Z"/>
            </svg>
            <!-------------------------------------------------------->
            @elseif($doc->type=="Fichier HTML")
            <!------------------------ HTML icon  -------------------->
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filetype-html" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M14 4.5V11h-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5Zm-9.736 7.35v3.999h-.791v-1.714H1.79v1.714H1V11.85h.791v1.626h1.682V11.85h.79Zm2.251.662v3.337h-.794v-3.337H4.588v-.662h3.064v.662H6.515Zm2.176 3.337v-2.66h.038l.952 2.159h.516l.946-2.16h.038v2.661h.715V11.85h-.8l-1.14 2.596H9.93L8.79 11.85h-.805v3.999h.706Zm4.71-.674h1.696v.674H12.61V11.85h.79v3.325Z"/>
            </svg>
            <!-------------------------------------------------------->
            @elseif($doc->type=="Fichier image")
             <!------------------------ image icon  -------------------->
             <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-image" viewBox="0 0 16 16">
              <path d="M6.502 7a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/>
              <path d="M14 14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5V14zM4 1a1 1 0 0 0-1 1v10l2.224-2.224a.5.5 0 0 1 .61-.075L8 11l2.157-3.02a.5.5 0 0 1 .76-.063L13 10V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4z"/>
             </svg>
            <!-------------------------------------------------------->
            @elseif($doc->type=="Fichier PowerPoint")
             <!------------------------ ppt icon  -------------------->
             <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-ppt" viewBox="0 0 16 16">
              <path d="M7 5.5a1 1 0 0 0-1 1V13a.5.5 0 0 0 1 0v-2h1.188a2.75 2.75 0 0 0 0-5.5H7zM8.188 10H7V6.5h1.188a1.75 1.75 0 1 1 0 3.5z"/>
              <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/>
             </svg>
            <!-------------------------------------------------------->
            @elseif($doc->type=="Fichier texte")
             <!------------------------ txt icon  -------------------->
             <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filetype-txt" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2h-2v-1h2a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5ZM1.928 15.849v-3.337h1.136v-.662H0v.662h1.134v3.337h.794Zm4.689-3.999h-.894L4.9 13.289h-.035l-.832-1.439h-.932l1.228 1.983-1.24 2.016h.862l.853-1.415h.035l.85 1.415h.907l-1.253-1.992 1.274-2.007Zm1.93.662v3.337h-.794v-3.337H6.619v-.662h3.064v.662H8.546Z"/>
             </svg>
            <!-------------------------------------------------------->
            @else
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark" viewBox="0 0 16 16">
              <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/>
            </svg>
            @endif

            <!-------------------------------------------------->




            {{$doc->nom}}</td>
          <td style="text-align: left">{{$doc->type}}</td>
          <td style="text-align: left">{{$doc->taille}} ko</td>
          <td>
            {{-- Utilisation de la vue partielle moderne pour les actions --}}
            @include('partials.document-actions', ['document' => $doc])
          </td>
        </tr>

        <!------------------------------------------------>

        
        @endif
        @endisset
        @endforeach
        @endisset


        
        <!------------------------------- end Affichage des Documents---------------------------------->













        
       <!------------------------ Affichage des documents--------------------------->
       @isset($documentsInFolder)
  
       @foreach ($documentsInFolder as $doc)
       
       <tr>
        
         <td style="text-align: left">
           
           @if($doc->type=="Fichier PDF")
           <!------------------   pdf icon ------------------->
           <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filetype-pdf" viewBox="0 0 16 16">
             <path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5ZM1.6 11.85H0v3.999h.791v-1.342h.803c.287 0 .531-.057.732-.173.203-.117.358-.275.463-.474a1.42 1.42 0 0 0 .161-.677c0-.25-.053-.476-.158-.677a1.176 1.176 0 0 0-.46-.477c-.2-.12-.443-.179-.732-.179Zm.545 1.333a.795.795 0 0 1-.085.38.574.574 0 0 1-.238.241.794.794 0 0 1-.375.082H.788V12.48h.66c.218 0 .389.06.512.181.123.122.185.296.185.522Zm1.217-1.333v3.999h1.46c.958 0 1.76-.56 2.311-1.184C7.985 10.648 8.48 10 9.5 10h4a1.5 1.5 0 0 1 1.5 1.5v7a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 18.5v-9zM2.19 4a1 1 0 0 0-.996 1.09l.637 7a1 1 0 0 0 .995.91h10.348a1 1 0 0 0 .995-.91l.637-7A1 1 0 0 0 13.81 4H2.19z"/>
           </svg>
           <!------------------------------------------------->
           @elseif($doc->type=="fichier Word")
         
           <!-------------------word icon -------------------->
           <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-word-fill" viewBox="0 0 16 16">
             <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0zM9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1zM5.485 6.879l1.036 4.144.997-3.655a.5.5 0 0 1 .964 0l.997 3.655 1.036-4.144a.5.5 0 0 1 .97.242l-1.5 6a.5.5 0 0 1-.967.01L8 9.402l-1.018 3.73a.5.5 0 0 1-.967-.01l-1.5-6a.5.5 0 1 1 .97-.242z"/>
           </svg>
           <!------------------------------------------------->
           @elseif($doc->type=="fichier Excel")
           <!-------------------- excel icon ------------------>
           <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-excel-fill" viewBox="0 0 16 16">
             <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0zM9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1zM5.884 6.68 8 9.219l2.116-2.54a.5.5 0 1 1 .768.641L8.651 10l2.233 2.68a.5.5 0 0 1-.768.64L8 10.781l-2.116 2.54a.5.5 0 0 1-.768-.641L7.349 10 5.116 7.32a.5.5 0 1 1 .768-.64z"/>
           </svg>
           <!--------------------------------------------------->
           @elseif($doc->type=="Fichier exécutable")

           <!------------------ exe icon --------------------------->
           <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filetype-exe" viewBox="0 0 16 16">
             <path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5ZM2.575 15.202H.785v-1.073H2.47v-.606H.785v-1.025h1.79v-.648H0v3.999h2.575v-.647ZM6.31 11.85h-.893l-.823 1.439h-.036l-.832-1.439h-.931l1.227 1.983-1.239 2.016h.861l.853-1.415h.035l.85 1.415h.908l-1.254-1.992L6.31 11.85Zm1.025 3.352h1.79v.647H6.548V11.85h2.576v.648h-1.79v1.025h1.684v.606H7.334v1.073Z"/>
           </svg>
           <!-------------------------------------------------------->
           @elseif($doc->type=="Fichier HTML")
           <!------------------------ HTML icon  -------------------->
           <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filetype-html" viewBox="0 0 16 16">
             <path fill-rule="evenodd" d="M14 4.5V11h-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5Zm-9.736 7.35v3.999h-.791v-1.714H1.79v1.714H1V11.85h.791v1.626h1.682V11.85h.79Zm2.251.662v3.337h-.794v-3.337H4.588v-.662h3.064v.662H6.515Zm2.176 3.337v-2.66h.038l.952 2.159h.516l.946-2.16h.038v2.661h.715V11.85h-.8l-1.14 2.596H9.93L8.79 11.85h-.805v3.999h.706Zm4.71-.674h1.696v.674H12.61V11.85h.79v3.325Z"/>
           </svg>
           <!-------------------------------------------------------->
           @elseif($doc->type=="Fichier image")
            <!------------------------ image icon  -------------------->
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-image" viewBox="0 0 16 16">
             <path d="M6.502 7a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/>
             <path d="M14 14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5V14zM4 1a1 1 0 0 0-1 1v10l2.224-2.224a.5.5 0 0 1 .61-.075L8 11l2.157-3.02a.5.5 0 0 1 .76-.063L13 10V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4z"/>
            </svg>
           <!-------------------------------------------------------->
           @elseif($doc->type=="Fichier PowerPoint")
            <!------------------------ ppt icon  -------------------->
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-ppt" viewBox="0 0 16 16">
             <path d="M7 5.5a1 1 0 0 0-1 1V13a.5.5 0 0 0 1 0v-2h1.188a2.75 2.75 0 0 0 0-5.5H7zM8.188 10H7V6.5h1.188a1.75 1.75 0 1 1 0 3.5z"/>
              <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/>
             </svg>
            <!-------------------------------------------------------->
            @elseif($doc->type=="Fichier texte")
            <!------------------------ txt icon  -------------------->
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filetype-txt" viewBox="0 0 16 16">
             <path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2h-2v-1h2a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5ZM1.928 15.849v-3.337h1.136v-.662H0v.662h1.134v3.337h.794Zm4.689-3.999h-.894L4.9 13.289h-.035l-.832-1.439h-.932l1.228 1.983-1.24 2.016h.862l.853-1.415h.035l.85 1.415h.907l-1.253-1.992 1.274-2.007Zm1.93.662v3.337h-.794v-3.337H6.619v-.662h3.064v.662H8.546Z"/>
             </svg>
            <!-------------------------------------------------------->
            @else
           
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark" viewBox="0 0 16 16">
              <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/>
            </svg>
            @endif

            <!-------------------------------------------------->




            {{$doc->nom}}</td>
          <td style="text-align: left">{{$doc->type}}</td>
          <td style="text-align: left">{{$doc->taille}} ko</td>
          <td>
          @if($doc->user_id==Auth::user()->id || Auth::user()->est_admin==1)
           <!--   Supprimer document-->
           <button type="button" class="btn btn-danger" onclick="window.location.href='/delete/{{ $doc->id }}'">
                 
             <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
               <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
               <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
             </svg>

         </button>
        @endif
         @if($doc->visibility=="public" || $doc->user_id==Auth::user()->id || Auth::user()->est_admin==1)
         <!------------Télécharger un Document -->
         <button type="button" class="btn btn-success"   onclick="window.location.href='/download/{{ $doc->id }}'"> 
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
            <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
          </svg>
        </button>
        @endif

         @if(!($doc->visibility=="public" || $doc->user_id==Auth::user()->id || Auth::user()->est_admin==1))
        <!------------ Demande d'accès a un Document -->
        @isset($demandes)
        
        @if($demandes->where('document_id',$doc->id)->where('emetteur_id',Auth::user()->id)->isEmpty())
  
        <button type="button" title="demander l'accès" class="btn btn-warning demande" name="{{$doc->id}}_{{Auth::user()->id}}" >
          <svg  xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-send-plus-fill" viewBox="0 0 16 16">
            <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 1.59 2.498C8 14 8 13 8 12.5a4.5 4.5 0 0 1 5.026-4.47L15.964.686Zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471-.47 1.178Z"/>
            <path d="M16 12.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Zm-3.5-2a.5.5 0 0 0-.5.5v1h-1a.5.5 0 0 0 0 1h1v1a.5.5 0 0 0 1 0v-1h1a.5.5 0 0 0 0-1h-1v-1a.5.5 0 0 0-.5-.5Z"/>
          </svg>
         </button>
        @elseif($demandes->where('document_id',$doc->id)->where('emetteur_id',Auth::user()->id)->where('etat',1)->isNotEmpty())
         <!------------Télécharger un Document -->
         <button type="button" class="btn btn-success"   onclick="window.location.href='/download/{{ $doc->id }}'"> 
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
            <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
          </svg>
        </button>
        @elseif($demandes->where('document_id',$doc->id)->where('emetteur_id',Auth::user()->id)->where('etat',2)->isNotEmpty())
         <!------------Demande refusé -->
         <button type="button" class="btn" title="votre demande a été refusé"  style="background-color:rgb(0, 0, 0)" > 
          <svg style="color:white" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-lock2" viewBox="0 0 16 16">
            <path d="M8 5a1 1 0 0 1 1 1v1H7V6a1 1 0 0 1 1-1zm2 2.076V6a2 2 0 1 0-4 0v1.076c-.54.166-1 .597-1 1.224v2.4c0 .816.781 1.3 1.5 1.3h3c.719 0 1.5-.484 1.5-1.3V8.3c0-.627-.46-1.058-1-1.224z"/>
            <path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/>
          </svg>
        </button>
        @else
        <button style="background-color:rgb(97, 76, 37)" type="button" title="annuler la demande" class="btn btn-warning annuler"  name="{{$doc->id}}_{{Auth::user()->id}}" >
          <svg style="color:yellow" xmlns="http://www.w3.org/2000/svg" width="19" height="19" fill="currentColor" class="bi bi-stopwatch-fill" viewBox="0 0 16 16">
            <path d="M6.5 0a.5.5 0 0 0 0 1H7v1.07A7.001 7.001 0 0 0 8 16a7 7 0 0 0 5.29-11.584.531.531 0 0 0 .013-.012l.354-.354.353.354a.5.5 0 1 0 .707-.707l-1.414-1.415a.5.5 0 1 0-.707.707l.354.354-.354.354a.717.717 0 0 0-.012.012A6.973 6.973 0 0 0 9 2.071V1h.5a.5.5 0 0 0 0-1h-3zm2 5.6V9a.5.5 0 0 1-.5.5H4.5a.5.5 0 0 1 0-1h3V5.6a.5.5 0 1 1 1 0z"/>
          </svg>
         </button>
        @endif 
        @endisset
       @endif



          <!--    details d'un Document -->
          <button type="button" title="voire plus" class="btn btn-info detail"  name="{{$doc->id}}">
           <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-square" viewBox="0 0 16 16">
             <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
             <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
           </svg>
         </button>
          
      

       </td>
       </tr>
   
       @endforeach
       @endisset
       <!------------------------------- end Affichage des Documents In Folder---------------------------------->

      </tbody>
    </table>
    @isset($documents)
    <div>
     {{ $documents->links('pagination::bootstrap-4') }}
    </div>
   @endisset
  </div>

 
</div>
</div>
  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>



@endSection

{{-- Inclusion des scripts pour les interactions documentaires --}}
@include('partials.document-scripts')