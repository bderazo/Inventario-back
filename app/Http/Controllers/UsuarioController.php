<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\UsuarioComercio;
use App\Models\UsuarioSucursal;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UsuarioController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:api');
    }
    public function crearUsuario(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombres' => 'required',
                'apellidos' => 'required',
                'correo' => 'required|unique:usuarios,correo',
                'password' => 'required',
                'rol' => 'required',
                'identificacion' => 'unique:usuarios,identificacion',
                'registrado_por' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Error al validar los datos de entrada.',
                    'data' => $validator->errors()
                ], 422);
            } else {
                $usuario = Usuario::create([
                    'nombres' => $request->nombres,
                    'apellidos' => $request->apellidos,
                    'correo' => $request->correo,
                    'password' => Hash::make($request->password),
                    'rol' => $request->rol,
                    'identificacion' => $request->identificacion,
                    'registrado_por' => $request->registrado_por,
                ]);
                // $input = $request->only('correo');
                return response()->json([
                    'status' => 201,
                    'message' => 'Usuario creado correctamente.',
                    // 'email' => Password::sendResetLink($input),
                    'data' => $usuario
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

    public function verUsuario($id)
    {
        try {
            if (Str::isUuid($id)) {
                $usuario = Usuario::get()->where('id', $id)->first();
                if ($usuario != null) {
                    return response()->json([
                        'status' => 200,
                        'message' => 'Datos de Usuario.',
                        'data' => $usuario
                    ]);
                } else {
                    return response()->json([
                        'status' => 200,
                        'message' => 'No se encontro datos del Usuario indicado.',
                        'data' => null
                    ]);
                }
            } else {
                $usuario = Usuario::get()->where('id', $id)->first();
                if ($usuario != null) {
                    return response()->json([
                        'status' => 200,
                        'message' => 'Datos de Usuario.',
                        'data' => $usuario
                    ]);
                } else {
                    return response()->json([
                        'status' => 200,
                        'message' => 'No se encontro datos del Usuario indicado.',
                        'data' => null
                    ]);
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

    public function listarUsuarios(Request $request)
    {
        try {
            $lst_comercios = Usuario::with('comercios', 'comercios.comercio')->get();
            if ($lst_comercios != null) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Lista de usuarios. ',
                    'data' => $lst_comercios
                ]);
            } else {
                return response()->json([
                    'status' => 200,
                    'message' => 'No existen usuarios',
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

    public function asignarUsuarioEntidad(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'usuario_id' => 'required|exists:usuarios,id',
                'comercio_id' => 'required|exists:comercio,id',
                'rol' => 'required',
                'estado' => 'required',
                'registrado_por' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Error al validar los datos de entrada.',
                    'data' => $validator->errors()
                ], 422);
            } else {
                $usuario = UsuarioComercio::create([
                    'usuario_id' => $request->usuario_id,
                    'comercio_id' => $request->comercio_id,
                    'rol' => $request->rol,
                    'estado' => $request->estado,
                    'registrado_por' => $request->registrado_por,
                ]);
                // $input = $request->only('correo');
                return response()->json([
                    'status' => 201,
                    'message' => 'Usuario creado correctamente.',
                    // 'email' => Password::sendResetLink($input),
                    'data' => $usuario
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

    public function asignarUsuarioSucursal(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'usuario_id' => 'required|exists:usuarios,id',
                'sucursal_id' => 'required|exists:sucursal,id',
                'rol' => 'required',
                'estado' => 'required',
                'registrado_por' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Error al validar los datos de entrada.',
                    'data' => $validator->errors()
                ], 422);
            } else {
                $usuario = UsuarioSucursal::create([
                    'usuario_id' => $request->usuario_id,
                    'sucursal_id' => $request->sucursal_id,
                    'rol' => $request->rol,
                    'estado' => $request->estado,
                    'registrado_por' => $request->registrado_por,
                ]);
                // $input = $request->only('correo');
                return response()->json([
                    'status' => 201,
                    'message' => 'Usuario creado correctamente.',
                    // 'email' => Password::sendResetLink($input),
                    'data' => $usuario
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
}
