<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidature extends Model
{
    protected $fillable = [
        'offre_id',
        'profil_id',
        'message',
        'statut',
    ];

    public function offre()
    {
        return $this->belongsTo(Offre::class);
    }

    public function profil()
    {
        return $this->belongsTo(Profil::class);
    }
}
