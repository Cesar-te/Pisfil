# Contexto del Sistema: PISFIL EMSAC

## 1. Descripción General
El sistema es un **Sistema de Información Contable y de Gestión** desarrollado para la empresa **PISFIL EMSAC**. Abarca áreas críticas de la empresa como contabilidad, ventas, compras, inventario y producción.

## 2. Pila Tecnológica (Tech Stack)
- **Framework Backend:** Laravel (PHP 8.3+)
- **Base de Datos:** MySQL
- **Frontend / Construcción de Activos:** Vite (configurado vía `vite.config.js`), NPM
- **Entorno:** Servidor local / Entorno de desarrollo local (`APP_ENV=local`)

## 3. Arquitectura y Módulos Principales
Basado en los modelos de datos existentes, el sistema está compuesto por los siguientes módulos integrados:

### 3.1. Contabilidad y Finanzas
Gestiona las cuentas, el flujo de dinero y las transacciones.
- `CuentaContable`: Registro del Plan de Cuentas (PCGE).
- `CuentaFinanciera`: Gestión de cuentas bancarias y caja.
- `TransaccionFinanciera`: Movimientos y asientos de dinero.

### 3.2. Producción
Control del proceso de manufactura o servicios.
- `OrdenProduccion`: Órdenes principales de trabajo.
- `ProcesoProduccion`: Etapas o flujos de la producción.
- `TareaProduccion`: Actividades específicas asignadas.
- `ReporteTarea`: Seguimiento del trabajo realizado.
- `ConsumoMaterial`: Materiales utilizados en la producción.
- `IncidenciaReproceso`: Control de calidad y corrección de errores.

### 3.3. Inventario y Productos
Gestión de almacén y catalogación.
- `Producto`: Catálogo de bienes o servicios.
- `Categoria`: Agrupación de productos.
- `UnidadMedida`: Gestión de unidades (kg, litros, unidades, etc.).
- `Kardex`: Control de existencias, entradas y salidas valorizadas.

### 3.4. Compras (Proveedores)
Abastecimiento de la empresa.
- `Proveedor`: Directorio de proveedores.
- `EntradaCompra`: Registro de facturas/boletas de compra.
- `DetalleEntradaCompra`: Ítems comprados por cada documento.

### 3.5. Ventas (Clientes)
Gestión comercial.
- `Cliente`: Directorio de clientes.
- `Venta`: Registro de comprobantes de pago emitidos.
- `DetalleVenta`: Ítems vendidos.

### 3.6. Seguridad y Accesos
- `User`: Usuarios del sistema.
- `Rol`: Perfiles y permisos de usuario.

## 4. Documentos de Referencia en el Proyecto
El proyecto cuenta con documentación complementaria almacenada en su raíz:
- **Plan de Cuentas PCGE 2026.pdf**: Estructura contable base.
- **ANÁLISIS Y MODELAMIENTO PRELIMINAR DEL SISTEMA...pdf**: Documento de análisis del sistema.
- **INFORME V2.pdf** / **PAmodeladoTOBEV5.2f.pdf**: Informes de procesos y flujos de trabajo (As-Is / To-Be).

## 5. Integrantes
- Muro Montenegro Alexis
-