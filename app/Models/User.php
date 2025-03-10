<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo', // Añadir este campo
    ];

    /**
     * Los atributos que deberían ser ocultos para las matrices.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Los atributos que deberían ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Los atributos que deben ser añadidos automáticamente al serializar el modelo.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Obtener la URL completa de la foto de perfil.
     *
     * @return string|null
     */
    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo ? asset('storage/' . $this->profile_photo) : null;
    }
}
