# Presentacion funcional del sistema PISFIL SIG

Fecha de revision: 31/08/2026  
Sistema: PISFIL SIG   
Tipo de software: Sistema integrado de gestion operativa, inventario, produccion, ventas, tesoreria y contabilidad.

## 1. Resumen ejecutivo

PISFIL SIG es un sistema web desarrollado para centralizar la gestion diaria de una empresa operativa/industrial. El sistema conecta los procesos de almacen, compras, ventas, produccion, caja/bancos, reportes, seguridad y contabilidad en una sola plataforma.

El objetivo principal es que cada operacion registrada en el sistema tenga impacto controlado en las areas que correspondan:

- Una compra puede generar entrada de stock en Kardex, cuenta por pagar y asiento contable.
- Una venta puede descontar stock, registrar ingreso de dinero si es al contado, generar cuenta por cobrar si es al credito y crear asientos contables.
- Un consumo de material en produccion descuenta inventario y carga costo a una orden de produccion.
- Un movimiento financiero afecta el saldo de caja o banco y, si se indica una cuenta contable, puede generar asiento.
- El dashboard resume ventas, deudas, flujo de caja, inventario, produccion y contabilidad.
- El menu se adapta al rol del usuario y a los permisos asignados.

En terminos funcionales, el sistema ya implementa los siguientes bloques:

- Autenticacion de usuarios.
- Roles y permisos.
- Menu dinamico por permisos.
- Dashboard operativo general.
- Inventario y Kardex.
- Productos, categorias y unidades de medida.
- Proveedores y entradas de compra.
- Clientes y ventas.
- Caja y bancos.
- Plan contable.
- Libro Diario.
- Libro Mayor.
- Balance de Comprobacion.
- Reportes gerenciales y exportaciones CSV.
- Ordenes de produccion.
- Tareas de produccion.
- Consumo de materiales.
- Costos adicionales de produccion.
- Auditoria de operaciones.
- Notificaciones globales.

## 2. Objetivo del sistema

El sistema busca funcionar como un centro de control para PISFIL EMSAC, permitiendo registrar operaciones de negocio y consultar su impacto operativo, financiero y contable.

Sus objetivos funcionales son:

- Controlar el stock de productos, materias primas, herrajes, consumibles y productos terminados.
- Mantener un Kardex con trazabilidad de entradas, salidas, ajustes y devoluciones.
- Registrar compras de proveedores y controlar su estado logistico.
- Registrar pagos a proveedores y actualizar cuentas por pagar.
- Registrar ventas al contado o al credito.
- Registrar cobros a clientes y controlar cuentas por cobrar.
- Manejar saldos de caja y cuentas bancarias.
- Registrar transferencias entre cuentas financieras de la misma moneda.
- Generar asientos contables automaticos asociados a ventas, compras, pagos, cobros, transferencias y consumos.
- Consultar Libro Diario, Libro Mayor y Balance de Comprobacion.
- Dar visibilidad gerencial con KPIs, graficos y reportes exportables.
- Controlar acceso por roles para que cada usuario vea y ejecute solo lo permitido.
- Registrar auditoria de acciones importantes.

## 3. Arquitectura general

El sistema esta construido como una aplicacion web Laravel.

Componentes principales:

- Backend: Laravel, PHP 8.3 o superior.
- Frontend: Blade, CSS integrado, Vite, Tailwind CSS y JavaScript.
- Autenticacion: sesiones web de Laravel y middleware de autenticacion.
- Seguridad funcional: middleware de permisos basado en roles.
- Base de datos: modelo relacional con migraciones Laravel.
- Graficos: ApexCharts en dashboard principal e inventario; Chart.js en dashboard gerencial.
- Exportaciones: archivos CSV generados por streaming.

El sistema organiza su logica en:

- Controladores: reciben solicitudes, validan datos y coordinan procesos.
- Modelos: representan tablas y relaciones del negocio.
- Servicios: contienen logica de negocio reutilizable para Kardex, asientos contables y auditoria.
- Vistas Blade: pantallas del sistema.
- Migraciones: definen la estructura de base de datos.
- Seeders: cargan datos iniciales como roles, permisos, menus, plan contable, categorias, unidades, procesos y usuarios base.

## 4. Acceso y autenticacion

El sistema cuenta con pantalla de login propia.

Funcionamiento implementado:

- El usuario ingresa email y contrasena.
- El sistema valida credenciales contra la tabla de usuarios.
- Solo permite login a usuarios activos.
- Al iniciar sesion, la sesion se regenera para evitar reutilizacion insegura.
- Al cerrar sesion, se invalida la sesion y se regenera el token CSRF.
- Si el usuario no esta autenticado, se redirige al login.
- Si el usuario ya esta autenticado y entra a la raiz del sistema, se redirige al dashboard.

Campos principales del usuario:

- Nombre.
- Email.
- Password cifrado.
- Rol asignado.
- Documento de identidad.
- Telefono.
- Estado activo/inactivo.

## 5. Roles, permisos y control de acceso

El sistema implementa control de acceso basado en roles y permisos.

Cada usuario pertenece a un rol. Cada rol tiene una lista de permisos. El middleware `permission` verifica si el usuario posee el permiso requerido antes de permitir acceso a una ruta.

Permiso especial:

- `*`: acceso total al sistema.

Roles iniciales implementados:

| Rol | Alcance funcional |
| --- | --- |
| Gerente | Acceso total al sistema mediante permiso `*`. |
| Operario | Acceso a dashboard, produccion, consumo, costos, tareas y reportes basicos. |
| Encargado de Almacen | Acceso a inventario, Kardex, entradas de compra y aprobacion de compras. |
| Encargado de Caja y Bancos | Acceso a caja/bancos, transacciones, plan contable, reportes y exportaciones contables. |

Permisos implementados:

