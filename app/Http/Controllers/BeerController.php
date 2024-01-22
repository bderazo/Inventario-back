<?php

namespace App\Http\Controllers;

use App\Models\ArrayExport;
use App\Models\BeerRfid;
use App\Models\Ventas;
use App\Models\Usuario;
use App\Models\Maquina;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;


class BeerController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:api');
    }

    public function crearBeerCode(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'usuario_id' => 'nullable|exists:usuarios,id',
                'cupo_max' => 'nullable',
                'estado' => 'nullable',
                'tipo_usuario' => 'nullable',
                'tipo_sensor' => 'nullable',
                'codigo_sensor' => 'nullable',
                'usuario_registra' => 'exists:usuarios,id',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Error al validar los datos de entrada.',
                    'data' => $validator->errors()
                ], 422);
            } else {
                $tarjeta = new BeerRfid([
                    'usuario_id' => $request->usuario_id,
                    'cupo_max' => $request->cupo_max,
                    'estado' => $request->estado,
                    'tipo_usuario' => $request->tipo_usuario,
                    'tipo_sensor' => $request->tipo_sensor,
                    'codigo_sensor' => $request->codigo_sensor,
                    'usuario_registra' => $request->usuario_registra,
                ]);
                $tarjeta->save();
                return response()->json([
                    'status' => 201,
                    'message' => 'Tarjeta beer creada correctamente.',
                    'data' => $tarjeta
                ], 201);
            }
        } catch (AuthorizationException $th) {
            return response()->json([
                'status' => $th->getCode(),
                'message' => 'No autorizado!.',
                'data' => $th->getMessage()
            ], 401);
        } catch (Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Ocurrio un error!.',
                'data' => $e->getMessage()
            ], 400);
        }
    }

    public function escanearSensor(Request $request)
    {
        try {
                $validator = Validator::make($request->query(), [
                    'id_maquina' => 'required|exists:maquinas,id',
                    'codigo_sensor' => 'required|exists:beer_rfid,codigo_sensor',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => 422,
                        'message' => 'Error al validar los datos de entrada.',
                        'data' => $validator->errors()
                    ], 422);
                }else{
                    $maquina = Maquina::where('id', $request->id_maquina)->first();
                    $sensor = BeerRfid::where('codigo_sensor', $request->codigo_sensor)->first();
                    if($maquina){
                        if($sensor){
                            if(intval($maquina->estado) === 1){
                                if(intval($sensor->estado) === 1){
                                    if($sensor->cupo_max < $maquina->cantidad){
                                        return response()->json([
                                            'status' => 202,
                                            'message' => 'Sensor habilitado.',
                                            'data' => $sensor
                                        ], 202);
                                    }else{
                                        return response()->json([
                                            'status' => 203,
                                            'message' => 'Maquina habilitada.',
                                            'data' => $maquina
                                        ], 203);
                                    }
                                }elseif(intval($sensor->estado) === 0){
                                    $maquina->codigo_sensor = $sensor->codigo_sensor;
                                    $maquina->save();
                                    return response()->json([
                                        'status' => 200,
                                        'message' => 'Alerta: Sensor enviada.',
                                        'data' => $maquina
                                    ], 200);
                                }
                            }else{
                                return response()->json([
                                    'status' => 201,
                                    'message' => 'Maquina deshabilitada.',
                                    'data' => false
                                ], 201);
                            }
                        }else{
                            return response()->json([
                                'status' => 404,
                                'message' => 'Sensor no encontrado.',
                                'data' => null
                            ], 404);
                        }
                    }else{
                        return response()->json([
                            'status' => 404,
                            'message' => 'Maquina no encontrada.',
                            'data' => null
                        ], 404);
                    }
                }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => $th->getCode(),
                'message' => 'Ocurrio un error!.',
                'data' => $th->getMessage()
            ], 400);
        }
    }

    public function asignarTarjeta(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'usuario_id' => 'required|exists:usuarios,id',
                'codigo_sensor' => 'required|exists:beer_rfid,codigo_sensor',
                'usuario_registra' => 'required|exists:usuarios,id'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Error al validar los datos de entrada.',
                    'data' => $validator->errors()
                ], 422);
            } else {
                $registro = BeerRfid::where('codigo_sensor', $request->codigo_sensor)->first();

                if ($registro) {
                    $usuario = Usuario::where('id', $request->usuario_id)->first();
                    if ($usuario) {
                        // Asigna el ID del usuario a la tarjeta
                        $registro->usuario_id = $usuario->id;
                        $registro->cupo_max = 4;
                        $registro->estado = 1;
                        $registro->tipo_usuario = 'Gold';
                        $registro->usuario_registra = $request->usuario_registra;
                        // Guarda la tarjeta en la base de datos con el usuario vinculado
                        $registro->save();

                        return response()->json([
                            'status' => 201,
                            'message' => 'Usuario vinculado a la tarjeta exitosamente.',
                            'data' => $registro,
                        ], 201);
                    } else {
                        return response()->json([
                            'status' => 404,
                            'message' => 'Usuario no encontrado.',
                        ], 404);
                    }
                } else {
                    return response()->json([
                        'status' => 404,
                        'message' => 'Tarjeta no encontrada.',
                    ], 404);
                }
            }
        } catch (AuthorizationException $th) {
            return response()->json([
                'status' => $th->getCode(),
                'message' => 'No autorizado!.',
                'data' => $th->getMessage()
            ], 401);
        } catch (Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Ocurrio un error!.',
                'data' => $e->getMessage()
            ], 400);
        }
    }

    public function limpiarTarjeta(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'codigo_sensor' => 'required|exists:beer_rfid,codigo_sensor',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Error al validar los datos de entrada.',
                    'data' => $validator->errors()
                ], 422);
            } else {
                $registro = BeerRfid::where('codigo_sensor', $request->codigo_sensor)->first();
                if ($registro) {
                        // Asigna el ID del usuario a la tarjeta
                        $registro->usuario_id = null;
                        $registro->cupo_max = null;
                        $registro->estado = 0;
                        $registro->tipo_usuario = null;
                        $registro->usuario_registra = null;
                        // Guarda la tarjeta en la base de datos con el usuario vinculado
                        $registro->save();
                        return response()->json([
                            'status' => 201,
                            'message' => 'Sensor vaciado con éxito.',
                            'data' => $registro,
                        ], 201);

                } else {
                    return response()->json([
                        'status' => 404,
                        'message' => 'Sensor no encontrado.',
                    ], 404);
                }
            }
        } catch (AuthorizationException $th) {
            return response()->json([
                'status' => $th->getCode(),
                'message' => 'No autorizado!.',
                'data' => $th->getMessage()
            ], 401);
        } catch (Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Ocurrio un error!.',
                'data' => $e->getMessage()
            ], 400);
        }
    }

    public function listadoTarjetas()
    {
        try {
            // Obtiene todos los registros de la tabla beer_rfid
            $tarjetas = BeerRfid::with(['usuario', 'usuario_registra'])->get();
            return ($tarjetas->count() > 0) ?
                response()->json([
                    'status' => 200,
                    'message' => 'Listado de tarjetas.',
                    'data' => $tarjetas
                ], 200) :
                response()->json([
                    'status' => 201,
                    'message' => 'No existen tarjetas.',
                    'data' => null
                ], 200);
        } catch (AuthorizationException $th) {
            return response()->json([
                'status' => $th->getCode(),
                'message' => 'No autorizado!.',
                'data' => $th->getMessage()
            ], 401);
        } catch (Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Ocurrió un error!.',
                'data' => $e->getMessage()
            ], 400);
        }
    }

    public function listadoUsuariosBeer()
    {
        try {
            $usuarios = Usuario::where('rol', 'BEER')->get();
            // Obtiene todos los registros de la tabla beer_rfid
            return ($usuarios->count() > 0) ?
                response()->json([
                    'status' => 200,
                    'message' => 'Listado de usuarios Beer.',
                    'data' => $usuarios
                ], 200) :
                response()->json([
                    'status' => 201,
                    'message' => 'No existen usuarios Beer.',
                    'data' => null
                ], 200);
        } catch (AuthorizationException $th) {
            return response()->json([
                'status' => $th->getCode(),
                'message' => 'No autorizado!.',
                'data' => $th->getMessage()
            ], 401);
        } catch (Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Ocurrió un error!.',
                'data' => $e->getMessage()
            ], 400);
        }
    }

    public function listadoMaquinas()
    {
        try {
            // Obtiene todos los registros de la tabla maquinas
            $maquinas = Maquina::all();
            return ($maquinas->count() > 0) ?
                response()->json([
                    'status' => 200,
                    'message' => 'Listado de maquinas.',
                    'data' => $maquinas
                ], 200) :
                response()->json([
                    'status' => 201,
                    'message' => 'No existen maquinas.',
                    'data' => null
                ], 200);
        } catch (AuthorizationException $th) {
            return response()->json([
                'status' => $th->getCode(),
                'message' => 'No autorizado!.',
                'data' => $th->getMessage()
            ], 401);
        } catch (Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Ocurrió un error!.',
                'data' => $e->getMessage()
            ], 400);
        }
    }

    public function activarMaquina(Request $request)
    {
        try {
            $validator = Validator::make($request->query(), [
                'id_maquina' => 'required|exists:maquinas,id',
                'estado' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Error al validar los datos de entrada.',
                    'data' => $validator->errors()
                ], 422);
            } else {
                $maquina = Maquina::where('id', $request->id_maquina)->first();

                if ($maquina) {
                        // Asigna el ID del usuario a la tarjeta
                        $maquina->estado = $request->estado;
                        // Guarda la tarjeta en la base de datos con el usuario vinculado
                        $maquina->save();

                        return response()->json([
                            'status' => 201,
                            'message' => 'Maquina encendida.',
                            'data' => $maquina,
                        ], 201);

                } else {
                    return response()->json([
                        'status' => 404,
                        'message' => 'Maquina no encontrada.',
                    ], 404);
                }
            }
        } catch (AuthorizationException $th) {
            return response()->json([
                'status' => $th->getCode(),
                'message' => 'No autorizado!.',
                'data' => $th->getMessage()
            ], 401);
        } catch (Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Ocurrio un error!.',
                'data' => $e->getMessage()
            ], 400);
        }
    }

    public function verMaquina(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_maquina' => 'required|exists:maquinas,id',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Error al validar los datos de entrada.',
                    'data' => $validator->errors()
                ], 422);
            } else {
                $maquina = Maquina::where('id', $request->id_maquina)->first();
                if ($maquina) {
                        return response()->json([
                            'status' => 201,
                            'message' => 'Maquina encontrada.',
                            'data' => $maquina,
                        ], 201);

                } else {
                    return response()->json([
                        'status' => 404,
                        'message' => 'Maquina no encontrada.',
                    ], 404);
                }
            }
        } catch (AuthorizationException $th) {
            return response()->json([
                'status' => $th->getCode(),
                'message' => 'No autorizado!.',
                'data' => $th->getMessage()
            ], 401);
        } catch (Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Ocurrio un error!.',
                'data' => $e->getMessage()
            ], 400);
        }
    }

    public function borrarSensorMaquina(Request $request)
    {
        try {
                $validator = Validator::make($request->all(), [
                    'codigo_sensor' => 'required|exists:beer_rfid,codigo_sensor',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => 422,
                        'message' => 'Error al validar los datos de entrada.',
                        'data' => $validator->errors()
                    ], 422);
                }else{
                    $maquina = Maquina::where('codigo_sensor', $request->codigo_sensor)->first();
                    if($maquina->codigo_sensor){
                        $maquina->codigo_sensor = '';
                        $maquina->save();
                        return response()->json([
                            'status' => 200,
                            'message' => 'Sensor borrado.',
                            'data' => $maquina
                        ], 200);
                    }else{
                        return response()->json([
                            'status' => 404,
                            'message' => 'Maquina no encontrada.',
                            'data' => null
                        ], 404);
                    }
                }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => $th->getCode(),
                'message' => 'Ocurrio un error!.',
                'data' => $th->getMessage()
            ], 400);
        }
    }

    public function crearVenta(Request $request)
    {
        try {
            $validator = Validator::make($request->query(), [
                'id_beer' => 'required|exists:beer_rfid,codigo_sensor',
                'total' => 'required',
                'id_maquina' => 'required|exists:maquinas,id',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Error al validar los datos de entrada.',
                    'data' => $validator->errors()
                ], 422);
            } else {
                $sensor = BeerRfid::where('codigo_sensor', $request->id_beer)->first();
                $maquina = Maquina::where('id', $request->id_maquina)->first();
                    if($sensor->usuario_id && $maquina){
                        $sensor->cupo_max = round($sensor->cupo_max, 2) - round($request->total, 2);
                        $sensor->save();
                        $maquina->cantidad = round($maquina->cantidad, 2) - round($request->total, 2);
                        $maquina->save();
                        $tarjeta = new Ventas([
                            'id_beer' => $sensor->id,
                            'total' => round($request->total, 2),
                            'precio' => round($maquina->precio, 2) * round($request->total, 2),
                            'id_maquina' => $request->id_maquina,
                            'estado' => 0,
                        ]);
                        $tarjeta->save();
                        return response()->json([
                            'status' => 201,
                            'message' => 'Venta beer creada correctamente.',
                            'data' => $tarjeta
                        ], 201);
                    }else{
                        return response()->json([
                            'status' => 404,
                            'message' => 'Tarjeta no asignada o maquina no encontrada.',
                            'data' => null
                        ], 404);
                    }

            }
        } catch (AuthorizationException $th) {
            return response()->json([
                'status' => $th->getCode(),
                'message' => 'No autorizado!.',
                'data' => $th->getMessage()
            ], 401);
        } catch (Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Ocurrio un error!.',
                'data' => $e->getMessage()
            ], 400);
        }
    }

    public function verVentas(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'codigo_sensor' => 'required|exists:beer_rfid,codigo_sensor',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Error al validar los datos de entrada.',
                    'data' => $validator->errors()
                ], 422);
            } else {
                $sensor = BeerRfid::where('codigo_sensor', $request->codigo_sensor)->first();
                if ($sensor) {
                    // Verifica si la relación 'venta' está cargada
                    if ($sensor->relationLoaded('ventas')) {
                        $ventas = $sensor->venta->where('estado', 0)->load('maquina', 'beer.usuario')->values();
                    } else {
                        // Si no está cargada, carga la relación 'venta' y filtra por estado
                        $ventas = $sensor->load('ventas.maquina', 'ventas.beer.usuario')->ventas->where('estado', 0)->values();
                    }

                    return response()->json([
                        'status' => 201,
                        'message' => 'Sensor encontrado.',
                        'data' => [
                            'ventas' => $ventas,
                        ],
                    ], 201);
                } else {
                    return response()->json([
                        'status' => 404,
                        'message' => 'Sensor no encontrada.',
                    ], 404);
                }
            }
        } catch (AuthorizationException $th) {
            return response()->json([
                'status' => $th->getCode(),
                'message' => 'No autorizado!.',
                'data' => $th->getMessage()
            ], 401);
        } catch (Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Ocurrio un error!.',
                'data' => $e->getMessage()
            ], 400);
        }
    }

    public function pagarVentas(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'codigo_sensor' => 'required|exists:beer_rfid,codigo_sensor',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Error al validar los datos de entrada.',
                    'data' => $validator->errors()
                ], 422);
            } else {
                $sensor = BeerRfid::where('codigo_sensor', $request->codigo_sensor)->first();
                if ($sensor) {
                    // Verifica si la relación 'venta' está cargada
                    if ($sensor->relationLoaded('ventas')) {
                        $ventas = $sensor->venta->where('estado', 0)->load('maquina', 'beer.usuario')->values();
                    } else {
                        // Si no está cargada, carga la relación 'venta' y filtra por estado
                        $ventas = $sensor->load('ventas.maquina', 'ventas.beer.usuario')->ventas->where('estado', 0)->values();
                    }
                    // Actualizar el estado de todas las ventas
                    foreach ($ventas as $venta) {
                        $venta->estado = 1; // Cambia el estado según tus necesidades
                        $venta->usuario_registra = $sensor->usuario_registra;
                        $venta->save();
                    }
                    // Verifica si todas las ventas tienen estado 1
                    $todasVentasEstadoUno = $ventas->every(function ($venta) {
                        return $venta->estado == 1;
                    });

                    if ($todasVentasEstadoUno) {
                        $this->limpiarTarjeta($request);
                    }
                    return response()->json([
                        'status' => 201,
                        'message' => 'Ventas pagadas.',
                        'data' => [
                            'ventas' => $ventas,
                        ],
                    ], 201);
                } else {
                    return response()->json([
                        'status' => 404,
                        'message' => 'Sensor no encontrado.',
                    ], 404);
                }
            }
        } catch (AuthorizationException $th) {
            return response()->json([
                'status' => $th->getCode(),
                'message' => 'No autorizado!.',
                'data' => $th->getMessage()
            ], 401);
        } catch (Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Ocurrio un error!.',
                'data' => $e->getMessage()
            ], 400);
        }
    }
}
