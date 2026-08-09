<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AccountingNavigationTest extends TestCase
{
    public function test_accounting_report_routes_are_registered(): void
    {
        $routes = [
            'contabilidad.index',
            'contabilidad.libro_diario',
            'contabilidad.libro_diario.exportar',
            'contabilidad.libro_mayor',
            'contabilidad.libro_mayor.exportar',
            'contabilidad.balance_comprobacion',
            'contabilidad.balance_comprobacion.exportar',
        ];

        foreach ($routes as $route) {
            $this->assertTrue(Route::has($route), "La ruta {$route} debe existir.");
        }
    }

    public function test_accounting_report_views_exist(): void
    {
        $views = [
            'contabilidad.index',
            'contabilidad.libro_diario',
            'contabilidad.libro_mayor',
            'contabilidad.balance_comprobacion',
        ];

        foreach ($views as $view) {
            $this->assertTrue(view()->exists($view), "La vista {$view} debe existir.");
        }
    }

    public function test_main_navigation_routes_are_registered(): void
    {
        $routes = [
            'dashboard',
            'inventario.dashboard',
            'inventario.reporte_stock',
            'inventario.clasificacion_abc',
            'productos.index',
            'proveedores.index',
            'entradas-compra.index',
            'ventas.index',
            'caja-bancos.dashboard',
            'reportes.dashboard',
            'ordenes-produccion.index',
            'cuentas-contables.index',
        ];

        foreach ($routes as $route) {
            $this->assertTrue(Route::has($route), "La ruta {$route} debe existir.");
        }
    }
}