| Permiso | Uso |
| --- | --- |
| `dashboard.view` | Ver dashboard general. |
| `inventario.view` | Ver modulo de inventario. |
| `inventario.create` | Crear movimientos o registros de inventario. |
| `inventario.export` | Exportar reportes de inventario. |
| `kardex.view` | Ver movimientos de Kardex. |
| `entradas.view` | Ver compras y proveedores. |
| `entradas.create` | Crear compras, proveedores y detalles de compra. |
| `entradas.approve` | Cambiar estado de compras y validar entradas. |
| `entradas.pay` | Registrar pagos de compras. |
| `ventas.view` | Ver clientes y ventas. |
| `ventas.create` | Crear clientes y ventas. |
| `ventas.collect` | Registrar cobros de ventas. |
| `produccion.view` | Ver modulo de produccion. |
| `produccion.create` | Crear ordenes y tareas de produccion. |
| `produccion.consume` | Registrar consumo de materiales. |
| `produccion.cost` | Registrar costos adicionales de produccion. |
| `tareas.view` | Ver tareas de produccion. |
| `tareas.update_avance` | Actualizar avance de tareas. |
| `reportes.view` | Ver dashboard gerencial. |
| `reportes.create` | Crear reportes, reservado en permisos. |
| `reportes.export` | Exportar reportes CSV. |
| `caja_bancos.view` | Ver caja y bancos. |
| `caja_bancos.create` | Crear cuentas financieras. |
| `caja_bancos.update` | Editar cuentas financieras, reservado en permisos. |
| `transacciones.view` | Ver transacciones financieras. |
| `transacciones.create` | Crear transacciones financieras. |
| `plan_contable.view` | Ver plan contable y libros. |
| `plan_contable.manage` | Gestionar cuentas contables. |
| `contabilidad.export` | Exportar libros contables. |
| `auditoria.view` | Ver auditoria de operaciones. |
| `backup.run` | Ejecutar copias de seguridad, reservado en permisos. |
| `usuarios.manage` | Gestionar usuarios. |
| `roles.manage` | Gestionar roles y permisos. |

## 6. Menu dinamico

El sistema tiene un menu lateral construido desde la base de datos. Cada item de menu puede tener un permiso asociado.

Funcionamiento:

- El menu solo muestra opciones que el usuario puede ver.
- Se soportan menus principales y submenus.
- El menu se ordena por el campo `orden`.
- Los submenus se despliegan como acordeon.
- La opcion activa se detecta segun la ruta actual.
- El sidebar puede contraerse o expandirse.

Menus principales implementados:

- Dashboard.
- Inventario.
- Compras.
- Ventas.
- Finanzas.
- Produccion.
- Reportes.
- Administracion.

Submenus destacados:

- Inventario: Dashboard Inventario, Productos, Kardex, Movimiento Manual, Stock Bajo, Reporte de Stock, Clasificacion ABC.
- Compras: Proveedores, Entradas.
- Ventas: Clientes, Comprobantes.
- Finanzas: Caja y Bancos, Resumen Contable, Vista General, Plan de Cuentas, Libro Diario, Libro Mayor, Balance de Comprobacion.
- Produccion: Ordenes de Produccion.
- Administracion: Usuarios, Roles y Permisos, Auditoria.

## 7. Interfaz general

La interfaz usa un layout comun para todas las pantallas autenticadas.

Elementos implementados:

- Sidebar lateral con logo PISFIL SIG.
- Topbar con titulo dinamico por pantalla.
- Boton para contraer/expandir menu.
- Cambio de tema claro/oscuro guardado en `localStorage`.
- Menu de usuario con cierre de sesion.
- Indicador del rol actual del usuario.
- Notificaciones globales.
- Tarjetas KPI.
- Paneles y tablas con estilo industrial/corporativo.
- Mensajes temporales de exito o error con cierre automatico.

El sistema usa un estilo visual orientado a gestion operativa: fondo oscuro tipo blueprint, colores para estados, tarjetas de indicadores, tablas de datos y botones de accion.

## 8. Notificaciones globales

El sistema genera alertas globales cada vez que se renderiza el layout principal para usuarios autenticados.

Alertas implementadas:

- Productos con stock bajo.
- Cuentas por pagar pendientes.
- Cuentas por cobrar abiertas.
- Ordenes de produccion proximas a vencer.
- Libro Diario sin asientos en el mes actual.
- Estado "sin alertas pendientes" cuando no hay avisos criticos.

Estas notificaciones aparecen en la campana del topbar e incluyen enlace hacia la pantalla relacionada.

## 9. Dashboard operativo general

El dashboard es la pantalla central del sistema. Resume informacion comercial, financiera, contable, productiva e inventario.

Indicadores implementados:

- Ventas del mes.
- Variacion de ventas contra el mes anterior.
- Cuentas por cobrar.
- Cantidad de clientes con deuda.
- Cuentas por pagar.
- Cantidad de proveedores con deuda.
- Ingresos del mes.
- Egresos del mes.
- Flujo de caja del mes.
- Asientos contables del mes.
- Ordenes de produccion totales.
- Ordenes en proceso.
- Ordenes completadas.
- Tareas pendientes.
- Tareas en progreso.
- Tareas completadas en la semana.
- Productos activos.
- Productos con stock bajo.
- Valor total del inventario.
- Ordenes proximas a vencer.

Graficos implementados:

- Ventas vs compras de los ultimos 6 meses.
- Cuentas por cobrar y por pagar por periodo.
- Flujo de caja por periodo.
- Asientos contables por periodo.
- Distribucion ABC del inventario.
- Mini graficos en tarjetas KPI.

Tablas del dashboard:

- Ultimos movimientos de Kardex.
- Ultimos comprobantes de ventas y compras.
- Accesos rapidos a gestion contable y operativa.

## 10. Inventario

El modulo de inventario centraliza productos, stock, valorizacion y movimientos de Kardex.

Pantallas implementadas:

- Dashboard de inventario.
- Listado de productos de inventario.
- Movimientos de Kardex.
- Reporte valorizado de stock.
- Productos con stock bajo.
- Clasificacion ABC.
- Movimiento manual de inventario.

### 10.1 Dashboard de inventario

Muestra:

- Cantidad de productos activos.
- Valor total del inventario.
- Cantidad de items con stock bajo.
- Historial reciente de movimientos de Kardex.

El valor de inventario se calcula como:

```text
stock_actual * precio_unitario
```

### 10.2 Productos

El sistema permite gestionar productos con:

- Codigo unico.
- Nombre.
- Descripcion.
- Categoria.
- Unidad de medida.
- Precio unitario.
- Stock minimo.
- Stock maximo.
- Stock actual.
- Estado.
- Notas.

Operaciones implementadas:

- Listar productos.
- Crear productos.
- Ver detalle de producto.
- Editar productos.
- Eliminar productos.
- Consultar productos activos por JSON para pantallas dinamicas.

Reglas implementadas:

- El codigo de producto debe ser unico.
- La categoria debe existir.
- La unidad de medida debe existir.
- El precio unitario no puede ser negativo.
- El stock actual y stock minimo aceptan valores decimales.
- Si un producto ya tiene movimientos de Kardex, el sistema evita modificar directamente el stock actual desde la edicion del producto para proteger la trazabilidad.

### 10.3 Categorias

Categorias iniciales cargadas:

- Materia Prima.
- Herrajes y Accesorios.
- Consumibles.
- Productos Terminados.

Estas categorias permiten clasificar el catalogo y agrupar reportes.

### 10.4 Unidades de medida

Unidades iniciales cargadas:

- Kilogramo (`kg`).
- Metro (`m`).
- Metro Cuadrado.
- Pieza (`pz`).
- Litro (`L`).

El sistema trabaja con simbolo de unidad de medida para mostrar cantidades de forma legible.

