<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /** Redirige Eloquent vers la vraie table utilisateurs */
    protected $table = 'utilisateurs';

    protected $fillable = [
        'role_id',
        'structure_id',
        'matricule',
        'nom',
        'prenom',
        'telephone',
        'email',
        'mots_de_passe',
    ];

    protected $hidden = [
        'mots_de_passe',
        'remember_token',
    ];

    // ─── Authentification Laravel ──────────────────────────────────────────────

    /**
     * Indique à Laravel quelle colonne contient le mot de passe haché.
     */
    public function getAuthPassword(): string
    {
        return $this->mots_de_passe;
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    // ─── Relations ─────────────────────────────────────────────────────────────

    /** Un utilisateur a un rôle (Admin DSI, Agent DSI, Responsable de structure) */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /** Un utilisateur peut être rattaché à une structure */
    public function structure()
    {
        return $this->belongsTo(Structure::class);
    }

    // ─── Helpers ───────────────────────────────────────────────────────────────

    /** Renvoie le rôle libellé de l'utilisateur courant */
    public function getRoleLibelleAttribute(): string
    {
        return $this->role?->libelle ?? 'Inconnu';
    }

    /** Vérifie si l'utilisateur est Administrateur DSI */
    public function isAdmin(): bool
    {
        return $this->role?->libelle === 'Administrateur DSI';
    }

    /** Vérifie si l'utilisateur est Agent DSI */
    public function isAgent(): bool
    {
        return $this->role?->libelle === 'Agent DSI';
    }

    /** Vérifie si l'utilisateur est Responsable de structure */
    public function isResponsable(): bool
    {
        return $this->role?->libelle === 'Responsable de structure';
    }
}
