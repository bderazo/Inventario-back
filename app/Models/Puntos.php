<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Puntos extends Model
{
    use HasFactory, Uuid;
    protected $table = 'puntos';
    public $incrementing = false;
    protected $keyType = 'uuid';
    protected $fillable = [
        'cantidad',
        'user_tarjeta_id',
        'user_registra',
        'detalle',
        'estado',
    ];

    public function user_tarjeta_id(): BelongsTo
    {
        return $this->belongsTo(UserTarjeta::class, 'id');
    }

    public function user_registra(): BelongsTo
    {
        return $this->belongsTo(UserTarjeta::class, 'id');
    }

    public function promocion(): BelongsTo
{
    return $this->belongsTo(Promociones::class, 'detalle');
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