### 10.5 Kardex

El Kardex registra los movimientos de inventario de cada producto.

Datos almacenados por movimiento:

- Producto.
- Tipo de movimiento.
- Cantidad.
- Precio unitario aplicado.
- Saldo anterior.
- Saldo actual.
- Tipo de referencia.
- ID de referencia.
- Usuario que registra.
- Observaciones.
- Fecha del movimiento.

Tipos de movimiento implementados:

- Entrada.
- Salida.
- Ajuste.
- Devolucion.

Reglas del Kardex:

- La cantidad debe ser mayor a cero.
- Las salidas validan stock suficiente.
- Las entradas recalculan el precio promedio ponderado.
- Las salidas usan el costo promedio vigente.
- Cada movimiento actualiza el `stock_actual` del producto.
- El producto se bloquea durante la transaccion para evitar problemas de concurrencia.

Formula de promedio ponderado en entradas:

```text
nuevo_precio = (valor_anterior + valor_ingreso) / nuevo_saldo
```

Donde:

- `valor_anterior = stock_anterior * precio_actual`
- `valor_ingreso = cantidad_ingresada * precio_ingreso`
- `nuevo_saldo = stock_anterior + cantidad_ingresada`

### 10.6 Movimientos manuales

El usuario autorizado puede registrar movimientos manuales de inventario.

Campos:

- Producto.
- Tipo de movimiento: entrada, salida o ajuste.
- Cantidad.
- Precio unitario, requerido para entradas.
- Observaciones.

Impacto:

- Crea registro Kardex.
- Actualiza stock.
- Actualiza precio promedio cuando corresponde.
- Registra auditoria.

### 10.7 Stock bajo

El sistema identifica productos con stock bajo cuando:

```text
stock_actual <= stock_minimo
```

Esta informacion se usa en:

- Dashboard general.
- Dashboard de inventario.
- Pantalla Stock Bajo.
- Notificaciones globales.

### 10.8 Clasificacion ABC

El sistema clasifica productos por valor de inventario.

Proceso:

- Calcula el valor por producto: `stock_actual * precio_unitario`.
- Ordena los productos de mayor a menor valor.
- Calcula el porcentaje de participacion sobre el valor total.
- Acumula porcentajes y asigna clasificacion:
  - A: hasta 80% acumulado.
  - B: de 80% a 95% acumulado.
  - C: sobre 95% acumulado.

Uso:

- Priorizar productos de mayor valor.
- Apoyar decisiones de control, reposicion y revision fisica.

## 11. Compras y proveedores

El modulo de compras administra proveedores, entradas de compra, detalles de productos recibidos, validacion logistica, pagos y registro contable.

Pantallas implementadas:

- Proveedores.
- Crear proveedor.
- Editar proveedor.
- Ver proveedor.
- Entradas de compra.
- Crear entrada.
- Editar entrada.
- Ver detalle de entrada.
- Agregar detalle de productos a entrada.
- Cambiar estado de entrada.
- Registrar pago.

### 11.1 Proveedores

Datos del proveedor:

- Codigo unico.
- Nombre de empresa.
- Nombre de contacto.
- Documento de identidad.
- RUC.
- Email.
- Telefono.
- Celular.
- Direccion.
- Ciudad.
- Pais.
- Condicion de pago.
- Plazo de entrega.
- Estado.

Operaciones implementadas:

- Listar proveedores.
- Crear proveedores.
- Ver detalle del proveedor y sus entradas.
- Editar proveedor.
- Eliminar proveedor.
- Consultar proveedores activos por JSON.

Reglas:

- Codigo de proveedor unico.
- RUC unico cuando se registra.
- Validacion de formato de email.
- Proveedor activo para aparecer en formularios de compra.

### 11.2 Entradas de compra

La entrada de compra representa un documento de proveedor que luego puede recibir productos, validarse y pagarse.

Datos principales:

- Numero de documento.
- Proveedor.
- Fecha de emision.
- Fecha de recepcion.
- Estado logistico.
- Observaciones.
- Usuario que registra.
- Estado de pago.
- Monto pagado.

Estados logisticos implementados:

- Pendiente.
- Recibida.
- Validada.
- Rechazada.

Estados de pago implementados:

- Pendiente.
- Parcial.
- Pagado.

### 11.3 Detalles de compra

Cada entrada puede contener uno o mas productos.

Datos del detalle:

- Producto.
- Cantidad solicitada.
- Cantidad recibida.
- Precio unitario.
- Costo total.
- Observaciones.

El costo total se calcula como:

```text
cantidad_solicitada * precio_unitario
```

### 11.4 Validacion de compra y entrada a Kardex

Cuando una entrada cambia a estado `validada`, el sistema ejecuta procesos automaticos:

1. Verifica que la compra tenga detalles.
2. Recorre cada producto del detalle.
3. Registra una entrada en Kardex.
4. Aumenta el stock del producto.
5. Recalcula el precio promedio ponderado.
6. Guarda la fecha de recepcion.
7. Genera asiento contable de compra.
8. Registra auditoria.

Asiento contable automatico de compra:

| Cuenta | Movimiento | Concepto |
| --- | --- | --- |
| 601 | Debe | Compra de materiales o mercaderias. |
| 401 | Debe | Credito fiscal IGV. |
| 421 | Haber | Cuenta por pagar al proveedor. |

La separacion de IGV se calcula con tasa interna de 18%.

### 11.5 Pagos de compras

El sistema permite registrar pagos a compras pendientes o parciales.

Campos:

- Monto.
- Cuenta financiera de pago.
- Cuenta contable opcional.

Reglas:

- El monto debe ser mayor a cero.
- El pago no puede exceder el total de la compra.
- La cuenta financiera debe existir.
- La cuenta de pago debe estar en PEN.
- La cuenta debe tener saldo suficiente.

Impacto automatico:

- Aumenta el monto pagado de la compra.
- Cambia estado de pago a parcial o pagado.
- Crea una transaccion financiera de tipo egreso.
- Descuenta saldo de la cuenta financiera.
- Genera asiento contable de pago.
- Registra auditoria.

Asiento contable automatico del pago:

| Cuenta | Movimiento | Concepto |
| --- | --- | --- |
| 421 | Debe | Disminuye la cuenta por pagar al proveedor. |
| 101 o 104 | Haber | Salida de caja o banco. |

La cuenta contable 101 se usa para caja y la 104 para banco.

## 12. Ventas y clientes

El modulo de ventas permite registrar clientes, emitir comprobantes operativos, descontar stock, registrar cobros y generar contabilidad.

Pantallas implementadas:

- Clientes.
- Ventas.
- Crear venta.
- Ver venta.
- Registrar cobro.

### 12.1 Clientes

Datos del cliente:

- Nombre.
- Documento de identidad.
- Direccion.
- Telefono.
- Email.
- Estado.

