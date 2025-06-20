<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;/**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'telephone',
        'email',
        'adresse',
        'fonction',
        'motif',
        'type_personne', // client, temoin, expert, autre
        'visibilite', // public, prive
        'logo_path',
        'est_admin',
        'role', // admin, utilisateur, visiteur
        'permissions', // JSON des permissions spécifiques
        'status', // active, inactive
        'password',
        'two_factor_enabled',
        'two_factor_secret'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'permissions' => 'array',
        'two_factor_enabled' => 'boolean',
    ];

    // Constantes pour les rôles selon le cahier des charges
    const ROLE_ADMIN = 'admin';
    const ROLE_UTILISATEUR = 'utilisateur';
    const ROLE_VISITEUR = 'visiteur';

    // Constantes pour les types de personnes
    const TYPE_CLIENT = 'client';
    const TYPE_TEMOIN = 'temoin';
    const TYPE_EXPERT = 'expert';
    const TYPE_AUTRE = 'autre';

    // Constantes pour la visibilité
    const VISIBILITE_PUBLIC = 'public';
    const VISIBILITE_PRIVE = 'prive';

    // Constantes pour les permissions
    const PERMISSION_READ = 'lecture';
    const PERMISSION_WRITE = 'modification';
    const PERMISSION_DELETE = 'suppression';
    const PERMISSION_DOWNLOAD = 'telecharger';

    // Méthodes pour vérifier les permissions
    public function hasPermission($permission)
    {
        if ($this->role === self::ROLE_ADMIN) {
            return true; // Admin a tous les droits
        }
        return in_array($permission, $this->permissions ?? []);
    }

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN || $this->est_admin;
    }

    public function canRead()
    {
        return $this->hasPermission(self::PERMISSION_READ);
    }

    public function canWrite()
    {
        return $this->hasPermission(self::PERMISSION_WRITE);
    }

    public function canDelete()
    {
        return $this->hasPermission(self::PERMISSION_DELETE);
    }

    public function canDownload()
    {
        return $this->hasPermission(self::PERMISSION_DOWNLOAD);
    }

    public function isVisible()
    {
        return $this->visibilite === self::VISIBILITE_PUBLIC || $this->isAdmin();
    }

    public function getFullNameAttribute()
    {
        return $this->prenom . ' ' . $this->nom;
    }    // Relations conformes au CDC
    public function documents()
    {
        return $this->hasMany(\App\Models\Document::class, 'created_by');
    }

    public function dossiers()
    {
        return $this->hasMany(\App\Models\Dossier::class, 'created_by');
    }

    public function activityLogs()
    {
        return $this->hasMany(\App\Models\ActivityLog::class, 'user_id');
    }

    public function searchHistory()
    {
        return $this->hasMany(\App\Models\SearchHistory::class, 'user_id');
    }

    /**
     * Boot function to handle events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->logo_path)) {
                $user->logo_path = 'default/user.png';
            }
        });
    }
}
