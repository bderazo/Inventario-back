<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use App\Mail\ResetPasswordMailP;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    // FUNCIONANDO OK
    public function login(Request $request)
    {
        try {
            $request->validate([
                'correo' => 'required|string|email',
                'password' => 'required|string',
            ]);
            $credentials = $request->only('correo', 'password');
            $token = JWTAuth::attempt($credentials);
            if (!$token) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Datos incorrectos',
                    'data' => null
                ], Response::HTTP_UNAUTHORIZED);
            } else {
                $user = JWTAuth::user();
                $usuario = Usuario::with('userTarjeta', 'comercios')->find($user->id);
                return response()->json([
                    'status' => 200,
                    'authorisation' => [
                        'data' => $usuario,
                        'token' => $token,
                        'type' => 'bearer',
                        'expires_in' => (auth('api')->factory()->getTTL()) / 60 . ' horas'
                    ]
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => $th->getCode(),
                'message' => 'Error al intentar iniciar sesion.',
                'data' => $th->getMessage()

            ], 400);
        }
    }

    //onlytap
    public function sendResetLinkEmail(Request $request)
    {
        try {
            $request->validate(['correo' => 'required|email|exists:usuarios,correo']);

            $email = $request->input('correo');

            $token = Str::random(64);

            DB::table('password_resets')->insert([
                'email' => $email,
                'token' => $token,
                'created_at' => now()
            ]);
            $passwordResetObject = DB::table('password_resets')
                ->where('email', $email)
                ->where('token', $token)
                ->first();

            // var_dump($passwordResetObject);
            Mail::to($email)->send(new ResetPasswordMail($passwordResetObject));

            // Mostrar mensaje de éxito
            return response()->json([
                'status' => 200,
                'message' => 'Se ha enviado un enlace de restablecimiento de contraseña a tu correo electrónico.'
            ]);
        } catch (\PDOException $e) {
            // Verificar si el error es debido a la violación de integridad
            if ($e->getCode() === '23000') {
                // Mostrar mensaje de error personalizado
                return response()->json([
                    'status' => 400,
                    'message' => 'Ya se ha realizado una petición de cambio de contraseña para este correo electrónico.'
                ]);
            }

            // Si el error no es de integridad, puedes manejarlo de otra forma o mostrar un mensaje genérico de error
            return response()->json([
                'status' => 500,
                'message' => 'Ocurrió un error al procesar la solicitud.'
            ]);
        }
    }

    protected function cambiarClave(Request $request)
    {

        $input = $request->only('correo', 'token', 'password', 'password_confirmation');
        $validator = Validator::make($input, [
            'token' => 'required',
            'correo' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return response([
                'status' => 422,
                'message' => 'Ocurrió un error al validar los datos.',
                'data' => $validator->errors()
            ], 422);
        }

        $tokenData = DB::table('password_resets')
            ->where('email', $input['correo'])
            ->where('token', $input['token'])
            ->first();

        if (!$tokenData) {
            return response([
                'status' => 422,
                'message' => 'El token de recuperación de contraseña no es válido.',
                'data' => []
            ], 422);
        }

        // Verificación exitosa del token, proceder a cambiar la contraseña
        $user = Usuario::where('correo', $input['correo'])->first();
        $user->password = Hash::make($input['password']);
        $user->save();

        // Eliminar el registro del token de recuperación de contraseña
        DB::table('password_resets')
            ->where('email', $input['correo'])
            ->delete();

        // Acciones adicionales después de actualizar la contraseña
        // ...

        return response([
            'status' => 200,
            'message' => 'La contraseña se ha cambiado correctamente.',
            'data' => []
        ], 200);
    }

    //proatek
    public function sendResetEmailLink(Request $request)
    {
        try {
            $request->validate(['correo' => 'required|email|exists:usuarios,correo']);

            $email = $request->input('correo');

            $token = Str::random(64);

            DB::table('password_resets')->insert([
                'email' => $email,
                'token' => $token,
                'created_at' => now()
            ]);
            $passwordResetObject = DB::table('password_resets')
                ->where('email', $email)
                ->where('token', $token)
                ->first();

            // var_dump($passwordResetObject);
            Mail::to($email)->send(new ResetPasswordMailP($passwordResetObject));

            // Mostrar mensaje de éxito
            return response()->json([
                'status' => 200,
                'message' => 'Se ha enviado un enlace de restablecimiento de contraseña a tu correo electrónico.'
            ]);
        } catch (\PDOException $e) {
            // Verificar si el error es debido a la violación de integridad
            if ($e->getCode() === '23000') {
                // Mostrar mensaje de error personalizado
                return response()->json([
                    'status' => 400,
                    'message' => 'Ya se ha realizado una petición de cambio de contraseña para este correo electrónico.'
                ]);
            }

            // Si el error no es de integridad, puedes manejarlo de otra forma o mostrar un mensaje genérico de error
            return response()->json([
                'status' => 500,
                'message' => 'Ocurrió un error al procesar la solicitud.'
            ]);
        }
    }
}
