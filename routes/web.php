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
use App\Http\Controllers\ConsumoMaterialController;
use App\Http\Controllers\UsuarioController;
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
        
        // Movimientos Manuales Kardex
        Route::get('/create-movimiento', [InventarioController::class, 'createMovimiento'])->name('create_movimiento');
        Route::post('/store-movimiento', [InventarioController::class, 'storeMovimiento'])->name('store_movimiento');
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
    Route::post('/entradas-compra/{entradaCompra}/registrar-pago', [EntradaCompraController::class, 'registrarPago'])->name('entradas-compra.registrar-pago');

    // ========== GESTIÓN DE PRODUCCIÓN ==========
    Route::resource('ordenes-produccion', OrdenProduccionController::class);
    Route::post('/ordenes-produccion/{ordenProduccion}/estado', [OrdenProduccionController::class, 'updateEstado'])->name('ordenes-produccion.estado');
    
    // Tareas
    Route::post('/ordenes-produccion/{ordenProduccion}/tareas', [TareaProduccionController::class, 'store'])->name('tareas-produccion.store');
    Route::post('/tareas-produccion/{tareaProduccion}/avance', [TareaProduccionController::class, 'updateAvance'])->name('tareas-produccion.avance');
    
    // Consumo de Materiales
    Route::post('/ordenes-produccion/{ordenProduccion}/consumo', [ConsumoMaterialController::class, 'store'])->name('consumos-material.store');

    // ========== GESTIÓN ADMINISTRATIVA ==========
    Route::resource('usuarios', UsuarioController::class)->except(['show', 'destroy']);
    Route::resource('roles', RolController::class)->only(['index', 'edit', 'update']);

});
