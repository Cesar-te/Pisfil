<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer('layouts.app', function ($view) {
            $menus = \App\Models\Menu::whereNull('padre_id')
                ->with([
                    'permiso',
                    'submenus.permiso',
                    'submenus.submenus.permiso',
                    'submenus.submenus.submenus.permiso',
                ])
                ->orderBy('orden')
                ->get();
            $view->with('global_menus', $menus);

            if (! auth()->check()) {
                $view->with('global_notifications', collect());
                $view->with('global_notifications_count', 0);
                return;
            }

            $notifications = $this->buildGlobalNotifications();
            $view->with('global_notifications', $notifications);
            $view->with('global_notifications_count', $notifications->where('countable', true)->count());
        });
    }

    private function buildGlobalNotifications(): \Illuminate\Support\Collection
    {
        $notifications = collect();
        $inicioMes = now()->startOfMonth();
        $finMes = now()->endOfMonth();

        $productosStockBajo = \App\Models\Producto::whereRaw('stock_actual <= stock_minimo')
            ->where('estado', 'activo')
            ->count();

        if ($productosStockBajo > 0) {
            $notifications->push([
                'icon' => 'fas fa-triangle-exclamation',
                'tone' => 'warn',
                'title' => "{$productosStockBajo} producto(s) con stock bajo",
                'description' => 'Revisar reposicion o movimiento de inventario.',
                'url' => route('inventario.stock_bajo'),
                'countable' => true,
            ]);
        }

        $comprasPendientes = \App\Models\EntradaCompra::where('estado_pago', '!=', 'pagado')
            ->withSum('detalles as total_compra', 'costo_total')
            ->get();
        $cuentasPorPagar = $comprasPendientes->sum(fn ($compra) => max(($compra->total_compra ?? 0) - $compra->monto_pagado, 0));
        $proveedoresConDeuda = \App\Models\EntradaCompra::where('estado_pago', '!=', 'pagado')
            ->distinct('proveedor_id')
            ->count('proveedor_id');

        if ($cuentasPorPagar > 0) {
            $notifications->push([
                'icon' => 'fas fa-file-invoice-dollar',
                'tone' => 'warn',
                'title' => 'Cuentas por pagar pendientes',
                'description' => 'S/ ' . number_format($cuentasPorPagar, 2) . " en {$proveedoresConDeuda} proveedor(es).",
                'url' => route('entradas-compra.index'),
                'countable' => true,
            ]);
        }

        $clientesConDeuda = \App\Models\Venta::where('estado_pago', '!=', 'pagado')
            ->distinct('cliente_id')
            ->count('cliente_id');

        if ($clientesConDeuda > 0) {
            $notifications->push([
                'icon' => 'fas fa-hand-holding-dollar',
                'tone' => 'info',
                'title' => 'Cuentas por cobrar abiertas',
                'description' => "{$clientesConDeuda} cliente(s) con saldo pendiente.",
                'url' => route('ventas.index'),
                'countable' => true,
            ]);
        }

        $ordenesPorVencer = \App\Models\OrdenProduccion::where('estado', '!=', 'completada')
            ->whereDate('fecha_fin_planificada', '<=', now()->addDays(7))
            ->where('fecha_fin_planificada', '>', now())
            ->count();

        if ($ordenesPorVencer > 0) {
            $notifications->push([
                'icon' => 'fas fa-industry',
                'tone' => 'warn',
                'title' => 'Ordenes proximas a vencer',
                'description' => "{$ordenesPorVencer} orden(es) requieren seguimiento.",
                'url' => route('ordenes-produccion.index'),
                'countable' => true,
            ]);
        }

        $asientosMes = \App\Models\AsientoContable::whereBetween('fecha', [$inicioMes, $finMes])->count();

        if ($asientosMes === 0) {
            $notifications->push([
                'icon' => 'fas fa-book-open',
                'tone' => 'info',
                'title' => 'Libro Diario sin asientos este mes',
                'description' => 'Verificar si falta registrar operaciones contables.',
                'url' => route('contabilidad.libro_diario'),
                'countable' => true,
            ]);
        }

        if ($notifications->isEmpty()) {
            $notifications->push([
                'icon' => 'fas fa-circle-check',
                'tone' => 'ok',
                'title' => 'Sin alertas pendientes',
                'description' => 'Inventario, pagos y contabilidad sin avisos criticos.',
                'url' => route('dashboard'),
                'countable' => false,
            ]);
        }

        return $notifications;
    }
}
