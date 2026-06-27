<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rol;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'codigo' => 'gerente',
                'nombre' => 'Gerente',
                'descripcion' => 'Gerente General - Acceso total al sistema',
                'estado' => true,
                'permisos_json' => json_encode(['*']),
            ],
            [
                'codigo' => 'operario',
                'nombre' => 'Operario',
                'descripcion' => 'Operario de Producción - Acceso a tareas y reportes',
                'estado' => true,
                'permisos_json' => json_encode([
                    'tareas.view',
                    'tareas.update_avance',
                    'reportes.create',
                ]),
            ],
            [
                'codigo' => 'encargado_almacen',
                'nombre' => 'Encargado de Almacén',
                'descripcion' => 'Encargado de Almacén - Gestión de inventario',
                'estado' => true,
                'permisos_json' => json_encode([
                    'inventario.view',
                    'kardex.view',
                    'entradas.view',
                    'entradas.create',
                ]),
            ],
        ];

        foreach ($roles as $rol) {
            Rol::updateOrCreate(['codigo' => $rol['codigo']], $rol);
        }
    }
}
