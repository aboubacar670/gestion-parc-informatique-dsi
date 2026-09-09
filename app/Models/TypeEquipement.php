<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeEquipement extends Model
{
    protected $fillable = ['libelle'];

    /**
     * Un type d'équipement regroupe plusieurs équipements.
     */
    public function equipements()
    {
        return $this->hasMany(Equipement::class);
    }
}
