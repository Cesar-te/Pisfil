<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CuentaContable;
use Illuminate\Support\Facades\DB;

class PlanContableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        CuentaContable::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $cuentas = [
            // ELEMENTO 1: ACTIVO DISPONIBLE Y EXIGIBLE
            ['codigo' => '10', 'descripcion' => 'Efectivo y equivalentes de efectivo', 'elemento' => '1', 'tipo' => 'Activo'],
            ['codigo' => '101', 'descripcion' => 'Caja', 'padre_codigo' => '10'],
            ['codigo' => '102', 'descripcion' => 'Fondos fijos', 'padre_codigo' => '10'],
            ['codigo' => '103', 'descripcion' => 'Efectivo en tránsito', 'padre_codigo' => '10'],
            ['codigo' => '104', 'descripcion' => 'Cuentas corrientes en instituciones financieras', 'padre_codigo' => '10'],
            
            ['codigo' => '11', 'descripcion' => 'Inversiones financieras', 'elemento' => '1', 'tipo' => 'Activo'],
            ['codigo' => '111', 'descripcion' => 'Inversiones mantenidas para negociación', 'padre_codigo' => '11'],
            ['codigo' => '112', 'descripcion' => 'Inversiones disponibles para la venta', 'padre_codigo' => '11'],
            
            ['codigo' => '12', 'descripcion' => 'Cuentas por cobrar comerciales - Terceros', 'elemento' => '1', 'tipo' => 'Activo'],
            ['codigo' => '121', 'descripcion' => 'Facturas, boletas y otros comprobantes por cobrar', 'padre_codigo' => '12'],
            ['codigo' => '122', 'descripcion' => 'Anticipos de clientes', 'padre_codigo' => '12'],
            ['codigo' => '123', 'descripcion' => 'Letras por cobrar', 'padre_codigo' => '12'],
            
            ['codigo' => '13', 'descripcion' => 'Cuentas por cobrar comerciales - Relacionadas', 'elemento' => '1', 'tipo' => 'Activo'],
            ['codigo' => '14', 'descripcion' => 'Cuentas por cobrar al personal, accionistas y gerentes', 'elemento' => '1', 'tipo' => 'Activo'],
            ['codigo' => '16', 'descripcion' => 'Cuentas por cobrar diversas - Terceros', 'elemento' => '1', 'tipo' => 'Activo'],
            ['codigo' => '17', 'descripcion' => 'Cuentas por cobrar diversas - Relacionadas', 'elemento' => '1', 'tipo' => 'Activo'],
            ['codigo' => '18', 'descripcion' => 'Servicios y otros contratados por anticipado', 'elemento' => '1', 'tipo' => 'Activo'],
            ['codigo' => '19', 'descripcion' => 'Estimación de cuentas de cobranza dudosa', 'elemento' => '1', 'tipo' => 'Activo'],

            // ELEMENTO 2: ACTIVO REALIZABLE
            ['codigo' => '20', 'descripcion' => 'Mercaderías', 'elemento' => '2', 'tipo' => 'Activo'],
            ['codigo' => '201', 'descripcion' => 'Mercaderías manufacturadas', 'padre_codigo' => '20'],
            ['codigo' => '21', 'descripcion' => 'Productos terminados', 'elemento' => '2', 'tipo' => 'Activo'],
            ['codigo' => '211', 'descripcion' => 'Productos manufacturados', 'padre_codigo' => '21'],
            ['codigo' => '22', 'descripcion' => 'Subproductos, desechos y desperdicios', 'elemento' => '2', 'tipo' => 'Activo'],
            ['codigo' => '23', 'descripcion' => 'Productos en proceso', 'elemento' => '2', 'tipo' => 'Activo'],
            ['codigo' => '24', 'descripcion' => 'Materias primas', 'elemento' => '2', 'tipo' => 'Activo'],
            ['codigo' => '25', 'descripcion' => 'Materiales auxiliares, suministros y repuestos', 'elemento' => '2', 'tipo' => 'Activo'],
            ['codigo' => '26', 'descripcion' => 'Envases y embalajes', 'elemento' => '2', 'tipo' => 'Activo'],
            ['codigo' => '27', 'descripcion' => 'Activos no corrientes mantenidos para la venta', 'elemento' => '2', 'tipo' => 'Activo'],
            ['codigo' => '28', 'descripcion' => 'Existencias por recibir', 'elemento' => '2', 'tipo' => 'Activo'],
            ['codigo' => '29', 'descripcion' => 'Desvalorización de existencias', 'elemento' => '2', 'tipo' => 'Activo'],

            // ELEMENTO 3: ACTIVO INMOVILIZADO
            ['codigo' => '30', 'descripcion' => 'Inversiones mobiliarias', 'elemento' => '3', 'tipo' => 'Activo'],
            ['codigo' => '31', 'descripcion' => 'Inversiones inmobiliarias', 'elemento' => '3', 'tipo' => 'Activo'],
            ['codigo' => '32', 'descripcion' => 'Activos adquiridos en arrendamiento financiero', 'elemento' => '3', 'tipo' => 'Activo'],
            ['codigo' => '33', 'descripcion' => 'Inmuebles, maquinaria y equipo', 'elemento' => '3', 'tipo' => 'Activo'],
            ['codigo' => '331', 'descripcion' => 'Terrenos', 'padre_codigo' => '33'],
            ['codigo' => '332', 'descripcion' => 'Edificaciones', 'padre_codigo' => '33'],
            ['codigo' => '333', 'descripcion' => 'Maquinarias y equipos de explotación', 'padre_codigo' => '33'],
            ['codigo' => '334', 'descripcion' => 'Unidades de transporte', 'padre_codigo' => '33'],
            ['codigo' => '335', 'descripcion' => 'Muebles y enseres', 'padre_codigo' => '33'],
            ['codigo' => '336', 'descripcion' => 'Equipos diversos', 'padre_codigo' => '33'],
            ['codigo' => '34', 'descripcion' => 'Intangibles', 'elemento' => '3', 'tipo' => 'Activo'],
            ['codigo' => '35', 'descripcion' => 'Activos biológicos', 'elemento' => '3', 'tipo' => 'Activo'],
            ['codigo' => '36', 'descripcion' => 'Desvalorización de activo inmovilizado', 'elemento' => '3', 'tipo' => 'Activo'],
            ['codigo' => '37', 'descripcion' => 'Activo diferido', 'elemento' => '3', 'tipo' => 'Activo'],
            ['codigo' => '38', 'descripcion' => 'Otros activos', 'elemento' => '3', 'tipo' => 'Activo'],
            ['codigo' => '39', 'descripcion' => 'Depreciación y amortización acumulados', 'elemento' => '3', 'tipo' => 'Activo'],

            // ELEMENTO 4: PASIVO
            ['codigo' => '40', 'descripcion' => 'Tributos, contraprestaciones y aportes al sistema de salud por pagar', 'elemento' => '4', 'tipo' => 'Pasivo'],
            ['codigo' => '401', 'descripcion' => 'Gobierno central', 'padre_codigo' => '40'],
            ['codigo' => '41', 'descripcion' => 'Remuneraciones y participaciones por pagar', 'elemento' => '4', 'tipo' => 'Pasivo'],
            ['codigo' => '411', 'descripcion' => 'Remuneraciones por pagar', 'padre_codigo' => '41'],
            ['codigo' => '42', 'descripcion' => 'Cuentas por pagar comerciales - Terceros', 'elemento' => '4', 'tipo' => 'Pasivo'],
            ['codigo' => '421', 'descripcion' => 'Facturas, boletas y otros comprobantes por pagar', 'padre_codigo' => '42'],
            ['codigo' => '43', 'descripcion' => 'Cuentas por pagar comerciales - Relacionadas', 'elemento' => '4', 'tipo' => 'Pasivo'],
            ['codigo' => '44', 'descripcion' => 'Cuentas por pagar a los accionistas, directores y gerentes', 'elemento' => '4', 'tipo' => 'Pasivo'],
            ['codigo' => '45', 'descripcion' => 'Obligaciones financieras', 'elemento' => '4', 'tipo' => 'Pasivo'],
            ['codigo' => '46', 'descripcion' => 'Cuentas por pagar diversas - Terceros', 'elemento' => '4', 'tipo' => 'Pasivo'],
            ['codigo' => '47', 'descripcion' => 'Cuentas por pagar diversas - Relacionadas', 'elemento' => '4', 'tipo' => 'Pasivo'],
            ['codigo' => '48', 'descripcion' => 'Provisiones', 'elemento' => '4', 'tipo' => 'Pasivo'],
            ['codigo' => '49', 'descripcion' => 'Pasivo diferido', 'elemento' => '4', 'tipo' => 'Pasivo'],

            // ELEMENTO 5: PATRIMONIO
            ['codigo' => '50', 'descripcion' => 'Capital', 'elemento' => '5', 'tipo' => 'Patrimonio'],
            ['codigo' => '501', 'descripcion' => 'Capital social', 'padre_codigo' => '50'],
            ['codigo' => '51', 'descripcion' => 'Acciones de inversión', 'elemento' => '5', 'tipo' => 'Patrimonio'],
            ['codigo' => '52', 'descripcion' => 'Capital adicional', 'elemento' => '5', 'tipo' => 'Patrimonio'],
            ['codigo' => '56', 'descripcion' => 'Resultados no realizados', 'elemento' => '5', 'tipo' => 'Patrimonio'],
            ['codigo' => '57', 'descripcion' => 'Excedente de revaluación', 'elemento' => '5', 'tipo' => 'Patrimonio'],
            ['codigo' => '58', 'descripcion' => 'Reservas', 'elemento' => '5', 'tipo' => 'Patrimonio'],
            ['codigo' => '59', 'descripcion' => 'Resultados acumulados', 'elemento' => '5', 'tipo' => 'Patrimonio'],
            ['codigo' => '591', 'descripcion' => 'Utilidades no distribuidas', 'padre_codigo' => '59'],

            // ELEMENTO 6: GASTOS POR NATURALEZA
            ['codigo' => '60', 'descripcion' => 'Compras', 'elemento' => '6', 'tipo' => 'Gasto'],
            ['codigo' => '601', 'descripcion' => 'Mercaderías', 'padre_codigo' => '60'],
            ['codigo' => '602', 'descripcion' => 'Materias primas', 'padre_codigo' => '60'],
            ['codigo' => '61', 'descripcion' => 'Variación de existencias', 'elemento' => '6', 'tipo' => 'Gasto'],
            ['codigo' => '62', 'descripcion' => 'Gastos de personal, directores y gerentes', 'elemento' => '6', 'tipo' => 'Gasto'],
            ['codigo' => '621', 'descripcion' => 'Remuneraciones', 'padre_codigo' => '62'],
            ['codigo' => '63', 'descripcion' => 'Gastos de servicios prestados por terceros', 'elemento' => '6', 'tipo' => 'Gasto'],
            ['codigo' => '631', 'descripcion' => 'Transporte, correos y gastos de viaje', 'padre_codigo' => '63'],
            ['codigo' => '64', 'descripcion' => 'Gastos por tributos', 'elemento' => '6', 'tipo' => 'Gasto'],
            ['codigo' => '65', 'descripcion' => 'Otros gastos de gestión', 'elemento' => '6', 'tipo' => 'Gasto'],
            ['codigo' => '66', 'descripcion' => 'Pérdida por medición de activos al valor razonable', 'elemento' => '6', 'tipo' => 'Gasto'],
            ['codigo' => '67', 'descripcion' => 'Gastos financieros', 'elemento' => '6', 'tipo' => 'Gasto'],
            ['codigo' => '671', 'descripcion' => 'Gastos en operaciones de endeudamiento', 'padre_codigo' => '67'],
            ['codigo' => '68', 'descripcion' => 'Valuación y deterioro de activos y provisiones', 'elemento' => '6', 'tipo' => 'Gasto'],
            ['codigo' => '69', 'descripcion' => 'Costo de ventas', 'elemento' => '6', 'tipo' => 'Gasto'],
            ['codigo' => '691', 'descripcion' => 'Mercaderías', 'padre_codigo' => '69'],

            // ELEMENTO 7: INGRESOS
            ['codigo' => '70', 'descripcion' => 'Ventas', 'elemento' => '7', 'tipo' => 'Ingreso'],
            ['codigo' => '701', 'descripcion' => 'Mercaderías', 'padre_codigo' => '70'],
            ['codigo' => '702', 'descripcion' => 'Productos terminados', 'padre_codigo' => '70'],
            ['codigo' => '703', 'descripcion' => 'Subproductos, desechos y desperdicios', 'padre_codigo' => '70'],
            ['codigo' => '704', 'descripcion' => 'Prestación de servicios', 'padre_codigo' => '70'],
            ['codigo' => '71', 'descripcion' => 'Variación de la producción almacenada', 'elemento' => '7', 'tipo' => 'Ingreso'],
            ['codigo' => '72', 'descripcion' => 'Producción de activo inmovilizado', 'elemento' => '7', 'tipo' => 'Ingreso'],
            ['codigo' => '73', 'descripcion' => 'Descuentos, rebajas y bonificaciones obtenidos', 'elemento' => '7', 'tipo' => 'Ingreso'],
            ['codigo' => '74', 'descripcion' => 'Descuentos, rebajas y bonificaciones concedidos', 'elemento' => '7', 'tipo' => 'Ingreso'],
            ['codigo' => '75', 'descripcion' => 'Otros ingresos de gestión', 'elemento' => '7', 'tipo' => 'Ingreso'],
            ['codigo' => '76', 'descripcion' => 'Ganancia por medición de activos al valor razonable', 'elemento' => '7', 'tipo' => 'Ingreso'],
            ['codigo' => '77', 'descripcion' => 'Ingresos financieros', 'elemento' => '7', 'tipo' => 'Ingreso'],
            ['codigo' => '78', 'descripcion' => 'Cargas cubiertas por provisiones', 'elemento' => '7', 'tipo' => 'Ingreso'],
            ['codigo' => '79', 'descripcion' => 'Cargas imputables a cuentas de costos y gastos', 'elemento' => '7', 'tipo' => 'Ingreso'],
        ];

        // First pass: parent accounts (2 digits)
        $idMap = [];
        foreach ($cuentas as $cuentaData) {
            if (!isset($cuentaData['padre_codigo'])) {
                $cuenta = CuentaContable::create([
                    'codigo' => $cuentaData['codigo'],
                    'descripcion' => $cuentaData['descripcion'],
                    'elemento' => $cuentaData['elemento'],
                    'nivel' => strlen($cuentaData['codigo']),
                    'tipo' => $cuentaData['tipo'],
                    'estado' => true
                ]);
                $idMap[$cuenta->codigo] = $cuenta->id;
            }
        }

        // Second pass: child accounts (3 digits)
        foreach ($cuentas as $cuentaData) {
            if (isset($cuentaData['padre_codigo'])) {
                $padreCodigo = $cuentaData['padre_codigo'];
                if (isset($idMap[$padreCodigo])) {
                    $padreId = $idMap[$padreCodigo];
                    $padreElemento = substr($cuentaData['codigo'], 0, 1);
                    $padreTipo = null;
                    
                    // Derivar tipo basado en el elemento
                    switch($padreElemento) {
                        case '1': case '2': case '3': $padreTipo = 'Activo'; break;
                        case '4': $padreTipo = 'Pasivo'; break;
                        case '5': $padreTipo = 'Patrimonio'; break;
                        case '6': $padreTipo = 'Gasto'; break;
                        case '7': $padreTipo = 'Ingreso'; break;
                    }

                    CuentaContable::create([
                        'codigo' => $cuentaData['codigo'],
                        'descripcion' => $cuentaData['descripcion'],
                        'elemento' => $padreElemento,
                        'nivel' => strlen($cuentaData['codigo']),
                        'tipo' => $padreTipo,
                        'padre_id' => $padreId,
                        'estado' => true
                    ]);
                }
            }
        }
    }
}
