<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UnidadMedida;

class UnidadMedidaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unidades = [
            [
                'codigo' => 'KG',
                'nombre' => 'Kilogramo',
                'simbolo' => 'kg',
                'descripcion' => 'Unidad de masa',
                'estado' => true,
            ],
            [
                'codigo' => 'M',
                'nombre' => 'Metro',
                'simbolo' => 'm',
                'descripcion' => 'Unidad de longitud',
                'estado' => true,
            ],
            [
                'codigo' => 'M2',
                'nombre' => 'Metro Cuadrado',
                'simbolo' => 'm²',
                'descripcion' => 'Unidad de área',
                'estado' => true,
            ],
            [
                'codigo' => 'PZ',
                'nombre' => 'Pieza',
                'simbolo' => 'pz',
                'descripcion' => 'Unidad de cantidad',
                'estado' => true,
            ],
            [
                'codigo' => 'L',
                'nombre' => 'Litro',
                'simbolo' => 'L',
                'descripcion' => 'Unidad de volumen',
                'estado' => true,
            ],
        ];

        foreach ($unidades as $unidad) {
            UnidadMedida::updateOrCreate(['codigo' => $unidad['codigo']], $unidad);
        }
    }
}
