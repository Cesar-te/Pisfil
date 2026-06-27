<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Rol;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener roles
        $rolGerente = Rol::where('codigo', 'gerente')->first();
        $rolOperario = Rol::where('codigo', 'operario')->first();
        $rolEncargadoAlmacen = Rol::where('codigo', 'encargado_almacen')->first();

        $usuarios = [
            [
                'name' => 'Gerente General',
                'email' => 'gerente@pisfil.com',
                'password' => bcrypt('password'),
                'documento_identidad' => '12345678',
                'telefono' => '074-000000',
                'rol_id' => $rolGerente?->id,
                'estado' => true,
            ],
            [
                'name' => 'Juan Soldador',
                'email' => 'juan.soldador@pisfil.com',
                'password' => bcrypt('password'),
                'documento_identidad' => '87654321',
                'telefono' => '952-123456',
                'rol_id' => $rolOperario?->id,
                'estado' => true,
            ],
            [
                'name' => 'María Acabado',
                'email' => 'maria.acabado@pisfil.com',
                'password' => bcrypt('password'),
                'documento_identidad' => '45678912',
                'telefono' => '952-234567',
                'rol_id' => $rolOperario?->id,
                'estado' => true,
            ],
            [
                'name' => 'Carlos Almacén',
                'email' => 'carlos.almacen@pisfil.com',
                'password' => bcrypt('password'),
                'documento_identidad' => '23456789',
                'telefono' => '952-345678',
                'rol_id' => $rolEncargadoAlmacen?->id,
                'estado' => true,
            ],
        ];

        foreach ($usuarios as $usuario) {
            User::updateOrCreate(['email' => $usuario['email']], $usuario);
        }
    }
}