Operaciones implementadas:

- Listar clientes.
- Crear clientes.
- Editar clientes.
- Activar o inactivar clientes.

Reglas:

- Documento de identidad unico.
- Email con formato valido cuando se informa.
- Solo clientes activos aparecen en formularios de venta.

### 12.2 Registro de ventas

Datos de una venta:

- Cliente.
- Tipo de comprobante.
- Serie.
- Numero.
- Fecha de venta.
- Moneda.
- Total.
- Condicion de pago.
- Estado del documento.
- Estado de pago.
- Monto cobrado.
- Cuenta financiera asociada.
- Usuario que registra.

Tipos de comprobante implementados:

- Factura.
- Boleta.
- Ticket.

Monedas implementadas:

- PEN.
- USD.

Condiciones de pago:

- Contado.
- Credito.

### 12.3 Detalles de venta

Cada venta contiene productos, cantidades y precios.

Por cada detalle:

- Se registra producto.
- Se registra cantidad.
- Se registra precio unitario.
- Se calcula subtotal.
- Se descuenta inventario mediante Kardex.

Reglas:

- Debe existir al menos un producto.
- Las cantidades deben ser mayores a cero.
- Los precios no pueden ser negativos.
- El total de venta debe ser mayor a cero.
- El Kardex valida stock suficiente antes de permitir la salida.

### 12.4 Venta al contado

Cuando la venta es al contado:

1. Se crea la venta.
2. Se crean sus detalles.
3. Se registra salida de Kardex por cada producto.
4. Se actualiza stock.
5. Se marca la venta como pagada.
6. Se registra monto cobrado igual al total.
7. Se valida que la cuenta financiera tenga la misma moneda de la venta.
8. Se crea una transaccion financiera de ingreso.
9. Se incrementa el saldo de caja o banco.
10. Se genera asiento contable de venta.
11. Se genera asiento contable de costo de venta.
12. Se registra auditoria.

### 12.5 Venta al credito

Cuando la venta es al credito:

1. Se crea la venta.
2. Se crean sus detalles.
3. Se registra salida de Kardex por cada producto.
4. Se actualiza stock.
5. Se marca la venta como pagada a nivel de documento procesado.
6. Se deja el estado de pago como pendiente.
7. Se registra monto cobrado en cero.
8. Se genera una cuenta por cobrar contable.
9. Se genera asiento contable de venta.
10. Se genera asiento contable de costo de venta.
11. Se registra auditoria.

### 12.6 Cobros de ventas

El sistema permite registrar cobros posteriores para ventas con deuda.

Campos:

- Monto.
- Cuenta financiera destino.
- Cuenta contable opcional.

Reglas:

- El monto debe ser mayor a cero.
- El cobro no puede exceder el total pendiente.
- La cuenta financiera debe existir.
- La cuenta destino debe tener la misma moneda que la venta.

Impacto automatico:

- Actualiza monto cobrado.
- Cambia estado de pago a parcial o pagado.
- Crea transaccion financiera de ingreso.
- Incrementa saldo de caja o banco.
- Genera asiento contable de cobro.
- Registra auditoria.

Asiento contable automatico de cobro:

| Cuenta | Movimiento | Concepto |
| --- | --- | --- |
| 101 o 104 | Debe | Ingreso a caja o banco. |
| 121 | Haber | Disminuye la cuenta por cobrar al cliente. |

### 12.7 Asiento contable de venta

Para ventas, el sistema separa IGV al 18%.

Si la venta es al contado:

| Cuenta | Movimiento | Concepto |
| --- | --- | --- |
| 101 o 104 | Debe | Entrada a caja o banco. |
| 701 | Haber | Base imponible de venta. |
| 401 | Haber | IGV de venta. |

Si la venta es al credito:

| Cuenta | Movimiento | Concepto |
| --- | --- | --- |
| 121 | Debe | Cuenta por cobrar comercial. |
| 701 | Haber | Base imponible de venta. |
| 401 | Haber | IGV de venta. |

Asiento automatico de costo de venta:

| Cuenta | Movimiento | Concepto |
| --- | --- | --- |
| 691 | Debe | Reconocimiento de costo de venta. |
| 20 | Haber | Salida valorizada de inventario. |

## 13. Caja y bancos

El modulo de caja y bancos gestiona cuentas financieras, saldos, ingresos, egresos y transferencias.

Pantallas implementadas:

- Dashboard de caja y bancos.
- Estado de cuenta.
- Registro de cuenta financiera.
- Registro de movimiento financiero.
- Registro de transferencia entre cuentas.

### 13.1 Cuentas financieras

Datos de una cuenta:

- Nombre.
- Tipo: caja o banco.
- Banco.
- Numero de cuenta.
- Moneda.
- Saldo actual.
- Estado.

Monedas soportadas:

- PEN.
- USD.

Indicadores:

- Total en soles.
- Total en dolares.
- Cantidad de transacciones por cuenta.
- Ultimas transacciones.

### 13.2 Movimientos financieros manuales

El sistema permite registrar:

- Ingresos.
- Egresos.

Campos:

- Tipo.
- Monto.
- Motivo.
- Referencia.
- Fecha de transaccion.
- Cuenta contable opcional.

Reglas:

- Los egresos no pueden exceder el saldo disponible.
- El monto debe ser mayor a cero.
- La fecha es obligatoria.
- Si se informa cuenta contable, debe existir.

Impacto:

- Crea transaccion financiera.
- Incrementa o decrementa saldo de la cuenta.
- Si tiene cuenta contable, genera asiento contable.
- Registra auditoria.

### 13.3 Transferencias

El sistema permite transferir dinero entre cuentas financieras.

Reglas:

- La cuenta destino debe ser distinta a la cuenta origen.
- Ambas cuentas deben tener la misma moneda.
- No hay soporte de tipo de cambio en transferencias.
- La cuenta origen debe tener saldo suficiente.

Impacto:

- Crea una transaccion de salida en cuenta origen.
- Crea una transaccion de ingreso en cuenta destino.
- Descuenta saldo de origen.
- Incrementa saldo de destino.
- Genera asiento contable de transferencia.
- Registra auditoria.

Asiento automatico:

| Movimiento | Concepto |
| --- | --- |
| Debe | Cuenta financiera destino. |
| Haber | Cuenta financiera origen. |

## 14. Produccion

El modulo de produccion controla ordenes, tareas, consumo de materiales y costos adicionales.

Pantallas implementadas:

- Listado de ordenes de produccion.
- Crear orden de produccion.
- Ver detalle de orden.
- Cambiar estado de orden.
- Crear tareas dentro de una orden.
- Actualizar avance de tareas.
- Registrar consumo de materiales.
- Registrar costos adicionales.

### 14.1 Ordenes de produccion

