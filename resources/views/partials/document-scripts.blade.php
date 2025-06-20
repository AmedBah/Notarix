{{-- Scripts JavaScript pour les interactions documentaires --}}
<script>
// Configuration CSRF pour les requêtes AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Fonction pour consulter un document
function viewDocument(documentId) {
    // Enregistrer la consultation pour la traçabilité
    $.post('/log-view/' + documentId, function(data) {
        // Ouvrir le document en nouvelle fenêtre
        window.open('/view/' + documentId, '_blank');
    });
}

// Fonction pour demander l'accès à un document
function requestAccess(documentId) {
    if (confirm('Demander l\'accès à ce document ?')) {
        $.ajax({
            url: '/request-access/' + documentId,
            method: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    showNotification('Demande d\'accès envoyée avec succès', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('Erreur lors de l\'envoi de la demande', 'error');
                }
            },
            error: function() {
                showNotification('Erreur lors de l\'envoi de la demande', 'error');
            }
        });
    }
}

// Fonction pour annuler une demande d'accès
function cancelRequest(documentId) {
    if (confirm('Annuler la demande d\'accès ?')) {
        $.ajax({
            url: '/cancel-request/' + documentId,
            method: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    showNotification('Demande d\'accès annulée', 'info');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('Erreur lors de l\'annulation', 'error');
                }
            },
            error: function() {
                showNotification('Erreur lors de l\'annulation', 'error');
            }
        });
    }
}

// Système de notifications toast
function showNotification(message, type = 'info') {
    const types = {
        'success': { icon: '✅', color: '#28a745' },
        'error': { icon: '❌', color: '#dc3545' },
        'warning': { icon: '⚠️', color: '#ffc107' },
        'info': { icon: 'ℹ️', color: '#17a2b8' }
    };
    
    const config = types[type] || types['info'];
    
    // Créer l'élément de notification
    const notification = $(`
        <div class="custom-toast" style="
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            border-left: 4px solid ${config.color};
            padding: 15px 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-radius: 5px;
            z-index: 9999;
            max-width: 400px;
            animation: slideInRight 0.3s ease;
        ">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 18px;">${config.icon}</span>
                <span>${message}</span>
            </div>
        </div>
    `);
    
    // Ajouter au DOM
    $('body').append(notification);
    
    // Supprimer après 3 secondes
    setTimeout(() => {
        notification.fadeOut(300, function() {
            $(this).remove();
        });
    }, 3000);
}

// Animation CSS pour les notifications
$('<style>').text(`
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
`).appendTo('head');

// Gestion des clics sur les demandes d'accès (pour compatibilité avec l'ancien code)
$(document).ready(function() {
    $('.demande').click(function() {
        const ids = $(this).attr('name').split('_');
        const documentId = ids[0];
        requestAccess(documentId);
    });
    
    $('.annuler').click(function() {
        const ids = $(this).attr('name').split('_');
        const documentId = ids[0];
        cancelRequest(documentId);
    });
    
    $('.detail').click(function() {
        const documentId = $(this).attr('name');
        viewDocument(documentId);
    });
});

// Gestion du drag & drop pour l'upload
$(document).ready(function() {
    const dropZone = $('.modal-body');
    
    dropZone.on('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('drag-over');
    });
    
    dropZone.on('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('drag-over');
    });
    
    dropZone.on('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('drag-over');
        
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            $('#formFileMultiple')[0].files = files;
            $('#formFileMultiple').trigger('change');
        }
    });
});

// Style pour le drag & drop
$('<style>').text(`
    .drag-over {
        background-color: rgba(0, 123, 255, 0.1) !important;
        border: 2px dashed #007bff !important;
    }
`).appendTo('head');
</script>
