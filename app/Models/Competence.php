<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Competence extends Model
{
    protected $fillable = [
        'nom',
        'categorie',
    ];

    public function profils(){
        return $this->belongsToMany(Profil::class,'profil_competence')->withPivot('niveau');
    }
}
