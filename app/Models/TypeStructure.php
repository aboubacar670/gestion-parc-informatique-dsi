<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeStructure extends Model
{
    protected $fillable = ['libelle'];

    /**
     * Un type de structure peut regrouper plusieurs structures.
     */
    public function structures()
    {
        return $this->hasMany(Structure::class);
    }
}