Datos de una orden:

- Numero de orden.
- Cliente.
- Descripcion del trabajo.
- Estado.
- Fecha inicio planificada.
- Fecha fin planificada.
- Fecha inicio real.
- Fecha fin real.
- Observaciones.
- Usuario creador.
- Usuario asignado.

Estados implementados:

- Planificada.
- En proceso.
- Pausada.
- Completada.
- Cancelada.

Reglas:

- Numero de orden unico.
- Cliente obligatorio.
- Descripcion obligatoria.
- Fecha fin planificada no puede ser anterior a fecha inicio planificada.
- Usuario asignado opcional.

Automatizaciones de estado:

- Al pasar a `en_proceso`, si no existe fecha de inicio real, se registra la fecha actual.
- Al pasar a `completada`, si no existe fecha fin real, se registra la fecha actual.
- Se registra auditoria al crear y actualizar estado.

### 14.2 Tareas de produccion

Una orden puede contener varias tareas.

Datos:

- Numero de tarea.
- Nombre.
- Descripcion.
- Proceso de produccion.
- Fechas planificadas.
- Usuario responsable.
- Estado.
- Porcentaje de avance.
- Fechas reales.

Reglas:

- El numero de tarea debe ser unico dentro de la orden.
- El proceso de produccion debe existir.
- La fecha fin planificada debe ser posterior o igual a la fecha inicio.
- El responsable debe existir.
- El porcentaje de avance va de 0 a 100.

Automatizacion:

- Si avance es 100%, la tarea queda completada y se marca fecha fin real.
- Si avance es mayor a 0%, la tarea queda en progreso y se marca fecha inicio real.
- Si avance es 0%, se mantiene el estado anterior salvo logica de pantalla.

Procesos iniciales cargados:

- Preparacion de Materiales.
- Soldadura.
- Acabado.
- Pintura.
- Control de Calidad.

### 14.3 Consumo de materiales

El sistema permite retirar materiales del almacen para cargarlos a una orden de produccion.

Campos:

- Producto.
- Cantidad.
- Tarea de produccion opcional.

Impacto automatico:

- Registra salida de Kardex.
- Valida stock suficiente.
- Usa costo promedio vigente.
- Crea registro de consumo de material.
- Calcula costo total del consumo.
- Genera asiento contable por consumo de produccion.
- Registra auditoria.

Asiento automatico de consumo:

| Cuenta | Movimiento | Concepto |
| --- | --- | --- |
| 61 | Debe | Consumo/variacion de existencias. |
| 24 | Haber | Salida de materias primas hacia produccion. |

### 14.4 Costos adicionales de produccion

El sistema permite registrar costos no asociados directamente a un producto de inventario.

Tipos:

- Mano de obra.
- Gasto indirecto.
- Servicio.

Datos:

- Tipo.
- Descripcion.
- Monto.
- Fecha.
- Usuario.
- Orden de produccion.

Estos costos quedan asociados a la orden para medir el costo total operativo.

## 15. Contabilidad

El modulo contable consolida operaciones del sistema en asientos, libros y reportes contables.

Pantallas implementadas:

- Vista general contable.
- Plan de cuentas.
- Gestion CRUD de cuentas contables.
- Libro Diario.
- Libro Mayor.
- Balance de Comprobacion.
- Exportacion de Libro Diario.
- Exportacion de Libro Mayor.
- Exportacion de Balance de Comprobacion.

### 15.1 Vista general contable

Permite filtrar por mes y ano.

Muestra:

- Ventas pagadas del periodo.
- Compras del periodo.
- Movimientos bancarios/tesoreria del periodo.
- Total de ventas.
- Base imponible de ventas.
- IGV de ventas.
- Total de compras.
- Base imponible de compras.
- IGV de compras.

### 15.2 Plan de cuentas

El sistema carga un plan contable inicial estructurado por elementos.

Elementos incluidos:

- Elemento 1: Activo disponible y exigible.
- Elemento 2: Activo realizable.
- Elemento 3: Activo inmovilizado.
- Elemento 4: Pasivo.
- Elemento 5: Patrimonio.
- Elemento 6: Gastos por naturaleza.
- Elemento 7: Ingresos.

Cuentas clave usadas automaticamente:

| Cuenta | Uso en el sistema |
| --- | --- |
| 101 | Caja. |
| 104 | Cuentas corrientes/bancos. |
| 121 | Cuentas por cobrar comerciales. |
| 20 | Mercaderias/inventario. |
| 24 | Materias primas. |
| 401 | IGV. |
| 421 | Cuentas por pagar a proveedores. |
| 601 | Compras de materiales o mercaderias. |
| 61 | Variacion/consumo de existencias. |
| 691 | Costo de ventas. |
| 701 | Ventas. |

### 15.3 Gestion de cuentas contables

Operaciones implementadas:

- Listar cuentas contables.
- Crear cuenta contable.
- Ver detalle de cuenta contable.
- Editar cuenta contable.
- Eliminar cuenta contable.

Datos:

- Codigo.
- Descripcion.
- Elemento.
- Nivel.
- Tipo.
- Cuenta padre.
- Estado.

Reglas:

- Codigo unico.
- Nivel entre 2 y 6.
- Cuenta padre opcional.
- No permite eliminar cuentas que tengan subcuentas.
- Las cuentas pueden estructurarse jerarquicamente.

### 15.4 Asientos contables

Los asientos se generan automaticamente desde operaciones del sistema.

Datos:

- Numero automatico.
- Fecha.
- Descripcion.
- Tipo de origen.
- ID de origen.
- Moneda.
- Total debe.
- Total haber.
- Estado.
- Usuario.
- Detalles con cuenta contable, tipo de movimiento, monto y glosa.

Reglas:

- Cada asiento debe tener al menos dos lineas.
- El total Debe debe cuadrar con el total Haber.
- Diferencias mayores a 0.01 generan error.
- Antes de crear una linea, el sistema valida que exista la cuenta contable.
- El numero se genera con formato `ASI-YYYYMMDD-HHMMSS-XXXXX`.
- Para varias operaciones se evita duplicar asientos del mismo origen.

### 15.5 Libro Diario

Implementado.

Funcion:

- Lista asientos contables por mes y ano.
- Ordena por fecha y numero de asiento.
- Muestra las lineas contables con cuenta, glosa, Debe y Haber.
- Calcula total Debe y total Haber.
- Permite exportar a CSV.

Uso:

- Revisar el registro cronologico de operaciones contables.
- Validar que los asientos se esten generando correctamente.
- Exportar informacion para revision externa.

### 15.6 Libro Mayor

Implementado.

Funcion:

- Agrupa movimientos por cuenta contable.
- Filtra por mes y ano.
- Calcula total Debe por cuenta.
- Calcula total Haber por cuenta.
- Calcula saldo por cuenta.
- Ordena por codigo contable, fecha y numero de asiento.
- Permite exportar a CSV.

