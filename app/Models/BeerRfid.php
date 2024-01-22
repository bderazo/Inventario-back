<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BeerRfid extends Model
{
    use HasFactory,Uuid;
    protected $table = 'beer_rfid';
    public $incrementing = false;
    protected $keyType = 'uuid';
    protected $fillable = [
        'usuario_id',
        'cupo_max',
        'estado',
        'tipo_usuario',
        'tipo_sensor',
        'codigo_sensor',
        'usuario_registra',
    ];


    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function usuario_registra(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_registra');
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Ventas::class, 'id_beer');
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
