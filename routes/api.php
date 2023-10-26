<?php

use App\Http\Controllers\CategoriaProductoController;
use App\Http\Controllers\ConfiguracionesTarjetaController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\SocialesTarjetaController;
use App\Http\Controllers\UserTarjetaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ComercioController;
use App\Http\Controllers\BeerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// //LOGIN
Route::controller(LoginController::class)->group(function () {
    Route::post('auth/login', 'login');
    Route::post('auth/password/cambiar-password', 'cambiarClave');
    //onlytap
    Route::post('auth/password/recuperar', 'sendResetLinkEmail');
    //proatek
    Route::post('auth/password/recuperarP', 'sendResetEmailLink');
});

//USUARIO
Route::controller(UsuarioController::class)->group(function () {
    Route::post('usuario/crear', 'crearUsuario');
    Route::post('usuario/entidad/crear', 'asignarUsuarioEntidad');
    Route::post('usuario/sucursal/crear', 'asignarUsuarioSucursal');
    Route::post('usuario/ver/{usuario}', 'verUsuario');
    Route::post('usuario/listar', 'listarUsuarios');
});

//COMERCIO
Route::controller(ComercioController::class)->group(function () {
    Route::post('comercio/crear', 'crearComercio');
    Route::post('sucursal/crear', 'crearSucursal');
    Route::post('mesa/crear', 'crearMesaSucursal');
    Route::post('mesas/listar/{id}', 'listarMesasIdSucursal');
    Route::post('comercio/ver/{id}', 'verComercio');
    Route::post('comercio/actualizar/{id}', 'actualizarComercio');
    Route::delete('comercio/eliminar/{id}', 'eliminarComercio');
    Route::post('comercio/listar/todos', 'listarAllComercios');
    Route::post('sucursal/listarAll/{id}', 'listarAllSucursalesIdComercio');
    Route::post('sucursal/listar/{id}', 'listarSucursalesIdComercio'); //paginado de 10 en 10
    Route::post('comercio/listar', 'listarComercios'); //paginado de 10 en 10
    Route::post('tipo/comercio/listar', 'listarTipoComercios');
});

//CATEGORIAS PRODUCTO
Route::controller(CategoriaProductoController::class)->group(function () {
    Route::post('categoria/producto/crear', 'crearCategoria');
    Route::post('categoria/producto/ver/{id}', 'verCategoria');
    Route::post('categoria/producto/listar', 'listarAllCategorias');
});

//TARJETA DE USUARIO
Route::controller(UserTarjetaController::class)->group(function () {
    Route::post('usuario/tarjeta/crear', 'crearUserTarjeta');
    Route::post('usuario/tarjeta/actualizar/{id}', 'actualizarUserTarjeta');
    Route::post('usuario/tarjeta/ver/{id}', 'verTarjetaUser');
    Route::post('usuario/tarjeta/listar', 'listadoTarjetas');
    Route::post('usuario/tarjeta/cargar', 'cargar');
    Route::post('verificar-id/{id}', 'verificarID');
});

//SOCIALES DE TARJETA DE USUARIO
Route::controller(SocialesTarjetaController::class)->group(function () {
    Route::post('tarjeta/sociales/crear', 'crearSocialesTarjeta');
    Route::post('tarjeta/sociales/actualizar/{id}', 'actualizarSocialesTarjeta');
    Route::post('sociales/actualizar/label', 'encontrarPorUrlLabel');
    Route::post('sociales/clic', 'clicUrlLabel');
});

//CONFIGURACIONES DE TARJETA DE USUARIO
Route::controller(ConfiguracionesTarjetaController::class)->group(function () {
    Route::post('tarjeta/configuraciones/crear', 'crearConfiguracionesTarjeta');
    Route::post('tarjeta/configuraciones/actualizar/{id}', 'actualizarConfiguracionesTarjeta');
});

//PRODUCTO
Route::controller(ProductoController::class)->group(function () {
    Route::post('producto/crear', 'crearProducto');
    Route::post('/ingresar/productos', 'ingresarRegistros'); //carga masiva objeto json
    Route::post('productos/listar/todos', 'listarAllProductos');
    Route::post('productos/listar/{id}', 'listarProductosIdComercio'); //paginado de 10 en 10
    Route::post('productos/categorias/listar', 'listarProductosIdComercioCategoria'); //paginado de 10 en 10
});

//TARJETA DE USUARIO
Route::controller(BeerController::class)->group(function () {
    Route::post('usuario/beer/crear', 'crearBeerCode');
    Route::post('usuario/beer/consultar/{id}', 'verificarID');
    Route::post('usuario/beer/listar', 'listadoTarjetas');
    // Route::post('usuario/tarjeta/actualizar/{id}', 'actualizarUserTarjeta');
    // Route::post('usuario/tarjeta/ver/{id}', 'verTarjetaUser');
    // Route::post('usuario/tarjeta/listar', 'listadoTarjetas');
    // Route::post('usuario/tarjeta/cargar', 'cargar');
    // Route::post('verificar-id/{id}', 'verificarID');
});