Uso:

- Analizar el movimiento de cada cuenta.
- Ver acumulados por cuenta.
- Revisar trazabilidad contable desde los asientos.

### 15.7 Balance de Comprobacion

Implementado.

Funcion:

- Agrupa movimientos por cuenta.
- Calcula total Debe.
- Calcula total Haber.
- Calcula saldo deudor.
- Calcula saldo acreedor.
- Calcula totales generales.
- Permite exportar a CSV.

Uso:

- Comprobar igualdad entre Debe y Haber.
- Revisar saldos contables por cuenta.
- Preparar informacion para cierre o revision contable.

## 16. Reportes gerenciales

El modulo de reportes ofrece una vista ejecutiva y exportaciones.

Pantallas implementadas:

- Dashboard gerencial.
- Exportacion de ventas.
- Exportacion de compras.
- Exportacion de stock.
- Exportacion de Kardex.
- Exportacion de caja y bancos.

### 16.1 Dashboard gerencial

Indicadores:

- Ventas del mes.
- Flujo de caja del mes.
- Valorizacion de almacen.
- Ventas totales.
- Ingresos y egresos de los ultimos 6 meses.
- Top 5 productos mas vendidos.
- Estado de produccion.

Graficos:

- Flujo financiero mensual.
- Estado de ordenes de produccion.

### 16.2 Exportacion de ventas

Genera CSV con:

- Fecha.
- Comprobante.
- Cliente.
- Documento.
- Condicion de pago.
- Estado de pago.
- Total.

### 16.3 Exportacion de compras

Genera CSV con:

- Fecha.
- Documento.
- Proveedor.
- RUC.
- Estado logistico.
- Estado de pago.
- Total.

### 16.4 Exportacion de stock

Genera CSV con:

- Codigo.
- Producto.
- Categoria.
- Unidad.
- Stock actual.
- Stock minimo.
- Costo unitario.
- Valor total.
- Estado.

### 16.5 Exportacion de Kardex

Genera CSV con:

- Fecha.
- Producto.
- Tipo.
- Cantidad.
- Costo unitario.
- Saldo anterior.
- Saldo actual.
- Referencia.
- Usuario.

### 16.6 Exportacion de caja y bancos

Genera CSV con:

- Fecha.
- Cuenta.
- Tipo.
- Monto.
- Motivo.
- Referencia.
- Cuenta contable.
- Usuario.

Cada exportacion registra auditoria con la accion `reporte.exportado`.

## 17. Auditoria

El sistema implementa auditoria de acciones relevantes.

Datos registrados:

- Usuario.
- Accion.
- Tipo de entidad.
- ID de entidad.
- Valores anteriores.
- Valores nuevos.
- IP.
- User agent.
- Fecha y hora.

Pantalla implementada:

- Listado de auditorias.

Filtros implementados:

- Accion.
- Entidad.
- Fecha desde.
- Fecha hasta.

Acciones auditadas en el sistema:

- Creacion, actualizacion y eliminacion de productos.
- Creacion, actualizacion y eliminacion de proveedores.
- Creacion y actualizacion de compras.
- Cambio de estado de compras.
- Agregado de detalles de compra.
- Registro de pagos de compra.
- Creacion y actualizacion de clientes.
- Creacion de ventas.
- Registro de cobros de venta.
- Creacion de cuentas financieras.
- Movimientos de tesoreria.
- Transferencias entre cuentas.
- Creacion y cambios de estado de ordenes de produccion.
- Consumo de materiales.
- Costos adicionales de produccion.
- Exportacion de reportes.

## 18. Datos maestros y configuracion inicial

El sistema incluye seeders para inicializar informacion base.

Seeders implementados:

- Roles y permisos.
- Menus.
- Plan contable.
- Categorias.
- Unidades de medida.
- Procesos de produccion.
- Usuarios iniciales.
- Productos iniciales.

Usuarios iniciales:

| Usuario | Email | Rol |
| --- | --- | --- |
| Gerente General | `gerente@pisfil.com` | Gerente |
| Juan Soldador | `juan.soldador@pisfil.com` | Operario |
| Maria Acabado | `maria.acabado@pisfil.com` | Operario |
| Carlos Almacen | `carlos.almacen@pisfil.com` | Encargado de Almacen |

Nota: las contrasenas iniciales cargadas por seeder usan el valor `password`.

Productos iniciales:

- Tubo Cuadrado 50x50 mm.
- Tornillo M8 x 30 mm.
- Tuerca M8.
- Plancheta de acero 3 mm.

## 19. Flujos principales del sistema

### 19.1 Flujo de compra completa

1. Se crea proveedor si no existe.
2. Se crea entrada de compra con documento, proveedor y fecha.
3. Se agregan productos al detalle de la compra.
4. Se valida la compra.
5. El sistema registra entrada en Kardex.
6. El sistema actualiza stock.
7. El sistema recalcula costo promedio.
8. El sistema genera asiento contable de compra.
9. La compra queda como deuda pendiente.
10. Se registra pago parcial o total.
11. El sistema descuenta caja/banco.
12. El sistema genera asiento de pago.
13. El dashboard actualiza cuentas por pagar, egresos y asientos.

### 19.2 Flujo de venta al contado

1. Se crea cliente si no existe.
2. Se registra comprobante de venta.
3. Se agregan productos, cantidades y precios.
4. El sistema descuenta stock por Kardex.
5. El sistema valida stock suficiente.
6. El sistema registra ingreso en caja/banco.
7. La venta queda pagada.
8. Se genera asiento de venta.
9. Se genera asiento de costo de venta.
10. El dashboard actualiza ventas, ingresos, inventario y contabilidad.

### 19.3 Flujo de venta al credito

1. Se crea cliente si no existe.
2. Se registra venta al credito.
3. Se agregan productos, cantidades y precios.
4. El sistema descuenta stock por Kardex.
5. La venta queda con estado de pago pendiente.
6. Se genera asiento de venta contra cuenta por cobrar.
7. Se genera asiento de costo de venta.
8. Cuando el cliente paga, se registra cobro.
9. El sistema aumenta caja/banco.
10. El sistema genera asiento de cobro.

### 19.4 Flujo de produccion

1. Se crea orden de produccion.
2. Se asigna responsable.
3. Se crean tareas por proceso productivo.
4. Se actualiza avance de tareas.
5. Se cambia estado de orden segun avance operativo.
6. Se consumen materiales desde inventario.
7. El sistema descuenta Kardex.
8. El costo del material se carga a la orden.
9. Se registran costos adicionales: mano de obra, servicio o gasto indirecto.
10. La orden acumula materiales y costos.

### 19.5 Flujo financiero manual

