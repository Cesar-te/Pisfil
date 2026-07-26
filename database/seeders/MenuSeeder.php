<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Permiso;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Menú Dashboard
        Menu::updateOrCreate(['nombre' => 'Dashboard'], [
            'url' => '/dashboard',
            'icono' => 'fas fa-home',
            'orden' => 10,
            'permiso_id' => Permiso::where('codigo', 'dashboard.view')->value('id')
        ]);

        // 2. Menú Inventario y sus submenús
        $inventario = Menu::updateOrCreate(['nombre' => 'Inventario'], [
            'url' => null,
            'icono' => 'fas fa-boxes',
            'orden' => 20,
            'permiso_id' => Permiso::where('codigo', 'inventario.view')->value('id')
        ]);

        Menu::updateOrCreate(['nombre' => 'Productos'], [
            'url' => '/inventario/productos',
            'icono' => 'fas fa-box',
            'orden' => 1,
            'padre_id' => $inventario->id,
            'permiso_id' => Permiso::where('codigo', 'inventario.view')->value('id')
        ]);

        Menu::updateOrCreate(['nombre' => 'Kárdex'], [
            'url' => '/inventario/movimientos-kardex',
            'icono' => 'fas fa-exchange-alt',
            'orden' => 2,
            'padre_id' => $inventario->id,
            'permiso_id' => Permiso::where('codigo', 'kardex.view')->value('id')
        ]);

        // 3. Menú Compras
        $compras = Menu::updateOrCreate(['nombre' => 'Compras'], [
            'url' => null,
            'icono' => 'fas fa-shopping-cart',
            'orden' => 30,
            'permiso_id' => Permiso::where('codigo', 'entradas.view')->value('id')
        ]);

        Menu::updateOrCreate(['nombre' => 'Proveedores'], [
            'url' => '/proveedores',
            'icono' => 'fas fa-truck',
            'orden' => 1,
            'padre_id' => $compras->id,
        ]);

        Menu::updateOrCreate(['nombre' => 'Entradas'], [
            'url' => '/entradas-compra',
            'icono' => 'fas fa-file-invoice-dollar',
            'orden' => 2,
            'padre_id' => $compras->id,
            'permiso_id' => Permiso::where('codigo', 'entradas.view')->value('id')
        ]);

        // 4. Menú Ventas
        $ventas = Menu::updateOrCreate(['nombre' => 'Ventas'], [
            'url' => null,
            'icono' => 'fas fa-store',
            'orden' => 40,
            'permiso_id' => Permiso::where('codigo', 'ventas.view')->value('id')
        ]);

        Menu::updateOrCreate(['nombre' => 'Clientes'], [
            'url' => '/clientes',
            'icono' => 'fas fa-users',
            'orden' => 1,
            'padre_id' => $ventas->id,
        ]);

        Menu::updateOrCreate(['nombre' => 'Comprobantes'], [
            'url' => '/ventas',
            'icono' => 'fas fa-receipt',
            'orden' => 2,
            'padre_id' => $ventas->id,
            'permiso_id' => Permiso::where('codigo', 'ventas.view')->value('id')
        ]);

        // 5. Menú Finanzas
        $finanzas = Menu::updateOrCreate(['nombre' => 'Finanzas'], [
            'url' => null,
            'icono' => 'fas fa-chart-line',
            'orden' => 50,
            'permiso_id' => Permiso::where('codigo', 'caja_bancos.view')->value('id')
        ]);

        Menu::updateOrCreate(['nombre' => 'Caja y Bancos'], [
            'url' => '/caja-bancos',
            'icono' => 'fas fa-wallet',
            'orden' => 1,
            'padre_id' => $finanzas->id,
            'permiso_id' => Permiso::where('codigo', 'caja_bancos.view')->value('id')
        ]);

        Menu::updateOrCreate(['nombre' => 'Plan de Cuentas'], [
            'url' => '/cuentas-contables',
            'icono' => 'fas fa-list-ol',
            'orden' => 2,
            'padre_id' => $finanzas->id,
            'permiso_id' => Permiso::where('codigo', 'plan_contable.manage')->value('id')
        ]);

        // 6. Administración
        $admin = Menu::updateOrCreate(['nombre' => 'Administración'], [
            'url' => null,
            'icono' => 'fas fa-cogs',
            'orden' => 99,
            'permiso_id' => Permiso::where('codigo', 'usuarios.manage')->value('id')
        ]);

        Menu::updateOrCreate(['nombre' => 'Usuarios'], [
            'url' => '/usuarios',
            'icono' => 'fas fa-user-shield',
            'orden' => 1,
            'padre_id' => $admin->id,
            'permiso_id' => Permiso::where('codigo', 'usuarios.manage')->value('id')
        ]);

        Menu::updateOrCreate(['nombre' => 'Roles y Permisos'], [
            'url' => '/roles',
            'icono' => 'fas fa-key',
            'orden' => 2,
            'padre_id' => $admin->id,
            'permiso_id' => Permiso::where('codigo', 'roles.manage')->value('id')
        ]);
    }
}
