<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $table = 'people';

    protected $fillable = [
        'nom',
        'prenom', 
        'fonction',
        'motif',
        'contact',
        'email'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Scope pour recherche
    public function scopeSearch($query, $term)
    {
        return $query->where('nom', 'LIKE', "%{$term}%")
                    ->orWhere('prenom', 'LIKE', "%{$term}%")
                    ->orWhere('email', 'LIKE', "%{$term}%")
                    ->orWhere('contact', 'LIKE', "%{$term}%");
    }

    // Accesseur pour nom complet
    public function getNomCompletAttribute()
    {
        return $this->prenom . ' ' . $this->nom;
    }
}
