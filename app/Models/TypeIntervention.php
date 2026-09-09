<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeIntervention extends Model
{
    protected $fillable = ['libelle'];

    /**
     * Un type d'intervention regroupe plusieurs interventions.
     */
    public function interventions()
    {
        return $this->hasMany(Intervention::class);
    }
}