1. Se crea cuenta financiera.
2. Se registra ingreso o egreso.
3. El sistema valida saldo si es egreso.
4. El sistema actualiza saldo.
5. Si se informa cuenta contable, se genera asiento.
6. Se registra auditoria.

### 19.6 Flujo contable

1. Las operaciones generan asientos automaticamente.
2. Cada asiento tiene detalles en Debe y Haber.
3. El sistema valida que el asiento cuadre.
4. El Libro Diario muestra asientos cronologicos.
5. El Libro Mayor agrupa por cuenta.
6. El Balance de Comprobacion resume saldos.
7. Los libros pueden exportarse a CSV.

## 20. Tablas principales del modelo de datos

| Tabla | Proposito |
| --- | --- |
| `users` | Usuarios del sistema. |
| `roles` | Roles de usuario. |
| `permisos` | Permisos funcionales. |
| `rol_permiso` | Relacion entre roles y permisos. |
| `menus` | Menu dinamico del sistema. |
| `categorias` | Categorias de productos. |
| `unidades_medida` | Unidades de medida. |
| `productos` | Catalogo de productos e inventario. |
| `kardex` | Movimientos de inventario. |
| `proveedores` | Proveedores. |
| `entradas_compra` | Cabecera de compras. |
| `detalles_entrada_compra` | Detalle de productos comprados. |
| `clientes` | Clientes. |
| `ventas` | Cabecera de ventas. |
| `detalle_ventas` | Detalle de productos vendidos. |
| `cuentas_financieras` | Caja y cuentas bancarias. |
| `transacciones_financieras` | Ingresos, egresos y transferencias. |
| `cuentas_contables` | Plan contable. |
| `asientos_contables` | Cabecera de asientos contables. |
| `detalle_asientos_contables` | Lineas contables de Debe/Haber. |
| `ordenes_produccion` | Ordenes de produccion. |
| `procesos_produccion` | Procesos disponibles para tareas. |
| `tareas_produccion` | Tareas asignadas dentro de ordenes. |
| `consumos_material` | Materiales consumidos por produccion. |
| `costos_produccion` | Costos adicionales de ordenes. |
| `reportes_tareas` | Base para reportes de tareas. |
| `incidencias_reproceso` | Base para incidencias y reprocesos. |
| `auditorias` | Registro de acciones importantes. |
| `sessions` | Sesiones web. |
| `jobs`, `job_batches`, `failed_jobs` | Infraestructura de colas Laravel. |
| `cache`, `cache_locks` | Infraestructura de cache Laravel. |

## 21. Integraciones internas entre modulos

### 21.1 Compras e inventario

- La compra validada crea entradas de Kardex.
- El Kardex incrementa stock.
- El precio promedio se actualiza segun valor de compra.
- El stock bajo se recalcula automaticamente al consultar dashboards/reportes.

### 21.2 Ventas e inventario

- La venta crea salidas de Kardex.
- El Kardex descuenta stock.
- El costo de salida se toma del promedio vigente.
- La venta no avanza si no hay stock suficiente.

### 21.3 Ventas y tesoreria

- Venta al contado genera ingreso financiero.
- Venta al credito no genera ingreso inmediato.
- Cobros posteriores generan ingresos y actualizan deuda.

### 21.4 Compras y tesoreria

- Pago de compra genera egreso financiero.
- El pago exige saldo suficiente.
- El estado de pago cambia automaticamente a parcial o pagado.

### 21.5 Operaciones y contabilidad

- Ventas generan asiento de venta.
- Ventas generan asiento de costo.
- Compras generan asiento de compra.
- Pagos generan asiento de cancelacion de proveedor.
- Cobros generan asiento de cancelacion de cliente.
- Transferencias generan asiento entre cuentas financieras.
- Consumos de produccion generan asiento de consumo.

### 21.6 Produccion e inventario

- El consumo de materiales descuenta almacen.
- El costo promedio del producto se usa para valorizar el consumo.
- El costo total queda asociado a la orden.

### 21.7 Seguridad y menu

- Los permisos controlan rutas.
- El menu se filtra por permisos.
- El usuario no ve opciones sin acceso.
- El middleware bloquea rutas no permitidas con error 403.

## 22. Validaciones y controles implementados

Controles de datos:

- Codigos unicos en productos, proveedores, cuentas contables y ordenes.
- Documentos unicos en clientes.
- Numeros de documento unicos en compras.
- Fechas obligatorias en operaciones principales.
- Montos positivos.
- Cantidades positivas.
- Precios no negativos.
- Relacion obligatoria con entidades existentes.

Controles de stock:

- Salidas no permiten stock negativo.
- Entradas recalculan promedio ponderado.
- Productos con Kardex no permiten edicion directa de stock actual.
- Movimientos quedan trazados con referencia y usuario.

Controles financieros:

- Egresos no permiten saldo insuficiente.
- Transferencias solo entre cuentas de la misma moneda.
- Pagos de compra no exceden deuda.
- Cobros de venta no exceden saldo pendiente.
- Cobros validan moneda de cuenta contra moneda de venta.
- Pagos de compra se restringen a cuentas PEN.

Controles contables:

- Asientos deben cuadrar Debe/Haber.
- Las cuentas contables deben existir.
- No se crean lineas con monto cero.
- Se evita duplicar asientos para un mismo origen en operaciones definidas como unicas.

Controles de seguridad:

- Login solo para usuarios activos.
- Rutas protegidas por autenticacion.
- Rutas protegidas por permisos.
- Roles inactivos no otorgan permisos.

## 23. Exportaciones implementadas

El sistema exporta archivos CSV separados por punto y coma.

Exportaciones disponibles:

- Reporte de ventas.
- Reporte de compras.
- Reporte de stock.
- Reporte de Kardex.
- Reporte de caja y bancos.
- Libro Diario.
- Libro Mayor.
- Balance de Comprobacion.

Estas exportaciones estan pensadas para revision, analisis en hojas de calculo o entrega a areas administrativas.

## 24. Estado actual de implementacion

Implementado y operativo en codigo:

- Login/logout.
- Usuarios activos/inactivos.
- CRUD de usuarios.
- CRUD de roles y asignacion de permisos.
- Middleware de permisos.
- Menu dinamico por permisos.
- Dashboard operativo.
- Notificaciones globales.
- CRUD de productos.
- CRUD de proveedores.
- CRUD parcial de clientes.
- Entradas de compra con detalle.
- Validacion de compras contra Kardex.
- Pagos de compras.
- Ventas con detalle.
- Ventas al contado y al credito.
- Cobros de ventas.
- Caja y bancos.
- Movimientos financieros.
- Transferencias entre cuentas.
- Plan contable.
- CRUD de cuentas contables.
- Asientos automaticos.
- Libro Diario.
- Libro Mayor.
- Balance de Comprobacion.
- Dashboard gerencial.
- Exportaciones CSV.
- Ordenes de produccion.
- Tareas de produccion.
- Consumo de materiales.
- Costos adicionales.
- Auditoria.
- Seeders iniciales.
- Migraciones completas.

