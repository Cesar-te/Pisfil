<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Process\Process;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('backup:database {--path=}', function () {
    $connectionName = config('database.default');
    $connection = config("database.connections.{$connectionName}");
    $directory = $this->option('path') ?: storage_path('app/backups');
    $timestamp = now()->format('Ymd_His');

    File::ensureDirectoryExists($directory);

    if (($connection['driver'] ?? null) === 'sqlite') {
        $database = $connection['database'] ?? null;
        if (!$database || !File::exists($database)) {
            $this->error('No se encontro el archivo SQLite para respaldar.');
            return Command::FAILURE;
        }

        $target = $directory . DIRECTORY_SEPARATOR . "backup_{$connectionName}_{$timestamp}.sqlite";
        File::copy($database, $target);
        $this->info("Backup creado: {$target}");
        return Command::SUCCESS;
    }

    if (($connection['driver'] ?? null) === 'mysql') {
        $target = $directory . DIRECTORY_SEPARATOR . "backup_{$connection['database']}_{$timestamp}.sql";
        $command = [
            'mysqldump',
            '--host=' . ($connection['host'] ?? '127.0.0.1'),
            '--port=' . ($connection['port'] ?? '3306'),
            '--user=' . ($connection['username'] ?? 'root'),
        ];

        if (!empty($connection['password'])) {
            $command[] = '--password=' . $connection['password'];
        }

        $command[] = $connection['database'];
        $process = new Process($command);
        $process->setTimeout(120);

        $handle = fopen($target, 'w');
        $process->run(function ($type, $buffer) use ($handle) {
            fwrite($handle, $buffer);
        });
        fclose($handle);

        if (!$process->isSuccessful()) {
            File::delete($target);
            $this->error('No se pudo crear el backup. Verifica que mysqldump este instalado y disponible en PATH.');
            $this->line($process->getErrorOutput());
            return Command::FAILURE;
        }

        $this->info("Backup creado: {$target}");
        return Command::SUCCESS;
    }

    $this->error("Driver de base de datos no soportado para backup: {$connection['driver']}");
    return Command::FAILURE;
})->purpose('Crear una copia de seguridad de la base de datos');
