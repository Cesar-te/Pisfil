<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\UnidadMedida;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categMateriaPrima = Categoria::where('codigo', 'MAT-PRIMA')->first();
        $categHerraje = Categoria::where('codigo', 'HERRAJE')->first();
        $unidadKg = UnidadMedida::where('codigo', 'KG')->first();
        $unidadPz = UnidadMedida::where('codigo', 'PZ')->first();

        $productos = [
            [
                'codigo' => 'TUBO-50x50',
                'nombre' => 'Tubo Cuadrado 50x50 mm',
                'descripcion' => 'Tubo de acero cuadrado de 50x50 mm',
                'categoria_id' => $categMateriaPrima?->id,
                'unidad_medida_id' => $unidadKg?->id,
                'precio_unitario' => 45.50,
                'stock_minimo' => 50,
                'stock_maximo' => 500,
                'stock_actual' => 150,
                'estado' => 'activo',
            ],
            [
                'codigo' => 'TORNILLO-M8',
                'nombre' => 'Tornillo M8 x 30 mm',
                'descripcion' => 'Tornillo de acero inoxidable',
                'categoria_id' => $categHerraje?->id,
                'unidad_medida_id' => $unidadPz?->id,
                'precio_unitario' => 0.85,
                'stock_minimo' => 500,
                'stock_maximo' => 5000,
                'stock_actual' => 2000,
                'estado' => 'activo',
            ],
            [
                'codigo' => 'TUERCA-M8',
                'nombre' => 'Tuerca M8',
                'descripcion' => 'Tuerca de acero inoxidable',
                'categoria_id' => $categHerraje?->id,
                'unidad_medida_id' => $unidadPz?->id,
                'precio_unitario' => 0.45,
                'stock_minimo' => 500,
                'stock_maximo' => 5000,
                'stock_actual' => 1500,
                'estado' => 'activo',
            ],
            [
                'codigo' => 'PLANCHETA-3MM',
                'nombre' => 'Plancheta de acero 3 mm',
                'descripcion' => 'Plancheta de acero al carbono',
                'categoria_id' => $categMateriaPrima?->id,
                'unidad_medida_id' => $unidadKg?->id,
                'precio_unitario' => 52.00,
                'stock_minimo' => 100,
                'stock_maximo' => 1000,
                'stock_actual' => 80,
                'estado' => 'activo',
            ],
        ];

        foreach ($productos as $producto) {
            Producto::updateOrCreate(['codigo' => $producto['codigo']], $producto);
        }
    }
}
