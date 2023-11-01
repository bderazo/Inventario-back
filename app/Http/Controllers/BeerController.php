<?php

namespace App\Http\Controllers;

use App\Models\ArrayExport;
use App\Models\BeerRfid;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

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

    public function verificarID($id)
    {
        // Realiza la consulta en la base de datos
        $registro = BeerRfid::where('codigo_sensor', $id)->first();

        if ($registro) {
            return response()->json(['existe' => true, 'data' => $registro]);
        } else {
            return response()->json(['existe' => false]);
        }
    }
//     public function cargar(Request $request)
//     {
//         try {
//             $cantidadRegistros = $request->input('cantidad'); // Obtener el valor de cantidad desde el request

//             $idsCreados = []; // Array para almacenar los IDs creados

//             for ($i = 0; $i < $cantidadRegistros; $i++) {
//                 $tarjeta = new UserTarjeta([
//                     'usuario_id' => $request->usuario_id,
//                     'comercio_id' => $request->comercio_id,
//                     'estado' => true,
//                     'img_perfil' => $request->img_perfil,
//                     'img_portada' => $request->img_portada,
//                     'nombre' => $request->nombre,
//                     'profesion' => $request->profesion,
//                     'empresa' => $request->empresa,
//                     'acreditaciones' => json_encode($request->acreditaciones),
//                     'telefono' => $request->telefono,
//                     'direccion' => $request->direccion,
//                     'correo' => $request->correo,
//                     'sitio_web' => $request->sitio_web,
//                 ]);

//                 $tarjeta->id = 'EC' . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT); // Genera una cadena aleatoria de 8 caracteres (EC+ 6 digitos)

//                 $tarjeta->save();

//                 $url = 'https://onlytap.proatek.com/OnlyTap/Presentacion/';

//                 $almacenar = $url .  $tarjeta->id;
//                 // dd($almacenar);
//                 $idsCreados[] = $almacenar; // Almacenar el ID creado en el array
//             }
//             return Excel::download(new ArrayExport($idsCreados), 'codigos.xlsx');
//             // return response()->json($idsCreados);
//         } catch (AuthorizationException $e) {
//             return response()->json([
//                 'status' => 401,
//                 'message' => 'No tiene permisos!.',
//                 'data' => $e->getMessage(),
//             ], 401);
//         }
//     }

//     public function actualizarUserTarjeta(Request $request, $id)
//     {
//         try {
//             $tarjeta = UserTarjeta::find($id);
//             if ($tarjeta != null) {
//                 $tarjeta->update($request->all());
//                 return response()->json([
//                     'status' => 200,
//                     'message' => 'Información de tarjeta actualizada correctamente.',
//                     'data' => $tarjeta
//                 ], 200);
//             } else {
//                 return response()->json([
//                     'status' => 200,
//                     'message' => 'No se encontro la tarjeta indicada.',
//                     'data' => null
//                 ], 200);
//             }
//         } catch (AuthorizationException $th) {
//             return response()->json([
//                 'status' => $th->getCode(),
//                 'message' => 'No autorizado!.',
//                 'data' => $th->getMessage()
//             ], 401);
//         } catch (Exception $e) {
//             return response()->json([
//                 'status' => $e->getCode(),
//                 'message' => 'Ocurrio un error!.',
//                 'data' => $e->getMessage()
//             ], 400);
//         }
//     }

//     public function verTarjetaUser($id)
//     {
//         try {
//             if ($id) {
//                 $tarjeta = UserTarjeta::with([
//                     'socialesTarjeta' => function ($query) {
//                         $query->where('estado', 1);
//                     },
//                     'configuracionesTarjeta'
//                 ])
//                     ->find($id);
//                 return ($tarjeta != null) ?
//                     response()->json([
//                         'status' => 200,
//                         'message' => 'Tarjeta de usuario indicado.',
//                         'data' => $tarjeta
//                     ], 200) :
//                     response()->json([
//                         'status' => 200,
//                         'message' => 'No se encontro la Tarjeta solicitada.',
//                         'data' => null
//                     ], 200);
//             } else {
//                 return response()->json([
//                     'status' => 422,
//                     'message' => 'El id ingresado es incorrecto.',
//                     'data' => 'UUID Formato incorrecto.'
//                 ], 422);
//             }
//         } catch (AuthorizationException $th) {
//             return response()->json([
//                 'status' => $th->getCode(),
//                 'message' => 'No autorizado!.',
//                 'data' => $th->getMessage()
//             ], 401);
//         } catch (Exception $e) {
//             return response()->json([
//                 'status' => $e->getCode(),
//                 'message' => 'Ocurrio un error!.',
//                 'data' => $e->getMessage()
//             ], 400);
//         }
//     }

//     public function verificarID($id)
//     {
//         $registro = UserTarjeta::find($id);

//         if ($registro) {
//             return response()->json(['existe' => true, 'data' => $registro]);
//         } else {
//             return response()->json(['existe' => false]);
//         }
//     }
// //en esta consulta me gustaria solo consultar las tarjetas cuyo campo usuario_id no sea null?
    // public function listadoTarjetas()
    // {
    //     try {
    //         $tarjetas = BeerRfid::with('codigo_sensor')->whereNotNull('codigo_sensor')->get();
    //         return ($tarjetas != null) ?
    //             response()->json([
    //                 'status' => 200,
    //                 'message' => 'Listado de tarjetas.',
    //                 'data' => $tarjetas
    //             ], 200) :
    //             response()->json([
    //                 'status' => 201,
    //                 'message' => 'No existen tarjetas.',
    //                 'data' => null
    //             ], 200);
    //     } catch (AuthorizationException $th) {
    //         return response()->json([
    //             'status' => $th->getCode(),
    //             'message' => 'No autorizado!.',
    //             'data' => $th->getMessage()
    //         ], 401);
    //     } catch (Exception $e) {
    //         return response()->json([
    //             'status' => $e->getCode(),
    //             'message' => 'Ocurrio un error!.',
    //             'data' => $e->getMessage()
    //         ], 400);
    //     }
    // }


//     public function listadoTarjetas()
// {
//     try {
//         $codigoSensores = BeerRfid::whereNotNull('codigo_sensor')->pluck('codigo_sensor');
//         return ($codigoSensores->count() > 0) ?
//             response()->json([
//                 'status' => 200,
//                 'message' => 'Listado de códigos de sensor.',
//                 'data' => $codigoSensores
//             ], 200) :
//             response()->json([
//                 'status' => 201,
//                 'message' => 'No existen códigos de sensor.',
//                 'data' => null
//             ], 200);
//     } catch (AuthorizationException $th) {
//         return response()->json([
//             'status' => $th->getCode(),
//             'message' => 'No autorizado!.',
//             'data' => $th->getMessage()
//         ], 401);
//     } catch (Exception $e) {
//         return response()->json([
//             'status' => $e->getCode(),
//             'message' => 'Ocurrió un error!.',
//             'data' => $e->getMessage()
//         ], 400);
//     }
// }

public function listadoTarjetas()
{
    try {
        $tarjetas = BeerRfid::all(); // Obtiene todos los registros de la tabla beer_rfid
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

}