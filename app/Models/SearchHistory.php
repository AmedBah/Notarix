<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchHistory extends Model
{
    use HasFactory;

    protected $table = 'searches_history';

    protected $fillable = [
        'user_id',
        'type',
        'terme_recherche',
        'filtres',
        'nb_resultats',
        'searched_at',
    ];

    protected $casts = [
        'filtres' => 'array',
        'nb_resultats' => 'integer',
        'searched_at' => 'datetime',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope pour un utilisateur spécifique
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope pour un type de recherche
     */
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pour les recherches récentes
     */
    public function scopeRecent($query, $jours = 7)
    {
        return $query->where('searched_at', '>=', now()->subDays($jours));
    }
}
