<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UsuarioSucursal extends Model
{
    use HasFactory,Uuid;
    protected $table = 'usuario_comercio';
    public $incrementing = false;
    protected $keyType = 'uuid';
    protected $fillable = [
        'usuario_id',
        'comercio_id',
        'rol',
        'estado',
        'registrado_por'
    ];

    public function usuario_id(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id');

    }

    public function comercio_id(): BelongsTo
    {
        return $this->belongsTo(Comercio::class, 'id');

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