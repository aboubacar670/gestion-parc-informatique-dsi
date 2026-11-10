<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilTechnicien extends Model
{
   protected $fillable = ['libelle'];

public function techniciens()
{
    return $this->hasMany(Technicien::class);
}
}
