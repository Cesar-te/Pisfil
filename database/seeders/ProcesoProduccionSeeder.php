<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProcesoProduccion;

class ProcesoProduccionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $procesos = [
            [
                'codigo' => 'PREP-MAT',
                'nombre' => 'Preparación de Materiales',
                'descripcion' => 'Corte y preparación de materiales',
                'duracion_estimada' => 60,
                'duracion_estimada_unidad' => 'minutos',
                'orden_secuencia' => 1,
                'estado' => true,
            ],
            [
                'codigo' => 'SOLDA',
                'nombre' => 'Soldadura',
                'descripcion' => 'Proceso de soldadura de estructuras',
                'duracion_estimada' => 120,
                'duracion_estimada_unidad' => 'minutos',
                'orden_secuencia' => 2,
                'estado' => true,
            ],
            [
                'codigo' => 'ACABADO',
                'nombre' => 'Acabado',
                'descripcion' => 'Pulido y acabado de superficies',
                'duracion_estimada' => 90,
                'duracion_estimada_unidad' => 'minutos',
                'orden_secuencia' => 3,
                'estado' => true,
            ],
            [
                'codigo' => 'PINTURA',
                'nombre' => 'Pintura',
                'descripcion' => 'Aplicación de pintura y recubrimiento',
                'duracion_estimada' => 120,
                'duracion_estimada_unidad' => 'minutos',
                'orden_secuencia' => 4,
                'estado' => true,
            ],
            [
                'codigo' => 'CONTROL-CALIDAD',
                'nombre' => 'Control de Calidad',
                'descripcion' => 'Inspección y control de calidad',
                'duracion_estimada' => 45,
                'duracion_estimada_unidad' => 'minutos',
                'orden_secuencia' => 5,
                'estado' => true,
            ],
        ];

        foreach ($procesos as $proceso) {
            ProcesoProduccion::updateOrCreate(['codigo' => $proceso['codigo']], $proceso);
        }
    }
}
