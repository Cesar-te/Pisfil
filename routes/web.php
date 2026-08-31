<?php

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CajaBancosController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ConsumoMaterialController;
use App\Http\Controllers\ContabilidadController;
use App\Http\Controllers\CostoProduccionController;
use App\Http\Controllers\CuentaContableController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EntradaCompraController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\OrdenProduccionController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\TareaProduccionController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('permission:dashboard.view');

    Route::group(['prefix' => 'inventario', 'as' => 'inventario.', 'middleware' => 'permission:inventario.view'], function () {
        Route::get('/dashboard', [InventarioController::class, 'dashboard'])->name('dashboard');
        Route::get('/productos', [InventarioController::class, 'productos'])->name('productos');
        Route::get('/movimientos-kardex', [InventarioController::class, 'movimientosKardex'])
            ->name('movimientos_kardex')
            ->middleware('permission:kardex.view');
        Route::get('/reporte-stock', [InventarioController::class, 'reporteStock'])->name('reporte_stock');
        Route::get('/stock-bajo', [InventarioController::class, 'stockBajo'])->name('stock_bajo');
        Route::get('/clasificacion-abc', [InventarioController::class, 'clasificacionABC'])->name('clasificacion_abc');
        Route::get('/create-movimiento', [InventarioController::class, 'createMovimiento'])
            ->name('create_movimiento')
            ->middleware('permission:inventario.create');
        Route::post('/store-movimiento', [InventarioController::class, 'storeMovimiento'])
            ->name('store_movimiento')
            ->middleware('permission:inventario.create');
    });

    Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index')->middleware('permission:inventario.view');
    Route::get('/productos/create', [ProductoController::class, 'create'])->name('productos.create')->middleware('permission:inventario.create');
    Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store')->middleware('permission:inventario.create');
    Route::get('/productos/{producto}', [ProductoController::class, 'show'])->name('productos.show')->middleware('permission:inventario.view');
    Route::get('/productos/{producto}/edit', [ProductoController::class, 'edit'])->name('productos.edit')->middleware('permission:inventario.create');
    Route::match(['put', 'patch'], '/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update')->middleware('permission:inventario.create');
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy')->middleware('permission:inventario.create');
    Route::get('/productos-activos', [ProductoController::class, 'activos'])
        ->name('productos.activos')
        ->middleware('permission:inventario.view');

    // Categorías (AJAX)
    Route::get('/categorias', [ProductoController::class, 'categoriasIndex'])->name('categorias.index')->middleware('permission:inventario.view');
    Route::post('/categorias', [ProductoController::class, 'categoriasStore'])->name('categorias.store')->middleware('permission:inventario.create');

    Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedores.index')->middleware('permission:entradas.view');
    Route::get('/proveedores/create', [ProveedorController::class, 'create'])->name('proveedores.create')->middleware('permission:entradas.create');
    Route::post('/proveedores', [ProveedorController::class, 'store'])->name('proveedores.store')->middleware('permission:entradas.create');
    Route::get('/proveedores/{proveedor}', [ProveedorController::class, 'show'])->name('proveedores.show')->middleware('permission:entradas.view');
    Route::get('/proveedores/{proveedor}/edit', [ProveedorController::class, 'edit'])->name('proveedores.edit')->middleware('permission:entradas.create');
    Route::match(['put', 'patch'], '/proveedores/{proveedor}', [ProveedorController::class, 'update'])->name('proveedores.update')->middleware('permission:entradas.create');
    Route::delete('/proveedores/{proveedor}', [ProveedorController::class, 'destroy'])->name('proveedores.destroy')->middleware('permission:entradas.create');
    Route::get('/proveedores-activos', [ProveedorController::class, 'activos'])
        ->name('proveedores.activos')
        ->middleware('permission:entradas.view');

    Route::get('/entradas-compra', [EntradaCompraController::class, 'index'])->name('entradas-compra.index')->middleware('permission:entradas.view');
    Route::get('/entradas-compra/create', [EntradaCompraController::class, 'create'])->name('entradas-compra.create')->middleware('permission:entradas.create');
    Route::post('/entradas-compra', [EntradaCompraController::class, 'store'])->name('entradas-compra.store')->middleware('permission:entradas.create');
    Route::get('/entradas-compra/{entradaCompra}', [EntradaCompraController::class, 'show'])->name('entradas-compra.show')->middleware('permission:entradas.view');
    Route::get('/entradas-compra/{entradaCompra}/edit', [EntradaCompraController::class, 'edit'])->name('entradas-compra.edit')->middleware('permission:entradas.create');
    Route::match(['put', 'patch'], '/entradas-compra/{entradaCompra}', [EntradaCompraController::class, 'update'])->name('entradas-compra.update')->middleware('permission:entradas.create');
    Route::delete('/entradas-compra/{entradaCompra}', [EntradaCompraController::class, 'destroy'])->name('entradas-compra.destroy')->middleware('permission:entradas.create');
    Route::post('/entradas-compra/{entradaCompra}/cambiar-estado', [EntradaCompraController::class, 'cambiarEstado'])
        ->name('entradas-compra.cambiar-estado')
        ->middleware('permission:entradas.approve');
    Route::post('/entradas-compra/{entradaCompra}/agregar-detalle', [EntradaCompraController::class, 'agregarDetalle'])
        ->name('entradas-compra.agregar-detalle')
        ->middleware('permission:entradas.create');
    Route::post('/entradas-compra/{entradaCompra}/registrar-pago', [EntradaCompraController::class, 'registrarPago'])
        ->name('entradas-compra.registrar-pago')
        ->middleware('permission:entradas.pay');

    Route::get('/ordenes-produccion', [OrdenProduccionController::class, 'index'])->name('ordenes-produccion.index')->middleware('permission:produccion.view');
    Route::get('/ordenes-produccion/create', [OrdenProduccionController::class, 'create'])->name('ordenes-produccion.create')->middleware('permission:produccion.create');
    Route::post('/ordenes-produccion', [OrdenProduccionController::class, 'store'])->name('ordenes-produccion.store')->middleware('permission:produccion.create');
    Route::get('/ordenes-produccion/{ordenProduccion}', [OrdenProduccionController::class, 'show'])->name('ordenes-produccion.show')->middleware('permission:produccion.view');
    Route::post('/ordenes-produccion/{ordenProduccion}/estado', [OrdenProduccionController::class, 'updateEstado'])
        ->name('ordenes-produccion.estado')
        ->middleware('permission:produccion.create');
    Route::post('/ordenes-produccion/{ordenProduccion}/tareas', [TareaProduccionController::class, 'store'])
        ->name('tareas-produccion.store')
        ->middleware('permission:produccion.create');
    Route::post('/tareas-produccion/{tareaProduccion}/avance', [TareaProduccionController::class, 'updateAvance'])
        ->name('tareas-produccion.avance')
        ->middleware('permission:tareas.update_avance');
    Route::post('/ordenes-produccion/{ordenProduccion}/consumo', [ConsumoMaterialController::class, 'store'])
        ->name('consumos-material.store')
        ->middleware('permission:produccion.consume');
    Route::post('/ordenes-produccion/{ordenProduccion}/costos', [CostoProduccionController::class, 'store'])
        ->name('costos-produccion.store')
        ->middleware('permission:produccion.cost');

    Route::get('/caja-bancos', [CajaBancosController::class, 'dashboard'])
        ->name('caja-bancos.dashboard')
        ->middleware('permission:caja_bancos.view');
    Route::post('/caja-bancos/cuentas', [CajaBancosController::class, 'storeCuenta'])
        ->name('caja-bancos.store-cuenta')
        ->middleware('permission:caja_bancos.create');
    Route::get('/caja-bancos/cuentas/{cuenta}', [CajaBancosController::class, 'showCuenta'])
        ->name('caja-bancos.show-cuenta')
        ->middleware('permission:caja_bancos.view');
    Route::post('/caja-bancos/cuentas/{cuenta}/movimiento', [CajaBancosController::class, 'registrarMovimiento'])
        ->name('caja-bancos.movimiento')
        ->middleware('permission:transacciones.create');
    Route::post('/caja-bancos/cuentas/{cuentaOrigen}/transferencia', [CajaBancosController::class, 'registrarTransferencia'])
        ->name('caja-bancos.transferencia')
        ->middleware('permission:transacciones.create');

    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index')->middleware('permission:ventas.view');
    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store')->middleware('permission:ventas.create');
    Route::match(['put', 'patch'], '/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update')->middleware('permission:ventas.create');
    Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index')->middleware('permission:ventas.view');
    Route::get('/ventas/create', [VentaController::class, 'create'])->name('ventas.create')->middleware('permission:ventas.create');
    Route::post('/ventas', [VentaController::class, 'store'])->name('ventas.store')->middleware('permission:ventas.create');
    Route::get('/ventas/{venta}', [VentaController::class, 'show'])->name('ventas.show')->middleware('permission:ventas.view');
    Route::post('/ventas/{venta}/registrar-cobro', [VentaController::class, 'registrarCobro'])
        ->name('ventas.registrar-cobro')
        ->middleware('permission:ventas.collect');

    Route::get('/reportes', [ReporteController::class, 'dashboard'])
        ->name('reportes.dashboard')
        ->middleware('permission:reportes.view');
    Route::get('/reportes/exportar/ventas', [ReporteController::class, 'exportVentas'])
        ->name('reportes.exportar.ventas')
        ->middleware('permission:reportes.export');
    Route::get('/reportes/exportar/compras', [ReporteController::class, 'exportCompras'])
        ->name('reportes.exportar.compras')
        ->middleware('permission:reportes.export');
    Route::get('/reportes/exportar/stock', [ReporteController::class, 'exportStock'])
        ->name('reportes.exportar.stock')
        ->middleware('permission:reportes.export');
    Route::get('/reportes/exportar/kardex', [ReporteController::class, 'exportKardex'])
        ->name('reportes.exportar.kardex')
        ->middleware('permission:reportes.export');
    Route::get('/reportes/exportar/caja', [ReporteController::class, 'exportCaja'])
        ->name('reportes.exportar.caja')
        ->middleware('permission:reportes.export');

    Route::get('/contabilidad', [ContabilidadController::class, 'index'])
        ->name('contabilidad.index')
        ->middleware('permission:plan_contable.view');
    Route::get('/contabilidad/plan-cuentas', [ContabilidadController::class, 'planCuentas'])
        ->name('contabilidad.plan_cuentas')
        ->middleware('permission:plan_contable.view');
    Route::get('/contabilidad/libro-diario', [ContabilidadController::class, 'libroDiario'])
        ->name('contabilidad.libro_diario')
        ->middleware('permission:plan_contable.view');
    Route::get('/contabilidad/libro-diario/exportar', [ContabilidadController::class, 'exportLibroDiario'])
        ->name('contabilidad.libro_diario.exportar')
        ->middleware('permission:contabilidad.export');
    Route::get('/contabilidad/libro-mayor', [ContabilidadController::class, 'libroMayor'])
        ->name('contabilidad.libro_mayor')
        ->middleware('permission:plan_contable.view');
    Route::get('/contabilidad/libro-mayor/exportar', [ContabilidadController::class, 'exportLibroMayor'])
        ->name('contabilidad.libro_mayor.exportar')
        ->middleware('permission:contabilidad.export');
    Route::get('/contabilidad/balance-comprobacion', [ContabilidadController::class, 'balanceComprobacion'])
        ->name('contabilidad.balance_comprobacion')
        ->middleware('permission:plan_contable.view');
    Route::get('/contabilidad/balance-comprobacion/exportar', [ContabilidadController::class, 'exportBalanceComprobacion'])
        ->name('contabilidad.balance_comprobacion.exportar')
        ->middleware('permission:contabilidad.export');
    Route::resource('cuentas-contables', CuentaContableController::class)
        ->parameters(['cuentas-contables' => 'cuentaContable'])
        ->middleware('permission:plan_contable.manage');

    Route::get('/auditorias', [AuditoriaController::class, 'index'])
        ->name('auditorias.index')
        ->middleware('permission:auditoria.view');
    Route::resource('usuarios', UsuarioController::class)
        ->except(['show', 'destroy'])
        ->middleware('permission:usuarios.manage');
    Route::resource('roles', RolController::class)
        ->except(['show', 'destroy'])
        ->middleware('permission:roles.manage');
});
