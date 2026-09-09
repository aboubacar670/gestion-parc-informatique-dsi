<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatutIncident extends Model
{
    protected $fillable = ['libelle'];

    /**
     * Un statut peut être associé à plusieurs incidents.
     */
    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }
}
