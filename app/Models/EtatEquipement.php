<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EtatEquipement extends Model
{
    protected $fillable = ['libelle'];

    /**
     * Un état peut s'appliquer à plusieurs équipements.
     */
    public function equipements()
    {
        return $this->hasMany(Equipement::class);
    }
}