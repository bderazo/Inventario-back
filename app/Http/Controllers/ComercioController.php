<?php

namespace App\Http\Controllers;

use App\Models\Comercio;
use App\Models\Mesa;
use App\Models\Sucursal;
use App\Models\TipoComercio;
use App\Models\UsuarioComercio;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ComercioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function crearComercio(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre_comercial' => 'required',
                'razon_social' => 'required',
                'ruc' => 'required|unique:comercio,ruc',
                'direccion' => 'required',
                'telefono' => 'required',
                'whatsapp' => 'required',
                'correo' => 'required|unique:comercio,correo',
                'logo' => 'required',
                'sitio_web' => 'required',
                'estado' => 'nullable',
                'tipo_comercio' => 'required|exists:tipo_comercio,id'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Error al validar los datos de entrada.',
                    'data' => $validator->errors()
                ], 422);
            } else {
                $comercio = Comercio::create([
                    'nombre_comercial' => $request->nombre_comercial,
                    'razon_social' => $request->razon_social,
                    'ruc' => $request->ruc,
                    'direccion' => $request->direccion,
                    'telefono' => $request->telefono,
                    'whatsapp' => $request->whatsapp,
                    'correo' => $request->correo,
                    'logo' => $request->logo,
                    'sitio_web' => $request->sitio_web,
                    'tipo_comercio' => $request->tipo_comercio,
                    'estado' => true
                ]);
                return response()->json([
                    'status' => 201,
                    'message' => 'Comercio creado correctamente.',
                    'data' => $comercio
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

    public function verComercio($id)
    {
        try {
            if (Str::isUuid($id)) {
                $comercio = Comercio::find($id);
                return ($comercio != null) ?
                    response()->json([
                        'status' => 200,
                        'message' => 'Solicitud de Firma previa indicado.',
                        'data' => $comercio
                    ], 200) :
                    response()->json([
                        'status' => 200,
                        'message' => 'No se encontro la Solicitud de Firma previa.',
                        'data' => null
                    ], 200);
            } else {
                return response()->json([
                    'status' => 422,
                    'message' => 'El id ingresado es incorrecto.',
                    'data' => 'UUID Formato incorrecto.'
                ], 422);
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

    public function actualizarComercio(Request $request, $id)
    {
        try {
            $comercio = Comercio::find($id);
            if ($comercio != null) {
                $comercio->update($request->all());
                return response()->json([
                    'status' => 200,
                    'message' => 'Información comercio actualizada correctamente.',
                    'data' => $comercio
                ], 200);
            } else {
                return response()->json([
                    'status' => 200,
                    'message' => 'No se encontro el comercio indicado.',
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

    public function eliminarComercio($id)
    {
        try {
            $comercio = Comercio::find($id);
            if ($comercio != null) {
                $comercio->update([
                    'estado' => false
                ]);
                return response()->json([
                    'status' => 200,
                    'message' => 'Entidad Comercial eliminada correctamente.',
                    'data' => null
                ], 200);
            } else {
                return response()->json([
                    'status' => 200,
                    'message' => 'No se encontro la Entidad Comercial indicada.',
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

    public function listarAllComercios()
    {
        try {
            $lst_comercios = Comercio::all()->where('estado', true);
            if ($lst_comercios != null) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Lista de entidades comerciales. ',
                    'data' => $lst_comercios
                ]);
            } else {
                return response()->json([
                    'status' => 200,
                    'message' => 'No existen entidades comerciales',
                    'data' => $lst_comercios
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => $th->getCode(),
                'message' => 'Ocurrio un error!. ',
                'data' => $th->getMessage()
            ], $th->getCode());
        }
    }

    public function listarTipoComercios()
    {
        try {
            $tipo_comercios = TipoComercio::all();
            if ($tipo_comercios != null) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Lista de tipo de comercios. ',
                    'data' => $tipo_comercios
                ]);
            } else {
                return response()->json([
                    'status' => 200,
                    'message' => 'No existen tipo de comercios',
                    'data' => $tipo_comercios
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => $th->getCode(),
                'message' => 'Ocurrio un error!. ',
                'data' => $th->getMessage()
            ], $th->getCode());
        }
    }

    public function listarComercios()
    {
        try {
            $lst_comercios = Comercio::with('tipo_comercio', 'usuarios', 'usuarios.usuario', 'usuarios.usuario.userTarjeta')->paginate(10);

            if ($lst_comercios->count()) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Lista de entidades comerciales.',
                    'data' => $lst_comercios
                ]);
            } else {
                return response()->json([
                    'status' => 204,
                    'message' => 'No existen entidades comerciales registradas.'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => $th->getCode(),
                'message' => 'Ocurrio un error!',
                'data' => $th->getMessage()
            ], $th->getCode());
        }
    }


    public function crearSucursal(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'comercio_id' => 'required|exists:comercio,id',
                'nombre' => 'required',
                'ruc' => 'required|unique:sucursal,ruc',
                'direccion' => 'required',
                'telefono' => 'required',
                'whatsapp' => 'required',
                'correo' => 'required|unique:sucursal,correo',
                'secuencial_facturas' => 'required',
                'siguiente_factura' => 'required',
                'reponsable' => 'required',
                'estado' => 'nullable',
                'pax_capacidad' => 'required',
                'es_matriz' => 'required',
                'registrado_por' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Error al validar los datos de entrada.',
                    'data' => $validator->errors()
                ], 422);
            } else {
                $sucursal = Sucursal::create([
                    'comercio_id' => $request->comercio_id,
                    'nombre' => $request->nombre,
                    'ruc' => $request->ruc,
                    'direccion' => $request->direccion,
                    'telefono' => $request->telefono,
                    'whatsapp' => $request->whatsapp,
                    'correo' => $request->correo,
                    'secuencial_facturas' => $request->secuencial_facturas,
                    'siguiente_factura' => $request->siguiente_factura,
                    'reponsable' => $request->reponsable,
                    'estado' => true,
                    'pax_capacidad' => $request->pax_capacidad,
                    'es_matriz' => $request->es_matriz,
                    'registrado_por' => $request->registrado_por,
                ]);
                return response()->json([
                    'status' => 201,
                    'message' => 'Sucursal creada correctamente.',
                    'data' => $sucursal
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

    public function listarAllSucursalesIdComercio($id)
    {
        try {
            $sucursales = Sucursal::where('comercio_id', $id)->get();

            // Haz algo con las sucursales obtenidas, como devolverlas como respuesta JSON
            // return response()->json($sucursales);

            if ($sucursales != null) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Lista de sucursales de la entidad comercial. ',
                    'data' => $sucursales
                ]);
            } else {
                return response()->json([
                    'status' => 200,
                    'message' => 'No existen sucursales en la entidad comercial',
                    'data' => $sucursales
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => $th->getCode(),
                'message' => 'Ocurrio un error!. ',
                'data' => $th->getMessage()
            ], $th->getCode());
        }
    }
    public function listarSucursalesIdComercio($id, Request $request)
    {
        try {
            $sucursales = Sucursal::where('comercio_id', $id)->paginate(10);

            // Haz algo con las sucursales obtenidas, como devolverlas como respuesta JSON
            // return response()->json($sucursales);

            if ($sucursales != null) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Lista de sucursales de la entidad comercial. ',
                    'data' => $sucursales
                ]);
            } else {
                return response()->json([
                    'status' => 200,
                    'message' => 'No existen sucursales en la entidad comercial',
                    'data' => $sucursales
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => $th->getCode(),
                'message' => 'Ocurrio un error!. ',
                'data' => $th->getMessage()
            ], $th->getCode());
        }
    }

    public function crearMesaSucursal(Request $request)
    {
        try {
            DB::beginTransaction();
            $errores = [];
            $ids_solicitudes = [];
            $solicitudes = $request->mesas;

            foreach ($solicitudes as $key => $value2) {
                try {
                    $validator = Validator::make($value2, [
                        'sucursal_id' => 'required|exists:sucursal,id',
                        'num_mesa' => 'required|unique:mesas,num_mesa',
                        'ubicacion_x' => 'required',
                        'ubicacion_y' => 'required',
                        'num_personas' => 'required',
                        'estado' => 'nullable',
                    ]);
                    if ($validator->fails()) {
                        $errores[] = $validator->errors()->add('mesa', 'Error en la mesa ' . $value2['num_mesa']);
                    } else {
                        Mesa::create($value2);
                    }
                } catch (\Exception $th) {
                    $errores[] = $th->getMessage();
                }
            }
            if (!empty($errores)) {
                DB::rollBack();
                return response()->json([
                    'status' => 400,
                    'message' => 'Error en la carga de mesas, revise los datos.',
                    'errors' => $errores,
                ], 400);
            } else {
                DB::commit();
                return response()->json([
                    'status' => 200,
                    'message' => 'Mesas cargadas correctamente.',
                    'data' => null,
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

    public function listarMesasIdSucursal($id)
    {
        try {
            $sucursales = Mesa::where('sucursal_id', $id)->get();

            // Haz algo con las sucursales obtenidas, como devolverlas como respuesta JSON
            // return response()->json($sucursales);

            if ($sucursales != null) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Lista de mesas de la sucursal. ',
                    'data' => $sucursales
                ]);
            } else {
                return response()->json([
                    'status' => 200,
                    'message' => 'No existen mesas en esta sucursal',
                    'data' => $sucursales
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => $th->getCode(),
                'message' => 'Ocurrio un error!. ',
                'data' => $th->getMessage()
            ], $th->getCode());
        }
    }

}
