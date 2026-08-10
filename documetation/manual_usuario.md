# Manual de Usuario - PISFIL SIG v1.0

## 1. Acceso al sistema

1. Ingresar a la URL local del sistema.
2. Escribir correo y contrasena del usuario registrado.
3. Presionar **Iniciar sesion**.
4. Al ingresar, el sistema muestra el dashboard operativo segun el rol asignado.

## 2. Dashboard operativo

El dashboard resume informacion real de ventas, compras, caja, inventario, produccion y contabilidad. Desde esta pantalla se pueden revisar indicadores principales, alertas de stock, ultimos comprobantes y accesos a modulos frecuentes.

El usuario puede cambiar entre modo claro y oscuro desde el boton de configuracion. La preferencia se conserva al navegar entre pantallas.

## 3. Inventario

Desde el menu **Inventario** se accede a:

- **Dashboard de inventario:** muestra productos activos, valor de inventario, stock bajo y ultimos movimientos.
- **Productos:** permite registrar y actualizar materiales o productos.
- **Movimientos Kardex:** permite consultar entradas, salidas, ajustes y saldos.
- **Movimiento manual:** permite registrar ajustes de inventario autorizados.
- **Stock bajo:** muestra productos cuyo stock actual esta por debajo del minimo.
- **Reporte de stock:** muestra stock actual, costo unitario y valor total por producto.
- **Clasificacion ABC:** clasifica productos segun su valor acumulado en inventario.

## 4. Compras

El modulo **Compras** permite gestionar proveedores y entradas de compra.

Flujo recomendado:

1. Registrar proveedor si no existe.
2. Crear entrada de compra.
3. Agregar productos comprados con cantidad y precio.
4. Cambiar el estado de la compra cuando corresponda.
5. Al validar la compra, el sistema actualiza Kardex y genera el asiento contable.
6. Registrar pagos parciales o totales al proveedor.

## 5. Ventas

El modulo **Ventas** permite registrar comprobantes al contado o al credito.

Flujo recomendado:

1. Registrar cliente si no existe.
2. Crear venta con tipo de comprobante, fecha, moneda y condicion de pago.
3. Agregar productos, cantidades y precios.
4. Guardar la venta.
5. El sistema descuenta inventario, genera el asiento de venta y registra el costo de ventas.
6. Si la venta es al contado, se registra ingreso en caja o banco.
7. Si la venta es al credito, se pueden registrar cobros posteriores.

## 6. Caja y bancos

Desde **Finanzas > Caja y Bancos** se administran cuentas financieras y movimientos.

Funciones principales:

- Registrar cuentas de caja o banco.
- Registrar ingresos.
- Registrar egresos.
- Realizar transferencias entre cuentas de la misma moneda.
- Asociar cuentas contables cuando corresponda.

## 7. Contabilidad

Desde **Finanzas** se accede a:

- **Resumen contable:** compras, ventas, IGV y movimientos financieros del periodo.
- **Plan de cuentas:** mantenimiento del catalogo PCGE.
- **Libro Diario:** asientos contables generados por operaciones.
- **Libro Mayor:** movimientos agrupados por cuenta contable.
- **Balance de Comprobacion:** movimientos y saldos deudores o acreedores por cuenta.

Los reportes contables incluyen boton de impresion/PDF desde el navegador y descarga CSV compatible con Excel.

## 8. Reportes y exportaciones

Desde **Reportes** se pueden descargar archivos CSV compatibles con Excel para:

- Ventas.
- Compras.
- Stock.
- Kardex.
- Caja y bancos.

Estas exportaciones permiten revisar informacion fuera del sistema o adjuntarla como evidencia.

## 9. Produccion

El modulo **Produccion** permite registrar ordenes de trabajo metalmecanico.

Flujo recomendado:

1. Crear orden de produccion.
2. Registrar tareas asociadas.
3. Actualizar avance de tareas.
4. Registrar consumo de materiales.
5. El sistema descuenta inventario por Kardex y genera el asiento contable del consumo.
6. Registrar costos adicionales como mano de obra, servicios y gastos indirectos.
7. Revisar el costo total de la orden sumando materiales y costos adicionales.

## 10. Administracion

Desde **Administracion** se gestionan usuarios, roles y permisos. El rol define los modulos visibles y las acciones permitidas para cada usuario.

Tambien se puede ingresar a **Auditoria**, donde se revisan operaciones importantes del sistema, filtrando por accion, entidad y fecha.

## 11. Copias de seguridad

El sistema incluye el comando:

```bash
php artisan backup:database
```

Este comando genera una copia de seguridad en `storage/app/backups`. Para MySQL requiere que `mysqldump` este disponible en el equipo.

## 12. Mensajes del sistema

Los mensajes de inicio de sesion, rol, validacion y operaciones exitosas se muestran temporalmente. El usuario tambien puede cerrarlos manualmente con el boton de cierre.

## 13. Recomendaciones de uso

- Registrar primero catalogos base: productos, clientes, proveedores, cuentas financieras y cuentas contables.
- Validar compras antes de revisar Kardex.
- Revisar stock bajo antes de registrar ventas o consumos de produccion.
- Usar Libro Diario, Libro Mayor y Balance de Comprobacion para verificar cuadre contable.
- Exportar CSV cuando se necesite revisar informacion en Excel.
- Revisar auditoria cuando se necesite confirmar quien realizo una operacion.
- Ejecutar backups antes de cambios importantes o entregas finales.
