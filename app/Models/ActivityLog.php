<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_id',
        'action',
        'details',
        'ip_address',
        'user_agent',
        'restaurable'
    ];

    protected $casts = [
        'details' => 'array',
        'restaurable' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Constantes pour les actions
    const ACTION_VIEW = 'view';
    const ACTION_DOWNLOAD = 'download';
    const ACTION_CREATE = 'create';
    const ACTION_MODIFY = 'modify';
    const ACTION_DELETE = 'delete';
    const ACTION_RESTORE = 'restore';
    const ACTION_ARCHIVE = 'archive';
    const ACTION_FINALIZE = 'finalize';
    const ACTION_GENERATE_PDF = 'generate_pdf';

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le document
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Scope pour les actions restaurables
     */
    public function scopeRestorables($query)
    {
        return $query->where('restaurable', true);
    }

    /**
     * Scope pour les actions d'un utilisateur
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope pour les actions sur un document
     */
    public function scopeByDocument($query, $documentId)
    {
        return $query->where('document_id', $documentId);
    }

    /**
     * Scope pour une période donnée
     */
    public function scopeInPeriod($query, $dateDebut, $dateFin)
    {
        return $query->whereBetween('created_at', [$dateDebut, $dateFin]);
    }

    /**
     * Accessor pour formater la date
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d/m/Y H:i:s');
    }

    /**
     * Accessor pour obtenir le nom de l'action
     */
    public function getActionNameAttribute()
    {
        $actions = [
            self::ACTION_VIEW => 'Consultation',
            self::ACTION_DOWNLOAD => 'Téléchargement',
            self::ACTION_CREATE => 'Création',
            self::ACTION_MODIFY => 'Modification',
            self::ACTION_DELETE => 'Suppression',
            self::ACTION_RESTORE => 'Restauration',
            self::ACTION_ARCHIVE => 'Archivage',
            self::ACTION_FINALIZE => 'Finalisation',
            self::ACTION_GENERATE_PDF => 'Génération PDF',
        ];

        return $actions[$this->action] ?? $this->action;
    }
}
