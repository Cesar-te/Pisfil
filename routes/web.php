<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\EntradaCompraController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\OrdenProduccionController;
use App\Http\Controllers\TareaProduccionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolController;

// Ruta de bienvenida
Route::get('/', function () {
    return view('welcome');
});

// Rutas de Autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Rutas autenticadas
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ========== GESTIÓN DE USUARIOS Y ROLES ==========
    Route::resource('usuarios', UserController::class);
    Route::resource('roles', RolController::class);

    // ========== GESTIÓN DE INVENTARIO ==========
    Route::group(['prefix' => 'inventario', 'as' => 'inventario.'], function () {
        Route::get('/dashboard', [InventarioController::class, 'dashboard'])->name('dashboard');
        Route::get('/productos', [InventarioController::class, 'productos'])->name('productos');
        Route::get('/movimientos-kardex', [InventarioController::class, 'movimientosKardex'])->name('movimientos_kardex');
        Route::get('/reporte-stock', [InventarioController::class, 'reporteStock'])->name('reporte_stock');
        Route::get('/stock-bajo', [InventarioController::class, 'stockBajo'])->name('stock_bajo');
        Route::get('/clasificacion-abc', [InventarioController::class, 'clasificacionABC'])->name('clasificacion_abc');
    });

    // Gestión de Productos
    Route::resource('productos', ProductoController::class);
    Route::get('/productos-activos', [ProductoController::class, 'activos'])->name('productos.activos');

    // Gestión de Proveedores
    Route::resource('proveedores', ProveedorController::class);
    Route::get('/proveedores-activos', [ProveedorController::class, 'activos'])->name('proveedores.activos');

    // Entradas de Compra
    Route::resource('entradas-compra', EntradaCompraController::class, [
        'names' => [
            'index' => 'entradas-compra.index',
            'create' => 'entradas-compra.create',
            'store' => 'entradas-compra.store',
            'show' => 'entradas-compra.show',
            'edit' => 'entradas-compra.edit',
            'update' => 'entradas-compra.update',
            'destroy' => 'entradas-compra.destroy',
        ]
    ]);
    Route::post('/entradas-compra/{entradaCompra}/cambiar-estado', [EntradaCompraController::class, 'cambiarEstado'])->name('entradas-compra.cambiar-estado');
    Route::post('/entradas-compra/{entradaCompra}/agregar-detalle', [EntradaCompraController::class, 'agregarDetalle'])->name('entradas-compra.agregar-detalle');

    // ========== GESTIÓN DE PRODUCCIÓN ==========
    
    // Órdenes de Producción
    Route::resource('ordenes-produccion', OrdenProduccionController::class, [
        'names' => [
            'index' => 'ordenes-produccion.index',
            'create' => 'ordenes-produccion.create',
            'store' => 'ordenes-produccion.store',
            'show' => 'ordenes-produccion.show',
            'edit' => 'ordenes-produccion.edit',
            'update' => 'ordenes-produccion.update',
            'destroy' => 'ordenes-produccion.destroy',
        ]
    ]);
    Route::post('/ordenes-produccion/{ordenProduccion}/cambiar-estado', [OrdenProduccionController::class, 'cambiarEstado'])->name('ordenes-produccion.cambiar-estado');

    // Tareas de Producción
    Route::resource('tareas-produccion', TareaProduccionController::class, [
        'names' => [
            'index' => 'tareas-produccion.index',
            'create' => 'tareas-produccion.create',
            'store' => 'tareas-produccion.store',
            'show' => 'tareas-produccion.show',
            'edit' => 'tareas-produccion.edit',
            'update' => 'tareas-produccion.update',
            'destroy' => 'tareas-produccion.destroy',
        ]
    ]);
    Route::post('/tareas-produccion/{tareaProduccion}/cambiar-estado', [TareaProduccionController::class, 'cambiarEstado'])->name('tareas-produccion.cambiar-estado');
    Route::post('/tareas-produccion/{tareaProduccion}/actualizar-avance', [TareaProduccionController::class, 'actualizarAvance'])->name('tareas-produccion.actualizar-avance');
});
