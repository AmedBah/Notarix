<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'categorie',
        'telephone',
        'email',
        'adresse',
        'notes',
        'favori',
    ];

    protected $casts = [
        'favori' => 'boolean',
    ];

    /**
     * Scope pour les contacts favoris
     */
    public function scopeFavori($query)
    {
        return $query->where('favori', true);
    }

    /**
     * Scope pour filtrer par catégorie
     */
    public function scopeCategorie($query, $categorie)
    {
        return $query->where('categorie', $categorie);
    }

    /**
     * Scope pour rechercher dans les contacts
     */
    public function scopeRecherche($query, $terme)
    {
        return $query->where(function($q) use ($terme) {
            $q->where('nom', 'LIKE', "%{$terme}%")
              ->orWhere('telephone', 'LIKE', "%{$terme}%")
              ->orWhere('email', 'LIKE', "%{$terme}%")
              ->orWhere('notes', 'LIKE', "%{$terme}%");
        });
    }

    /**
     * Marquer comme favori
     */
    public function marquerFavori()
    {
        $this->update(['favori' => true]);
    }

    /**
     * Retirer des favoris
     */
    public function retirerFavori()
    {
        $this->update(['favori' => false]);
    }
}
