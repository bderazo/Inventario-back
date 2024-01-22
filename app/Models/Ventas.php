<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
//clase ventas para cerveza, se relaciona con la tabla beer_rfid y maquina_ventas 
class Ventas extends Model
{
    use HasFactory,Uuid;
    protected $table = 'ventas';
    public $incrementing = false;
    protected $keyType = 'uuid';
    protected $fillable = [
        'id_beer',
        'total',
        'precio',
        'id_maquina',
        'estado',
    ];


    public function beer(): BelongsTo
    {
        return $this->belongsTo(BeerRfid::class, 'id_beer');
    }

    public function maquina(): BelongsTo
    {
        return $this->belongsTo(Maquina::class, 'id_maquina');
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