<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

// 🔗 import related models
use App\Models\Profil;
use App\Models\Offre;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // 🔗 1 user → 1 profil
    public function profil()
    {
        return $this->hasOne(Profil::class);
    }

    // 🔗 1 user (recruteur) → many offres
    public function offres()
    {
        return $this->hasMany(Offre::class);
    }
}