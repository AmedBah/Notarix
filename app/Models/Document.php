<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Traits\Traceable;

class Document extends Model
{
    use HasFactory, Traceable;    protected $fillable = [
        'nom_fichier',
        'path',
        'taille_fichier',
        'type_fichier',
        'type_document', // acte, courrier, piece_justificative, scan
        'template_id',
        'champ_activite_id',
        'user_id',
        'dossier_id',
        'contenu_indexe', // Pour la recherche full-text
        'parties', // JSON des parties concernées (pour les actes)
        'champs_personnalises', // JSON des champs personnalisés
        'statut', // brouillon, finalise, signe, archive
        'archive_status', // manuel, auto, notification
        'scan_available',
        'nb_consultations',
        'derniere_consultation',
        'restaurable',
        'visibility',
        'classement_auto'
    ];    protected $casts = [
        'taille_fichier' => 'integer',
        'parties' => 'array',
        'champs_personnalises' => 'array',
        'classement_auto' => 'array',
        'scan_available' => 'boolean',
        'restaurable' => 'boolean',
        'nb_consultations' => 'integer',
        'derniere_consultation' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Constantes pour les types de documents
    const TYPE_ACTE = 'acte';
    const TYPE_COURRIER = 'courrier';
    const TYPE_PIECE_JUSTIFICATIVE = 'piece_justificative';
    const TYPE_SCAN = 'scan';

    // Constantes pour les statuts
    const STATUT_BROUILLON = 'brouillon';
    const STATUT_FINALISE = 'finalise';
    const STATUT_SIGNE = 'signe';
    const STATUT_ARCHIVE = 'archive';

    // Constantes pour l'archivage
    const ARCHIVE_MANUEL = 'manuel';
    const ARCHIVE_AUTO = 'auto';
    const ARCHIVE_NOTIFICATION = 'notification';    // Méthodes pour l'archivage selon le cahier des charges
    public function archiver($type = 'manuel', $notification = false)
    {
        $this->update([
            'statut' => self::STATUT_ARCHIVE,
            'archive_status' => $type,
        ]);

        if ($notification) {
            $this->envoyerNotificationArchive();
        }

        return $this;
    }

    public function incrementerConsultations()
    {
        $this->increment('nb_consultations');
        $this->update(['derniere_consultation' => now()]);
    }

    public function isArchive()
    {
        return $this->statut === self::STATUT_ARCHIVE;
    }

    public function needsNotification()
    {
        return $this->archive_status === self::ARCHIVE_NOTIFICATION;
    }

    private function envoyerNotificationArchive()
    {
        // Logique de notification à implémenter
    }    // Scope pour la recherche multicritères (cahier des charges)
    public function scopeSearch($query, $term)
    {
        return $query->where('nom_fichier', 'LIKE', "%{$term}%")
                    ->orWhere('contenu_indexe', 'LIKE', "%{$term}%")
                    ->orWhereHas('dossier', function ($q) use ($term) {
                        $q->where('nom_dossier', 'LIKE', "%{$term}%");
                    })
                    ->orWhereHas('user', function ($q) use ($term) {
                        $q->where('nom', 'LIKE', "%{$term}%")
                          ->orWhere('prenom', 'LIKE', "%{$term}%");
                    });
    }

    public function scopeByTypeDocument($query, $type)
    {
        return $query->where('type_document', $type);
    }

    public function scopeByChampActivite($query, $champId)
    {
        return $query->where('champ_activite_id', $champId);
    }

    public function scopeByStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    public function scopeByArchiveStatus($query, $status)
    {
        return $query->where('archive_status', $status);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('created_at', $date);
    }

    public function scopeBetweenDates($query, $dateDebut, $dateFin)
    {
        return $query->whereBetween('created_at', [$dateDebut, $dateFin]);
    }    // Accesseur pour formater la taille
    public function getTailleFormatteeAttribute()
    {
        if ($this->taille_fichier < 1024) {
            return $this->taille_fichier . ' octets';
        } elseif ($this->taille_fichier < 1048576) {
            return round($this->taille_fichier / 1024, 2) . ' Ko';
        } else {
            return round($this->taille_fichier / 1048576, 2) . ' Mo';
        }
    }

    // Relations conformes au CDC
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dossier()
    {
        return $this->belongsTo(Dossier::class);
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function champActivite()
    {
        return $this->belongsTo(ChampActivite::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}
