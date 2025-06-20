<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Section;
use App\Models\User;
use App\Traits\Traceable;

class Document extends Model
{
    use HasFactory, Traceable;protected $fillable = [
        'nom',
        'chemin',
        'taille',
        'type',
        'logo_path',
        'content',
        'user_id',
        'dossier_id',
        'section_id',
        'entreprise_id',
        'visibility',
        'is_archived',
        'archived_at',
        'archive_path',
        'needs_update',
        'status',
        'client_name',
        'dossier_number'
    ];

    protected $casts = [
        'taille' => 'decimal:2',
        'is_archived' => 'boolean',
        'needs_update' => 'boolean',
        'archived_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Méthodes pour l'archivage selon le cahier des charges
    public function archive()
    {
        $this->update([
            'is_archived' => true,
            'archived_at' => now(),
            'status' => 'archive',
            'archive_path' => 'archives/' . $this->chemin
        ]);
    }

    public function markForUpdate()
    {
        $this->update(['needs_update' => true]);
    }

    // Scope pour la recherche multicritères (cahier des charges)
    public function scopeSearch($query, $term)
    {
        return $query->where('nom', 'LIKE', "%{$term}%")
                    ->orWhere('client_name', 'LIKE', "%{$term}%")
                    ->orWhere('dossier_number', 'LIKE', "%{$term}%")
                    ->orWhere('type', 'LIKE', "%{$term}%");
    }

    public function scopeByClient($query, $client)
    {
        return $query->where('client_name', 'LIKE', "%{$client}%");
    }

    public function scopeByDossier($query, $dossier)
    {
        return $query->where('dossier_number', 'LIKE', "%{$dossier}%");
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('created_at', $date);
    }

    // Accesseur pour formater la taille
    public function getTailleFormattedAttribute()
    {
        return number_format($this->taille, 2, ',', ' ') . ' Ko';
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function section(){
        return $this->belongsTo(Section::class);
    }

    public function dossier(){
        return $this->belongsTo(Dossier::class);
    }
    public function entreprise(){
        return $this->belongsTo(Entreprise::class);
    }
}
