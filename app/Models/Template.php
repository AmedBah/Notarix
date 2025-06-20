<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'type',
        'categorie',
        'description',
        'fichier_nom',
        'fichier_path',
        'fichier_extension',
        'fichier_taille',
        'nb_telechargements',
        'actif',
    ];

    protected $casts = [
        'fichier_taille' => 'integer',
        'nb_telechargements' => 'integer',
        'actif' => 'boolean',
    ];

    /**
     * Scope pour les templates actifs uniquement
     */
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Scope pour filtrer par type
     */
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pour filtrer par catégorie
     */
    public function scopeCategorie($query, $categorie)
    {
        return $query->where('categorie', $categorie);
    }

    /**
     * Incrémenter le nombre de téléchargements
     */
    public function incrementerTelechargements()
    {
        $this->increment('nb_telechargements');
    }

    /**
     * Obtenir la taille formatée du fichier
     */
    public function getTailleFormateeAttribute()
    {
        $bytes = $this->fichier_taille;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
