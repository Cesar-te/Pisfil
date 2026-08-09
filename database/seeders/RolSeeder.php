<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rol;
use App\Models\Permiso;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permisosData = [
            ['codigo' => '*', 'descripcion' => 'Acceso Total al Sistema'],
            ['codigo' => 'dashboard.view', 'descripcion' => 'Ver Dashboard General'],
            ['codigo' => 'inventario.view', 'descripcion' => 'Acceso a Módulo de Inventario'],
            ['codigo' => 'kardex.view', 'descripcion' => 'Ver movimientos de Kárdex'],
            ['codigo' => 'entradas.view', 'descripcion' => 'Ver listado de Entradas/Compras'],
            ['codigo' => 'entradas.create', 'descripcion' => 'Crear nuevas Entradas/Compras'],
            ['codigo' => 'ventas.view', 'descripcion' => 'Ver listado de Ventas'],
            ['codigo' => 'ventas.create', 'descripcion' => 'Crear nuevas Ventas'],
            ['codigo' => 'produccion.view', 'descripcion' => 'Acceso a Módulo de Producción'],
            ['codigo' => 'tareas.view', 'descripcion' => 'Ver Tareas de Producción'],
            ['codigo' => 'tareas.update_avance', 'descripcion' => 'Actualizar avance de tareas'],
            ['codigo' => 'reportes.view', 'descripcion' => 'Ver reportes gerenciales'],
            ['codigo' => 'reportes.create', 'descripcion' => 'Crear reportes'],
            ['codigo' => 'caja_bancos.view', 'descripcion' => 'Acceso a Módulo de Caja y Bancos'],
            ['codigo' => 'caja_bancos.create', 'descripcion' => 'Crear cuentas bancarias'],
            ['codigo' => 'caja_bancos.update', 'descripcion' => 'Editar cuentas bancarias'],
            ['codigo' => 'transacciones.view', 'descripcion' => 'Ver transacciones financieras'],
            ['codigo' => 'transacciones.create', 'descripcion' => 'Crear transacciones financieras'],
            ['codigo' => 'plan_contable.view', 'descripcion' => 'Ver plan contable'],
            ['codigo' => 'plan_contable.manage', 'descripcion' => 'Gestionar plan contable'],
            ['codigo' => 'usuarios.manage', 'descripcion' => 'Gestionar Usuarios'],
            ['codigo' => 'roles.manage', 'descripcion' => 'Gestionar Roles y Permisos'],
        ];

        foreach ($permisosData as $permiso) {
            Permiso::updateOrCreate(['codigo' => $permiso['codigo']], $permiso);
        }

        $roles = [
            [
                'codigo' => 'gerente',
                'nombre' => 'Gerente',
                'descripcion' => 'Gerente General - Acceso total al sistema',
                'estado' => true,
                'permisos' => ['*'],
            ],
            [
                'codigo' => 'operario',
                'nombre' => 'Operario',
                'descripcion' => 'Operario de Producción - Acceso a tareas y reportes',
                'estado' => true,
                'permisos' => [
                    'produccion.view',
                    'tareas.view',
                    'tareas.update_avance',
                    'reportes.view',
                    'reportes.create',
                ],
            ],
            [
                'codigo' => 'encargado_almacen',
                'nombre' => 'Encargado de Almacén',
                'descripcion' => 'Encargado de Almacén - Gestión de inventario',
                'estado' => true,
                'permisos' => [
                    'inventario.view',
                    'kardex.view',
                    'entradas.view',
                    'entradas.create',
                ],
            ],
            [
                'codigo' => 'encargado_caja_bancos',
                'nombre' => 'Encargado de Caja y Bancos',
                'descripcion' => 'Encargado del control de efectivo, cuentas bancarias y transacciones financieras',
                'estado' => true,
                'permisos' => [
                    'caja_bancos.view',
                    'caja_bancos.create',
                    'caja_bancos.update',
                    'transacciones.view',
                    'transacciones.create',
                    'plan_contable.view',
                    'reportes.view',
                ],
            ],
        ];

        foreach ($roles as $rolData) {
            $permisos = $rolData['permisos'];
            unset($rolData['permisos']);
            
            $rol = Rol::updateOrCreate(['codigo' => $rolData['codigo']], $rolData);
            
            // Attach permissions
            $permisoIds = Permiso::whereIn('codigo', $permisos)->pluck('id')->toArray();
            $rol->permisos()->sync($permisoIds);
        }
    }
}
