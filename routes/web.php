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
use App\Http\Controllers\CostoProduccionController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\CajaBancosController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ContabilidadController;
use App\Http\Controllers\CuentaContableController;
use App\Http\Controllers\AuditoriaController;

// Ruta principal del sistema
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

// Rutas de Autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Rutas autenticadas
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


    // ========== GESTIÓN DE INVENTARIO ==========
    Route::group(['prefix' => 'inventario', 'as' => 'inventario.'], function () {
        Route::get('/dashboard', [InventarioController::class, 'dashboard'])->name('dashboard');
        Route::get('/productos', [InventarioController::class, 'productos'])->name('productos');
        Route::get('/movimientos-kardex', [InventarioController::class, 'movimientosKardex'])->name('movimientos_kardex');
        Route::get('/reporte-stock', [InventarioController::class, 'reporteStock'])->name('reporte_stock');
        Route::get('/stock-bajo', [InventarioController::class, 'stockBajo'])->name('stock_bajo');
        Route::get('/clasificacion-abc', [InventarioController::class, 'clasificacionABC'])->name('clasificacion_abc');
        
        // Movimientos Manuales Kardex
        Route::get('/create-movimiento', [InventarioController::class, 'createMovimiento'])->name('create_movimiento')->middleware('permission:inventario.create');
        Route::post('/store-movimiento', [InventarioController::class, 'storeMovimiento'])->name('store_movimiento')->middleware('permission:inventario.create');
    });

    // Gestión de Productos
    Route::resource('productos', ProductoController::class);
    Route::get('/productos-activos', [ProductoController::class, 'activos'])->name('productos.activos');

    // Gestión de Proveedores
    Route::resource('proveedores', ProveedorController::class)->parameters([
        'proveedores' => 'proveedor'
    ]);
    Route::get('/proveedores-activos', [ProveedorController::class, 'activos'])->name('proveedores.activos');

    // Entradas de Compra
    Route::resource('entradas-compra', EntradaCompraController::class)->parameters([
        'entradas-compra' => 'entradaCompra'
    ]);
    Route::post('/entradas-compra/{entradaCompra}/cambiar-estado', [EntradaCompraController::class, 'cambiarEstado'])->name('entradas-compra.cambiar-estado')->middleware('permission:entradas.approve');
    Route::post('/entradas-compra/{entradaCompra}/agregar-detalle', [EntradaCompraController::class, 'agregarDetalle'])->name('entradas-compra.agregar-detalle');
    Route::post('/entradas-compra/{entradaCompra}/registrar-pago', [EntradaCompraController::class, 'registrarPago'])->name('entradas-compra.registrar-pago')->middleware('permission:entradas.pay');

    // ========== GESTIÓN DE PRODUCCIÓN ==========
    Route::resource('ordenes-produccion', OrdenProduccionController::class)->parameters([
        'ordenes-produccion' => 'ordenProduccion'
    ]);
    Route::post('/ordenes-produccion/{ordenProduccion}/estado', [OrdenProduccionController::class, 'updateEstado'])->name('ordenes-produccion.estado');
    
    // Tareas
    Route::post('/ordenes-produccion/{ordenProduccion}/tareas', [TareaProduccionController::class, 'store'])->name('tareas-produccion.store');
    Route::post('/tareas-produccion/{tareaProduccion}/avance', [TareaProduccionController::class, 'updateAvance'])->name('tareas-produccion.avance');
    
    // Consumo de Materiales
    Route::post('/ordenes-produccion/{ordenProduccion}/consumo', [ConsumoMaterialController::class, 'store'])->name('consumos-material.store')->middleware('permission:produccion.consume');
    Route::post('/ordenes-produccion/{ordenProduccion}/costos', [CostoProduccionController::class, 'store'])->name('costos-produccion.store')->middleware('permission:produccion.cost');

    // ========== GESTIÓN DE CAJA Y BANCOS (TESORERÍA) ==========
    Route::get('/caja-bancos', [CajaBancosController::class, 'dashboard'])->name('caja-bancos.dashboard');
    Route::post('/caja-bancos/cuentas', [CajaBancosController::class, 'storeCuenta'])->name('caja-bancos.store-cuenta');
    Route::get('/caja-bancos/cuentas/{cuenta}', [CajaBancosController::class, 'showCuenta'])->name('caja-bancos.show-cuenta');
    Route::post('/caja-bancos/cuentas/{cuenta}/movimiento', [CajaBancosController::class, 'registrarMovimiento'])->name('caja-bancos.movimiento')->middleware('permission:transacciones.create');
    Route::post('/caja-bancos/cuentas/{cuentaOrigen}/transferencia', [CajaBancosController::class, 'registrarTransferencia'])->name('caja-bancos.transferencia')->middleware('permission:transacciones.create');

    // ========== VENTAS Y CLIENTES ==========
    Route::resource('clientes', ClienteController::class)->except(['show', 'create', 'edit', 'destroy']);
    Route::resource('ventas', VentaController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('/ventas/{venta}/registrar-cobro', [VentaController::class, 'registrarCobro'])->name('ventas.registrar-cobro')->middleware('permission:ventas.collect');

    // ========== REPORTES GERENCIALES ==========
    Route::get('/reportes', [ReporteController::class, 'dashboard'])->name('reportes.dashboard');
    Route::get('/reportes/exportar/ventas', [ReporteController::class, 'exportVentas'])->name('reportes.exportar.ventas')->middleware('permission:reportes.export');
    Route::get('/reportes/exportar/compras', [ReporteController::class, 'exportCompras'])->name('reportes.exportar.compras')->middleware('permission:reportes.export');
    Route::get('/reportes/exportar/stock', [ReporteController::class, 'exportStock'])->name('reportes.exportar.stock')->middleware('permission:reportes.export');
    Route::get('/reportes/exportar/kardex', [ReporteController::class, 'exportKardex'])->name('reportes.exportar.kardex')->middleware('permission:reportes.export');
    Route::get('/reportes/exportar/caja', [ReporteController::class, 'exportCaja'])->name('reportes.exportar.caja')->middleware('permission:reportes.export');

    // ========== CONTABILIDAD ==========
    Route::get('/contabilidad', [App\Http\Controllers\ContabilidadController::class, 'index'])->name('contabilidad.index');
    Route::get('/contabilidad/plan-cuentas', [App\Http\Controllers\ContabilidadController::class, 'planCuentas'])->name('contabilidad.plan_cuentas');
    Route::get('/contabilidad/libro-diario', [App\Http\Controllers\ContabilidadController::class, 'libroDiario'])->name('contabilidad.libro_diario');
    Route::get('/contabilidad/libro-diario/exportar', [App\Http\Controllers\ContabilidadController::class, 'exportLibroDiario'])->name('contabilidad.libro_diario.exportar')->middleware('permission:contabilidad.export');
    Route::get('/contabilidad/libro-mayor', [App\Http\Controllers\ContabilidadController::class, 'libroMayor'])->name('contabilidad.libro_mayor');
    Route::get('/contabilidad/libro-mayor/exportar', [App\Http\Controllers\ContabilidadController::class, 'exportLibroMayor'])->name('contabilidad.libro_mayor.exportar')->middleware('permission:contabilidad.export');
    Route::get('/contabilidad/balance-comprobacion', [App\Http\Controllers\ContabilidadController::class, 'balanceComprobacion'])->name('contabilidad.balance_comprobacion');
    Route::get('/contabilidad/balance-comprobacion/exportar', [App\Http\Controllers\ContabilidadController::class, 'exportBalanceComprobacion'])->name('contabilidad.balance_comprobacion.exportar')->middleware('permission:contabilidad.export');
    Route::resource('cuentas-contables', CuentaContableController::class);

    // ========== GESTIÓN ADMINISTRATIVA ==========
    Route::get('/auditorias', [AuditoriaController::class, 'index'])->name('auditorias.index')->middleware('permission:auditoria.view');
    Route::resource('usuarios', App\Http\Controllers\UsuarioController::class)->except(['show', 'destroy']);
    Route::resource('roles', RolController::class)->except(['show', 'destroy']);

});
