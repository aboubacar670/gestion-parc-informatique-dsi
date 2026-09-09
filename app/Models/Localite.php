<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Localite extends Model
{
    protected $fillable = [
        'code_localite',
        'nom',
        'type_localite_id',
        'parent_id',
        'longitude',
        'latitude',
    ];

    // ─── Relations récursives ──────────────────────────────────────────────────

    /** Le parent de cette localité (nullable) */
    public function parent()
    {
        return $this->belongsTo(Localite::class, 'parent_id');
    }

    /** Les localités enfants de cette localité */
    public function enfants()
    {
        return $this->hasMany(Localite::class, 'parent_id');
    }

    // ─── Relations directes ────────────────────────────────────────────────────

    /** La localité appartient à un type (Région, Province, Commune…) */
    public function typeLocalite()
    {
        return $this->belongsTo(TypeLocalite::class);
    }

    /** Une localité peut abriter plusieurs structures */
    public function structures()
    {
        return $this->hasMany(Structure::class);
    }
}
