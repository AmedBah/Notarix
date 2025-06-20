<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Section;
use App\Models\Demande;
use App\Models\Document;
use App\Models\Entreprise;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */    protected $fillable = [
        'nom',
        'telephone',
        'email',
        'logo_path',
        'est_admin',
        'role', // admin, utilisateur, visiteur
        'permissions', // JSON des permissions spécifiques
        'password',
        'entreprise_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */    protected $casts = [
        'email_verified_at' => 'datetime',
        'permissions' => 'array',
    ];

    // Constantes pour les rôles selon le cahier des charges
    const ROLE_ADMIN = 'admin';
    const ROLE_UTILISATEUR = 'utilisateur';
    const ROLE_VISITEUR = 'visiteur';

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


    public function sections(){
        return $this->hasMany(Section::class);
    }

    public function demandes(){
        return $this->hasMany(Demande::class);
    }

    public function documents(){
        return $this->hasMany(Document::class);
    }

    public function entreprise(){
        return $this->belongsTo(Entreprise::class);
    }

    public function mission(){
        return $this->belongsTo(Mission::class);
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
