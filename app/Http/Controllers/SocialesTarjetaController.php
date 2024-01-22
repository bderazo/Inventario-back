<?php

namespace App\Http\Controllers;

use App\Models\SocialesTarjeta;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class SocialesTarjetaController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:api');
    }


    public function crearSocialesTarjeta(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_tarjeta_id' => 'required|exists:user_tarjeta,id',
                'estado' => 'nullable',
                'text_label' => 'nullable',
                'url_label' => 'nullable',
                'tipo_social' => 'nullable',
                'icon_social' => 'nullable',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Error al validar los datos de entrada.',
                    'data' => $validator->errors()
                ], 422);
            } else {
                $sociales = SocialesTarjeta::create([
                    'user_tarjeta_id' => $request->user_tarjeta_id,
                    'estado' => true,
                    'text_label' => $request->text_label,
                    'url_label' => $request->url_label,
                    'tipo_social' => $request->tipo_social,
                    'icon_social' => $request->icon_social,
                ]);
                return response()->json([
                    'status' => 201,
                    'message' => 'Sociales de Tarjeta creado correctamente.',
                    'data' => $sociales
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

    public function actualizarSocialesTarjeta(Request $request, $id)
    {
        try {
            $sociales = SocialesTarjeta::where('id', $id)->first();
            if ($sociales != null) {
                $sociales->update($request->all());
                return response()->json([
                    'status' => 200,
                    'message' => 'Información social de tarjeta actualizada correctamente.',
                    'data' => $sociales
                ], 200);
            } else {
                return response()->json([
                    'status' => 200,
                    'message' => 'No se encontro la informacion social indicada.',
                    'data' => null
                ], 200);
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
    public function encontrarPorUrlLabel(Request $request)
    {
        try {
            $registro = SocialesTarjeta::where('id', $request->id)->first();

            // Verificar si se encontró el registro
            if ($registro) {
                $requestData = [
                    'clics_realizados' => $request->clics_realizados
                ];
                return  $this->actualizarSocialesTarjeta(new Request($requestData), $registro->id);
            } else {
                return response()->json([
                    'status' => 200,
                    'message' => 'No se encontró la información social indicada.',
                    'data' => null
                ], 200);
            }
        } catch (AuthorizationException $th) {
            return response()->json([
                'status' => $th->getCode(),
                'message' => 'No autorizado.',
                'data' => $th->getMessage()
            ], 401);
        } catch (Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Ocurrió un error.',
                'data' => $e->getMessage()
            ], 400);
        }
    }

    public function clicUrlLabel(Request $request)
    {
        try {
            $registro = SocialesTarjeta::where('id', $request->id)->first();

            // Verificar si se encontró el registro
            if ($registro) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Numero de clics social.',
                    'data' => $registro->clics_realizados
                ], 200);
            } else {
                return response()->json([
                    'status' => 200,
                    'message' => 'No se encontró la información social indicada.',
                    'data' => null
                ], 200);
            }
        } catch (AuthorizationException $th) {
            return response()->json([
                'status' => $th->getCode(),
                'message' => 'No autorizado.',
                'data' => $th->getMessage()
            ], 401);
        } catch (Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Ocurrió un error.',
                'data' => $e->getMessage()
            ], 400);
        }
    }
}
