<?php

namespace Database\Seeders;

use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        $permisosData = [
            ['codigo' => '*', 'descripcion' => 'Acceso total al sistema'],
            ['codigo' => 'dashboard.view', 'descripcion' => 'Ver dashboard general'],
            ['codigo' => 'inventario.view', 'descripcion' => 'Acceso a modulo de inventario'],
            ['codigo' => 'inventario.create', 'descripcion' => 'Crear movimientos de inventario'],
            ['codigo' => 'inventario.export', 'descripcion' => 'Exportar reportes de inventario'],
            ['codigo' => 'kardex.view', 'descripcion' => 'Ver movimientos de Kardex'],
            ['codigo' => 'entradas.view', 'descripcion' => 'Ver listado de entradas/compras'],
            ['codigo' => 'entradas.create', 'descripcion' => 'Crear nuevas entradas/compras'],
            ['codigo' => 'entradas.approve', 'descripcion' => 'Aprobar o cambiar estado de compras'],
            ['codigo' => 'entradas.pay', 'descripcion' => 'Registrar pagos de compras'],
            ['codigo' => 'ventas.view', 'descripcion' => 'Ver listado de ventas'],
            ['codigo' => 'ventas.create', 'descripcion' => 'Crear nuevas ventas'],
            ['codigo' => 'ventas.collect', 'descripcion' => 'Registrar cobros de ventas'],
            ['codigo' => 'produccion.view', 'descripcion' => 'Acceso a modulo de produccion'],
            ['codigo' => 'produccion.create', 'descripcion' => 'Crear ordenes de produccion'],
            ['codigo' => 'produccion.consume', 'descripcion' => 'Registrar consumo de materiales'],
            ['codigo' => 'produccion.cost', 'descripcion' => 'Registrar costos adicionales de produccion'],
            ['codigo' => 'tareas.view', 'descripcion' => 'Ver tareas de produccion'],
            ['codigo' => 'tareas.update_avance', 'descripcion' => 'Actualizar avance de tareas'],
            ['codigo' => 'reportes.view', 'descripcion' => 'Ver reportes gerenciales'],
            ['codigo' => 'reportes.create', 'descripcion' => 'Crear reportes'],
            ['codigo' => 'reportes.export', 'descripcion' => 'Exportar reportes'],
            ['codigo' => 'caja_bancos.view', 'descripcion' => 'Acceso a modulo de caja y bancos'],
            ['codigo' => 'caja_bancos.create', 'descripcion' => 'Crear cuentas bancarias'],
            ['codigo' => 'caja_bancos.update', 'descripcion' => 'Editar cuentas bancarias'],
            ['codigo' => 'transacciones.view', 'descripcion' => 'Ver transacciones financieras'],
            ['codigo' => 'transacciones.create', 'descripcion' => 'Crear transacciones financieras'],
            ['codigo' => 'plan_contable.view', 'descripcion' => 'Ver plan contable'],
            ['codigo' => 'plan_contable.manage', 'descripcion' => 'Gestionar plan contable'],
            ['codigo' => 'contabilidad.export', 'descripcion' => 'Exportar libros contables'],
            ['codigo' => 'auditoria.view', 'descripcion' => 'Ver auditoria de operaciones'],
            ['codigo' => 'backup.run', 'descripcion' => 'Ejecutar copias de seguridad'],
            ['codigo' => 'usuarios.manage', 'descripcion' => 'Gestionar usuarios'],
            ['codigo' => 'roles.manage', 'descripcion' => 'Gestionar roles y permisos'],
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
                'descripcion' => 'Operario de Produccion - Acceso a tareas y reportes',
                'estado' => true,
                'permisos' => [
                    'produccion.view',
                    'produccion.consume',
                    'produccion.cost',
                    'tareas.view',
                    'tareas.update_avance',
                    'reportes.view',
                    'reportes.create',
                ],
            ],
            [
                'codigo' => 'encargado_almacen',
                'nombre' => 'Encargado de Almacen',
                'descripcion' => 'Encargado de Almacen - Gestion de inventario',
                'estado' => true,
                'permisos' => [
                    'inventario.view',
                    'inventario.create',
                    'inventario.export',
                    'kardex.view',
                    'entradas.view',
                    'entradas.create',
                    'entradas.approve',
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
                    'contabilidad.export',
                    'reportes.view',
                    'reportes.export',
                ],
            ],
        ];

        foreach ($roles as $rolData) {
            $permisos = $rolData['permisos'];
            unset($rolData['permisos']);

            $rol = Rol::updateOrCreate(['codigo' => $rolData['codigo']], $rolData);
            $permisoIds = Permiso::whereIn('codigo', $permisos)->pluck('id')->toArray();
            $rol->permisos()->sync($permisoIds);
        }
    }
}
