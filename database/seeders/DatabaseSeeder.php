<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecutar seeders en orden de dependencias
        $this->call([
            RolSeeder::class,
            CategoriaSeeder::class,
            UnidadMedidaSeeder::class,
            ProcesoProduccionSeeder::class,
            UsuarioSeeder::class,
            ProductoSeeder::class,
        ]);
    }
}
