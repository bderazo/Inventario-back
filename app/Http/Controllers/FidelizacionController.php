<?php

namespace App\Http\Controllers;

use App\Models\Promociones;
use App\Models\Puntos;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class FidelizacionController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:api');
    }


    public function crearPuntos(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cantidad' => 'required',
                'user_tarjeta_id' => 'required|exists:user_tarjeta,id',
                'user_registra' => 'required|exists:user_tarjeta,id',
                'detalle' => 'nullable',
                'estado' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Error al validar los datos de entrada.',
                    'data' => $validator->errors()
                ], 422);
            } else {
                $puntos = Puntos::create([
                    'cantidad' => $request->cantidad,
                    'user_tarjeta_id' => $request->user_tarjeta_id,
                    'user_registra' => $request->user_registra,
                    'detalle' => $request->detalle,
                    'estado' => $request->estado
                ]);
                return response()->json([
                    'status' => 201,
                    'message' => 'Puntos almacenados correctamente.',
                    'data' => $puntos
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

    public function crearPromociones(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required',
                'puntos' => 'required',
                'imagen' => 'required',
                'descripcion' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Error al validar los datos de entrada.',
                    'data' => $validator->errors()
                ], 422);
            } else {
                $promocion = Promociones::create([
                    'nombre' => $request->nombre,
                    'puntos' => $request->puntos,
                    'imagen' => $request->imagen,
                    'descripcion' => $request->descripcion,
                ]);
                return response()->json([
                    'status' => 201,
                    'message' => 'Promocion almacenada correctamente.',
                    'data' => $promocion
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

    public function listarPromociones()
    {
        try {
            // Recuperar todas las promociones de la base de datos
            $promociones = Promociones::all();

            // Verificar si se encontraron promociones
            if ($promociones->isEmpty()) {
                return response()->json([
                    'status' => 404,
                    'message' => 'No se encontraron promociones.',
                ], 404);
            }

            // Devolver las promociones encontradas
            return response()->json([
                'status' => 200,
                'message' => 'Promociones obtenidas correctamente.',
                'data' => $promociones
            ], 200);
        } catch (Exception $e) {
            // Manejo de excepciones
            return response()->json([
                'status' => $e->getCode() ? $e->getCode() : 500,
                'message' => 'Ocurrió un error al obtener las promociones.',
                'data' => $e->getMessage()
            ], $e->getCode() ? $e->getCode() : 500);
        }
    }

}
