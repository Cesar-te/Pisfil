<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            [
                'codigo' => 'MAT-PRIMA',
                'nombre' => 'Materia Prima',
                'descripcion' => 'Materiales y materias primas para la producción',
                'estado' => true,
            ],
            [
                'codigo' => 'HERRAJE',
                'nombre' => 'Herrajes y Accesorios',
                'descripcion' => 'Tornillos, tuercas, chavetas y otros accesorios',
                'estado' => true,
            ],
            [
                'codigo' => 'CONSUMI',
                'nombre' => 'Consumibles',
                'descripcion' => 'Materiales consumibles en producción',
                'estado' => true,
            ],
            [
                'codigo' => 'PRODUCTO',
                'nombre' => 'Productos Terminados',
                'descripcion' => 'Estructuras metálicas terminadas',
                'estado' => true,
            ],
        ];

        foreach ($categorias as $categoria) {
            Categoria::updateOrCreate(['codigo' => $categoria['codigo']], $categoria);
        }
    }
}
