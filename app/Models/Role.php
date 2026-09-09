<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['libelle'];

    /**
     * Un rôle est attribué à plusieurs utilisateurs.
     */
    public function utilisateurs()
    {
        return $this->hasMany(User::class);
    }
}
