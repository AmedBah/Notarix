<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChampActivite extends Model
{
    use HasFactory;

    protected $table = 'champs_activites';

    protected $fillable = [
        'nom',
        'description',
        'actif',
        'ordre',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'ordre' => 'integer',
    ];

    /**
     * Relation avec les dossiers
     */
    public function dossiers()
    {
        return $this->hasMany(Dossier::class);
    }

    /**
     * Relation avec les documents
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Scope pour les champs actifs uniquement
     */
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Scope pour trier par ordre
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('ordre')->orderBy('nom');
    }
}
