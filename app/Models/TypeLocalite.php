<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeLocalite extends Model
{
    protected $fillable = ['libelle'];

    /**
     * Un type de localité regroupe plusieurs localités.
     */
    public function localites()
    {
        return $this->hasMany(Localite::class);
    }
}