Implementado como base de datos/modelo, con alcance funcional parcial o reservado:

- Reportes de tareas.
- Incidencias y reprocesos.
- Permiso de backups.
- Permiso de creacion de reportes.
- Edicion avanzada de cuentas financieras.

## 25. Alcances y limites actuales

El sistema ya resuelve el flujo operativo principal, pero hay puntos que no aparecen implementados como funcionalidad completa en el codigo revisado:

- No se observa integracion con SUNAT para emision electronica real.
- No se observa generacion de PDF de comprobantes.
- No se observa anulacion operativa completa de ventas con reversion automatica de Kardex, caja y asientos.
- No se observa anulacion operativa completa de compras con reversion automatica de Kardex, caja y asientos desde una pantalla.
- No se observa manejo de tipo de cambio entre PEN y USD.
- No se permite transferir entre cuentas de distinta moneda.
- No se observa conciliacion bancaria automatica.
- No se observa modulo de backup ejecutable desde interfaz, aunque existe permiso reservado.
- No se observa aprobacion por flujo multinivel.
- No se observa control de lotes, series o ubicaciones de almacen.
- No se observa cierre contable mensual.
- No se observa carga masiva por Excel/CSV.
- No se observa API publica documentada.

Estos puntos pueden considerarse oportunidades de mejora o siguientes fases del proyecto.

## 26. Seguridad operativa recomendada

Recomendaciones para uso real:

- Cambiar contrasenas iniciales cargadas por seeders.
- Usar cuentas individuales por usuario.
- Mantener roles separados por responsabilidad.
- No entregar permiso `*` salvo a administradores reales.
- Revisar auditorias periodicamente.
- Hacer backups de base de datos antes de migraciones.
- Evitar borrar operaciones validadas directamente; preferir anulaciones con reversion controlada.
- Revisar rutas y permisos despues de crear nuevos modulos.
- Mantener el archivo `.env` fuera del repositorio publico.

## 27. Recomendaciones de evolucion

Mejoras sugeridas para siguientes versiones:

- Implementar anulacion formal de ventas y compras con reversion automatica.
- Agregar documentos PDF para ventas, compras y movimientos.
- Agregar soporte de tipo de cambio.
- Crear modulo de conciliacion bancaria.
- Crear reportes de rentabilidad por orden de produccion.
- Crear costeo detallado por orden: materiales, mano de obra, servicios y gastos indirectos.
- Agregar cierre mensual contable.
- Agregar control de numeracion de comprobantes.
- Agregar permisos mas granulares para editar, eliminar y aprobar.
- Agregar dashboard por rol.
- Agregar exportacion Excel nativa.
- Agregar importacion de productos, clientes y proveedores.
- Agregar pruebas automatizadas sobre flujos criticos de compra, venta, Kardex y contabilidad.
- Agregar bitacora de anulaciones.
- Agregar control de stock por almacen/ubicacion.
- Agregar alertas configurables por usuario.

## 28. Rutas funcionales principales

| Modulo | Ruta | Funcion |
| --- | --- | --- |
| Login | `/login` | Iniciar sesion. |
| Dashboard | `/dashboard` | Panel operativo general. |
| Inventario | `/inventario/dashboard` | Dashboard de inventario. |
| Productos | `/productos` | Gestion de productos. |
| Kardex | `/inventario/movimientos-kardex` | Historial de movimientos. |
| Movimiento manual | `/inventario/create-movimiento` | Registrar entrada/salida/ajuste manual. |
| Stock bajo | `/inventario/stock-bajo` | Productos bajo minimo. |
| Reporte stock | `/inventario/reporte-stock` | Inventario valorizado. |
| ABC | `/inventario/clasificacion-abc` | Clasificacion ABC. |
| Proveedores | `/proveedores` | Gestion de proveedores. |
| Compras | `/entradas-compra` | Gestion de entradas de compra. |
| Produccion | `/ordenes-produccion` | Ordenes de produccion. |
| Caja y bancos | `/caja-bancos` | Cuentas y saldos financieros. |
| Clientes | `/clientes` | Gestion de clientes. |
| Ventas | `/ventas` | Gestion de ventas. |
| Reportes | `/reportes` | Dashboard gerencial. |
| Contabilidad | `/contabilidad` | Vista general contable. |
| Plan contable | `/contabilidad/plan-cuentas` | Consulta jerarquica de plan. |
| Cuentas contables | `/cuentas-contables` | CRUD de plan contable. |
| Libro Diario | `/contabilidad/libro-diario` | Asientos cronologicos. |
| Libro Mayor | `/contabilidad/libro-mayor` | Movimientos por cuenta. |
| Balance | `/contabilidad/balance-comprobacion` | Balance de comprobacion. |
| Usuarios | `/usuarios` | Gestion de usuarios. |
| Roles | `/roles` | Gestion de roles y permisos. |
| Auditoria | `/auditorias` | Bitacora de operaciones. |

## 29. Pruebas y verificacion observada

En la revision tecnica reciente se verifico:

- Carga correcta de rutas con `php artisan route:list`.
- Compilacion de vistas Blade con `php artisan view:cache`.
- Sintaxis PHP del proyecto.
- Migraciones con seeders sobre una base SQLite temporal.
- Rollback de migraciones.
- Pruebas PHPUnit existentes.

Resultado observado:

- 97 rutas registradas.
- 157 archivos PHP sin errores de sintaxis.
- 6 pruebas automatizadas ejecutadas correctamente.
- 34 aserciones correctas.
- Migraciones y rollback correctos en entorno temporal.

## 30. Conclusiones

PISFIL SIG ya funciona como un sistema integrado de gestion empresarial con enfoque operativo, financiero y contable. Su valor principal esta en que no registra datos aislados: conecta compras, ventas, produccion, inventario, tesoreria y contabilidad.

Las funcionalidades mas relevantes ya implementadas son:

- Kardex automatico con promedio ponderado.
- Control de stock y alertas de stock bajo.
- Compras con validacion logistica y pagos.
- Ventas al contado y credito con cobros.
- Caja y bancos con saldos y transferencias.
- Asientos contables automaticos.
- Libro Diario, Libro Mayor y Balance de Comprobacion.
- Reportes gerenciales y exportaciones.
- Seguridad por roles/permisos.
- Auditoria de operaciones.

El sistema esta en una etapa funcional avanzada para uso interno administrativo y operativo. Para una etapa de produccion mas estricta, las mejoras mas importantes serian anulaciones con reversion contable/operativa, integracion documentaria, tipo de cambio, conciliacion bancaria y controles contables de cierre.
