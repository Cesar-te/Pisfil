<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Permiso;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Dashboard
        Menu::updateOrCreate(['nombre' => 'Dashboard'], [
            'url' => '/dashboard',
            'icono' => 'fas fa-home',
            'orden' => 10,
            'padre_id' => null,
            'permiso_id' => Permiso::where('codigo', 'dashboard.view')->value('id'),
        ]);

        // 2. Inventario
        $inventario = Menu::updateOrCreate(['nombre' => 'Inventario'], [
            'url' => null,
            'icono' => 'fas fa-boxes',
            'orden' => 20,
            'padre_id' => null,
            'permiso_id' => Permiso::where('codigo', 'inventario.view')->value('id'),
        ]);

        Menu::updateOrCreate(['nombre' => 'Dashboard Inventario'], [
            'url' => '/inventario/dashboard',
            'icono' => 'fas fa-chart-pie',
            'orden' => 1,
            'padre_id' => $inventario->id,
            'permiso_id' => Permiso::where('codigo', 'inventario.view')->value('id'),
        ]);

        Menu::updateOrCreate(['nombre' => 'Productos'], [
            'url' => '/productos',
            'icono' => 'fas fa-box',
            'orden' => 2,
            'padre_id' => $inventario->id,
            'permiso_id' => Permiso::where('codigo', 'inventario.view')->value('id'),
        ]);

        Menu::updateOrCreate(['nombre' => 'Kárdex'], [
            'url' => '/inventario/movimientos-kardex',
            'icono' => 'fas fa-exchange-alt',
            'orden' => 3,
            'padre_id' => $inventario->id,
            'permiso_id' => Permiso::where('codigo', 'kardex.view')->value('id'),
        ]);

        Menu::updateOrCreate(['nombre' => 'Movimiento Manual'], [
            'url' => '/inventario/create-movimiento',
            'icono' => 'fas fa-plus-circle',
            'orden' => 4,
            'padre_id' => $inventario->id,
            'permiso_id' => Permiso::where('codigo', 'kardex.view')->value('id'),
        ]);

        Menu::updateOrCreate(['nombre' => 'Stock Bajo'], [
            'url' => '/inventario/stock-bajo',
            'icono' => 'fas fa-triangle-exclamation',
            'orden' => 5,
            'padre_id' => $inventario->id,
            'permiso_id' => Permiso::where('codigo', 'inventario.view')->value('id'),
        ]);

        Menu::updateOrCreate(['nombre' => 'Reporte de Stock'], [
            'url' => '/inventario/reporte-stock',
            'icono' => 'fas fa-clipboard-list',
            'orden' => 6,
            'padre_id' => $inventario->id,
            'permiso_id' => Permiso::where('codigo', 'inventario.view')->value('id'),
        ]);

        Menu::updateOrCreate(['nombre' => 'Clasificación ABC'], [
            'url' => '/inventario/clasificacion-abc',
            'icono' => 'fas fa-ranking-star',
            'orden' => 7,
            'padre_id' => $inventario->id,
            'permiso_id' => Permiso::where('codigo', 'inventario.view')->value('id'),
        ]);

        // 3. Compras
        $compras = Menu::updateOrCreate(['nombre' => 'Compras'], [
            'url' => null,
            'icono' => 'fas fa-shopping-cart',
            'orden' => 30,
            'padre_id' => null,
            'permiso_id' => Permiso::where('codigo', 'entradas.view')->value('id'),
        ]);

        Menu::updateOrCreate(['nombre' => 'Proveedores'], [
            'url' => '/proveedores',
            'icono' => 'fas fa-truck',
            'orden' => 1,
            'padre_id' => $compras->id,
            'permiso_id' => Permiso::where('codigo', 'entradas.view')->value('id'),
        ]);

        Menu::updateOrCreate(['nombre' => 'Entradas'], [
            'url' => '/entradas-compra',
            'icono' => 'fas fa-file-invoice-dollar',
            'orden' => 2,
            'padre_id' => $compras->id,
            'permiso_id' => Permiso::where('codigo', 'entradas.view')->value('id'),
        ]);

        // 4. Ventas
        $ventas = Menu::updateOrCreate(['nombre' => 'Ventas'], [
            'url' => null,
            'icono' => 'fas fa-store',
            'orden' => 40,
            'padre_id' => null,
            'permiso_id' => Permiso::where('codigo', 'ventas.view')->value('id'),
        ]);

        Menu::updateOrCreate(['nombre' => 'Clientes'], [
            'url' => '/clientes',
            'icono' => 'fas fa-users',
            'orden' => 1,
            'padre_id' => $ventas->id,
            'permiso_id' => Permiso::where('codigo', 'ventas.view')->value('id'),
        ]);

        Menu::updateOrCreate(['nombre' => 'Comprobantes'], [
            'url' => '/ventas',
            'icono' => 'fas fa-receipt',
            'orden' => 2,
            'padre_id' => $ventas->id,
            'permiso_id' => Permiso::where('codigo', 'ventas.view')->value('id'),
        ]);

        // 5. Finanzas y contabilidad
        $finanzas = Menu::updateOrCreate(['nombre' => 'Finanzas'], [
            'url' => null,
            'icono' => 'fas fa-chart-line',
            'orden' => 50,
            'padre_id' => null,
            'permiso_id' => Permiso::where('codigo', 'caja_bancos.view')->value('id'),
        ]);

        Menu::updateOrCreate(['nombre' => 'Caja y Bancos'], [
            'url' => '/caja-bancos',
            'icono' => 'fas fa-wallet',
            'orden' => 1,
            'padre_id' => $finanzas->id,
            'permiso_id' => Permiso::where('codigo', 'caja_bancos.view')->value('id'),
        ]);

        Menu::updateOrCreate(['nombre' => 'Resumen Contable'], [
            'url' => '/contabilidad',
            'icono' => 'fas fa-calculator',
            'orden' => 2,
            'padre_id' => $finanzas->id,
            'permiso_id' => Permiso::where('codigo', 'plan_contable.view')->value('id'),
        ]);

        Menu::updateOrCreate(['nombre' => 'Plan de Cuentas'], [
            'url' => '/cuentas-contables',
            'icono' => 'fas fa-list-ol',
            'orden' => 3,
            'padre_id' => $finanzas->id,
            'permiso_id' => Permiso::where('codigo', 'plan_contable.manage')->value('id'),
        ]);

        Menu::updateOrCreate(['nombre' => 'Libro Diario'], [
            'url' => '/contabilidad/libro-diario',
            'icono' => 'fas fa-book-open',
            'orden' => 4,
            'padre_id' => $finanzas->id,
            'permiso_id' => Permiso::where('codigo', 'plan_contable.view')->value('id'),
        ]);

        Menu::updateOrCreate(['nombre' => 'Libro Mayor'], [
            'url' => '/contabilidad/libro-mayor',
            'icono' => 'fas fa-book',
            'orden' => 5,
            'padre_id' => $finanzas->id,
            'permiso_id' => Permiso::where('codigo', 'plan_contable.view')->value('id'),
        ]);

        Menu::updateOrCreate(['nombre' => 'Balance de Comprobacion'], [
            'url' => '/contabilidad/balance-comprobacion',
            'icono' => 'fas fa-balance-scale',
            'orden' => 6,
            'padre_id' => $finanzas->id,
            'permiso_id' => Permiso::where('codigo', 'plan_contable.view')->value('id'),
        ]);

        // 6. Produccion
        $produccion = Menu::updateOrCreate(['nombre' => 'Producción'], [
            'url' => null,
            'icono' => 'fas fa-industry',
            'orden' => 60,
            'padre_id' => null,
            'permiso_id' => Permiso::where('codigo', 'produccion.view')->value('id'),
        ]);

        Menu::updateOrCreate(['nombre' => 'Órdenes de Producción'], [
            'url' => '/ordenes-produccion',
            'icono' => 'fas fa-clipboard-check',
            'orden' => 1,
            'padre_id' => $produccion->id,
            'permiso_id' => Permiso::where('codigo', 'produccion.view')->value('id'),
        ]);

        // 7. Reportes
        Menu::updateOrCreate(['nombre' => 'Reportes'], [
            'url' => '/reportes',
            'icono' => 'fas fa-chart-column',
            'orden' => 70,
            'padre_id' => null,
            'permiso_id' => Permiso::where('codigo', 'reportes.view')->value('id'),
        ]);

        // 8. Administracion
        $admin = Menu::updateOrCreate(['nombre' => 'Administración'], [
            'url' => null,
            'icono' => 'fas fa-cogs',
            'orden' => 99,
            'padre_id' => null,
            'permiso_id' => Permiso::where('codigo', 'usuarios.manage')->value('id'),
        ]);

        Menu::updateOrCreate(['nombre' => 'Usuarios'], [
            'url' => '/usuarios',
            'icono' => 'fas fa-user-shield',
            'orden' => 1,
            'padre_id' => $admin->id,
            'permiso_id' => Permiso::where('codigo', 'usuarios.manage')->value('id'),
        ]);

        Menu::updateOrCreate(['nombre' => 'Roles y Permisos'], [
            'url' => '/roles',
            'icono' => 'fas fa-key',
            'orden' => 2,
            'padre_id' => $admin->id,
            'permiso_id' => Permiso::where('codigo', 'roles.manage')->value('id'),
        ]);
    }
}
