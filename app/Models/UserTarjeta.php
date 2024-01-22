<?php

namespace App\Models;

// use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserTarjeta extends Model
{
    // use HasFactory,Uuid;
    protected $table = 'user_tarjeta';
    public $incrementing = false;
    // protected $keyType = 'uuid';
    protected $fillable = [
        'usuario_id',
        'comercio_id',
        'estado',
        'img_perfil',
        'img_portada',
        'nombre',
        'profesion',
        'empresa',
        'acreditaciones',
        'telefono',
        'direccion',
        'correo',
        'sitio_web',
        'titulo',
        'wallet',
        'clics_realizados',
        'clics_guardar',
        'clics_correo',
        'clics_sitio_web',
        'whatsapp',
        'telegram',
        'clic_referidos',
        'clic_pagados'
    ];


    public function usuario_id(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id');
    }

    public function comercio_id(): BelongsTo
    {
        return $this->belongsTo(Comercio::class, 'id');
    }

    public function socialesTarjeta(): HasMany
    {
        return $this->hasMany(SocialesTarjeta::class);
    }

    public function configuracionesTarjeta(): HasMany
    {
        return $this->hasMany(ConfiguracionesTarjeta::class);
    }

    public function tarjetasComercio(): HasMany
    {
        return $this->hasMany(TarjetasComercio::class);
    }

    public function planTarjetas(): HasMany
    {
        return $this->hasMany(PlanTarjetas::class);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
